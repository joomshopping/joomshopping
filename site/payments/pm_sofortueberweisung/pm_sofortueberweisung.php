<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

class pm_sofortueberweisung extends PaymentRoot{

	//function call in admin
	function showAdminFormParams($params){
	  $array_params = array('user_id', 'project_id', 'project_password', 'notify_password', 'transaction_end_status', 'transaction_pending_status', 'transaction_failed_status');
	  foreach ($array_params as $key){
	  	if (!isset($params[$key])) $params[$key] = '';
	  }
	  $orders = \JSFactory::getModel('orders'); //admin model
      include(dirname(__FILE__)."/adminparamsform.php");
	}

	function checkTransaction($params, $order, $act){

		$order->order_total = $this->fixOrderTotal($order);

        if ($params['user_id'] != $_POST['user_id']){
            return array(0, 'Error user_id. Order ID '.$order->order_id);
        }
        if ($order->order_total != $_POST['amount']){
            return array(0, 'Error amount. Order ID '.$order->order_id);
        }
        if ($order->currency_code_iso != $_POST['currency_id']){
            return array(0, 'Error currency_id. Order ID '.$order->order_id);
        }

        if ($params['notify_password']){
            $params['project_password'] = $params['notify_password'];
        }

        $data = array(
          'transaction' => $_POST['transaction'],
          'user_id' => $_POST['user_id'],
          'project_id' => $_POST['project_id'],
          'sender_holder' => $_POST['sender_holder'],
          'sender_account_number' => $_POST['sender_account_number'],
          'sender_bank_code' => $_POST['sender_bank_code'],
          'sender_bank_name' => $_POST['sender_bank_name'],
          'sender_bank_bic' => $_POST['sender_bank_bic'],
          'sender_iban' => $_POST['sender_iban'],
          'sender_country_id' => $_POST['sender_country_id'],
          'recipient_holder' => $_POST['recipient_holder'],
          'recipient_account_number' => $_POST['recipient_account_number'],
          'recipient_bank_code' => $_POST['recipient_bank_code'],
          'recipient_bank_name' => $_POST['recipient_bank_name'],
          'recipient_bank_bic' => $_POST['recipient_bank_bic'],
          'recipient_iban' => $_POST['recipient_iban'],
          'recipient_country_id' => $_POST['recipient_country_id'],
          'international_transaction' => $_POST['international_transaction'],
          'amount' => $_POST['amount'],
          'currency_id' => $_POST['currency_id'],
          'reason_1' => $_POST['reason_1'],
          'reason_2' => $_POST['reason_2'],
          'security_criteria' => $_POST['security_criteria'],
          'user_variable_0' => $_POST['user_variable_0'],
          'user_variable_1' => $_POST['user_variable_1'],
          'user_variable_2' => $_POST['user_variable_2'],
          'user_variable_3' => $_POST['user_variable_3'],
          'user_variable_4' => $_POST['user_variable_4'],
          'user_variable_5' => $_POST['user_variable_5'],
          'created' => $_POST['created'],
          'project_password' => $params['project_password']
        );

        $data_implode = implode('|', $data);
        $hash = sha1($data_implode);

        $return = 0;

        if ($_POST['security_criteria']){
            if ($_POST['hash']==$hash){
                $return = 1;
            }else{
                \JSHelper::saveToLog("paymentdata.log", "Error hash. ".$hash);
            }
        }

    return array($return, "");
	}

	function showEndForm($params, $order) {
        $return_url = ltrim(\JSHelper::SEFLink('index.php?option=com_jshopping&controller=checkout&task=step7&act=return&js_paymentclass=pm_sofortueberweisung', 0, 1), '/');
        $cancel_url = ltrim(\JSHelper::SEFLink('index.php?option=com_jshopping&controller=checkout&task=step7&act=cancel&js_paymentclass=pm_sofortueberweisung', 0, 1), '/');
        $notify_url = ltrim(\JSHelper::SEFLink('index.php?option=com_jshopping&controller=checkout&task=step7&act=notify&js_paymentclass=pm_sofortueberweisung&no_lang=1', 0, 1), '/');
        $inputs     = [
            'user_id'               => $params['user_id'],
            'project_id'            => $params['project_id'],
            'sender_holder'         => '',
            'sender_account_number' => '',
            'sender_bank_code'      => '',
            'sender_country_id'     => '',
            'amount'                => $this->fixOrderTotal($order),
            'currency_id'           => $order->currency_code_iso,
            'reason_1'              => sprintf(\JText::_('JSHOP_PAYMENT_NUMBER'), $order->order_number),
            'reason_2'              => '',
            'user_variable_0'       => $order->order_id,
            'user_variable_1'       => $return_url,
            'user_variable_2'       => $cancel_url,
            'user_variable_3'       => $notify_url,
            'user_variable_4'       => '',
            'user_variable_5'       => '',
            'project_password'      => $params['project_password']
        ];
        $inputs     = array_merge(
            $inputs,
            [
                'hash'              => sha1(implode('|', $inputs)),
                'interface_version' => (
                    'joomshopping_' .
                    \JInstaller::parseXMLInstallFile(
                        \JSFactory::getConfig()->admin_path . 'jshopping.xml'
                    )['version']
                )
            ]
        );
        ?>
            <?php echo \JText::_('JSHOP_REDIRECT_TO_PAYMENT_PAGE')?>
            <form id="paymentform" action="https://www.sofortueberweisung.de/payment/start" name="paymentform" method="post">
                <?php
                    foreach ($inputs as $name => $value) {
                        if ($name !== 'project_password' && $value !== '') {
                            echo '<input type="hidden" name="' . $name . '" value="' . $value . '">' . "\n\t\t\t\t";
                        }
                    }
                ?>
            </form>
            <script>
                document.getElementById('paymentform').submit();
            </script>
        <?php
        die;
	}

    function getUrlParams($pmconfigs){
        $params = array();
        $params['order_id'] = \JFactory::getApplication()->input->getInt("user_variable_0");
        $params['hash'] = "";
        $params['checkHash'] = 0;
        $params['checkReturnParams'] = 0;
    return $params;
    }

	function fixOrderTotal($order){
        $total = $order->order_total;
        if ($order->currency_code_iso=='HUF'){
            $total = round($total);
        }else{
            $total = number_format($total, 2, '.', '');
        }
    return $total;
    }
}