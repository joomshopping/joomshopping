<?php
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Factory;

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
	  $orders = JSFactory::getModel('orders'); //admin model
      include(dirname(__FILE__)."/adminparamsform.php");
	}

	function checkTransaction($params, $order, $act){
        $post = Factory::getApplication()->input->post->getArray();
		$order->order_total = $this->fixOrderTotal($order);

        if ($params['user_id'] != $post['user_id']){
            return array(0, 'Error user_id. Order ID '.$order->order_id);
        }
        if ($order->order_total != $post['amount']){
            return array(0, 'Error amount. Order ID '.$order->order_id);
        }
        if ($order->currency_code_iso != $post['currency_id']){
            return array(0, 'Error currency_id. Order ID '.$order->order_id);
        }

        if ($params['notify_password']){
            $params['project_password'] = $params['notify_password'];
        }

        $data = array(
          'transaction' => $post['transaction'],
          'user_id' => $post['user_id'],
          'project_id' => $post['project_id'],
          'sender_holder' => $post['sender_holder'],
          'sender_account_number' => $post['sender_account_number'],
          'sender_bank_code' => $post['sender_bank_code'],
          'sender_bank_name' => $post['sender_bank_name'],
          'sender_bank_bic' => $post['sender_bank_bic'],
          'sender_iban' => $post['sender_iban'],
          'sender_country_id' => $post['sender_country_id'],
          'recipient_holder' => $post['recipient_holder'],
          'recipient_account_number' => $post['recipient_account_number'],
          'recipient_bank_code' => $post['recipient_bank_code'],
          'recipient_bank_name' => $post['recipient_bank_name'],
          'recipient_bank_bic' => $post['recipient_bank_bic'],
          'recipient_iban' => $post['recipient_iban'],
          'recipient_country_id' => $post['recipient_country_id'],
          'international_transaction' => $post['international_transaction'],
          'amount' => $post['amount'],
          'currency_id' => $post['currency_id'],
          'reason_1' => $post['reason_1'],
          'reason_2' => $post['reason_2'],
          'security_criteria' => $post['security_criteria'],
          'user_variable_0' => $post['user_variable_0'],
          'user_variable_1' => $post['user_variable_1'],
          'user_variable_2' => $post['user_variable_2'],
          'user_variable_3' => $post['user_variable_3'],
          'user_variable_4' => $post['user_variable_4'],
          'user_variable_5' => $post['user_variable_5'],
          'created' => $post['created'],
          'project_password' => $params['project_password']
        );

        $data_implode = implode('|', $data);
        $hash = sha1($data_implode);

        $return = 0;

        if ($post['security_criteria']){
            if ($post['hash']==$hash){
                $return = 1;
            }else{
                Helper::saveToLog("paymentdata.log", "Error hash. ".$hash);
            }
        }

    return array($return, "");
	}

	function showEndForm($params, $order) {
        $return_url = ltrim(Helper::SEFLink('index.php?option=com_jshopping&controller=checkout&task=step7&act=return&js_paymentclass=pm_sofortueberweisung', 1, 1), '/');
        $cancel_url = ltrim(Helper::SEFLink('index.php?option=com_jshopping&controller=checkout&task=step7&act=cancel&js_paymentclass=pm_sofortueberweisung', 1, 1), '/');
        $notify_url = ltrim(Helper::SEFLink('index.php?option=com_jshopping&controller=checkout&task=step7&act=notify&js_paymentclass=pm_sofortueberweisung&no_lang=1', 1, 1), '/');
        $inputs     = [
            'user_id'               => $params['user_id'],
            'project_id'            => $params['project_id'],
            'sender_holder'         => '',
            'sender_account_number' => '',
            'sender_bank_code'      => '',
            'sender_country_id'     => '',
            'amount'                => $this->fixOrderTotal($order),
            'currency_id'           => $order->currency_code_iso,
            'reason_1'              => sprintf(Text::_('JSHOP_PAYMENT_NUMBER'), $order->order_number),
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
                    Installer::parseXMLInstallFile(
                        JSFactory::getConfig()->admin_path . 'jshopping.xml'
                    )['version']
                )
            ]
        );
        ?>
		<html>
            <head>
                <meta http-equiv="content-type" content="text/html; charset=utf-8" />            
            </head>
            <body>
            <?php echo Text::_('JSHOP_REDIRECT_TO_PAYMENT_PAGE')?>
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
			</body>
        </html>
        <?php
        die;
	}

    function getUrlParams($pmconfigs){
        $params = array();
        $params['order_id'] = Factory::getApplication()->input->getInt("user_variable_0");
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