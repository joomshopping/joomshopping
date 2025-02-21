<?php
/**
* @version      5.2.1 08.09.2023
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
defined('_JEXEC') or die();

class CheckoutBuyModel  extends CheckoutModel{

	private $act;
	private $payment_method_class;
	private $no_lang = 0;
	private $pm_method;
	private $payment_system;
	private $pmconfigs;
	private $urlParamsPS;
	private $order_id;
	private $hash;
	private $checkHash;
	private $checkReturnParams;
	private $checkTransactionResCode;
	private $checkTransactionResText;

	public function setAct($act){
		$this->act = $act;
	}

	public function getAct(){
		return $this->act;
	}

	public function setPaymentMethodClass($payment_method_class){
		$this->payment_method_class = $payment_method_class;
	}

	public function getPaymentMethodClass(){
		return $this->payment_method_class;
	}

	public function setNoLang($no_lang){
		$this->no_lang = $no_lang;
	}

	public function getNoLang(){
		return $this->no_lang;
	}

	public function setPmMethod(&$pm_method){
		$this->pm_method = $pm_method;
	}

	public function getPmMethod(){
		return $this->pm_method;
	}

	public function setPaymentSystem(&$val){
		$this->payment_system = $val;
	}

	public function getPaymentSystem(){
		return $this->payment_system;
	}

	public function setPmConfigs(&$val){
		$this->pmconfigs = $val;
	}

	public function getPmConfigs(){
		return $this->pmconfigs;
	}

	public function setUrlParamsPS(&$val){
		$this->urlParamsPS = $val;
	}

	public function getUrlParamsPS(){
		return $this->urlParamsPS;
	}

	public function setOrderId($val){
		$this->order_id = $val;
	}

	public function getOrderId(){
		return $this->order_id;
	}

	public function setHash($val){
		$this->hash = $val;
	}

	public function getHash(){
		return $this->hash;
	}

	public function setCheckHash($val){
		$this->checkHash = $val;
	}

	public function getCheckHash(){
		return $this->checkHash;
	}

	public function setCheckReturnParams($val){
		$this->checkReturnParams = $val;
	}

	public function getCheckReturnParams(){
		return $this->checkReturnParams;
	}

	public function setCheckTransactionResCode($rescode){
		$this->checkTransactionResCode = $rescode;
	}

	public function getCheckTransactionResCode(){
		return $this->checkTransactionResCode;
	}

	public function setCheckTransactionResText($val){
		$this->checkTransactionResText = $val;
	}

	public function getCheckTransactionResText(){
		return $this->checkTransactionResText;
	}

	public function loadUrlParams(){
		$pm_method = JSFactory::getTable('paymentMethod');
        $pm_method->loadFromClass($this->payment_method_class);
		$load_by_scriptname = 0;
		if (!$pm_method->payment_id) {
			$pm_method->loadFromClass($this->payment_method_class, 1);
			$load_by_scriptname = 1;
			Helper::saveToLog("payment.log", "load url by scriptname ".$this->payment_method_class);
		}

        $paymentsysdata = $pm_method->getPaymentSystemData();
        $payment_system = $paymentsysdata->paymentSystem;
        if ($paymentsysdata->paymentSystemVerySimple){
            if ($this->no_lang){
				JSFactory::loadLanguageFile();
			}
            Helper::saveToLog("payment.log", "#001 - Error payment method file. PM ".$this->payment_method_class);
            $this->setError(Text::_('JSHOP_ERROR_PAYMENT'));
            return 0;
        }

        $pmconfigs = $pm_method->getConfigs();
        $urlParamsPS = $payment_system->getUrlParams($pmconfigs);

        $order_id = $urlParamsPS['order_id'];
        $hash = $urlParamsPS['hash'];
        $checkHash = $urlParamsPS['checkHash'];
        $checkReturnParams = $urlParamsPS['checkReturnParams'];
		
		if ($load_by_scriptname && $order_id) {
			$order = JSFactory::getTable('order');
			$order->load($order_id);
			$pm_method->load($order->payment_method_id);
			$paymentsysdata = $pm_method->getPaymentSystemData();
			$payment_system = $paymentsysdata->paymentSystem;
			if ($this->payment_method_class != $pm_method->scriptname || !$order->payment_method_id) {
				Helper::saveToLog("payment.log", "#0011 - Error load by script name ".$this->payment_method_class." / order_id ".$order_id);
				$this->setError(Text::_('JSHOP_ERROR_PAYMENT'));
				return 0;
			}
			$this->payment_method_class = $pm_method->payment_class;
			$pmconfigs = $pm_method->getConfigs();
			Helper::saveToLog("payment.log", "payment alias ".$pm_method->payment_class." / order_id ".$order_id);
		}
		if ($load_by_scriptname && !$order_id) {
			Helper::saveToLog("payment.log", "info: real payment not loaded by scriptname");
		}

		$this->setPmMethod($pm_method);
		$this->setPaymentSystem($payment_system);
		$this->setPmConfigs($pmconfigs);
		$this->setUrlParamsPS($urlParamsPS);
		$this->setOrderId($order_id);
		$this->setHash($hash);
		$this->setCheckHash($checkHash);
		$this->setCheckReturnParams($checkReturnParams);

		return 1;
	}

	public function buy(){
		$jshopConfig = JSFactory::getConfig();
		$dispatcher = Factory::getApplication();
		$order_id = $this->getOrderId();
		$checkHash = $this->getCheckHash();
		$hash = $this->getHash();
		$pmconfigs = $this->getPmConfigs();
		$act = $this->getAct();
		$payment_system = $this->getPaymentSystem();
		$pm_method = $this->getPmMethod();

		$order = JSFactory::getTable('order');
        $order->load($order_id);

		if ($jshopConfig->order_change_status_reload_global_lang) {
			$globalLanguage = Factory::getContainer()->get(\Joomla\CMS\Language\LanguageFactoryInterface::class)->createLanguage($order->getLang());
			Factory::$language = $globalLanguage;
		}
		JSFactory::loadLanguageFile($order->getLang(), true);
		$lang = JSFactory::getLang($order->getLang());

        if ($checkHash && $order->order_hash != $hash){
            Helper::saveToLog("payment.log", "#003 - Error order hash. Order id ".$order_id);
            $this->setError(Text::_('JSHOP_ERROR_ORDER_HASH'));
            return 0;
        }

        if (!$order->payment_method_id){
            Helper::saveToLog("payment.log", "#004 - Error payment method id. Order id ".$order_id);
            $this->setError(Text::_('JSHOP_ERROR_PAYMENT'));
            return 0;
        }

        if ($order->payment_method_id!=$pm_method->payment_id){
            $pm_method_order = JSFactory::getTable('paymentmethod');
            $pm_method_order->load($order->payment_method_id);
            if($pm_method_order->scriptname != $pm_method->scriptname || $pm_method_order->scriptname == '') {
            	Helper::saveToLog("payment.log", "#005 - Error payment method set url. Order id ".$order_id);
	            $this->setError(Text::_('JSHOP_ERROR_PAYMENT'));
	            return 0;
            }
        }

        $res = $payment_system->checkTransaction($pmconfigs, $order, $act);
        $rescode = $res[0];
        $restext = $res[1];
		$transaction = $res[2] ?? null;
        $transactiondata = $res[3] ?? null;

        $status = $payment_system->getStatusFromResCode($rescode, $pmconfigs);
		if (isset($transaction)) {
			$order->saveTransaction($transaction);
		}
        $order->saveTransactionData($rescode, $status, $transaction, $transactiondata);

        if ($restext!=''){
            Helper::saveToLog("payment.log", $restext);
        }

		if ($status) {
			$need_create_order = (!in_array($status, $jshopConfig->payment_status_no_create_order));
			$prev_order_status_data = $order->orderCreateAndSetStatus($status, $need_create_order);
			if (!$prev_order_status_data->order_created && $need_create_order) {
				$order = JSFactory::getTable('order');
        		$order->load($order_id);
				JSFactory::getModel('checkoutorder', 'Site')->couponFinished($order);
				$obj = $this;
				$dispatcher->triggerEvent('onStep7OrderCreated', array(&$order, &$res, &$obj, &$pmconfigs));
				$order->store();
				if ($jshopConfig->send_order_email){
					$this->sendOrderEmail($order->order_id);
				}
				$this->changeStatusOrder($order_id, $status, 0, $prev_order_status_data->order_status, 1);
			} elseif ($prev_order_status_data->order_status != $status) {
				$email_send = $prev_order_status_data->order_created && (!in_array($status, $jshopConfig->payment_status_no_send_mail_status));
				$this->changeStatusOrder($order_id, $status, $email_send, $prev_order_status_data->order_status, $email_send);
			}
		}

		$this->setCheckTransactionResCode($rescode);
		$this->setCheckTransactionResText($restext);

		$order = JSFactory::getTable('order');
        $order->load($order_id);

		$obj = $this;
        $dispatcher->triggerEvent('onStep7BefereNotify', array(&$order, &$obj, &$pmconfigs));

        if ($act == "notify"){
            $payment_system->nofityFinish($pmconfigs, $order, $rescode);
            return 2;
        }

        $payment_system->finish($pmconfigs, $order, $rescode, $act);

		return 1;
	}

	public function checkTransactionNoBuyCode(){
		$rescode = $this->getCheckTransactionResCode();
		$payment_system = $this->getPaymentSystem();
		$noBuyResCode = $payment_system->getNoBuyResCode();
		return in_array($rescode, $noBuyResCode);
	}

	public function saveToLogPaymentData(){
		$str = "url: ".$_SERVER['REQUEST_URI']."\n";
        foreach($_POST as $k=>$v) $str .= $k."=".$v."\n";		
        Helper::saveToLog("paymentdata.log", $str);
		$raw = file_get_contents('php://input');
		if ($raw) Helper::saveToLog("paymentdata.log", $raw);
	}

    public function noCheckReturnExecute(){
        $this->getPaymentSystem()->noCheckReturnExecute($this);
    }

}