<?php
/**
* @version      5.3.0 11.12.2023
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Model;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Helper\Helper;

defined('_JEXEC') or die();

class LanguagesModel extends BaseadminModel{
    
    protected $nameTable = 'language';

	public function getListItems(array $filters = [], array $orderBy = [], array $limit = [], array $params = []){
		return $this->getAllLanguages($filters['publish'] ?? 0);
	}

    public function getAllLanguages($publish = 1) {
        $jshopConfig = JSFactory::getConfig();
        $db = Factory::getDBO();
        $where_add = $publish ? "where `publish`='1'": ""; 
        $query = "SELECT * FROM `#__jshopping_languages` ".$where_add." order by `ordering`";
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        $rowssort = array();
        $rows = $db->loadObjectList();
        foreach($rows as $k=>$v){
            $rows[$k]->lang = substr($v->language, 0, 2);
            if ($jshopConfig->cur_lang == $v->language) $rowssort[] = $rows[$k];
        }
        foreach($rows as $k=>$v){
            if (isset($rowssort[0]) && $rowssort[0]->language==$v->language) continue;
            $rowssort[] = $v;
        }
        unset($rows);
        return $rowssort;
    }

    public function getAllTags($publish = 1) {
        $list = $this->getAllLanguages($publish);
        return array_map(function($lang){return $lang->language;}, $list);
    }
 
}