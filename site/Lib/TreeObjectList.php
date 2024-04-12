<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Lib;

defined('_JEXEC') or die();

class TreeObjectList{
	
	public $is_select = 1;
	public $sep = '-- ';
	private $id = 'id';
	private $parent = 'parent_id';
	private $name = 'name';
	private $objects = array();
	private $cats = array();
	
	public function __construct($objects, $params = array()){
		$this->setParams($params);
		$this->createArrayIndexParent($objects);
	}
	
	public function getList(){
		$this->recurseTreeForeach();		
		return $this->cats;
	}
	
	private function recurseTreeForeach($id = 0, $level = 0){
		if ($id === 0){
			$this->clearCats();
		}
		if (isset($this->objects[$id])){
			foreach ($this->objects[$id] as $key => $value){
				$this->recurseTreeAdd($value, $level);
			}
		}
	}
	
	private function recurseTreeAdd($value, $level){
		if ($this->is_select){
			$value->{$this->name} = ($this->getSep($level).$value->{$this->name});
			$this->cats[] = \JHTML::_('select.option', $value->{$this->id}, $value->{$this->name}, $this->id, $this->name);
		} else {
			$value->level = $level;
			$this->cats[] = $value;
		}
		$this->recurseTreeForeach($value->{$this->id}, ++$level);
		$level--;
	}
	
	private function getSep($level){
		$sep = '';
		for($i = 0; $i < $level; $i++){
			$sep .= $this->sep;
		}
		return $sep;
	}
	
	private function clearCats(){
		$this->cats = array();
	}
	
	private function createArrayIndexParent($objects){
		if (is_array($objects)){
			foreach($objects as $key => $value){
				$this->objects[$value->{$this->parent}][$value->{$this->id}] = $value;
			}
		}
	}
	
	private function setParams($params){
		foreach($params as $k=>$p){
			$this->setParam($k,$p);
		}
	}
	
	private function setParam($k, $value){
		if (isset($this->{$k})){
			$this->{$k} = $value;
		}
	}

}