<?php
/**
* @version      5.6.2 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Pagination\Pagination;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Jshopping\Site\Helper\SelectOptions;
defined('_JEXEC') or die();

class CouponsController extends BaseadminController{
    
    protected $urlEditParamId = 'coupon_id';
    
    function init(){
        HelperAdmin::checkAccessController("coupons");
        HelperAdmin::addSubmenu("other");
    }

    function display($cachable = false, $urlparams = false){
        $app = Factory::getApplication();
        $context = "jshoping.list.admin.coupons";
        $limit = $app->getUserStateFromRequest( $context.'limit', 'limit', $app->getCfg('list_limit'), 'int' );
        $limitstart = $app->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "C.coupon_code", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
        $text_search = $app->getUserStateFromRequest( $context.'text_search', 'text_search', '' );
        
        $jshopConfig = JSFactory::getConfig();
        $coupons = JSFactory::getModel("coupons");
        $total = $coupons->getCountItems(['text_search' => $text_search]);
        $pageNav = new Pagination($total, $limitstart, $limit);
        $rows = $coupons->getListItems(
            ['text_search' => $text_search],
            ['order' => $filter_order, 'dir' => $filter_order_Dir],
            ['limitstart'=> $pageNav->limitstart, 'limit' => $pageNav->limit]
        );

        foreach ($rows as $row) {
            $row->tmp_extra_column_cells = "";
        }
        
        $currency = JSFactory::getTable('currency');
        $currency->load($jshopConfig->mainCurrency);
                        
		$view = $this->getView("coupons", 'html');
        $view->setLayout("list");		
        $view->set('rows', $rows);        
        $view->set('currency', $currency->currency_code);
        $view->set('pageNav', $pageNav);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->set('text_search', $text_search);
        $view->tmp_html_start = "";
        $view->tmp_html_filter = "";
        $view->tmp_html_filter_end = "";
        $view->tmp_extra_column_headers = "";
        $view->tmp_html_end = "";
        $view->deltaColspan  = 0;

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayCoupons', array(&$view));		
		$view->displayList(); 
    }
    
    function edit() {
        Factory::getApplication()->input->set('hidemainmenu', true);
        $coupon_id = $this->input->getInt('coupon_id');
        $coupon = JSFactory::getTable('coupon'); 
        $coupon->load($coupon_id);
        $edit = $coupon_id ? 1 : 0;
        
        if (!$coupon_id){
          $coupon->coupon_type = 0;  
          $coupon->finished_after_used = 1;
          $coupon->for_user_id = 0;
        }
        if (Helper::datenull($coupon->coupon_start_date)){
            $coupon->coupon_start_date = '';
        }
        if (Helper::datenull($coupon->coupon_expire_date)){
            $coupon->coupon_expire_date = '';
        }
        $currency_code = Helper::getMainCurrencyCode();
        $lists['coupon_type'] = HTMLHelper::_('select.radiolist', SelectOptions::getCouponType(), 'coupon_type', 'onchange="jshopAdmin.changeCouponType()"', 'id', 'name', $coupon->coupon_type);
        $lists['tax'] = HTMLHelper::_('select.genericlist', SelectOptions::getTaxs(), 'tax_id', 'class = "inputbox form-select"', 'tax_id', 'tax_name', $coupon->tax_id);        
        
        $view = $this->getView("coupons", 'html');
        $view->setLayout("edit");        
        $view->set('coupon', $coupon);        
        $view->set('lists', $lists);        
        $view->set('edit', $edit);
        $view->set('currency_code', $currency_code);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditCoupons', array(&$view));
        $view->displayEdit();
    }
        
}