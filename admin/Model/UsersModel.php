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

class UsersModel extends BaseadminModel{
    
    protected $nameTable = 'usershop';

    function getAllUsers($limitstart, $limit, $text_search="", $order = null, $orderDir = null, $filter = array()) {
        $db = \JFactory::getDBO();
        $where = "";
        $queryorder = "";
        if ($text_search){
            $search = $db->escape($text_search);
            $where .= " and (U.u_name like '%".$search."%' or U.f_name like '%".$search."%' or U.l_name like '%".$search."%' or U.email like '%".$search."%' or U.firma_name like '%".$search."%'  or U.d_f_name like '%".$search."%'  or U.d_l_name like '%".$search."%'  or U.d_firma_name like '%".$search."%' or U.number='".$search."' or U.street like '%".$search."%' or U.city like '%".$search."%' or U.phone like '%".$search."%'or U.mobil_phone like '%".$search."%' or U.fax like '%".$search."%' or U.zip='".$search."') ";
        }
        if (isset($filter['usergroup_id']) && $filter['usergroup_id']){
            $where .= " and U.usergroup_id = ".(int)$filter['usergroup_id'];
        }
        if ($order && $orderDir){
            $queryorder = "order by ".$order." ".$orderDir;
        }
        $query = "SELECT U.number, U.u_name, U.f_name, U.l_name, U.firma_name, U.email, U.user_id, UM.block, UG.usergroup_name FROM `#__jshopping_users` AS U
                 INNER JOIN `#__users` AS UM ON U.user_id = UM.id
                 left join #__jshopping_usergroups as UG on UG.usergroup_id=U.usergroup_id
                 where 1 ".$where." ".$queryorder;
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query, $limitstart, $limit);
        return $db->loadObjectList();
    }

    function getCountAllUsers($text_search="", $filter = array()) {
        $db = \JFactory::getDBO(); 
        $where = "";
        if ($text_search){
            $search = $db->escape($text_search);
            $where .= " and (U.u_name like '%".$search."%' or U.f_name like '%".$search."%' or U.l_name like '%".$search."%' or U.email like '%".$search."%' or U.firma_name like '%".$search."%'  or U.d_f_name like '%".$search."%'  or U.d_l_name like '%".$search."%'  or U.d_firma_name like '%".$search."%' or U.number='".$search."' or U.street like '%".$search."%' or U.city like '%".$search."%' or U.phone like '%".$search."%'or U.mobil_phone like '%".$search."%' or U.fax like '%".$search."%' or U.zip='".$search."') ";
        }
        if (isset($filter['usergroup_id']) && $filter['usergroup_id']){
            $where .= " and U.usergroup_id = ".(int)$filter['usergroup_id'];
        }
        $query = "SELECT COUNT(U.user_id) FROM `#__jshopping_users` AS U
                 INNER JOIN `#__users` AS UM ON U.user_id = UM.id where 1 ".$where;
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadResult();
    }

    function getUsers(){
        $db = \JFactory::getDBO();
        $query = "SELECT U.`user_id`, IF (concat(U.`f_name`,U.`l_name`)='', U.firma_name, concat(U.`f_name`,' ',U.`l_name`)) as `name`
                  FROM `#__jshopping_users` as U INNER JOIN `#__users` AS UM ON U.user_id=UM.id
                  ORDER BY name";
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    public function save(array $post){
        \JSFactory::loadLanguageFile();        
        $dispatcher = \JFactory::getApplication();        
		$dispatcher->triggerEvent('onBeforeSaveUser', array(&$post));
        $user_id = $post['user_id'];
        
		if ($user_id){
			$model = \JSFactory::getModel('useredit', 'Site');
			$model->setUserId($user_id);
		}else{
			$model = \JSFactory::getModel('userregister', 'Site');
		}
		$model->setAdminRegistration(1);
		$model->setData($post);
		if (!$model->check('editaccountadmin.edituser')){
			$this->setError($model->getError());
			return 0;
		}        
		if (!$model->save()){
			$this->setError($model->getError());
			return 0;
		}
		
		$user_shop = $model->getUser();
		$user = $model->getUserJoomla();
		        
        $dispatcher->triggerEvent('onAfterSaveUser', array(&$user, &$user_shop));
        return $user_shop;
    }
    
    public function deleteList(array $cid, $msg = 1){
        $app = \JFactory::getApplication();
        $me = \JFactory::getUser();        
        $dispatcher = \JFactory::getApplication();
        $res = array();
        if (\JFactory::getUser()->authorise('core.admin', 'com_jshopping')){
            $dispatcher->triggerEvent('onBeforeRemoveUser', array(&$cid));
            foreach($cid as $id){
                if ($me->get('id')==(int)$id){
                    if ($msg){
                        $app->enqueueMessage(\JText::_('You cannot delete Yourself!'), 'error');
                    }                    
                    $res[$id] = false;
                    continue;
                }
                $user = \JUser::getInstance((int)$id);
                $user->delete();
                $app->logout((int)$id);
                $user_shop = \JSFactory::getTable('userShop');
                $user_shop->delete((int)$id);
                $res[$id] = true;
            }
            $dispatcher->triggerEvent('onAfterRemoveUser', array(&$cid));
        }
        return $res;
    }
    
    public function publish(array $cid, $flag){
        $block = (int)!$flag;
        $db = \JFactory::getDBO();
        $dispatcher = \JFactory::getApplication();        
        $dispatcher->triggerEvent('onBeforePublishUser', array(&$cid, &$block));
        foreach($cid as $id){
            $query = "UPDATE `#__users` SET `block`=".(int)$block." "
                . "WHERE `id` = ".(int)$id;
            $db->setQuery($query);
            $db->execute();
        }
        $dispatcher->triggerEvent('onAfterPublishUser', array(&$cid, &$block));
    }
}