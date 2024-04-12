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

abstract class ShopbaseTable extends \JTable{
    
    public function move($delta, $where = '', $field = 'ordering'){
    	$db = \JFactory::getDBO();
		if (empty($delta)){
			return true;
		}
		$query = $db->getQuery(true);

		$query->select(implode(',', $this->_tbl_keys) . ', '.$field)
			->from($this->_tbl);

		if ($delta < 0){
			$query->where($field.' < ' . (int) $this->$field)
				->order($field.' DESC');
		}elseif ($delta > 0){
			$query->where($field.' > ' . (int) $this->$field)
				->order($field.' ASC');
		}

		if ($where){
			$query->where($where);
		}
		$db->setQuery($query, 0, 1);
		$row = $db->loadObJect();

		if (!empty($row)){
			$query->clear()
				->update($this->_tbl)
				->set($field.' = ' . (int) $row->$field);
			$this->appendPrimaryKeys($query);
			$db->setQuery($query);
			$db->execute();

			$query->clear()
				->update($this->_tbl)
				->set($field.' = ' . (int) $this->$field);
			$this->appendPrimaryKeys($query, $row);
			$db->setQuery($query);
			$db->execute();

			$this->$field = $row->$field;
		}else{
			$query->clear()
				->update($this->_tbl)
				->set($field.' = ' . (int) $this->$field);
			$this->appendPrimaryKeys($query);
			$db->setQuery($query);
			$db->execute();
		}
		return true;
	}
    
    public function reorder($where = '', $fieldordering = 'ordering'){
    	$db = \JFactory::getDBO();
		$k = $this->_tbl_key;
		$query = $db->getQuery(true)
			->select(implode(',', $this->_tbl_keys) . ', '.$fieldordering)
			->from($this->_tbl)
			->where($fieldordering.' >= 0')
			->order($fieldordering);
		if ($where){
			$query->where($where);
		}

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		foreach ($rows as $i => $row){
			if ($row->$fieldordering >= 0){
				if ($row->$fieldordering != $i + 1){
					$query->clear()
						->update($this->_tbl)
						->set($fieldordering.' = ' . ($i + 1));
					$this->appendPrimaryKeys($query, $row);
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
		return true;
	}
    
}