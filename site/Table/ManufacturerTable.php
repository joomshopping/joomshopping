<?php
/**
* @version      5.1.0 15.09.2022
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\CMS\Language\Text;
defined('_JEXEC') or die();

class ManufacturerTable extends MultilangTable{

    function __construct(&$_db){
        parent::__construct('#__jshopping_manufacturers', 'manufacturer_id', $_db);
        PluginHelper::importPlugin('jshoppingproducts');
    }

	function getAllManufacturers($publish = 0, $order = "ordering", $dir ="asc" ) {
		$lang = JSFactory::getLang();
        $jshopConfig = JSFactory::getConfig();
		$db = Factory::getDBO();
        if ($order=="id") $orderby = "manufacturer_id";
        if ($order=="name") $orderby = "name";
        if ($order=="ordering") $orderby = "ordering";
        if (!$orderby) $orderby = "ordering"; 
		$query_where = ($publish)?("WHERE manufacturer_publish = '1'"):("");
		$query = "SELECT manufacturer_id, manufacturer_url, manufacturer_logo, manufacturer_publish, `".$lang->get('name')."` as name, `".$lang->get('description')."` as description,  `".$lang->get('short_description')."` as short_description, img_alt, img_title 
				  FROM `#__jshopping_manufacturers` $query_where ORDER BY ".$orderby." ".$dir;
		$db->setQuery($query);
		$list = $db->loadObJectList();
		
		foreach($list as $key=>$value){
            $list[$key]->link = Helper::SEFLink('index.php?option=com_jshopping&controller=manufacturer&task=view&manufacturer_id='.$list[$key]->manufacturer_id, 1);
            if (!$jshopConfig->product_img_seo) {
                if (!$list[$key]->img_alt) {
                    $list[$key]->img_alt = $value->name;
                }
                if (!$list[$key]->img_title) {
                    $list[$key]->img_title = $value->name;
                }
            }
        }
        extract(Helper::Js_add_trigger(get_defined_vars(), "after"));		
		return $list;
	}
    
    function getList(){
        $JshopConfig = JSFactory::getConfig();
        if ($JshopConfig->manufacturer_sorting==2){
            $morder = 'name';
        }else{
            $morder = 'ordering';
        }
    return $this->getAllManufacturers(1, $morder, 'asc');
    }
    
    function getDescription($preparePluginContent = 1){        
        if (!$this->manufacturer_id){
            $this->getDescriptionMainPage($preparePluginContent);
            return 1;
        }        
        $lang = JSFactory::getLang();
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
        
        if ($preparePluginContent && JSFactory::getConfig()->use_plugin_content){
            Helper::changeDataUsePluginContent($this, "manufacturer");
        }
        return $this->description;
    }
    
    function getDescriptionMainPage($preparePluginContent = 1){
        $statictext = JSFactory::getTable("statictext");
        $rowstatictext = $statictext->loadData("manufacturer");
        $this->description = $rowstatictext->text;
        if ($preparePluginContent && JSFactory::getConfig()->use_plugin_content){
            Helper::changeDataUsePluginContent($this, "manufacturer");
        }
        return $this->description;
    }
    
    function getCategorys(){
        $JshopConfig = JSFactory::getConfig();
        $user = Factory::getUser();
        $lang = JSFactory::getLang();
        $db = Factory::getDBO();
        $adv_query = "";
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $adv_query .=' AND prod.access IN ('.$groups.') AND cat.access IN ('.$groups.')';
        if ($JshopConfig->hide_product_not_avaible_stock){
            $adv_query .= " AND prod.product_quantity > 0";
        }
        $query = "SELECT distinct cat.category_id as id, cat.`".$lang->get('name')."` as name FROM `#__jshopping_products` AS prod
                  LEFT JOIN `#__jshopping_products_to_categories` AS categ USING (product_id)
                  LEFT JOIN `#__jshopping_categories` as cat on cat.category_id=categ.category_id
                  WHERE prod.product_publish=1 AND prod.product_manufacturer_id=".(int)$this->manufacturer_id." AND cat.category_publish=1 "
                 .$adv_query." order by name";
        $db->setQuery($query);
        $list = $db->loadObJectList();
        extract(Helper::Js_add_trigger(get_defined_vars(), "after"));        
        return $list;
           
    }
    
    function getFieldListOrdering(){
        $ordering = JSFactory::getConfig()->manufacturer_sorting==1 ? "ordering" : "name";
        return $ordering;
    }
	
	function getSortingDirection(){
		$sort = JSFactory::getConfig()->manufacturer_sorting_direction;
		if (!$sort){
			$sort = 'asc';
		}
		return $sort;
	}
    
    function checkView(){
        if (!$this->manufacturer_publish){
            return 0;
        }else{
            return 1;
        }
    }
	
	function getCountToRow(){
		return JSFactory::getConfig()->count_manufacturer_to_row;
	}

    public function delete($manufacturer_id = null){
        $db = Factory::getDBO();
        $result = false;
        if (is_null($manufacturer_id)){
            $manufacturer_id = $this->manufacturer_id;
        }
        $query = $db->getQuery(true);
        $query->select('COUNT(*)');
        $query->from('#__jshopping_products');
        $query->where("product_manufacturer_id='".$db->escape($manufacturer_id)."'");
        $db->setQuery($query);
        if ((int)$db->loadResult() == 0){
            $result = parent::delete($pk);
        }else{
            $this->setError(sprintf(Text::_('JSHOP_NOT_EMPTY_MANUFACTURER_DELETE_ERROR'), $manufacturer_id));
        }
        return $result;
    }
}