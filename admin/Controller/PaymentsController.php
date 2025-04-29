<?php
/**
* @version      5.6.2 09.04.2024
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Helper\SelectOptions;
defined('_JEXEC') or die();
include_once(JPATH_COMPONENT_SITE."/payments/payment.php");

class PaymentsController extends BaseadminController{
    
    protected $urlEditParamId = 'payment_id';

    function init(){
        HelperAdmin::checkAccessController("payments");
        HelperAdmin::addSubmenu("other");
    }
	
    function display($cachable = false, $urlparams = false) {
        $jshopConfig = JSFactory::getConfig();
        $payments = JSFactory::getModel("payments");
        $app = Factory::getApplication();
        $context = "jshoping.list.admin.payments";
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "payment_ordering", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        
        $rows = $payments->getListItems([], ['order' => $filter_order, 'dir' => $filter_order_Dir]);

        foreach ($rows as $row) {
            $row->tmp_extra_column_cells = "";
        }

        $view = $this->getView("payments", 'html');
        $view->setLayout("list");
	    $view->set('rows', $rows);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->set('config', $jshopConfig);
        $view->tmp_html_start = "";
        $view->tmp_extra_column_headers = "";
        $view->tmp_html_end = "";
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayPayments', array(&$view));
        $view->displayList();
    }
	
    function edit(){
        Factory::getApplication()->input->set('hidemainmenu', true);
        $jshopConfig = JSFactory::getConfig();
        $payment_id = $this->input->getInt("payment_id");
        $payment = JSFactory::getTable('paymentmethod');
        $payment->load($payment_id);        
        $params = $payment->getConfigs();        
        $edit = ($payment_id)?($edit = 1):($edit = 0);
                
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
		
        if ($edit){
            $paymentsysdata = $payment->getPaymentSystemData();
            if ($paymentsysdata->paymentSystem){
                ob_start();                
                $paymentsysdata->paymentSystem->showAdminFormParams($params);
                $lists['html'] = ob_get_contents();
                ob_get_clean();
            }else{
                $lists['html'] = '';
            }
		} else {
			$lists['html'] = '';
        }
        
        if ($jshopConfig->tax){
            $lists['tax'] = HTMLHelper::_('select.genericlist', SelectOptions::getTaxs(0, 0, array('product_tax_rate'=>1)), 'tax_id', 'class = "inputbox form-select"','tax_id','tax_name', $payment->tax_id);
        }

        $lists['price_type'] = HTMLHelper::_('select.genericlist', SelectOptions::getPaymentPriceTypes(), 'price_type', 'class = "inputbox form-select"', 'id', 'name', $payment->price_type);

        if ($jshopConfig->shop_mode==0 && $payment_id){
            $disabled = 'disabled';
        }else{
            $disabled = '';
        }
        $lists['type_payment'] = HTMLHelper::_('select.genericlist', SelectOptions::getPaymentType(), 'payment_type','class = "inputbox form-select" '.$disabled, 'id','name', $payment->payment_type);
        $lists['access'] = HTMLHelper::_('select.genericlist', SelectOptions::getAccessGroups(), 'access','class = "inputbox form-select"','id','title', $payment->access);
        
        $nofilter = array();
        OutputFilter::objectHTMLSafe($payment, ENT_QUOTES, $nofilter);
        
        $view = $this->getView("payments", 'html');
        $view->setLayout("edit");
        $view->set('payment', $payment);
        $view->set('edit', $edit);
        $view->set('params', $params);
        $view->set('lists', $lists);
        $view->set('languages', $languages);
        $view->set('multilang', $multilang);
        $view->set('config', $jshopConfig);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditPayments', array(&$view));
        $view->displayEdit();
    }

    function copy(){
        $cid = $this->input->getInt('cid');
        $text = JSFactory::getModel("payments")->copyList($cid);
        $this->setRedirect($this->getUrlListItems(), Text::_('JSHOP_ITEMS_WAS_COPIED'));
    }
	   
}