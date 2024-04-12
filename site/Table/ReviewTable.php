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

class ReviewTable extends ShopbaseTable{

    function __construct( &$_db ){
        parent::__construct('#__jshopping_products_reviews', 'review_id', $_db );
    }
    
    function getAllowReview(){
        $JshopConfig = \JSFactory::getConfig();
        $user = \JFactory::getUser();
		$res = 1;		
        if (!$JshopConfig->allow_reviews_prod){
            $res = 0;            
        }
        if ($res==1 && $JshopConfig->allow_reviews_only_registered && !$user->id){
            $res = -1;
        }		
		extract(\JSHelper::Js_add_trigger(get_defined_vars(), "after"));
        return $res;
    }

    function getText(){
		$allow_review = $this->getAllowReview();		
        if ($allow_review == -1){
            $res = \JText::_('JSHOP_REVIEW_NOT_LOGGED');
        } else {
            $res = '';
        }
		extract(\JSHelper::Js_add_trigger(get_defined_vars(), "after"));
		return $res;
    }
	
	function check(){
        $db = \JFactory::getDBO();
		$res = 1;
        if (!$this->product_id){
            $res = 0;
        }
        if ($this->user_name==''){
            $res = 0;
        }
        if ($this->user_email==''){
            $res = 0;
        }
        if ($this->review==''){
            $res = 0;
        }        
        $query = "SELECT product_id FROM #__jshopping_products WHERE product_id=".intval($this->product_id);
        $db->setQuery($query);
        $pid = intval($db->loadResult());
        if (!$pid){
            $res = 0;
        }
		extract(\JSHelper::Js_add_trigger(get_defined_vars(), "after"));
        return $res;
    }

}