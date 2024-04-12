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

class ProductFieldGroupsModel extends BaseadminModel{
    
    protected $nameTable = 'productFieldGroupTable';

    function getList() {
        $db = \JFactory::getDBO();
        $lang = \JSFactory::getLang();
        $query = "SELECT id, `".$lang->get("name")."` as name, ordering FROM `#__jshopping_products_extra_field_groups` order by ordering";
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    function save(array $post) {
        $productfieldgroup = \JSFactory::getTable('productFieldGroup');
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeSaveProductFieldGroup', array(&$post));
        $productfieldgroup->bind($post);
        if( !$post['id'] ) {
            $productfieldgroup->ordering = null;
            $productfieldgroup->ordering = $productfieldgroup->getNextOrder();
        }
        if( !$productfieldgroup->store() ) {
            $this->setError(\JText::_('JSHOP_ERROR_SAVE_DATABASE'));
            return 0;
        }
        $dispatcher->triggerEvent('onAfterSaveProductFieldGroup', array(&$productfieldgroup));
        return $productfieldgroup;
    }

    function deleteList(array $cid, $msg = 1){
        $app = \JFactory::getApplication();
        $db = \JFactory::getDBO();
        foreach($cid as $id){
            $query = "DELETE FROM `#__jshopping_products_extra_field_groups` WHERE `id` = ".(int)$id;
            $db->setQuery($query);
            $db->execute();
            if ($msg){
                $app->enqueueMessage(\JText::_('JSHOP_ITEM_DELETED'), 'message');
            }
        }
        \JFactory::getApplication()->triggerEvent('onAfterRemoveProductFieldGroup', array(&$cid));
    }

}