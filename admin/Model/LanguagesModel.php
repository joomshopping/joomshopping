<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Model;

defined('_JEXEC') or die();

class LanguagesModel extends BaseadminModel{
    
    protected $nameTable = 'languageTable';

    function getAllLanguages($publish = 1) {
        $jshopConfig = \JSFactory::getConfig();
        $db = \JFactory::getDBO();
        $where_add = $publish ? "where `publish`='1'": ""; 
        $query = "SELECT * FROM `#__jshopping_languages` ".$where_add." order by `ordering`";
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
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
 
}