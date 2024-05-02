<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\CMS\Language\Text;
defined('_JEXEC') or die();

class ProductReviewModel  extends BaseModel{
    
	protected $review;
	protected $data = array();
	
	public function __construct(){
		$this->review = JSFactory::getTable('review');
	}
	
	public function checkAllow(){
		if ($this->review->getAllowReview() <= 0){
			$this->setError($this->review->getText());
			return 0;
		}else{
			return 1;
		}
	}
	
	public function setData($data, $load_data_config = 1){
		$jshopConfig = JSFactory::getConfig();
		$this->data = $data;
		$review = $this->review;
		$review->bind($data);
		if ($load_data_config){
			$review->user_id = Factory::getUser()->id;
			$review->time = Helper::getJsDate();
			$review->ip = $_SERVER['REMOTE_ADDR'];
			if ($jshopConfig->display_reviews_without_confirm){
				$review->publish = 1;    
			}
		}
	}
	
	public function getData(){
		return $this->data;
	}
	
	public function setProductId($pid){
		$this->data['product_id'] = $pid;
	}
	
	public function getProductId(){
		return $this->data['product_id'];
	}
	
	public function check(){
		Factory::getApplication()->triggerEvent('onBeforeSaveReview', array(&$this->review));
		if (!$this->review->check()){
            $this->setError(Text::_('JSHOP_ENTER_CORRECT_INFO_REVIEW'));
			return 0;
		}else{
			return 1;
		}
	}
	
	public function save(){
		$this->review->store();
		$product_id = $this->getProductId();
		
        Factory::getApplication()->triggerEvent('onAfterSaveReview', array(&$this->review));

        $product = JSFactory::getTable('product');
        $product->load($product_id);
        $product->loadAverageRating();
        $product->loadReviewsCount();
        $product->store();
	}
	
	public function mailSend(){
		$data = array();
		$data['product_id'] = $this->getProductId();
		$data['review'] = $this->review;
		
		$mail = JSFactory::getModel('reviewMail', 'Site');
		$mail->setData($data);
		return $mail->send();
	}

}