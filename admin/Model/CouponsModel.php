<?php
/**
* @version      5.6.3 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model; defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Language\Text;

class CouponsModel extends BaseadminModel{
    
    protected $nameTable = 'coupon';
    protected $tableFieldPublish = 'coupon_publish';

	public function getListItems(array $filters = [], array $orderBy = [], array $limit = [], array $params = []){
		return $this->getAllCoupons($limit['limitstart'] ?? 0, $limit['limit'] ?? 0,$orderBy['order'] ?? null, $orderBy['dir'] ?? null, $filters['text_search'] ?? '');
	}

	public function getCountItems(array $filters = [], array $params = []) {
		return $this->getCountCoupons($filters['text_search'] ?? '');
	}

    function getAllCoupons($limitstart, $limit, $order = null, $orderDir = null, $text_search = "") {
        $db = Factory::getDBO(); 
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
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query, $limitstart, $limit);
        $list = $db->loadObjectList();
        $date = date('Y-m-d');
        foreach($list as $k=>$row) {
            $finished = 0;
            if ($row->used) $finished = 1;
            if ($row->coupon_expire_date < $date && !Helper::datenull($row->coupon_expire_date)) $finished = 1;
            $list[$k]->_finished = $finished;
        }
        return $list;
    }
    
    function getCountCoupons($text_search=""){
        $db = Factory::getDBO();
        $where = "";
        if ($text_search){
            $search = $db->escape($text_search);
            $where .= " and (C.coupon_code like '%".$search."%' or U.u_name like '%".$search."%' or U.f_name like '%".$search."%' or U.l_name like '%".$search."%' or U.email like '%".$search."%' ) ";
        }
        $query = "SELECT count(C.coupon_id) FROM `#__jshopping_coupons` as C "
                . "left join #__jshopping_users as U on C.for_user_id=U.user_id "
                . "WHERE 1 ".$where;
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadResult();   
    }
    
    public function getPrepareDataSave($input){
        $post = $input->post->getArray();
        $post['coupon_code'] = $input->getVar("coupon_code");
        $post['coupon_publish'] = $input->getInt("coupon_publish", 0);
        $post['finished_after_used'] = $input->getInt("finished_after_used", 0);
        $post['coupon_value'] = Helper::saveAsPrice($post['coupon_value']);
        return $post;
    }
    
    public function save(array $post){
        $coupon = JSFactory::getTable('coupon');        
        $dispatcher = Factory::getApplication();        
        $dispatcher->triggerEvent('onBeforeSaveCoupon', array(&$post));
        if (!$post['coupon_code']){
            $this->setError(Text::_('JSHOP_ERROR_COUPON_CODE'));
            return 0;
        }
        if ($post['coupon_value']<0 || ($post['coupon_value']>100 && $post['coupon_type']==0)){
            $this->setError(Text::_('JSHOP_ERROR_COUPON_VALUE'));
            return 0;
        }
        if (isset($post['coupon_start_date']) && $post['coupon_start_date'] == ''){
            $post['coupon_start_date'] = '0000-00-00';
        }
        if (isset($post['coupon_expire_date']) && $post['coupon_expire_date'] == ''){
            $post['coupon_expire_date'] = '0000-00-00';
        }
        $coupon->bind($post);
        if ($coupon->getExistCode()){
            $this->setError(Text::_('JSHOP_ERROR_COUPON_EXIST'));
            return 0;
        }
        if (!$coupon->store()) {
            $this->setError(Text::_('JSHOP_ERROR_SAVE_DATABASE')." ".$coupon->getError());
            return 0;
        }
        $dispatcher->triggerEvent('onAfterSaveCoupon', array(&$coupon));
        return $coupon;
    }
    
    public function deleteList(array $cid, $msg = 1){
        $db = Factory::getDBO();
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveCoupon', array(&$cid));
        foreach($cid as $id){
            $query = "DELETE FROM `#__jshopping_coupons` WHERE `coupon_id` = ".(int)$id;
            $db->setQuery($query);
            $db->execute();
        }
        if ($msg){
            $app = Factory::getApplication();
            $app->enqueueMessage(Text::_('JSHOP_COUPON_DELETED'), 'message');
        }
        $dispatcher->triggerEvent('onAfterRemoveCoupon', array(&$cid));
    }
    
    public function publish(array $cid, $flag){
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforePublishCoupon', array(&$cid, &$flag));
        parent::publish($cid, $flag);
        $dispatcher->triggerEvent('onAfterPublishCoupon', array(&$cid, &$flag));
    }

}