<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model;
defined('_JEXEC') or die();

class ImportExportStartModel{
	
	public function checkKey($key){	
		return \JSFactory::getConfig()->securitykey == $key;
	}
	
	public function getListStart($time, $filterAlias = '', $id = 0){
		$db = \JFactory::getDBO();
        $adv_query = '';
        if ($filterAlias){
            $adv_query .= " and `alias`='".$db->escape($filterAlias)."'";
        }
        if ($id){
            $adv_query .= " and `id`=".(int)$id;
        }
        $query = "SELECT * FROM `#__jshopping_import_export` where `steptime`>0 and (endstart + steptime < ".(int)$time.") ".$adv_query."  ORDER BY id";
        $db->setQuery($query);
        return $db->loadObjectList();
	}
	
	public function executeList($time = null, $print_alias = 1, $filterAlias = '', $id = 0){
		if (is_null($time)){
			$time = time();
		}        
        $list = $this->getListStart($time, $filterAlias, $id);

        foreach($list as $ie){
            $alias = $ie->alias;
            if (!file_exists(JPATH_COMPONENT_ADMINISTRATOR."/importexport/".$alias."/".$alias.".php")){
                print sprintf(\JText::_('JSHOP_ERROR_FILE_NOT_EXIST'), "/importexport/".$alias."/".$alias.".php");
                return 0;
            }            
			$this->execute($alias, $ie->id);
			if ($print_alias){
				print $alias."\n";
			}
        }
	}
	
	public function execute($alias, $id){
		$_importexport = \JSFactory::getTable('ImportExport'); 
        $_importexport->load($id);
		
		include_once(JPATH_COMPONENT_ADMINISTRATOR."/importexport/".$alias."/".$alias.".php");
		$classname = 'Ie'.$alias;
		$controller = new $classname(array(
            'ie_id' => $id,
            'alias' => $alias,
            'params' => $_importexport->get('params')
        ));		
		$controller->save();
	}
	
}