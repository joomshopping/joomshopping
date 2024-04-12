<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Model;
defined('_JEXEC') or die;

class UnitsModel extends BaseadminModel{
    
    protected $nameTable = 'unit';

    function getUnits(){
        $db = \JFactory::getDBO();
        $lang = \JSFactory::getLang();
        $query = "SELECT id, `".$lang->get('name')."` as name FROM `#__jshopping_unit` ORDER BY name";
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    function save(array $post){
		$unit = \JSFactory::getTable('unit');
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeSaveUnit', array(&$post));
		$unit->bind($post);
		if (!$unit->store()){
            $this->setError(\JText::_('JSHOP_ERROR_SAVE_DATABASE')." ".$unit->getError());
			return 0;
		}
        $dispatcher->triggerEvent('onAfterSaveUnit', array(&$unit));
        return $unit;
    }

    function deleteList(array $cid, $msg = 1){
        $app = \JFactory::getApplication();
		$db = \JFactory::getDBO();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveUnit', array(&$cid));
		foreach($cid as $id){
			$query = "DELETE FROM `#__jshopping_unit` WHERE `id` = ".(int)$id;
			$db->setQuery($query);
			$db->execute();
            if ($msg){
                $app->enqueueMessage(\JText::_('JSHOP_ITEM_DELETED'), 'message');
            }
		}
        $dispatcher->triggerEvent('onAfterRemoveUnit', array(&$cid));
    }

}