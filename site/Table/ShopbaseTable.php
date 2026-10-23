<?php
/**
* @version      5.10.0 18.10.2026
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Helper\Helper;

defined('_JEXEC') or die();

abstract class ShopbaseTable extends Table{

    // columns with serialize() data, checked before store
    protected $_serialized_fields = [];

    // columns with JSON data (old serialize() data is converted on store)
    protected $_json_fields = [];

    public function __construct($table, $key, $db, $dispatcher = null) {
		parent::__construct($table, $key, $db, $dispatcher);

		if ($this->hasField('access')) {
            $this->access = null;
        }
	}
    
    public function move($delta, $where = '', $field = 'ordering'){
    	$db = Factory::getDBO();
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
    	$db = Factory::getDBO();
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

	public function store($updateNulls = false) {
		$this->checkSerializedFields();
		$this->convertJsonFields();
		$result = parent::store($updateNulls);
		if (!$result) {
			$msg = 'Error store '.$this->_tbl.". ".$this->getError();
			Helper::saveToLog('error.log', $msg);
		}
		return $result;
	}

	// columns of $_serialized_fields: data that is not a safe serialize() string is replaced by an empty array
	protected function checkSerializedFields() {
		foreach ($this->_serialized_fields as $field) {
			if (isset($this->$field) && !Helper::isSafeSerialized($this->$field)) {
				Helper::saveToLog('error.log', 'Not allowed serialized data in '.$this->_tbl.'.'.$field);
				$this->$field = serialize(array());
			}
		}
	}

	protected function encodeData($data){
		return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
	}

	// JSON or old serialize() data (only arrays, scalars, stdClass)
	protected function decodeData($data){
		$data = (string)$data;
		if ($data === ''){
			return [];
		}
		if ($data[0] === '{' || $data[0] === '['){
			$value = json_decode($data, true);
			return is_array($value) ? $value : [];
		}
		$value = Helper::unserializeData($data);
		return $value === false ? [] : $value;
	}

	// columns of $_json_fields are always stored as JSON
	protected function convertJsonFields(){
		foreach ($this->_json_fields as $field){
			if (!isset($this->$field) || $this->$field === ''){
				continue;
			}
			$value = $this->decodeData($this->$field);
			$this->$field = $this->encodeData(is_array($value) ? $value : []);
		}
	}

}