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

class CategoryTable extends MultilangTable{
    
    function __construct(&$_db){
        parent::__construct('#__jshopping_categories', 'category_id', $_db);
        \JPluginHelper::importPlugin('jshoppingproducts');
    }
    
    function getSubCategories($parentId, $order = 'id', $ordering = 'asc', $publish = 0){
        $db = \JFactory::getDBO();
        $lang = \JSFactory::getLang();
        $user = \JFactory::getUser();
        $add_where = ($publish)?(" AND category_publish = '1' "):("");
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $add_where .=' AND access IN ('.$groups.')';
        if ($order=="id") $orderby = "category_id";
        if ($order=="name") $orderby = "`".$lang->get('name')."`";
        if ($order=="ordering") $orderby = "ordering";
        if (!$orderby) $orderby = "ordering";
        
        $query = "SELECT `".$lang->get('name')."` as name,`".$lang->get('description')."` as description,`".$lang->get('short_description')."` as short_description, category_id, category_publish, ordering, category_image FROM `#__jshopping_categories`
                   WHERE category_parent_id = '".$db->escape($parentId)."' ".$add_where."
                   ORDER BY ".$orderby." ".$ordering;
        $db->setQuery($query);
        $categories = $db->loadObJectList();
        foreach($categories as $key=>$value){
            $categories[$key]->category_link = \JSHelper::SEFLink('index.php?option=com_jshopping&controller=category&task=view&category_id='.$categories[$key]->category_id, 1);
        }        
        return $categories;
    }
    
    function getDescription($preparePluginContent = 1){
        
        if (!$this->category_id){
            $this->getDescriptionMainPage($preparePluginContent);
            return 1; 
        }
        
        $lang = \JSFactory::getLang();
        $name = $lang->get('name');        
        $description = $lang->get('description');
        $short_description = $lang->get('short_description');
        $meta_title = $lang->get('meta_title');
        $meta_keyword = $lang->get('meta_keyword');
        $meta_description = $lang->get('meta_description');
        
        $this->name = $this->$name;
        $this->description = $this->$description;
        $this->short_description = $this->$short_description;
        $this->meta_title = $this->$meta_title;
        $this->meta_keyword = $this->$meta_keyword;
        $this->meta_description = $this->$meta_description;
        if ($this->category_template==""){
            $this->category_template = "default";
        }
        if ($preparePluginContent){
            $this->preparePluginContent();
        }        
		return $this->description;
    }    

    function getTreeChild() {
        $category_parent_id = $this->category_parent_id;
        $i = 0;
        $list_category = array();
        $list_category[$i] = new \stdClass();
        $list_category[$i]->category_id = $this->category_id;
        $list_category[$i]->name = $this->name;
        $i++;
        while($category_parent_id) {
            $category = \JSFactory::getTable('category');
            $category->load($category_parent_id);
            $list_category[$i] = new \stdClass();
            $list_category[$i]->category_id = $category->category_id;
            $list_category[$i]->name = $category->getName();
            $category_parent_id = $category->category_parent_id;
            $i++;
        }
        $list_category = array_reverse($list_category);
        return $list_category;
    }

    function getAllCategories($publish = 1, $access = 1, $listType = 'id') {
        $db = \JFactory::getDBO();
        $user = \JFactory::getUser();
		$lang = \JSFactory::getLang();
        $where = array();
        if ($publish){
            $where[] = "category_publish = '1'";
        }
        if ($access){
            $groups = implode(',', $user->getAuthorisedViewLevels());
            $where[] =' access IN ('.$groups.')';
        }
        $add_where = "";
        if (count($where)){
            $add_where = " where ".implode(" and ", $where);
        }
		if ($listType=='id'){
			$query = "SELECT category_id, category_parent_id FROM `#__jshopping_categories` ".$add_where." ORDER BY ordering";
		}else{
			$query = "SELECT `".$lang->get('name')."` as name, category_id, category_parent_id, category_publish FROM `#__jshopping_categories`
				".$add_where." ORDER BY category_parent_id, ordering";
		}
        $db->setQuery($query);
        return $db->loadObJectList();
    }

    function getChildCategories($order='id', $ordering='asc', $publish=1){
        return $this->getSubCategories($this->category_id, $order, $ordering, $publish);
    }

    function getSisterCategories($order, $ordering = 'asc', $publish = 1) {
        return $this->getSubCategories($this->category_parent_id, $order, $ordering, $publish);
    }

    function getTreeParentCategories($publish = 1, $access = 1){
        $db = \JFactory::getDBO();
        $user = \JFactory::getUser();
        $cats_tree = array(); 
        $category_parent = $this->category_id;
        $where = array();
        if ($publish){
            $where[] = "category_publish = '1'";
        }
        if ($access){
            $groups = implode(',', $user->getAuthorisedViewLevels());
            $where[] =' access IN ('.$groups.')';
        }
        $add_where = "";
        if (count($where)){
            $add_where = "and ".implode(" and ", $where);
        }
        while($category_parent) {
            $cats_tree[] = $category_parent;
            $query = "SELECT category_parent_id FROM `#__jshopping_categories` WHERE category_id = '".$db->escape($category_parent)."' ".$add_where;
            $db->setQuery($query);
            $rows = $db->loadObJectList();
            $category_parent = $rows[0]->category_parent_id;
        }
        return array_reverse($cats_tree);
    }
	    
    function getDescriptionMainPage($preparePluginContent = 1){
        $statictext = \JSFactory::getTable("statictext");
        $row = $statictext->loadData("home");
        $this->description = $row->text;
        
        $seo = \JSFactory::getTable("seo");
        $row = $seo->loadData("category");
        $this->meta_title = $row->title;
        $this->meta_keyword = $row->keyword;
        $this->meta_description = $row->description;
        if ($preparePluginContent){
            $this->preparePluginContent();
        }
		return $this->description;
    }
        
    function getManufacturers(){
        $JshopConfig = \JSFactory::getConfig();
        $user = \JFactory::getUser();
        $lang = \JSFactory::getLang();
        $db = \JFactory::getDBO();
        $adv_query = "";
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $adv_query .=' AND prod.access IN ('.$groups.')';
        if ($JshopConfig->hide_product_not_avaible_stock){
            $adv_query .= " AND prod.product_quantity > 0";
        }
        if ($JshopConfig->manufacturer_sorting==2){
            $order = 'name';
        }else{
            $order = 'man.ordering';
        }
        $query = "SELECT distinct man.manufacturer_id as id, man.`".$lang->get('name')."` as name FROM `#__jshopping_products` AS prod
                  LEFT JOIN `#__jshopping_products_to_categories` AS categ USING (product_id)
                  LEFT JOIN `#__jshopping_manufacturers` as man on prod.product_manufacturer_id=man.manufacturer_id 
                  WHERE categ.category_id=".(int)$this->category_id." AND prod.product_publish=1 AND prod.product_manufacturer_id!=0 ".$adv_query." "
                . "order by ".$order;
        $db->setQuery($query);
        $list = $db->loadObJectList();
        return $list;
           
    }    
        
    function preparePluginContent(){
        if (\JSFactory::getConfig()->use_plugin_content){
            \JSHelper::changeDataUsePluginContent($this, "category");
        }        
    }
    
    function checkView($user){
        if (!$this->category_id || $this->category_publish==0 || !in_array($this->access, $user->getAuthorisedViewLevels())){
            return 0;
        }else{
            return 1;
        }
    }

    function getFieldListOrdering(){
        $ordering = \JSFactory::getConfig()->category_sorting==1 ? "ordering" : "name";
        return $ordering;
    }

    function getSortingDirection(){
		$sort = \JSFactory::getConfig()->category_sorting_direction;
		if (!$sort){
			$sort = 'asc';
		}
		return $sort;
	}

    function getCountToRow(){
		return \JSFactory::getConfig()->count_category_to_row;
	}

}