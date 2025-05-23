<?php
/**
* @version      5.6.2 21.04.2025
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
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Jshopping\Site\Lib\TreeObjectList;
use Joomla\Component\Jshopping\Site\Lib\UploadFile;
use Joomla\Component\Jshopping\Site\Lib\ImageLib;

defined('_JEXEC') or die();

class CategoriesModel extends BaseadminModel{
    
    protected $nameTable = 'category';
    protected $tableFieldPublish = 'category_publish';

	public function getListItems(array $filters = [], array $orderBy = [], array $limit = [], array $params = []) {
		return $this->getAllList($params['display'] ?? 0, $orderBy['order'] ?? null, $orderBy['dir'] ?? null);
	}
    
    function getAllList($display=0, $order = null, $orderDir = null){
        $db = Factory::getDBO();
        $lang = JSFactory::getLang();
        $orderby = "ordering";
        if (isset($order) && $order=="id") $orderby = "`category_id`";
        if (isset($order) && $order=="name") $orderby = "`".$lang->get('name')."`";
        if (isset($order) && $order=="ordering") $orderby = "ordering";
        if (isset($orderDir)) {
            $orderby .= " ".$orderDir;
        }
        $query = "SELECT `".$lang->get('name')."` as name, category_id FROM `#__jshopping_categories` ORDER BY ".$orderby;
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        $list = $db->loadObjectList();
        if ($display==1){
            $rows = [];
            foreach($list as $v){
                $rows[$v->category_id] = $v->name;
            }
            unset($list);
            $list = $rows;
        }
        return $list;
    }
    
    function getSubCategories($parentId, $order = 'id', $ordering = 'asc') {
        $db = Factory::getDBO();        
        $lang = JSFactory::getLang();
        if ($order=="id") $orderby = "`category_id`";
        if ($order=="name") $orderby = "`".$lang->get('name')."`";
        if ($order=="ordering") $orderby = "ordering";
        if (!$orderby) $orderby = "ordering";
        $query = "SELECT `".$lang->get('name')."` as name,`".$lang->get('short_description')."` as short_description, category_id, category_publish, ordering, category_image FROM `#__jshopping_categories`
                   WHERE category_parent_id = '".$db->escape($parentId)."'
                   ORDER BY ".$orderby." ".$ordering;
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);        
        return $db->loadObjectList();
    }
    
    function getAllCatCountSubCat() {
        $db = Factory::getDBO();        
        $query = "SELECT C.category_id, count(C.category_id) as k FROM `#__jshopping_categories` as C
                   inner join  `#__jshopping_categories` as SC on C.category_id=SC.category_parent_id
                   group by C.category_id";
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        $list = $db->loadObjectList();
        $rows = array();
        foreach($list as $row){
            $rows[$row->category_id] = $row->k;
        }        
        return $rows;
    }
    
    function getAllCatCountProducts(){
        $db = Factory::getDBO();    
        $query = "SELECT category_id, count(product_id) as k FROM `#__jshopping_products_to_categories` group by category_id";
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        $list = $db->loadObjectList();
        $rows = array();
        foreach($list as $row){
            $rows[$row->category_id] = $row->k;
        }        
        return $rows;
    }
    
    function deleteCategory($category_id){
        $db = Factory::getDBO();
        $query = "DELETE FROM `#__jshopping_categories` WHERE `category_id` = '" . $db->escape($category_id) . "'";
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        $db->execute();
    }
    
    function getTreeAllCategories($filter = array(), $order = null, $orderDir = null) {
        $db = Factory::getDBO();
        $user = Factory::getUser();
        $lang = JSFactory::getLang();

        $query = "SELECT ordering, category_id, category_parent_id, `".$lang->get('name')."` as name, `".$lang->get('short_description')."` as short_description, `".$lang->get('description')."` as description, category_publish, category_image FROM `#__jshopping_categories`
                  ORDER BY category_parent_id, ". $this->_allCategoriesOrder($order, $orderDir);
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
		$list = $db->loadObjectList();

		foreach($list as $key=>$category){
			$category->isPrev = 0; 
			$category->isNext = 0;
			if (isset($list[$key-1]) && $category->category_parent_id == $list[$key-1]->category_parent_id){
				$category->isPrev = 1;
			}
			if (isset($list[$key+1]) && $category->category_parent_id == $list[$key+1]->category_parent_id){
				$category->isNext = 1;
			}
		}

		$tree = new TreeObjectList($list, array(
			'parent' => 'category_parent_id',
			'id' => 'category_id',
			'is_select' => 0
		));
		$categories = $tree->getList();
        $listParents = [];
        foreach($categories as $v){
            $listParents[$v->category_id] = $v->category_parent_id;
        }
        foreach($categories as $key => $category){
            $category->parentsStr = '';
            $category_parent_id = $category->category_parent_id;
            $i = 0;
            while ($category_parent_id && $i < 1000){
                $category->parentsStr .= ' '.$category_parent_id;
                $category_parent_id = $listParents[$category_parent_id];
                $i++;
            }
            if ($category->category_parent_id){
                $category->parentsStr .= ' 0';
            }
            $category->parentsStr = trim($category->parentsStr);
        }

        if (count($categories)){
			if (isset($filter['text_search']) && !empty($filter['text_search'])){
                $originalCategories = $categories;
                $filter['text_search'] = strtolower($filter['text_search']);

                foreach($categories as $key => $category){
                    if (strpos(strtolower($category->name), $filter['text_search']) === false && 
						strpos(strtolower($category->short_description), $filter['text_search']) === false && 
						strpos(strtolower($category->description), $filter['text_search']) === false){
                        unset($categories[$key]);
                    }
                }

                if (count($categories)){
                    foreach($categories as $key => $category){
                        $categories[$key]->name = "<span class = 'jshop_green'>".$categories[$key]->name."</span>"; 
                        $category_parent_id = $category->category_parent_id;
                        $i = 0;
                        while ($category_parent_id && $i < 1000) {
                            foreach ($originalCategories as $originalKey => $originalCategory){
                                if ($originalCategory->category_id == $category_parent_id){
                                    $categories[$originalKey] = $originalCategory;
                                    $category_parent_id = $originalCategory->category_parent_id;
                                    break;
                                }
                            }
                            $i++;
                        }
                    }
                    
                    ksort($categories);
                }
            }
		
            foreach($categories as $key=>$category){
                $category->space = ''; 
                for ($i = 0; $i < $category->level; $i++){
                    $category->space .= '<span class = "gi">|—</span>';
                }
            }
        }

        return $categories;
    }
   
    function _allCategoriesOrder($order = null, $orderDir = null){
        $lang = JSFactory::getLang();
        if ($order && $orderDir){
            $fields = array("name" => "`".$lang->get('name')."`", "id" => "`category_id`", "description" => "`".$lang->get('description')."`", "ordering" => "`ordering`");
            if (strtolower($orderDir) != "asc") $orderDir = "desc";
            if (!$fields[$order]) return "`ordering` ".$orderDir;
            extract(Helper::js_add_trigger(get_defined_vars(), "before"));
            return $fields[$order]." ".$orderDir;
        }else{
            return "`ordering` asc";
        }
    }

    function uploadImage($post, $image = null){
        $jshopConfig = JSFactory::getConfig();
        if (is_null($image)){
            $image = $_FILES['category_image'];
        }
        $upload = new UploadFile($image);
        $upload->setAllowFile($jshopConfig->allow_image_upload);
        $upload->setDir($jshopConfig->image_category_path);
		if (isset($post["image_name"]) && $post["image_name"] != '') {
            $upload->setNameWithoutExt($post["image_name"]);
        }
        $upload->setFileNameMd5(0);
        $upload->setFilterName(1);
        if ($upload->upload()){
            $name = $upload->getName();
            Factory::getApplication()->triggerEvent('onAfterUploadCategoryImage', array(&$post, &$name));
            if ($post['old_image'] && $name!=$post['old_image']){
                @unlink($jshopConfig->image_category_path."/".$post['old_image']);
            }
            @chmod($jshopConfig->image_category_path."/".$name, 0777);
            
            if ($post['size_im_category'] < 3){
                if($post['size_im_category'] == 1){
                    $category_width_image = $jshopConfig->image_category_width; 
                    $category_height_image = $jshopConfig->image_category_height;
                }else{
                    $category_width_image = $post['category_width_image'];
                    $category_height_image = $post['category_height_image'];
                }

                $path_full = $jshopConfig->image_category_path."/".$name;
                $path_thumb = $jshopConfig->image_category_path."/".$name;
                if ($category_width_image || $category_height_image){
                    if (!ImageLib::resizeImageMagic($path_full, $category_width_image, $category_height_image, $jshopConfig->image_cut, $jshopConfig->image_fill, $path_thumb, $jshopConfig->image_quality, $jshopConfig->image_fill_color, $jshopConfig->image_interlace)) {
                        JSError::raiseWarning("",Text::_('JSHOP_ERROR_CREATE_THUMBAIL'));
                        Helper::saveToLog("error.log", "SaveCategory - Error create thumbail");
                    }
                }
                @chmod($jshopConfig->image_category_path."/".$name, 0777);
            }
            $category_image = $name;
            Factory::getApplication()->triggerEvent('onAfterSaveCategoryImage', array(&$post, &$category_image, &$path_full, &$path_thumb));
        }else{
            $category_image = '';
            if ($upload->getError() != 4){
                Factory::getApplication()->enqueueMessage(Text::_('JSHOP_ERROR_UPLOADING_IMAGE'), 'notice');
                Helper::saveToLog("error.log", "SaveCategory - Error upload image. code: ".$upload->getError());
            }
        }
        return $category_image;
    }
    
    public function getPrepareDataSave($input){
        $jshopConfig = JSFactory::getConfig();
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $_alias = JSFactory::getModel("alias");
        $post = $input->post->getArray();
        foreach($languages as $lang){
            $post['name_'.$lang->language] = trim($post['name_'.$lang->language]);
            if ($jshopConfig->create_alias_product_category_auto && $post['alias_'.$lang->language]==""){
                $post['alias_'.$lang->language] = $post['name_'.$lang->language];
            }
            //$post['alias_'.$lang->language] = \JApplication::stringURLSafe($post['alias_'.$lang->language]);
            $post['alias_'.$lang->language] = ApplicationHelper::stringURLSafe($post['alias_'.$lang->language]);
            if ($post['alias_'.$lang->language]!="" && !$_alias->checkExistAlias1Group($post['alias_'.$lang->language], $lang->language, $post['category_id'], 0)){
                $post['alias_'.$lang->language] = "";
                JSError::raiseWarning("",Text::_('JSHOP_ERROR_ALIAS_ALREADY_EXIST'));
            }
            $post['description_'.$lang->language] = $input->get('description'.$lang->id, '', 'RAW');
            $post['short_description_'.$lang->language] = $input->get('short_description_'.$lang->language, '', 'RAW');
        }
        return $post;
    }
    
    public function save(array $post, $image = null){
        $jshopConfig = JSFactory::getConfig();
        $category = JSFactory::getTable("category");
        $category->load($post["category_id"]);
        if (!$post["category_id"]){
            $post['category_add_date'] = Helper::getJsDate();
        }
        if (!isset($post['category_publish'])){
            $post['category_publish'] = 0;
        }
        if ($post['category_parent_id']==$post['category_id']){
            $post['category_parent_id'] = 0;
        }
        $fieldsint = ['products_page', 'products_row'];
		foreach($fieldsint as $v) {
			if (isset($post[$v]) && $post[$v] == '') {
				$post[$v] = 0;
			}
		}
        
        Factory::getApplication()->triggerEvent('onBeforeSaveCategory', array(&$post));

        $category->bind($post);
        if ($image){
            $upload_image = $this->uploadImage($post, $image);
            if ($upload_image!=''){
                $category->category_image = $upload_image;
            }
        }
        if ((!isset($upload_image) || !$upload_image) && $category->category_image && $post['image_name']) {
            $category->category_image = \Joomla\Component\Jshopping\Site\Helper\File::rename(
                $jshopConfig->image_category_path,
                $category->category_image,
                $post['image_name']
            );
        }
        $this->_reorderCategory($category);
 
        Factory::getApplication()->triggerEvent('onBeforeStoreCategory', array(&$post, &$category));
        if (!$category->store()){
            $this->setError(Text::_('JSHOP_ERROR_SAVE_DATABASE')." ".$category->getError());
            return 0;
        }
        
        Factory::getApplication()->triggerEvent('onAfterSaveCategory', array(&$category, &$post));
        return $category;
    }
    
    function _reorderCategory(&$category) {
        $db = Factory::getDBO();
        $query = "UPDATE `#__jshopping_categories` SET `ordering` = ordering + 1
                    WHERE `category_parent_id` = '" . $category->category_parent_id . "' AND `ordering` > '" . $category->ordering . "'";
        $db->setQuery($query);
        $db->execute();
        $category->ordering++;
    }
    
    function _getAllCategoriesLevel($parentId, $currentOrdering = 0){
        $rows = $this->getSubCategories($parentId, "ordering");
        $first[] = HTMLHelper::_('select.option', '0',Text::_('JSHOP_ORDERING_FIRST'),'ordering','name');
        $rows = array_merge($first, $rows);
        $currentOrdering = (!$currentOrdering) ? ($rows[count($rows) - 1]->ordering) : ($currentOrdering);
        return (HTMLHelper::_('select.genericlist', $rows,'ordering','class="inputbox form-select"','ordering','name', $currentOrdering));
    }
    
    public function deleteList(array $cid, $msg = 1){
        $jshopConfig = JSFactory::getConfig();
        $app = Factory::getApplication();
        
        Factory::getApplication()->triggerEvent('onBeforeRemoveCategory', array(&$cid));
        $allCatCountProducts = $this->getAllCatCountProducts();
        foreach($cid as $value){
            $category = JSFactory::getTable("category");
            $category->load($value);
            $name_category = $category->getName();
            $childs = $category->getChildCategories();
            if ((isset($allCatCountProducts[$value]) && $allCatCountProducts[$value]) || count($childs)){
                if ($msg){
                    $app->enqueueMessage(sprintf(Text::_('JSHOP_CATEGORY_NO_DELETED'), $name_category), 'error');
                }
                continue;
            }
            $this->deleteCategory($value);
            @unlink($jshopConfig->image_category_path.'/'.$category->category_image);            
            if ($msg){
                $app->enqueueMessage(sprintf(Text::_('JSHOP_CATEGORY_DELETED'), $name_category), 'message');
            }
        }
        Factory::getApplication()->triggerEvent('onAfterRemoveCategory', array(&$cid));
    }
    
    public function publish(array $cid, $flag){
        
        Factory::getApplication()->triggerEvent('onBeforePublishCategory', array(&$cid, &$flag));
        parent::publish($cid, $flag);
        Factory::getApplication()->triggerEvent('onAfterPublishCategory', array(&$cid, &$flag));
    }
    
    public function order($id, $move,  $where = ''){
        $table = JSFactory::getTable('category');
        $table->load($id);
        $table->move($move, 'category_parent_id="'.$table->category_parent_id.'"');
    }

    public function deleteFoto($id){
        $jshopConfig = JSFactory::getConfig();
        $category = JSFactory::getTable("category", "jshop");
        $category->load($id);
        @unlink($jshopConfig->image_category_path.'/'.$category->category_image);
        $category->category_image = "";
        $category->store();
    }
    
}