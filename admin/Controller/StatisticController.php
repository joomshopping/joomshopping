<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
defined( '_JEXEC' ) or die();

class StatisticController extends BaseadminController{

    function init(){
        \JSHelperAdmin::addSubmenu("other");
        \JSHelperAdmin::checkAccessController("statistic");
    }
    
    function display($cachable = false, $urlparams = false){        
        $jshopConfig = \JSFactory::getConfig(); 
        $_statisctic = \JSFactory::getModel("statistic");
        $rows = $_statisctic->getAllOrderStatus();

        $today = $_statisctic->getOrderStatistics('day');  
        $week = $_statisctic->getOrderStatistics('week');  
        $month = $_statisctic->getOrderStatistics('month');  
        $year = $_statisctic->getOrderStatistics('year');  

        $category = $_statisctic->getCategoryStatistics(); 
        $manufacture = $_statisctic->getManufactureStatistics();     
        $product = $_statisctic->getProductStatistics() ; 
        $pr_instok = $_statisctic->getProductStatistics('1') ; 
        $pr_outstok = $_statisctic->getProductStatistics('2') ; 
        $pr_download = $_statisctic->getProductDownloadStatistics() ;
        
        $customer= $_statisctic->getUsersStatistics();  
        $customer_enabled= $_statisctic->getUsersStatistics('1'); 
        $customer_loggedin= $_statisctic->getUsersStatistics('2');  
        
        $stuff1= $_statisctic->getUsersStaffStatistics('1'); 
        $stuff2= $_statisctic->getUsersStaffStatistics('2'); 
        $stuff3= $_statisctic->getUsersStaffStatistics('3'); 

        $usergroups= $_statisctic->getUserGroupsStatistics();

        $view=$this->getView("statistic", 'html');        
        $view->set('rows', $rows);   
        $view->set('today', $today);   
        $view->set('week', $week); 
        $view->set('month', $month); 
        $view->set('year', $year); 
        $view->set('paid_status', $jshopConfig->payment_status_enable_download_sale_file);    
        $view->set('category', $category);  
        $view->set('manufacture', $manufacture);  
        $view->set('product', $product); 
        $view->set('pr_instok', $pr_instok);  
        $view->set('pr_outstok', $pr_outstok);  
        $view->set('pr_download', $pr_download); 
        $view->set('customer', $customer);  
        $view->set('customer_enabled', $customer_enabled); 
        $view->set('customer_loggedin', $customer_loggedin);  
        $view->set('stuff1', $stuff1);   
        $view->set('stuff2', $stuff2);  
        $view->set('stuff3', $stuff3); 
        $view->set('usergroups', $usergroups);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayStatistic', array(&$view));
        $view->display();        
    }
    
    
}