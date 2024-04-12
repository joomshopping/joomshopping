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

class ExtTaxesModel extends BaseadminModel{
    
    protected $nameTable = 'taxExt';
    
    public function getPrepareDataSave($input){
        $post = $input->post->getArray();
        $post['tax'] = \JSHelper::saveAsPrice($post['tax']);
        $post['firma_tax'] = \JSHelper::saveAsPrice($post['firma_tax']);
        return $post;
    }
    
    public function save(array $post){
        $tax = \JSFactory::getTable('taxExt');
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeSaveExtTax', array(&$post));        
        $tax->bind($post);
        $tax->setZones($post['countries_id']);
        if (!$tax->store()){
            print $tax->getError();
            $this->setError(\JText::_('JSHOP_ERROR_SAVE_DATABASE'));
            return 0; 
        }
        \JSHelperAdmin::updateCountExtTaxRule();
        $dispatcher->triggerEvent('onAfterSaveExtTax', array(&$tax));
        return $tax;
    }
    
    public function deleteList(array $cid, $msg = 1){
        $db = \JFactory::getDBO();
        $app = \JFactory::getApplication();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveExtTax', array(&$cid));
        $res = array();
        foreach($cid as $id){
            $query = "DELETE FROM `#__jshopping_taxes_ext` WHERE `id` = ".(int)$id;
            $db->setQuery($query);
            if ($db->execute()){
                $res[$id] = true;
                if ($msg){
                    $app->enqueueMessage(\JText::_('JSHOP_ITEM_DELETED'), 'message');
                }
            }else{
                $res[$id] = false;
            }
        }
        \JSHelperAdmin::updateCountExtTaxRule();
        $dispatcher->triggerEvent('onAfterRemoveExtTax', array(&$cid));
        return $res;
    }

}