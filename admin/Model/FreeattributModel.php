<?php
/**
* @version      5.6.1 29.03.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model; 
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

class FreeAttributModel extends BaseadminModel{

	public function getListItems(array $filters = [], array $orderBy = [], array $limit = [], array $params = []){
		return $this->getAll($orderBy['order'] ?? null, $orderBy['dir'] ?? null, $filters);
	}
    
    function getNameAttrib($id) {
        $db = Factory::getDBO();
        $lang = JSFactory::getLang();
        $query = "SELECT `".$lang->get("name")."` as name FROM `#__jshopping_free_attr` WHERE id = '".$db->escape($id)."'";
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadResult();
    }

    function getAll($order = null, $orderDir = null, $filter = []) {
        $lang = JSFactory::getLang();
        $db = Factory::getDBO(); 
        $ordering = 'ordering';
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $where = '';
        if (isset($filter['text_search'])) {
            $word = addcslashes($db->escape($filter['text_search']), "_%");
            $where .= " AND (LOWER(`".$lang->get("name")."`) LIKE ".$db->q('%'.$word.'%')." OR LOWER(`".$lang->get('description')."`) LIKE '%" . $word . "%')";
        }
        $query = "SELECT id, `".$lang->get("name")."` as name, ordering, required, publish 
            FROM `#__jshopping_free_attr` 
            WHERE 1 ".$where."
            ORDER BY ".$ordering;
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);        
        return $db->loadObjectList();
    }
    
    public function save(array $post){
        $attribut = JSFactory::getTable('freeattribut');    
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeSaveFreeAtribut', array(&$post));
        if (!$post['id']){
            $attribut->ordering = null;
            $attribut->ordering = $attribut->getNextOrder();            
        }
        $attribut->bind($post);
        if (!$attribut->store()){
            $this->setError(Text::_('JSHOP_ERROR_SAVE_DATABASE').' '.$attribut->getError());
            return 0;
        }
        $dispatcher->triggerEvent('onAfterSaveFreeAtribut', array(&$attribut));
        return $attribut;
    }
    
    public function deleteList(array $cid = array(), $msg = 1){
        $app = Factory::getApplication();
        $db = Factory::getDBO();
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveFreeAtribut', array(&$cid));
		foreach($cid as $id){            
			$query = "DELETE FROM `#__jshopping_free_attr` WHERE `id` = ".(int)$id;
			$db->setQuery($query);
			$db->execute();
            
            $query = "delete from `#__jshopping_products_free_attr` where `attr_id` = ".(int)$id;
            $db->setQuery($query);
            $db->execute();
		}
        if ($msg){
            $app->enqueueMessage(Text::_('JSHOP_ATTRIBUT_DELETED'), 'message');
        }
        $dispatcher->triggerEvent('onAfterRemoveFreeAtribut', array(&$cid));
    }
    
}
