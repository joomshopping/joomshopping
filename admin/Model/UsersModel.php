<?php
/**
* @version      5.0.0 15.09.2018
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
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserHelper;
use Joomla\CMS\User\UserFactoryInterface;
defined('_JEXEC') or die();

class UsersModel extends BaseadminModel{
    
    protected $nameTable = 'usershop';

	public function getListItems(array $filters = [], array $orderBy = [], array $limit = [], array $params = []){
		return $this->getAllUsers($limit['limitstart'] ?? null, $limit['limit'] ?? null, $filters['text_search'] ?? '', $orderBy['order'] ?? null, $orderBy['dir'] ?? null, $filters);
	}
	public function getCountItems(array $filters = [], array $params = []) {
		return $this->getCountAllUsers($filters['text_search'] ?? '', $filters);
	}

    function getAllUsers($limitstart, $limit, $text_search="", $order = null, $orderDir = null, $filter = array()) {
        $db = Factory::getDBO();
        $where = "";
        $queryorder = "";
        if ($text_search){
            $search = $db->escape($text_search);
            $where .= " and (U.u_name like '%".$search."%' or U.f_name like '%".$search."%' or U.l_name like '%".$search."%' or U.email like '%".$search."%' or U.firma_name like '%".$search."%'  or U.d_f_name like '%".$search."%'  or U.d_l_name like '%".$search."%'  or U.d_firma_name like '%".$search."%' or U.number='".$search."' or U.street like '%".$search."%' or U.city like '%".$search."%' or U.phone like '%".$search."%'or U.mobil_phone like '%".$search."%' or U.fax like '%".$search."%' or U.zip='".$search."') ";
        }
        if (isset($filter['usergroup_id']) && $filter['usergroup_id']){
            $where .= " and U.usergroup_id = ".(int)$filter['usergroup_id'];
        }
        if (isset($filter['user_enable'])){
            if ($filter['user_enable'] == 1) {
                $where .= " and UM.block = 0";
            }
            if ($filter['user_enable'] == 2) {
                $where .= " and UM.block = 1";
            }
        }        
        if ($order && $orderDir){
            $queryorder = "order by ".$order." ".$orderDir;
        }
        $query = "SELECT U.number, U.u_name, U.f_name, U.l_name, U.firma_name, U.email, U.user_id, UM.block, UG.usergroup_name FROM `#__jshopping_users` AS U
                 INNER JOIN `#__users` AS UM ON U.user_id = UM.id
                 left join #__jshopping_usergroups as UG on UG.usergroup_id=U.usergroup_id
                 where 1 ".$where." ".$queryorder;
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query, $limitstart, $limit);
        return $db->loadObjectList();
    }

    function getCountAllUsers($text_search="", $filter = array()) {
        $db = Factory::getDBO(); 
        $where = "";
        if ($text_search){
            $search = $db->escape($text_search);
            $where .= " and (U.u_name like '%".$search."%' or U.f_name like '%".$search."%' or U.l_name like '%".$search."%' or U.email like '%".$search."%' or U.firma_name like '%".$search."%'  or U.d_f_name like '%".$search."%'  or U.d_l_name like '%".$search."%'  or U.d_firma_name like '%".$search."%' or U.number='".$search."' or U.street like '%".$search."%' or U.city like '%".$search."%' or U.phone like '%".$search."%'or U.mobil_phone like '%".$search."%' or U.fax like '%".$search."%' or U.zip='".$search."') ";
        }
        if (isset($filter['usergroup_id']) && $filter['usergroup_id']){
            $where .= " and U.usergroup_id = ".(int)$filter['usergroup_id'];
        }
        if (isset($filter['user_enable'])){
            if ($filter['user_enable'] == 1) {
                $where .= " and UM.block = 0";
            }
            if ($filter['user_enable'] == 2) {
                $where .= " and UM.block = 1";
            }
        }
        $query = "SELECT COUNT(U.user_id) FROM `#__jshopping_users` AS U
                 INNER JOIN `#__users` AS UM ON U.user_id = UM.id where 1 ".$where;
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadResult();
    }

    function getUsers(){
        $db = Factory::getDBO();
        $query = "SELECT U.`user_id`, IF (concat(U.`f_name`,U.`l_name`)='', U.firma_name, concat(U.`f_name`,' ',U.`l_name`)) as `name`
                  FROM `#__jshopping_users` as U INNER JOIN `#__users` AS UM ON U.user_id=UM.id
                  ORDER BY name";
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    public function save(array $post){
        JSFactory::loadLanguageFile();        
        $dispatcher = Factory::getApplication();        
		$dispatcher->triggerEvent('onBeforeSaveUser', array(&$post));
        $user_id = $post['user_id'];
        if ($user_id && !empty($post['block']) && $user_id == Factory::getApplication()->getIdentity()->id){
            $this->setError(Text::_('JSHOP_ERROR_CANNOT_BLOCK_SELF'));
            return 0;
        }

		if ($user_id){
			$model = JSFactory::getModel('useredit', 'Site');
			$model->setUserId($user_id);
		}else{
			$model = JSFactory::getModel('userregister', 'Site');
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
		if ($user_id && $user->block){
			UserHelper::destroyUserSessions($user->id);
		}

        $dispatcher->triggerEvent('onAfterSaveUser', array(&$user, &$user_shop));
        return $user_shop;
    }
    
    public function deleteList(array $cid, $msg = 1){
        $app = Factory::getApplication();
        $me = Factory::getUser();        
        $dispatcher = Factory::getApplication();
        $res = array();
        if (Factory::getUser()->authorise('core.admin', 'com_jshopping')){
            $dispatcher->triggerEvent('onBeforeRemoveUser', array(&$cid));
            foreach($cid as $id){
                if ($me->get('id')==(int)$id){
                    if ($msg){
                        $app->enqueueMessage(Text::_('You cannot delete Yourself!'), 'error');
                    }                    
                    $res[$id] = false;
                    continue;
                }
                $user = User::getInstance((int)$id);
                try {
                    $deleted = $user->delete();
                    $error = $user->getError();
                } catch (\Exception $e) {
                    $deleted = false;
                    $error = $e->getMessage();
                }
                if (!$deleted) {
                    if ($msg && $error) {
                        $app->enqueueMessage($error, 'error');
                    }
                    $res[$id] = false;
                    continue;
                }
                $app->logout((int)$id);
                $user_shop = JSFactory::getTable('userShop');
                $user_shop->delete((int)$id);
                $res[$id] = true;
            }
            $dispatcher->triggerEvent('onAfterRemoveUser', array(&$cid));
        }
        return $res;
    }
    
    public function publish(array $cid, $flag){
        $block = (int)!$flag;
        $app = Factory::getApplication();
        $me = $app->getIdentity();
        $userFactory = Factory::getContainer()->get(UserFactoryInterface::class);
        $app->triggerEvent('onBeforePublishUser', array(&$cid, &$block));
        foreach($cid as $i => $id){
            $user = $userFactory->loadUserById((int)$id);
            if (!$user->id){
                unset($cid[$i]);
                continue;
            }
            if ($block && $user->id == $me->id){
                $app->enqueueMessage(Text::_('JSHOP_ERROR_CANNOT_BLOCK_SELF'), 'error');
                unset($cid[$i]);
                continue;
            }
            $user->block = $block;
            if (!$user->save(true)){
                if ($user->getError()){
                    $app->enqueueMessage($user->getError(), 'error');
                }
                unset($cid[$i]);
                continue;
            }
            if ($block){
                UserHelper::destroyUserSessions($user->id);
            }
        }
        $cid = array_values($cid);
        $app->triggerEvent('onAfterPublishUser', array(&$cid, &$block));
    }
}