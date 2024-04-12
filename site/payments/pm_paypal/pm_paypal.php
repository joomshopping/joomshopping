<?php
/**
 * @version      5.0.0 15.09.2018
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */
defined('_JEXEC') or die();

class pm_paypal extends PaymentRoot {

    private $curlopt_sslversion = 6;

    //function call in admin
    function showAdminFormParams($params){
        $array_params = array('testmode', 'email_received', 'transaction_end_status', 'transaction_pending_status', 'transaction_failed_status', 'rm1', 'checkdatareturn', 'address_override', 'notifyurlsef');
      foreach ($array_params as $key){
        if (!isset($params[$key])) $params[$key] = '';
      }
      if (!isset($params['address_override'])) $params['address_override'] = 0;
      
      $orders = \JSFactory::getModel('orders'); //admin model
      include(dirname(__FILE__)."/adminparamsform.php");
    }

    function checkTransaction($pmconfigs, $order, $act) {
        $jshopConfig = \JSFactory::getConfig();

        if ($pmconfigs['testmode']) {
            $host = "www.sandbox.paypal.com";
        } else {
            $host = "www.paypal.com";
        }

        $post = \JFactory::getApplication()->input->post->getArray();
        $order->order_total = $this->fixOrderTotal($order);
        
        $opending = 0;
        if ($order->order_total != $_POST['mc_gross'] || $order->currency_code_iso != $_POST['mc_currency']) {
            $opending = 1;
        }

        $payment_status = trim($post['payment_status']);
        $transaction = $post['txn_id'];
        $transactiondata = array('txn_id' => $post['txn_id'], 'payer_email' => $post['payer_email'], 'mc_gross' => $post['mc_gross'], 'mc_currency' => $post['mc_currency'], 'payment_status' => $post['payment_status']);

        if (strtolower($pmconfigs['email_received']) != strtolower($_POST['business']) && strtolower($pmconfigs['email_received']) != strtolower($_POST['receiver_email'])) {
            return array(0, 'Error email received. Order ID ' . $order->order_id, $transaction, $transactiondata);
        }

        $req = 'cmd=_notify-validate';
        if (function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exists = true;
        }
        foreach ($_POST as $key => $value){
            if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                $value = urlencode(stripslashes($value));
            } else {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }

        $ch = curl_init('https://' . $host . '/cgi-bin/webscr');
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_SSLVERSION, $this->curlopt_sslversion);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PayPal-PHP-SDK');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
        if (!($res = curl_exec($ch))) {
            \JSHelper::saveToLog("payment.log", "Paypal failed: " . curl_error($ch) . '(' . curl_errno($ch) . ')');
            curl_close($ch);
            exit;
        } else {
            curl_close($ch);
        }
        \JSHelper::saveToLog("paymentdata.log", "RES: $res");

        if (strcmp($res, "VERIFIED") == 0) {
            if ($payment_status == 'Completed') {
                if ($opending) {
                    \JSHelper::saveToLog("payment.log", "Status pending. Order ID " . $order->order_id . ". Error mc_gross or mc_currency.");
                    return array(2, "Status pending. Order ID " . $order->order_id, $transaction, $transactiondata);
                } else {
                    return array(1, '', $transaction, $transactiondata);
                }
            } elseif ($payment_status == 'Pending') {
                \JSHelper::saveToLog("payment.log", "Status pending. Order ID " . $order->order_id . ". Reason: " . $_POST['pending_reason']);
                return array(2, trim(stripslashes($_POST['pending_reason'])), $transaction, $transactiondata);
            } else {
                return array(3, "Status $payment_status. Order ID " . $order->order_id, $transaction, $transactiondata);
            }
        } else if (strcmp($res, "INVALID") == 0) {
            return array(0, 'Invalid response. Order ID ' . $order->order_id, $transaction, $transactiondata);
        }
    }

    function showEndForm($pmconfigs, $order) {
        $jshopConfig = \JSFactory::getConfig();
        $pm_method = $this->getPmMethod();
        $item_name = sprintf(\JText::_('JSHOP_PAYMENT_NUMBER'), $order->order_number);

        if ($pmconfigs['testmode']) {
            $host = "www.sandbox.paypal.com";
        } else {
            $host = "www.paypal.com";
        }
        $email = $pmconfigs['email_received'];
        $address_override = isset($pmconfigs['address_override']) ? (int)$pmconfigs['address_override'] : 0;

        $uri = \JURI::getInstance();
        $liveurlhost = $uri->toString(array("scheme", 'host', 'port'));

        if (isset($pmconfigs['notifyurlsef']) && $pmconfigs['notifyurlsef']){
            $notify_url = $liveurlhost . \JSHelper::SEFLink("index.php?option=com_jshopping&controller=checkout&task=step7&act=notify&js_paymentclass=" . $pm_method->payment_class . "&no_lang=1");
        } else {
            $notify_url = \JURI::root() . "index.php?option=com_jshopping&controller=checkout&task=step7&act=notify&js_paymentclass=" . $pm_method->payment_class . "&no_lang=1";
        }
        $return = $liveurlhost . \JSHelper::SEFLink("index.php?option=com_jshopping&controller=checkout&task=step7&act=return&js_paymentclass=" . $pm_method->payment_class);
        $cancel_return = $liveurlhost . \JSHelper::SEFLink("index.php?option=com_jshopping&controller=checkout&task=step7&act=cancel&js_paymentclass=" . $pm_method->payment_class);

        $_country = \JSFactory::getTable('country');
        $_country->load($order->d_country);
        $country = $_country->country_code_2;
        $order->order_total = $this->fixOrderTotal($order);
        
        if (isset($pmconfigs['rm1']) && $pmconfigs['rm1']==1){
            $rm = 1;
        }else{
            $rm = 2;
        }
        ?>
        <html>
            <head>
                <meta http-equiv="content-type" content="text/html; charset=utf-8" />            
            </head>
            <body>
                <form id="paymentform" action="https://<?php print $host ?>/cgi-bin/webscr" name = "paymentform" method = "post">
                    <input type='hidden' name='cmd' value='_xclick'>
                    <input type='hidden' name='business' value='<?php print $email ?>'>        
                    <input type='hidden' name='notify_url' value='<?php print $notify_url ?>'>
                    <input type='hidden' name='return' value='<?php print $return ?>'>
                    <input type='hidden' name='cancel_return' value='<?php print $cancel_return ?>'>
                    <input type='hidden' name='rm' value='<?php print $rm?>'>
                    <input type='hidden' name='handling' value='0.00'>
                    <input type='hidden' name='tax' value='0.00'>
                    <input type='hidden' name='charset' value='utf-8'>
                    <input type='hidden' name='no_shipping' value='1'>
                    <input type='hidden' name='no_note' value='1'>
                    <input type='hidden' name='item_name' value='<?php print $item_name; ?>'>
                    <input type='hidden' name='custom' value='<?php print $order->order_id ?>'>
                    <input type='hidden' name='invoice' value='<?php print $order->order_id ?>'>
                    <input type='hidden' name='amount' value='<?php print $order->order_total ?>'>
                    <input type='hidden' name='currency_code' value='<?php print $order->currency_code_iso ?>'>
                    <input type='hidden' name='address_override' value='<?php print $address_override ?>'>
                    <input type='hidden' name='country' value='<?php print $country ?>'>
                    <input type='hidden' name='first_name' value='<?php print $order->d_f_name ?>'>
                    <input type='hidden' name='last_name' value='<?php print $order->d_l_name ?>'>  
                    <input type='hidden' name='address1' value='<?php print $order->d_street ?>'>  
                    <input type='hidden' name='city' value='<?php print $order->d_city ?>'>  
                    <input type='hidden' name='state' value='<?php print $order->d_state ?>'>
                    <input type='hidden' name='zip' value='<?php print $order->d_zip ?>'>
                    <input type='hidden' name='night_phone_b' value='<?php print $order->d_phone ?>'>
                    <input type='hidden' name='email' value='<?php print $order->email ?>'>
                    <input type='hidden' name='bn' value='JoomShopping_Cart_ECM'>
                </form>        
                <?php print \JText::_('JSHOP_REDIRECT_TO_PAYMENT_PAGE')?>
                <br>
                <script type="text/javascript">document.getElementById('paymentform').submit();</script>
            </body>
        </html>
        <?php
        die();
    }

    function getUrlParams($pmconfigs) {
        $params = array();
        $params['order_id'] = \JFactory::getApplication()->input->getInt("custom");
        $params['hash'] = "";
        $params['checkHash'] = 0;
        $params['checkReturnParams'] = $pmconfigs['checkdatareturn'];
        return $params;
    }

    function fixOrderTotal($order) {
        $total = $order->order_total;
        if ($order->currency_code_iso == 'HUF') {
            $total = round($total);
        } else {
            $total = number_format($total, 2, '.', '');
        }
        return $total;
    }

}
