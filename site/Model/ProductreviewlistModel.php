<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model;
defined('_JEXEC') or die();

class ProductReviewListModel  extends BaseModel{

	protected $model = null;	
	protected $list = null;
	protected $total = null;
	protected $limit = null;
	protected $limitstart = null;
	protected $pagination = null;
	protected $count_per_page = 20;
	
	public function __construct(){
		$model = \JSFactory::getTable('product');
		$this->setModel($model);
	}
	
	public function setModel($model){
		$this->model = $model;
		extract(\JSHelper::Js_add_trigger(get_defined_vars(), "after"));
	}
	
	public function getModel(){
		return $this->model;
	}
	
	public function setProductId($pid){		
		$this->getModel()->load($pid);		
	}
	
	public function getProductId(){
		return $this->getModel()->product_id;
	}
	
	public function getContext(){
		$context = "jshoping.list.front.product.review";
		return $context;
	}
	
	public function getTotal(){
		return $this->total;
	}
	
	public function getList(){
		return $this->list;
	}
	
	public function getPagination(){
        return $this->pagination;
    }
	
	public function getLimit(){
        return $this->limit;
    }
	
	public function getLimitStart(){
        return $this->limitstart;
    }
	
	public function setCountPerPage($val){
		$this->count_per_page = $val;
	}
	
	public function getCountPerPage(){
		return $this->count_per_page;
	}
	
	protected function loadRequestData(){
		$app = \JFactory::getApplication();
		$context = $this->getContext();		
		$this->limitstart = $app->input->getInt('limitstart');
        $this->limit = $app->getUserStateFromRequest($context.'limit', 'limit', $this->getCountPerPage(), 'int');
	}
	
	public function load(){
		$this->loadRequestData();
		$model = $this->getModel();		
		$this->total =  $model->getReviewsCount();
		$this->pagination = new \JPagination($this->total, $this->limitstart, $this->limit);
		$this->list = $model->getReviews($this->limitstart, $this->limit);
		return 1;
	}

}