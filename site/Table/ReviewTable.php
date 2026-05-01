<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\CMS\Language\Text;
defined('_JEXEC') or die();

class ReviewTable extends ShopbaseTable{

    function __construct( &$_db ){
        parent::__construct('#__jshopping_products_reviews', 'review_id', $_db );
    }
    
    function getAllowReview(){
        $JshopConfig = JSFactory::getConfig();
        $user = Factory::getUser();
		$res = 1;		
        if (!$JshopConfig->allow_reviews_prod){
            $res = 0;            
        }
        if ($res==1 && $JshopConfig->allow_reviews_only_registered && !$user->id){
            $res = -1;
        }		
		extract(Helper::Js_add_trigger(get_defined_vars(), "after"));
        return $res;
    }

    function getText(){
		$allow_review = $this->getAllowReview();		
        if ($allow_review == -1){
            $res = Text::_('JSHOP_REVIEW_NOT_LOGGED');
        } else {
            $res = '';
        }
		extract(Helper::Js_add_trigger(get_defined_vars(), "after"));
		return $res;
    }
	
	function check(){
		$res = 1;

        if (!$this->review_id || $this->product_id !== null) {
            if (!$this->product_id) {
                $res = 0;
            } else {
                $db = Factory::getDBO();
                $query = "SELECT product_id FROM `#__jshopping_products` WHERE product_id=" . intval($this->product_id);
                $db->setQuery($query);
                if (!intval($db->loadResult())) {
                    $this->setError('Product not found');
                    $res = 0;
                }
            }
        }

        if (!$this->review_id || $this->user_name !== null) {
            if ($this->user_name == '') {
                $res = 0;
            }
        }

        if (!$this->review_id || $this->user_email !== null) {
            if ($this->user_email == '') {
                $res = 0;
            }
        }

        if (!$this->review_id || $this->review !== null) {
            if ($this->review == '') {
                $res = 0;
            }
        }

		extract(Helper::Js_add_trigger(get_defined_vars(), "after"));
        return $res;
    }

}