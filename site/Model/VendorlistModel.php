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

class VendorListModel  extends BaseModel{
	
	protected $model = null;
	protected $list = null;
	protected $total = null;
	protected $limit = null;
	protected $limitstart = null;
	protected $pagination = null;
	
	public function __construct(){
		$model = \JSFactory::getTable('vendor');
		$this->setModel($model);
	}
	
	public function setModel($model){
		$this->model = $model;
		extract(\JSHelper::Js_add_trigger(get_defined_vars(), "after"));
	}
	
	public function getModel(){
		return $this->model;
	}
	
	public function getContext(){
		$context = "jshoping.list.front.vendor";
		return $context;
	}
	
	public function getCountPerPage(){
		return $this->getModel()->getCountPerPage();
	}
	
	public function getCountToRow(){
		return $this->getModel()->getCountToRow();
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
	
	protected function loadRequestData(){
		$app = \JFactory::getApplication();
		$model = $this->getModel();
		$context = $this->getContext();
		
		$this->limitstart = \JFactory::getApplication()->input->getInt('limitstart');
        $this->limit = $app->getUserStateFromRequest($context.'limit', 'limit', $this->getCountPerPage(), 'int');
	}
	
	public function load(){
		$this->loadRequestData();		
		$vendor = $this->getModel();
		
        $this->total = $vendor->getCountAllVendors();        
        if ($this->limitstart>=$this->total){
			$this->limitstart = 0;
		}
		
        $this->list = $vendor->getAllVendors(1, $this->limitstart, $this->limit);
        $obj = $this;
        \JFactory::getApplication()->triggerEvent('onBeforeDisplayListVendors', array(&$this->list, &$obj));
        
        $this->pagination = new \JPagination($this->total, $this->limitstart, $this->limit);
        
        $this->list = $vendor->prepareViewListVendor($this->list);
		
		return 1;
	}
	
}