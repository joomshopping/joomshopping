<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model;
use Joomla\Component\Jshopping\Site\Lib\UploadFile;
defined('_JEXEC') or die();

class ProductLabelsModel extends BaseadminModel{
    
    protected $nameTable = 'productLabelTable';

    function getList($order = null, $orderDir = null){
        $db = \JFactory::getDBO();
        $ordering = "name";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
		$lang = \JSFactory::getLang();
        $query = "SELECT id, image, `".$lang->get("name")."` as name FROM `#__jshopping_product_labels` ORDER BY ".$ordering;
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    function save(array $post, $image = null){
        $jshopConfig = \JSFactory::getConfig();
		$productLabel = \JSFactory::getTable('productLabel');
		$lang = \JSFactory::getLang();
        $post['name'] = $post[$lang->get("name")];
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeSaveProductLabel', array(&$post));
        if ($image){
            $upload = new UploadFile($image);            
            $upload->setAllowFile($jshopConfig->allow_image_upload);
            $upload->setDir($jshopConfig->image_labels_path);
            $upload->setFileNameMd5(0);
            $upload->setFilterName(1);
            if ($upload->upload()){
                if ($post['old_image']){
                    @unlink($jshopConfig->image_labels_path."/".$post['old_image']);
                }
                $post['image'] = $upload->getName();
                @chmod($jshopConfig->image_labels_path."/".$post['image'], 0777);
            }else{
                if ($upload->getError() != 4){
                    \JSError::raiseWarning("", \JText::_('JSHOP_ERROR_UPLOADING_IMAGE'));
                    \JSHelper::saveToLog("error.log", "Label - Error upload image. code: ".$upload->getError());
                }
            }
        }
		$productLabel->bind($post);
		if (!$productLabel->store()){
			$this->setError(\JText::_('JSHOP_ERROR_SAVE_DATABASE').' '.$productLabel->getError());
			return 0;
		}        
        $dispatcher->triggerEvent('onAfterSaveProductLabel', array(&$productLabel));        
        return $productLabel;
    }
    
    public function deleteList(array $cid, $msg = 1){
        $app = \JFactory::getApplication();
        $jshopConfig = \JSFactory::getConfig();
        $res = array();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveProductLabel', array(&$cid));
		foreach($cid as $id){
            $table = $this->getDefaultTable();
            $table->load($id);
            @unlink($jshopConfig->image_labels_path."/".$productLabel->image);
            $table->delete();			
            if ($msg){
                $app->enqueueMessage(\JText::_('JSHOP_ITEM_DELETED'), 'message');
            }
            $res[$id] = true;
		}
        $dispatcher->triggerEvent('onAfterRemoveProductLabel', array(&$cid));
        return $res;
    }
    
}