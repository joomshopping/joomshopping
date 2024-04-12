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

class UserGroupTable extends ShopbaseTable{

    function __construct(&$_db){
        parent::__construct('#__jshopping_usergroups', 'usergroup_id', $_db);
    }
     
    function getDefaultUsergroup(){
        $db = \JFactory::getDBO(); 
        $query = "SELECT `usergroup_id` FROM `#__jshopping_usergroups` WHERE `usergroup_is_default`= '1'";
        $db->setQuery($query);
        return $db->loadResult();
    }
    
    function getList(){
        $db = \JFactory::getDBO();
        $lang = \JSFactory::getLang();
        $query = "SELECT *, `".$lang->get("name")."` as name, `".$lang->get("description")."` as description FROM `#__jshopping_usergroups`";
        $db->setQuery($query);
        $list = $db->loadObjectList();
        foreach($list as $k=>$v){
            if ($v->name==''){
                $list[$k]->name = $v->usergroup_name;
            }
        }
        return $list;
    }
	
	function getName(){
        $lang = \JSFactory::getLang();
        $field = $lang->get("name");
		$name = $this->$field;
		if ($name==''){
			$name = $thid->usergroup_name;
		}
		return $name;
    }
}