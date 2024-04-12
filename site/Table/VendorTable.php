<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;
defined('_JEXEC') or die();

class VendorTable extends MultilangTable{

    function __construct(&$_db){
        parent::__construct('#__jshopping_vendors', 'id', $_db);
        \JPluginHelper::importPlugin('jshoppingproducts');
    }
    
    function loadMain(){
        $db = \JFactory::getDBO();
        $query = "SELECT id FROM #__jshopping_vendors WHERE `main`=1";
        extract(\JSHelper::Js_add_trigger(get_defined_vars(), "query"));
        $db->setQuery($query);
        $id = intval($db->loadResult());
        $this->load($id);
    }
    
    function loadFull($id){
        if ($id){
            $this->load($id);
        }else{
            $this->loadMain();
        }
    }
    
	function check(){
        $db = \JFactory::getDBO();
        Jimport('Joomla.mail.helper');
            
	    if(trim($this->f_name) == '') {	    	
		    $this->setError(\JText::_('JSHOP_REGWARN_NAME'));
		    return false;
	    }
        
        if( (trim($this->email == "")) || ! \JMailHelper::isEmailAddress($this->email)) {
            $this->setError(\JText::_('JSHOP_REGWARN_MAIL'));
            return false;
        }
        if ($this->user_id){
            $query = "SELECT id FROM #__jshopping_vendors WHERE `user_id`='".$db->escape($this->user_id)."' AND id!=".(int)$this->id;
            $db->setQuery($query);
            $xid = intval($db->loadResult());
            if ($xid){
                $this->setError(sprintf(\JText::_('JSHOP_ERROR_SET_VENDOR_TO_MANAGER'), $this->user_id));
                return false;
            }
        }
        
	return true;
	}
    
    function getAllVendors($publish=1, $limitstart=0, $limit=0, $orderby = null) {
        $db = \JFactory::getDBO();
        $where = "";
        if (isset($publish)){
            $where = "and `publish`='".$db->escape($publish)."'";
        }
		if (!$orderby){
			$orderby = \JSFactory::getConfig()->get_vendors_order_query;
		}
        $query = "SELECT * FROM `#__jshopping_vendors` where 1 ".$where." ORDER BY ".$orderby;
        $db->setQuery($query, $limitstart, $limit);        
        return $db->loadObJectList();
    }
    
    function getCountAllVendors($publish=1){
        $db = \JFactory::getDBO(); 
        $where = "";
        if (isset($publish)){
            $where = "and `publish`='".$db->escape($publish)."'";
        }
        $query = "SELECT COUNT(id) FROM `#__jshopping_vendors` where 1 ".$where;
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    function prepareViewListVendor(&$rows){
        $JshopConfig = \JSFactory::getConfig();
        foreach($rows as $k=>$v){
            $rows[$k]->link = \JSHelper::SEFLink("index.php?option=com_jshopping&controller=vendor&task=products&vendor_id=".$v->id);
            if (!$v->logo){
                $rows[$k]->logo = $JshopConfig->image_vendors_live_path."/".$JshopConfig->noimage;
            }
        }
        return $rows;
    }
	
	function getCountryName(){
		$country = \JSFactory::getTable('country');
        $country->load($this->country);
        return $country->getName();
	}
	
	public function getCountPerPage(){
		return \JSFactory::getConfig()->count_products_to_page;
	}
	
	public function getCountToRow(){
		return \JSFactory::getConfig()->count_category_to_row;
	}
    
}