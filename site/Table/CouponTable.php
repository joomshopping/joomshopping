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

class CouponTable extends ShopbaseTable{
    
    function __construct( &$_db ){
        parent::__construct('#__jshopping_coupons', 'coupon_id', $_db);
    }
    
    function getExistCode(){
        $db = \JFactory::getDBO();
        $query = "SELECT `coupon_id` FROM `#__jshopping_coupons`
                  WHERE `coupon_code` = '" . $db->escape($this->coupon_code) . "' AND `coupon_id` <> '" . $db->escape($this->coupon_id) . "'";
		extract(\JSHelper::Js_add_trigger(get_defined_vars(), "query"));
        $db->setQuery($query);
        $db->execute();
        return $db->getNumRows();
    }
    
    function getEnableCode($code){
        $JshopConfig = \JSFactory::getConfig();
        $db = \JFactory::getDBO();
        if(!$JshopConfig->use_rabatt_code) {
            $this->error = \JText::_('JSHOP_RABATT_NON_SUPPORT');
            return 0;
        }
        $date = \JSHelper::getJsDate('now', 'Y-m-d');
        $query = "SELECT * FROM `#__jshopping_coupons` WHERE coupon_code = '".$db->escape($code)."' AND coupon_publish = '1'";
		extract(\JSHelper::Js_add_trigger(get_defined_vars(), "query"));
        $db->setQuery($query);
        $row = $db->loadObJect();
        
        if(!isset($row->coupon_id)) {
            $this->error = \JText::_('JSHOP_RABATT_NON_CORRECT');
            return 0;
        }
        
        if ($row->coupon_expire_date < $date && $row->coupon_expire_date!="0000-00-00"){
            $this->error = \JText::_('JSHOP_RABATT_NON_CORRECT');
            return 0;
        }
        
        if ($row->coupon_start_date > $date){
            $this->error = \JText::_('JSHOP_RABATT_NON_CORRECT');
            return 0;
        }
        
        if($row->used) {
            $this->error = \JText::_('JSHOP_RABATT_USED');
            return 0;
        }
        
        if ($row->for_user_id){
            $user = \JFactory::getUser();
            if (!$user->id){
                $this->error = \JText::_('JSHOP_FOR_USE_COUPON_PLEASE_LOGIN');
                return 0;
            }
            if ($row->for_user_id!=$user->id){
                $this->error = \JText::_('JSHOP_RABATT_NON_CORRECT');
                return 0;    
            }
        }
        
        $this->load($row->coupon_id);
        return 1;                
    }

    public function getIdFromCode($code){
        $db = \JFactory::getDBO();
        $query = "SELECT coupon_id FROM `#__jshopping_coupons` WHERE coupon_code = '".$db->escape($code)."'";
		extract(\JSHelper::Js_add_trigger(get_defined_vars(), "query"));
        $db->setQuery($query);
        return $db->loadResult();
    }

    public function finish($checkAfterUsed = 0, $user_id = -1, $free_discount = 0){
        if ($checkAfterUsed == 0 || ($checkAfterUsed && $this->finished_after_used)){
            $this->used = $user_id;
            if ($free_discount > 0){
                $this->coupon_value = $free_discount;
            }
            extract(\JSHelper::Js_add_trigger(get_defined_vars(), "before"));
            $this->store();
        }
    }

}