<?php
/**
* @version      5.6.1 14.09.2022
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Model;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\Component\Jshopping\Site\Helper\Error as JSError;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Lib\UploadFile;
use Joomla\Component\Jshopping\Site\Lib\ImageLib;

defined('_JEXEC') or die();

class ManufacturersModel extends BaseadminModel{
    
    protected $nameTable = 'manufacturer';
    protected $tableFieldPublish = 'manufacturer_publish';

	public function getListItems(array $filters = [], array $orderBy = [], array $limit = [], array $params = []){
		return $this->getAllManufacturers($filters['publish'] ?? 0, $orderBy['order'] ?? null, $orderBy['dir'] ?? null, $filters);
	}

    function getAllManufacturers($publish=0, $order=null, $orderDir=null, $filter = []){
        $db = Factory::getDBO();
        $lang = JSFactory::getLang();         
        $query_where = '';
        if ($publish){
            $query_where .= " AND  manufacturer_publish=1";
        }
        if (isset($filter['text_search'])) {
            $word = addcslashes($db->escape($filter['text_search']), "_%");
            $query_where .= " AND (LOWER(`".$lang->get("name")."`) LIKE ".$db->q('%'.$word.'%')." OR LOWER(`".$lang->get('short_description')."`) LIKE '%" . $word . "%' OR LOWER(`".$lang->get('description')."`) LIKE '%" . $word . "%')";
        }
        $queryorder = '';
        if ($order && $orderDir){
            $queryorder = "order by ".$order." ".$orderDir;
        }
        $query = "SELECT manufacturer_id, manufacturer_url, manufacturer_logo, manufacturer_publish, ordering, `".$lang->get('name')."` as name
                FROM `#__jshopping_manufacturers` WHERE 1 ".$query_where." ".$queryorder;
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    function getList(){
        $jshopConfig = JSFactory::getConfig();
        if ($jshopConfig->manufacturer_sorting==2){
            $morder = 'name';
        }else{
            $morder = 'ordering';
        }
    return $this->getAllManufacturers(0, $morder, 'asc');
    }
    
    public function getPrepareDataSave($input){
        $jshopConfig = JSFactory::getConfig();
        $post = $input->post->getArray();
        $_alias = JSFactory::getModel("alias");
        $man_id = $post["manufacturer_id"];
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        foreach($languages as $lang){
            $post['name_'.$lang->language] = trim($post['name_'.$lang->language]);
            if ($jshopConfig->create_alias_product_category_auto && $post['alias_'.$lang->language]==""){
                $post['alias_'.$lang->language] = $post['name_'.$lang->language];
            }
            $post['alias_'.$lang->language] = ApplicationHelper::stringURLSafe($post['alias_'.$lang->language]);
            if ($post['alias_'.$lang->language]!="" && !$_alias->checkExistAlias1Group($post['alias_'.$lang->language], $lang->language, 0, $man_id)){
                $post['alias_'.$lang->language] = "";
                JSError::raiseWarning("",Text::_('JSHOP_ERROR_ALIAS_ALREADY_EXIST'));
            }
            $post['description_'.$lang->language] = $input->get('description'.$lang->id, '', 'RAW');
            $post['short_description_'.$lang->language] = $input->get('short_description_'.$lang->language, '', 'RAW');
        }
        return $post;
    }
    
    public function imageUpload(array $post, $image){
        $jshopConfig = JSFactory::getConfig();
        $upload = new UploadFile($image);
        $upload->setAllowFile($jshopConfig->allow_image_upload);
        $upload->setDir($jshopConfig->image_manufs_path);
		if (isset($post["image_name"]) && $post["image_name"] != '') {
            $upload->setNameWithoutExt($post["image_name"]);
        }
        $upload->setFileNameMd5(0);
        $upload->setFilterName(1);
        if ($upload->upload()){            
            if ($post['old_image']){
                @unlink($jshopConfig->image_manufs_path."/".$post['old_image']);
            }
            $name = $upload->getName();
            @chmod($jshopConfig->image_manufs_path."/".$name, 0777);
            
            if ($post['size_im_category'] < 3){
                if ($post['size_im_category'] == 1){
                    $category_width_image = $jshopConfig->image_category_width; 
                    $category_height_image = $jshopConfig->image_category_height;
                }else{
                    $category_width_image = $post['category_width_image'];
                    $category_height_image = $post['category_height_image'];
                }

                $path_full = $jshopConfig->image_manufs_path."/".$name;
                $path_thumb = $jshopConfig->image_manufs_path."/".$name;

                if (!ImageLib::resizeImageMagic($path_full, $category_width_image, $category_height_image, $jshopConfig->image_cut, $jshopConfig->image_fill, $path_thumb, $jshopConfig->image_quality, $jshopConfig->image_fill_color, $jshopConfig->image_interlace)) {
                    JSError::raiseWarning("",Text::_('JSHOP_ERROR_CREATE_THUMBAIL'));
                    Helper::saveToLog("error.log", "SaveManufacturer - Error create thumbail");
                }
                @chmod($jshopConfig->image_manufs_path."/".$name, 0777);
            }
            return $name;
        }else{
            if ($upload->getError() != 4){
                JSError::raiseWarning("", Text::_('JSHOP_ERROR_UPLOADING_IMAGE'));
                Helper::saveToLog("error.log", "SaveManufacturer - Error upload image. code: ".$upload->getError());
            }
            return '';
        }
    }
    
    public function save(array $post, $image = null){
        $jshopConfig = JSFactory::getConfig();
        $dispatcher = Factory::getApplication();
        $man = JSFactory::getTable('manufacturer');
        $man->load($post["manufacturer_id"]);
        if (!$post['manufacturer_publish']){
            $post['manufacturer_publish'] = 0;
        }
        $dispatcher->triggerEvent('onBeforeSaveManufacturer', array(&$post));
        $post['products_page'] = isset($post['products_page']) ? intval($post['products_page']) : null;
        $post['products_row'] = isset($post['products_row']) ? intval($post['products_row']) : null;
        $man->bind($post);
        if (!$post["manufacturer_id"]){
            $man->ordering = null;
            $man->ordering = $man->getNextOrder();            
        }
        if ($image){
            $image_name = $this->imageUpload($post, $image);
            if ($image_name){
                $man->manufacturer_logo = $image_name;
            }
        }
        if ((!isset($image_name) || !$image_name) && $man->manufacturer_logo && $post['image_name']) {
            $man->manufacturer_logo = \Joomla\Component\Jshopping\Site\Helper\File::rename(
                $jshopConfig->image_manufs_path,
                $man->manufacturer_logo,
                $post['image_name']
            );
        }
        if (!$man->store()){
            $this->setError(Text::_('JSHOP_ERROR_SAVE_DATABASE')." ".$man->getError());
            return 0;
        }
        $dispatcher->triggerEvent('onAfterSaveManufacturer', array(&$man));
        return $man;
    }
    
    function deleteFoto($id){
        $jshopConfig = JSFactory::getConfig();
        $manuf = JSFactory::getTable('manufacturer');
        $manuf->load($id);
        @unlink($jshopConfig->image_manufs_path.'/'.$manuf->manufacturer_logo);
        $manuf->manufacturer_logo = "";
        $manuf->store();
    }
    
    public function deleteList(array $cid, $msg = 1){
        $app = Factory::getApplication();
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveManufacturer', array(&$cid));
        $res = array();
        foreach($cid as $value){
            $manuf = JSFactory::getTable('manufacturer');
            $manuf->load($value);
            if($manuf->delete()){
                if($manuf->manufacturer_logo){
                    @unlink(JSFactory::getConfig()->image_manufs_path.'/'.$manuf->manufacturer_logo);
                }
                if($msg){
                    $app->enqueueMessage(sprintf(Text::_('JSHOP_MANUFACTURER_DELETED'), $value), 'message');
                }
                $res[$value] = true;
            }else if($msg){
                $app->enqueueMessage(implode("<br/>", $manuf->getErrors()), 'warning');
            }
        }
        $dispatcher->triggerEvent('onAfterRemoveManufacturer', array(&$cid));
        return $res;
    }
    
    public function publish(array $cid, $flag){
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforePublishManufacturer', array(&$cid, &$flag));
        parent::publish($cid, $flag);
        $dispatcher->triggerEvent('onAfterPublishManufacturer', array(&$cid, &$flag));
    }
      
}