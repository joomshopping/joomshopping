<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model; defined('_JEXEC') or die();

class CouponsModel extends BaseadminModel{
    
    protected $nameTable = 'coupon';
    protected $tableFieldPublish = 'coupon_publish';

    function getAllCoupons($limitstart, $limit, $order = null, $orderDir = null, $text_search = "") {
        $db = \JFactory::getDBO(); 
        $queryorder = 'ORDER BY C.used, C.coupon_id desc';
        if ($order && $orderDir){
            $queryorder = "ORDER BY ".$order." ".$orderDir;
        }
        $where = "";
        if ($text_search){
            $search = $db->escape($text_search);
            $where .= " and (C.coupon_code like '%".$search."%' or U.u_name like '%".$search."%' or U.f_name like '%".$search."%' or U.l_name like '%".$search."%' or U.email like '%".$search."%' ) ";
        }
        $query = "SELECT C.*, U.f_name, U.l_name  FROM `#__jshopping_coupons` as C "
                . "left join #__jshopping_users as U on C.for_user_id=U.user_id "
                ."WHERE 1 ".$where
                .$queryorder;
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query, $limitstart, $limit);
        return $db->loadObjectList();
    }
    
    function getCountCoupons($text_search=""){
        $db = \JFactory::getDBO();
        $where = "";
        if ($text_search){
            $search = $db->escape($text_search);
            $where .= " and (C.coupon_code like '%".$search."%' or U.u_name like '%".$search."%' or U.f_name like '%".$search."%' or U.l_name like '%".$search."%' or U.email like '%".$search."%' ) ";
        }
        $query = "SELECT count(C.coupon_id) FROM `#__jshopping_coupons` as C "
                . "left join #__jshopping_users as U on C.for_user_id=U.user_id "
                . "WHERE 1 ".$where;
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadResult();   
    }
    
    public function getPrepareDataSave($input){
        $post = $input->post->getArray();
        $post['coupon_code'] = $input->getVar("coupon_code");
        $post['coupon_publish'] = $input->getInt("coupon_publish", 0);
        $post['finished_after_used'] = $input->getInt("finished_after_used", 0);
        $post['coupon_value'] = \JSHelper::saveAsPrice($post['coupon_value']);
        return $post;
    }
    
    public function save(array $post){
        $coupon = \JSFactory::getTable('coupon');        
        $dispatcher = \JFactory::getApplication();        
        $dispatcher->triggerEvent('onBeforeSaveCoupon', array(&$post));
        if (!$post['coupon_code']){
            $this->setError(\JText::_('JSHOP_ERROR_COUPON_CODE'));
            return 0;
        }
        if ($post['coupon_value']<0 || ($post['coupon_value']>100 && $post['coupon_type']==0)){
            $this->setError(\JText::_('JSHOP_ERROR_COUPON_VALUE'));
            return 0;
        }        
        $coupon->bind($post);
        if ($coupon->getExistCode()){
            $this->setError(\JText::_('JSHOP_ERROR_COUPON_EXIST'));
            return 0;
        }
        if (!$coupon->store()) {
            $this->setError(\JText::_('JSHOP_ERROR_SAVE_DATABASE'));
            return 0;
        }
        $dispatcher->triggerEvent('onAfterSaveCoupon', array(&$coupon));
        return $coupon;
    }
    
    public function deleteList(array $cid, $msg = 1){
        $db = \JFactory::getDBO();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveCoupon', array(&$cid));
        foreach($cid as $id){
            $query = "DELETE FROM `#__jshopping_coupons` WHERE `coupon_id` = ".(int)$id;
            $db->setQuery($query);
            $db->execute();
        }
        if ($msg){
            $app = \JFactory::getApplication();
            $app->enqueueMessage(\JText::_('JSHOP_COUPON_DELETED'), 'message');
        }
        $dispatcher->triggerEvent('onAfterRemoveCoupon', array(&$cid));
    }
    
    public function publish(array $cid, $flag){
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforePublishCoupon', array(&$cid, &$flag));
        parent::publish($cid, $flag);
        $dispatcher->triggerEvent('onAfterPublishCoupon', array(&$cid, &$flag));
    }

}