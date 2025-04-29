<?php
/**
* @version      5.6.2 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\Component\Jshopping\Site\Helper\Error as JSError;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Lib\UploadFile;
defined('_JEXEC') or die();

class ProductlabelsModel extends BaseadminModel{
    
    protected $nameTable = 'productlabel';

	public function getListItems(array $filters = [], array $orderBy = [], array $limit = [], array $params = []){
		return $this->getList($orderBy['order'] ?? null, $orderBy['dir'] ?? null, $filters);
	}

    function getList($order = null, $orderDir = null, $filter = []){
        $db = Factory::getDBO();
        $lang = JSFactory::getLang();
        $ordering = "name";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $where = '';
        if (isset($filter['text_search'])) {
            $where .= " AND (`".$lang->get("name")."` LIKE ".$db->q('%'.$filter['text_search'].'%').")";
        }
        $query = "SELECT id, image, `".$lang->get("name")."` as name 
        FROM `#__jshopping_product_labels`
        WHERE 1 ".$where." 
        ORDER BY ".$ordering;
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    function save(array $post, $image = null){
        $jshopConfig = JSFactory::getConfig();
		$productLabel = JSFactory::getTable('productLabel');
		$lang = JSFactory::getLang();
        $post['name'] = $post[$lang->get("name")];
        $dispatcher = Factory::getApplication();
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
                    JSError::raiseWarning("", Text::_('JSHOP_ERROR_UPLOADING_IMAGE'));
                    Helper::saveToLog("error.log", "Label - Error upload image. code: ".$upload->getError());
                }
            }
        }
		$productLabel->bind($post);
		if (!$productLabel->store()){
			$this->setError(Text::_('JSHOP_ERROR_SAVE_DATABASE').' '.$productLabel->getError());
			return 0;
		}        
        $dispatcher->triggerEvent('onAfterSaveProductLabel', array(&$productLabel));        
        return $productLabel;
    }
    
    public function deleteList(array $cid, $msg = 1){
        $app = Factory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $res = array();
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveProductLabel', array(&$cid));
		foreach($cid as $id){
            $table = $this->getDefaultTable();
            $table->load($id);
            @unlink($jshopConfig->image_labels_path."/".$productLabel->image);
            $table->delete();			
            if ($msg){
                $app->enqueueMessage(Text::_('JSHOP_ITEM_DELETED'), 'message');
            }
            $res[$id] = true;
		}
        $dispatcher->triggerEvent('onAfterRemoveProductLabel', array(&$cid));
        return $res;
    }
    
}