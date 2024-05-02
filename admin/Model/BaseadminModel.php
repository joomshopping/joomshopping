<?php
/**
* @version      5.3.5 21.03.2024
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Model;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die();

class BaseadminModel extends BaseDatabaseModel{
    
    protected $nameTable = '';
    protected $tableFieldPublish = 'publish';
    protected $tableFieldOrdering = 'ordering';

    public function getPrepareDataSave($input){
        return $input->post->getArray();
    }
    
    public function getNameTable(){
		if (empty($this->nameTable)){
			$r = null;
			preg_match('/Model\\\(.*)Model/i', get_class($this), $r);
			$this->nameTable = strtolower($r[1])."Table";
		}
		return $this->nameTable;
	}
    
    public function getDefaultTable(){
        return JSFactory::getTable($this->getNameTable());
    }
    
    public function save(array $post){
        $table = $this->getDefaultTable();
        $table->bind($post);
        if (!$table->check()){
            $this->setError($table->getError());
            return 0;
        }
        if (!$table->hasPrimaryKey() && $table->hasField('ordering')){
            $table->ordering = $table->getNextOrder();
        }
        if (!$table->store()){
            print $table->getError();
            $this->setError(Text::_('JSHOP_ERROR_SAVE_DATABASE'));
            return 0;
        }
        return $table;
    }
    
    public function deleteList(array $cid, $msg = 1){
        $app = Factory::getApplication();
        $res = array();
		foreach($cid as $id){
            $table = $this->getDefaultTable();
            $table->delete($id);
            if ($msg){
                $app->enqueueMessage(Text::_('JSHOP_ITEM_DELETED'), 'message');
            }
            $res[$id] = true;
		}
        return $res;
    }
    
    public function publish(array $cid, $flag){
        $field = $this->tableFieldPublish;
        foreach($cid as $id){
            $table = $this->getDefaultTable();
            $table->load($id);
            $table->$field = $flag;
            $table->store();
		}
    }
    
    public function order($id, $move, $where = ''){
        $table = $this->getDefaultTable();
        $table->load($id);
        $table->move($move, $where, $this->tableFieldOrdering);
    }
    
    public function saveorder(array $cid, array $order, $where = ''){
        $field = $this->tableFieldOrdering;
        foreach($cid as $k=>$id){
            $table = $this->getDefaultTable();
            $table->load($id);
            if ($table->$field != $order[$k]){
                $table->$field = $order[$k];
                $table->store();
            }
        }
        $table = $this->getDefaultTable();
        $table->$field = null;
        $table->reorder($where, $field);
    }

    public function getRawList($fields = '*', $order = null, $orderDir = 'asc', $limit = 0, $limitstart = 0) {
        $table = $this->getDefaultTable();
        if ($table) {
            $tablename = $table->getTableName();
        } else {
            throw new \Exception('Error get table for '.static::class);
        }
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select($fields);
        $query->from($db->qn($tablename));
        if (isset($order)) {
            $query->order($db->qn($order)." ".$orderDir);
        }
        $db->setQuery($query, $limitstart, $limit);
        return $db->loadObjectList(); 
    }

    public function getRawListCount() {
        $table = $this->getDefaultTable();
        if ($table) {
            $tablename = $table->getTableName();
        } else {
            throw new \Exception('Error get Table for '.static::class);
        }
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('count(*)');
        $query->from($db->qn($tablename));
        $db->setQuery($query);
        return $db->loadResult();
    }
    
}