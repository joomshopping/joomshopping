<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

class PaymentRoot{

    public $_errormessage = "";
    public $pm_method;
    
    /**
    * show form payment. Checkout Step3
    * @param array $params - entered params
    * @param array $pmconfigs - configs
    */    
    function showPaymentForm($params, $pmconfigs){
    }
    
    /**
    * check payment params. Checkout Step3save
    */
    function checkPaymentInfo($params, $pmconfigs){
        /*$this->setErrorMessage("error mgs");*/
        return 1;
    }
    
    /**
    * list display params name payment saved to order    
    */
    function getDisplayNameParams(){
        return array();
    }
	
	/**
	* get payment params for display in order
	*/
	function getPaymentParamsData($params){
		return $params;
	}

    /**
     * get save payment params
     * @return boolean
     */
    function getSavePaymentParams(){
        return false;
    }
    
    /**
    * get current params
    */
    function getParams(){
        return $this->_ps_params;
    }
    
    /**
    * set params
    */
    function setParams($params){
        $this->_ps_params = $params;
    }
    
    /**
    * Form parametrs. Edit params payment in administrator.
    * static
    */
    function showAdminFormParams($pmconfigs){
    }
    
    /**
    * Show form. Checkout Step6.
    */
    function showEndForm($pmconfigs, $order){        
    }
    
    function setPmMethod($pm_method){
        $this->pm_method = $pm_method;
    }
    
    function getPmMethod(){
        return $this->pm_method;
    }
    
    /**
    * Check Transaction
    * @param array $pmconfigs parametns
    * @param object $order order
    * @param string $act action
    * @return array($rescode, $restext, $transaction, $transactiondata)
    */
    function checkTransaction($pmconfigs, $order, $act){
        return array(1, '', '', array());
    }
    
    /**
    * Get status order from rescode payment
    * @param int $rescode
    * @param array $pmconfigs
    * @return int
    */
    function getStatusFromResCode($rescode, $pmconfigs){
        $status = 0;
        $types_status = array(
            0=>0, 
            1=>$pmconfigs['transaction_end_status'], 
            2=>$pmconfigs['transaction_pending_status'], 
            3=>$pmconfigs['transaction_failed_status'], 
            4=>$pmconfigs['transaction_cancel_status'], 
            5=>$pmconfigs['transaction_open_status'], 
            6=>$pmconfigs['transaction_shipping_status'], 
            7=>$pmconfigs['transaction_refunded_status'], 
            8=>$pmconfigs['transaction_confirm_status'], 
            9=>$pmconfigs['transaction_complete_status'], 
            10=>$pmconfigs['transaction_other_status'],
            99=>0
        );
        if (isset($types_status[$rescode])){
            $status = $types_status[$rescode];
        }
        return $status;
    }
	
	/**
	List code for redirect to checkout
	*/
	function getNoBuyResCode(){
		return array(0, 3, 4);
	}
    
    /**
    * get url parametr for payment. Step7
    */
    function getUrlParams($pmconfigs){
        return array();
    }
    
    /**
    * Exec after notify. Step7.
    */
    function nofityFinish($pmconfigs, $order, $rescode){
    }
    
    /**
    * exec before end. Step7.
    */
    function finish($pmconfigs, $order, $rescode, $act){
    }
    
    /**
    * exec complete. StepFinish.
    */
    function complete($pmconfigs, $order, $payment){
    }
    
    /**
	* exec before mail send
    */
    function prepareParamsDispayMail(&$order, &$pm_method){
    }
    
    /**
    * Set message error check payment
    */
    function setErrorMessage($msg){
        $this->_errormessage = $msg;
    }
    
    /**
    * Get message error check payment. Step3
    */
    function getErrorMessage(){
        if ($this->_errormessage==""){
            $this->_errormessage = \JText::_('JSHOP_ERROR_PAYMENT_DATA');
        }
    return $this->_errormessage;
    }

    /**
     * script after return without check params
     */
    function noCheckReturnExecute($modelCheckout){
    }
}