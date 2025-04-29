<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
defined('_JEXEC') or die();

class ExtTaxesModel extends BaseadminModel{
    
    protected $nameTable = 'taxext';
    
    public function getPrepareDataSave($input){
        $post = $input->post->getArray();
        $post['tax'] = Helper::saveAsPrice($post['tax']);
        $post['firma_tax'] = Helper::saveAsPrice($post['firma_tax']);
        return $post;
    }
    
    public function save(array $post){
        $tax = JSFactory::getTable('taxExt');
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeSaveExtTax', array(&$post));
        $tax->bind($post);
        $tax->setZones($post['countries_id']);
        if (!$tax->store()){
            $this->setError(Text::_('JSHOP_ERROR_SAVE_DATABASE')." ".$tax->getError());
            return 0; 
        }
        HelperAdmin::updateCountExtTaxRule();
        $dispatcher->triggerEvent('onAfterSaveExtTax', array(&$tax));
        return $tax;
    }
    
    public function deleteList(array $cid, $msg = 1){
        $db = Factory::getDBO();
        $app = Factory::getApplication();
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveExtTax', array(&$cid));
        $res = array();
        foreach($cid as $id){
            $query = "DELETE FROM `#__jshopping_taxes_ext` WHERE `id` = ".(int)$id;
            $db->setQuery($query);
            if ($db->execute()){
                $res[$id] = true;
                if ($msg){
                    $app->enqueueMessage(Text::_('JSHOP_ITEM_DELETED'), 'message');
                }
            }else{
                $res[$id] = false;
            }
        }
        HelperAdmin::updateCountExtTaxRule();
        $dispatcher->triggerEvent('onAfterRemoveExtTax', array(&$cid));
        return $res;
    }

}