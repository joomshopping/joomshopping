<?php
/**
* @version      5.6.1 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Model; 
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Language\Text;
defined('_JEXEC') or die;

class UsergroupsModel extends BaseadminModel{
    
    protected $nameTable = 'usergroup';

	public function getListItems(array $filters = [], array $orderBy = [], array $limit = [], array $params = []){
		return $this->getAllUsergroups($orderBy['order'] ?? null, $orderBy['dir'] ?? null, $filters);
	}

    function getAllUsergroups($order = null, $orderDir = null, $filter = []){
        $ordering = "usergroup_id";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $db = Factory::getDBO();
        $lang = JSFactory::getLang();
        $where = '';
        if (isset($filter['text_search'])) {
            $where .= " AND (`".$lang->get("name")."` LIKE ".$db->q('%'.$filter['text_search'].'%').")";
        }
        $query = "SELECT * FROM `#__jshopping_usergroups` WHERE 1 ".$where." ORDER BY ".$ordering;
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    function resetDefaultUsergroup(){
        $db = Factory::getDBO();
        $query = "SELECT `usergroup_id` FROM `#__jshopping_usergroups` WHERE `usergroup_is_default`= '1'";
        $db->setQuery($query);
        $usergroup_default = $db->loadResult();
        $query = "UPDATE `#__jshopping_usergroups` SET `usergroup_is_default` = '0'";
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        $db->execute();
    }

    function setDefaultUsergroup($usergroup_id){
        $db = Factory::getDBO();
        $query = "UPDATE `#__jshopping_usergroups` SET `usergroup_is_default` = '1' WHERE `usergroup_id`= '".$db->escape($usergroup_id)."'";
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        $db->execute();
    }

    function getDefaultUsergroup(){
        $db = Factory::getDBO();
        $query = "SELECT `usergroup_id` FROM `#__jshopping_usergroups` WHERE `usergroup_is_default`= '1'";
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadResult();
    }

    public function getPrepareDataSave($input){
        $post = $input->post->getArray();
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $lang = JSFactory::getLang();
        foreach($languages as $v){
            $post['name_' . $v->language] = trim($post['name_' . $v->language]);
            $post['description_' . $v->language] = $input->get('description' . $v->id, '', 'RAW');
        }
        $post['usergroup_name'] = $post[$lang->get("name")];
        $post['usergroup_description'] = $post[$lang->get("description")];
        return $post;
    }

    function save(array $post){
		$usergroup = JSFactory::getTable('userGroup');
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeSaveUserGroup', array(&$post));
        $post['usergroup_discount'] = Helper::saveAsPrice($post['usergroup_discount']);
		$usergroup->bind($post);
		if ($usergroup->usergroup_is_default){
			$default_usergroup_id = $this->resetDefaultUsergroup();
		}
		if (!$usergroup->store()){
			$this->setDefaultUsergroup($default_usergroup_id);
            $this->setError(Text::_('JSHOP_ERROR_SAVE_DATABASE').' '.$usergroup->getError());
            return 0;
		}
        $dispatcher->triggerEvent('onAfterSaveUserGroup', array(&$usergroup));
        return $usergroup;
    }

    function deleteList(array $cid, $msg = 1){
		$db = Factory::getDBO();
        $app = Factory::getApplication();
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveUserGroup', array(&$cid));
		$res = array();
		foreach($cid as $id){
			$query = "SELECT `usergroup_name` FROM `#__jshopping_usergroups` WHERE `usergroup_id` = ".(int)$id;
			$db->setQuery($query);
			$usergroup_name = $db->loadResult();
            
			$query = "DELETE FROM `#__jshopping_usergroups` WHERE `usergroup_id` = ".(int)$id;
			$db->setQuery($query);
			$db->execute();
            
            if ($msg){
                $app->enqueueMessage(sprintf(Text::_('JSHOP_USERGROUP_DELETED'), $usergroup_name), 'message');
            }
            $res[$id] = true;
		}
        $dispatcher->triggerEvent('onAfterRemoveUserGroup', array(&$cid));
        return $res;
    }

}