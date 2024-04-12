<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model; defined('_JEXEC') or die();

class StatisticModel extends BaseadminModel{    

    function getAllOrderStatus() {
        $db = \JFactory::getDBO(); 
        $lang = \JSFactory::getLang();
        $query = "SELECT status_id, `".$lang->get('name')."` as name FROM `#__jshopping_order_status` ORDER BY status_id";
        $db->setQuery($query);
        return $db->loadAssocList();
    }
    
    function getOrderStatistics($time='day')
    {
        $db = \JFactory::getDBO(); 
        $lang = \JSFactory::getLang();
                
		$db->setQuery('SET SQL_BIG_SELECTS=1');
        $db->execute();

        $db->setQuery("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
        $db->execute();
        
        if ($time=='day') $where=" DATE_FORMAT(ord.`order_date`,'%Y-%m-%d')=DATE_FORMAT(NOW(),'%Y-%m-%d') ";
        if ($time=='week') $where=" WEEK(DATE_FORMAT(ord.`order_date`,'%Y-%m-%d'))=WEEK(DATE_FORMAT(NOW(),'%Y-%m-%d')) "; 
        if ($time=='month') $where=" MONTH(DATE_FORMAT(ord.`order_date`,'%Y-%m-%d'))=MONTH(DATE_FORMAT(NOW(),'%Y-%m-%d')) ";   
        if ($time=='year') $where=" YEAR(DATE_FORMAT(ord.`order_date`,'%Y-%m-%d'))=YEAR(DATE_FORMAT(NOW(),'%Y-%m-%d')) ";  
		if ($time=='month' || $time == 'week') $where .=" and YEAR(DATE_FORMAT(ord.`order_date`,'%Y-%m-%d'))=YEAR(DATE_FORMAT(NOW(),'%Y-%m-%d')) ";
         
        $query = "SELECT count(ord.`order_id`) AS amount, SUM(ord.`order_total`/ord.`currency_exchange`) AS total_sum, ord.`order_status`, s.`".$lang->get('name')."` as status_name FROM  `#__jshopping_order_status` AS s
        LEFT JOIN `#__jshopping_orders` AS ord  ON s.`status_id` = ord.`order_status`  
        WHERE ".$where." GROUP BY s.`status_id` ORDER BY s.`status_id` ASC";
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before")); 
        $db->setQuery($query);
        return $db->loadAssocList(); 
         
    }
    
    function getCategoryStatistics()
    {
        $db = \JFactory::getDBO(); 
        $query = "SELECT cat.`category_publish`, count(cat.`category_id`) AS amount FROM  `#__jshopping_categories` AS cat  GROUP BY cat.`category_publish`";
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadAssocList();                    
    }
    
    function getManufactureStatistics() 
    {
        $db = \JFactory::getDBO(); 
        $query = "SELECT man.`manufacturer_publish`, count(man.`manufacturer_id`) AS amount FROM  `#__jshopping_manufacturers` AS man 
        GROUP BY man.`manufacturer_publish`"; 
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadAssocList();                    
    }
    
    function getProductStatistics($stok='') 
    {
        $db = \JFactory::getDBO(); 
        $where="";
        if ($stok=='1') $where = " WHERE pr.`product_quantity`>'0' AND pr.`product_publish`='1' ";
        if ($stok=='2') $where = " WHERE pr.`product_quantity`<='0' AND pr.`product_publish`='1' ";   
        $query = "SELECT pr.`product_publish`, count(pr.`product_id`) AS amount FROM  `#__jshopping_products` AS pr 
        ".$where."
        GROUP BY pr.`product_publish`"; 
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadAssocList();                    
    }
      
    function getProductDownloadStatistics() 
    {
        $db = \JFactory::getDBO(); 

        $query = "SELECT pr.`product_publish`, count(pr.`product_id`) AS amount FROM  `#__jshopping_products` AS pr 
        JOIN `#__jshopping_products_files` AS f  ON f.`product_id` = pr.`product_id` 
        WHERE pr.`product_publish`='1' AND (f.`demo`!='' OR f.`file`!='')
        GROUP BY pr.`product_publish`"; 
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadAssocList();                    
    }
    
    function getUserGroupsStatistics() 
    {
        $db = \JFactory::getDBO(); 
        
        $query = "SELECT ug.`usergroup_name`, count(u.`user_id`) AS amount FROM  `#__jshopping_usergroups` AS ug  
        LEFT JOIN `#__jshopping_users` AS u  ON ug.`usergroup_id` = u.`usergroup_id` 
        LEFT JOIN `#__users` AS us  ON us.`id` = u.`user_id` 
        GROUP BY ug.`usergroup_id`"; 
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadAssocList();                     
    }

    function getUsersStatistics($param='') 
    {
        $db = \JFactory::getDBO(); 
        
        $where="";
        if ($param=='1') $where = " WHERE us.`block`='0' ";
        if ($param=='2') $where = " WHERE s.`userid`!='' ";  
        $query = "SELECT u.`user_id` FROM  `#__jshopping_users` AS u 
        JOIN `#__users` AS us  ON us.`id` = u.`user_id` 
        LEFT JOIN `#__session` AS s ON s.`userid` = us.`id` 
        ".$where." GROUP BY u.`user_id`";
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        $db->execute();
        return $db->getNumRows();
    }
     
    function getUsersStaffStatistics($param='') 
    {
        return array();
    }               
}