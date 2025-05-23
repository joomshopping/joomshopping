<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
defined('_JEXEC') or die();

class StatictextModel extends BaseadminModel{

	public function getListItems(array $filters = [], array $orderBy = [], array $limit = [], array $params = []){
		return $this->getList($filters['use_for_return_policy'] ?? 0);
	}
    
    function getList($use_for_return_policy = 0){
        $lang = JSFactory::getLang();
        $db = Factory::getDBO(); 
        $where = $use_for_return_policy?' WHERE use_for_return_policy=1 ':'';
        $query = "SELECT id, alias, use_for_return_policy FROM `#__jshopping_config_statictext` ".$where." ORDER BY id";
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    public function getPrepareDataSave($input){
        $post = $input->post->getArray();
        $languages = JSFactory::getModel("languages")->getAllLanguages(1);
        foreach($languages as $lang){
            $post['text_'.$lang->language] = $input->get('text'.$lang->id, '', 'RAW');
        }
		if (!isset($post['use_for_return_policy'])){
			$post['use_for_return_policy'] = 0;
		}
        return $post;
    }
    
    public function save(array $post){
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeSaveConfigStaticPage', array(&$post));
        $statictext = JSFactory::getTable("statictext");
        if (isset($post['f-id'])){
            $post['id'] = $post['f-id'];
            unset($post['f-id']);
        }
        $statictext->load((int)$post['id']);
        $statictext->bind($post);        
        if (!$statictext->store()){
            print $statictext->getError(); die();
        }
        $dispatcher->triggerEvent('onAfterSaveConfigStaticPage', array(&$statictext));
        return $statictext;
    }
    
    public function delete($id){
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDeleteConfigStaticPage', array(&$id));
        $statictext = JSFactory::getTable("statictext");
        $statictext->load($id);
        $statictext->delete();
        $dispatcher->triggerEvent('onAfterDeleteConfigStaticPage', array(&$id));
    }
    
    public function useReturnPolicy(array $cid, $flag){
        $db = Factory::getDBO();        
        foreach($cid as $value){
            $query = "UPDATE `#__jshopping_config_statictext` SET `use_for_return_policy` = '" . $db->escape($flag) . "' "
                    . "WHERE `id` = '" . $db->escape($value) . "'";
            $db->setQuery($query);
            $db->execute();
        }
    }
}