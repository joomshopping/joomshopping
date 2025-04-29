<?php
/**
* @version      5.6.2 18.04.2025
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

    /**
     * Prepares data for saving by retrieving it from the input object.
     * 
     * @param \Joomla\Input\Input $input Input object containing POST data.
     * @return array Prepared data as an associative array.
     */
    public function getPrepareDataSave($input){
        return $input->post->getArray();
    }
    
    /**
     * Retrieves the name of the table associated with the model.
     * 
     * @return string The name of the table in lowercase.
     */
    public function getNameTable(){
        if (empty($this->nameTable)){
            $r = null;
            preg_match('/Model\\\(.*)Model/i', get_class($this), $r);
            $this->nameTable = strtolower($r[1])."Table";
        }
        return $this->nameTable;
    }
    
    /**
     * Gets the default table object for the model.
     * 
     * @return \Joomla\CMS\Table\Table The table object.
     */
    public function getDefaultTable(){
        return JSFactory::getTable($this->getNameTable());
    }
    
    /**
     * Saves data to the database table.
     * 
     * @param array $post Data to be saved.
     * @return mixed The table object on success, or 0 on failure.
     */
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
            $this->setError(Text::_('JSHOP_ERROR_SAVE_DATABASE')." ".$table->getError());
            return 0;
        }
        return $table;
    }
    
    /**
     * Deletes a list of items from the database.
     * 
     * @param array $cid Array of item IDs to delete.
     * @param int $msg Whether to display a success message (1 = yes, 0 = no).
     * @return array Array of results for each deleted item.
     */
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
    
    /**
     * Publishes or unpublishes a list of items.
     * 
     * @param array $cid Array of item IDs to update.
     * @param int $flag Publish state (1 = publish, 0 = unpublish).
     */
    public function publish(array $cid, $flag){
        $field = $this->tableFieldPublish;
        foreach($cid as $id){
            $table = $this->getDefaultTable();
            $table->load($id);
            $table->$field = $flag;
            $table->store();
        }
    }
    
    /**
     * Changes the ordering of an item.
     * 
     * @param int $id The ID of the item to move.
     * @param int $move The direction to move (-1 = up, 1 = down).
     * @param string $where Additional conditions for ordering.
     */
    public function order($id, $move, $where = ''){
        $table = $this->getDefaultTable();
        $table->load($id);
        $table->move($move, $where, $this->tableFieldOrdering);
    }
    
    /**
     * Saves the ordering of multiple items.
     * 
     * @param array $cid Array of item IDs.
     * @param array $order Array of new ordering values.
     * @param string $where Additional conditions for ordering.
     */
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
    
    /**
     * Retrieves a list of items based on filters, ordering, and limits.
     * 
     * @param array $filters Filters to apply (e.g., category_id, search).
     * @param array $orderBy Ordering options (e.g., order, dir).
     * @param array $limit Pagination options (e.g., limit, limitstart).
     * @param array $params Additional parameters (e.g., query_fields).
     * @return array List of items.
     */
    public function getListItems(array $filters = [], array $orderBy = [], array $limit = [], array $params = []) {
        return $this->getRawList($params['query_fields'] ?? '*', $orderBy['order'] ?? null, $orderBy['dir'] ?? 'asc', $limit['limit'] ?? 0, $limit['limitstart'] ?? 0);
    }
    
    /**
     * Retrieves the count of items based on filters.
     * 
     * @param array $filters Filters to apply.
     * @param array $params Additional parameters.
     * @return int The count of items.
     */
    public function getCountItems(array $filters = [], array $params = []) {
        return $this->getRawListCount();
    }

    /**
     * Retrieves raw data from the database table.
     * 
     * @param string $fields Fields to select.
     * @param string|null $order Field to order by.
     * @param string $orderDir Order direction ('asc' or 'desc').
     * @param int $limit Number of records to retrieve.
     * @param int $limitstart Offset for records.
     * @return array List of database records.
     * @throws \Exception If the table cannot be retrieved.
     */
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

    /**
     * Retrieves the count of records in the database table.
     * 
     * @return int The count of records.
     * @throws \Exception If the table cannot be retrieved.
     */
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