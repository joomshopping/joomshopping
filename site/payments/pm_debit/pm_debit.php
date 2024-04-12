<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

class pm_debit extends PaymentRoot{
    
    function showPaymentForm($params, $pmconfigs){
        if (!isset($params['acc_holder'])) $params['acc_holder'] = '';
        if (!isset($params['bank_iban'])) $params['bank_iban'] = '';
        if (!isset($params['bank_bic'])) $params['bank_bic'] = '';
        if (!isset($params['bank'])) $params['bank'] = '';
    	include(dirname(__FILE__)."/paymentform.php");
    }

    function getDisplayNameParams(){
        $names = array('acc_holder' => \JText::_('JSHOP_ACCOUNT_HOLDER'), 'bank_iban' => \JText::_('JSHOP_IBAN'), 'bank_bic' => \JText::_('JSHOP_BIC_BIC'), 'bank' => \JText::_('JSHOP_BANK'));
        return $names;
    }

    function getSavePaymentParams(){
        return true;
    }
    
}