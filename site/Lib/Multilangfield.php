<?php
/**
* @version      5.6.2 15.04.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Lib;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Helper\Error as JSError;

defined('_JEXEC') or die();

class MultiLangfield{
    
    public $table = "";
    public $lang = "";
    public $tableFields = array();
    
    function __construct(){
        $this->_LoadTableFields();        
    }
    
    function setTable($table){
        $this->table = $table;
    }
    
    function setLang($lang){
        $this->lang = $lang;
    }

    function getLang(){
        return $this->lang;
    }
    
    function get($field){
        return $field."_".$this->lang;
    }
    
    function getListFields(){
        $array = array();
        if ($this->table){
            $array = $this->tableFields[$this->table];    
        }
        return $array;
    }
    
    /**
    * get build query multi language fields
    * @return strin query ml fiels
    */
    function getBuildQuery(){
        $query = array();
        $fields = $this->getListFields();
        foreach($fields as $field){
            $query[] = " `".$this->get($field[0])."` as ".$field[0];
        }
    return implode(", ",$query);
    }
    
    function addNewFieldLandInTables($lang, $defaultLang = ""){
        $finish = 1;
        $db = Factory::getDBO();
        foreach($this->tableFields as $table_name_end=>$table){
            $table_name = "#__jshopping_".$table_name_end;
            
            $list_name_field = array();
            $query = 'SHOW FIELDS FROM `'.$table_name.'`';   
            $db->setQuery( $query );
            $fields = $db->loadObjectList();            
            foreach($fields as $field){
                $list_name_field[] = $field->Field;
            }
            
            //filter existent field
            foreach($table as $k=>$field){
                if (in_array($field[0]."_".$lang, $list_name_field)){
                    unset($table[$k]);
                }
            }
            
            $sql_array_add_field = array();
            foreach($table as $field){
                $name = $field[0]."_".$lang;
                $sql_array_add_field[] = "ADD `".$name."` ".$field[1];
            }
            
            $sql_array_update_field = array();
            foreach($table as $field){
                $name = $field[0]."_".$lang;
                $name2 = $field[0]."_".$defaultLang;
                if (in_array($name2, $list_name_field)){
                    $sql_array_update_field[] = " `".$name."` = `".$name2."`";
                }
            }            
            
            if (count($sql_array_add_field)){                
                $query = "ALTER TABLE `".$table_name."` ".implode(", ",$sql_array_add_field);            
                $db->setQuery($query);
                if (!$db->execute()){
                    JSError::raiseWarning(500, "Error install new language:<br>".$db->getErrorMsg());
                    $finish = 0;
                } 
                               
                //copy information
                if ($defaultLang!="" && count($sql_array_update_field)){
                    $query = "update `".$table_name."` set ".implode(", ",$sql_array_update_field);
                    $db->setQuery($query);
                    if (!$db->execute()){
                        JSError::raiseWarning(500, "Error copy new language:<br>".$db->getErrorMsg());
                        $finish = 0;
                    }
                }
            }
        }
    return $finish;
    }

    function updateFieldLandInTables($lang){
        $db = Factory::getDBO();
        foreach($this->tableFields as $table_name_end=>$table){
            foreach($table as $k=>$field){
                $nf = $field[0]."_".$lang;
                $query = "ALTER TABLE `#__jshopping_".$table_name_end."` MODIFY `".$nf."` ".$field[1];
                $db->setQuery($query);
                $db->execute();
            }
        }
    }
    
    /**
    * Static list Table and Fields
    */
    function _LoadTableFields(){
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL default ''");
        $this->tableFields["countries"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(100) NOT NULL default ''");
        $f[] = array("description","text DEFAULT NULL");
        $this->tableFields["shipping_method"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(100) NOT NULL default ''");
        $f[] = array("description","text DEFAULT NULL");
        $this->tableFields["payment_method"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(100) NOT NULL default ''");
        $this->tableFields["order_status"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL default ''");
        $this->tableFields["delivery_times"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL default ''");
        $this->tableFields["unit"] = $f;        
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL default ''");
		$f[] = array("description","text DEFAULT NULL");
        $this->tableFields["attr"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL default ''");
        $this->tableFields["attr_values"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL default ''");
        $this->tableFields["attr_groups"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL default ''");
		$f[] = array("description","text DEFAULT NULL");
        $this->tableFields["products_extra_fields"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL default ''");
        $this->tableFields["products_extra_field_values"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL default ''");
        $this->tableFields["products_extra_field_groups"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL default ''");
		$f[] = array("description","text DEFAULT NULL");
        $this->tableFields["free_attr"] = $f;
        
        $f=array();
        $f[] = array("title","varchar(255) NOT NULL default ''");
        $f[] = array("keyword","text DEFAULT NULL");
        $f[] = array("description","text DEFAULT NULL");
        $this->tableFields["config_seo"] = $f;
        
        $f=array();
        $f[] = array("text","LONGTEXT DEFAULT NULL");
        $this->tableFields["config_statictext"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL default ''");
		$this->tableFields["product_labels"] = $f;
		
		$f=array();
		$f[] = array("name","varchar(255) NOT NULL default ''");
        $f[] = array("alias","varchar(255) NOT NULL default ''");
        $f[] = array("short_description","text DEFAULT NULL");
        $f[] = array("description","text DEFAULT NULL");
        $f[] = array("meta_title","varchar(255) NOT NULL default ''");
        $f[] = array("meta_description","text DEFAULT NULL");
        $f[] = array("meta_keyword","text DEFAULT NULL");
        $this->tableFields["manufacturers"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL default ''");
        $f[] = array("alias","varchar(255) NOT NULL default ''");
        $f[] = array("short_description","text DEFAULT NULL");
        $f[] = array("description","text DEFAULT NULL");
        $f[] = array("meta_title","varchar(255) NOT NULL default ''");
        $f[] = array("meta_description","text DEFAULT NULL");
        $f[] = array("meta_keyword","text DEFAULT NULL");
        $this->tableFields["categories"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL default ''");
        $f[] = array("alias","varchar(255) NOT NULL default ''");
        $f[] = array("short_description","text DEFAULT NULL");
        $f[] = array("description","text DEFAULT NULL");
        $f[] = array("meta_title","varchar(255) NOT NULL default ''");
        $f[] = array("meta_description","text DEFAULT NULL");
        $f[] = array("meta_keyword","text DEFAULT NULL");
        $this->tableFields["products"] = $f;
        
        $f=array();
        $f[] = array("name","varchar(255) NOT NULL default ''");
        $f[] = array("description","text DEFAULT NULL");
        $this->tableFields["usergroups"] = $f;
        $obj = $this;
        Factory::getApplication()->triggerEvent('onLoadMultiLangTableField', array(&$obj));
    }
    
}