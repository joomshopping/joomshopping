<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model; defined('_JEXEC') or die();

class TaxesModel extends BaseadminModel{
    
    protected $nameTable = 'tax';

    function getAllTaxes($order = null, $orderDir = null) {
        $db = \JFactory::getDBO();
        $ordering = 'tax_name';
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT * FROM `#__jshopping_taxes` ORDER BY ".$ordering;
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    function getExtTaxes($tax_id = 0, $order = null, $orderDir = null) {
        $db = \JFactory::getDBO();
        $where = "";
        if ($tax_id){
            $where = " where ET.tax_id=".(int)$tax_id;
        }
        $ordering = 'ET.id';
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT ET.*, T.tax_name FROM `#__jshopping_taxes_ext` as ET left join #__jshopping_taxes as T on T.tax_id=ET.tax_id ".$where." ORDER BY ".$ordering;
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    function getValue($id){
        $db = \JFactory::getDBO();
        $query = "select tax_value from #__jshopping_taxes where tax_id=".(int)$id;
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadResult();
    }

    function save(array $post){
        $tax = \JSFactory::getTable('tax');
        $post['tax_value'] = \JSHelper::saveAsPrice($post['tax_value']);
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeSaveTax', array(&$tax));
        $tax->bind($post);
        if( !$tax->store() ) {
            $this->setError(\JText::_('JSHOP_ERROR_SAVE_DATABASE'));
            return 0;
        }
        $dispatcher->triggerEvent('onAfterSaveTax', array(&$tax));
        return $tax;
    }

    function deleteList(array $cid, $msg = 1){
        $app = \JFactory::getApplication();
        $db = \JFactory::getDBO();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveTax', array(&$cid));
        foreach($cid as $value) {
            $tax = \JSFactory::getTable('tax');
            $tax->load($value);
            $query = "SELECT pr.product_id
                       FROM `#__jshopping_products` AS pr
                       WHERE pr.product_tax_id = '" . $db->escape($value) . "'";
            $db->setQuery($query);
            $res = $db->execute();
            if ($db->getNumRows($res)){
                continue;
            }
            $query = "DELETE FROM `#__jshopping_taxes` WHERE `tax_id` = '" . $db->escape($value) . "'";
            $db->setQuery($query);
            $db->execute();
            $query = "DELETE FROM `#__jshopping_taxes_ext` WHERE `tax_id` = '" . $db->escape($value) . "'";
            $db->setQuery($query);
            $db->execute();
            if ($msg){
                $app->enqueueMessage(\JText::_('JSHOP_ITEM_DELETED'), 'message');
            }
        }
        $dispatcher->triggerEvent('onAfterRemoveTax', array(&$cid));
    }

}