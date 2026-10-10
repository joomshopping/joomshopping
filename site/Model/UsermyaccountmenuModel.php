<?php
/**
* @version      5.10.0 10.10.2026
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
defined('_JEXEC') or die();

class UsermyaccountmenuModel{

    public function getItems($active = ''){
        $jshopConfig = JSFactory::getConfig();
        $items = array();
        $items['myaccount'] = array(
            'href' => Helper::SEFLink('index.php?option=com_jshopping&controller=user&task=myaccount', 1, 0, $jshopConfig->use_ssl),
            'title' => Text::_('JSHOP_MY_ACCOUNT'),
            'icon' => 'fa-user'
        );
        $items['orders'] = array(
            'href' => Helper::SEFLink('index.php?option=com_jshopping&controller=user&task=orders', 1, 0, $jshopConfig->use_ssl),
            'title' => Text::_('JSHOP_MY_ORDERS'),
            'icon' => 'fa-box-open'
        );
        $items['editaccount'] = array(
            'href' => Helper::SEFLink('index.php?option=com_jshopping&controller=user&task=editaccount', 1, 0, $jshopConfig->use_ssl),
            'title' => Text::_('JSHOP_EDIT_DATA'),
            'icon' => 'fa-user-edit'
        );
        if ($jshopConfig->display_user_groups_info){
            $items['groupsinfo'] = array(
                'href' => Helper::SEFLink('index.php?option=com_jshopping&controller=user&task=groupsinfo', 1),
                'title' => Text::_('JSHOP_USER_GROUPS_INFO'),
                'icon' => 'fa-percent'
            );
        }
        Factory::getApplication()->triggerEvent('onBeforeDisplayMyAccountMenu', array(&$items, &$active));
        $items['logout'] = array(
            'href' => Helper::SEFLink('index.php?option=com_jshopping&controller=user&task=logout', 1),
            'title' => Text::_('JSHOP_LOGOUT'),
            'icon' => 'fa-sign-out-alt'
        );
        foreach($items as $key => $item){
            if (!isset($item['active'])){
                $items[$key]['active'] = ($key == $active);
            }
            if (!isset($item['icon'])){
                $items[$key]['icon'] = '';
            }
        }
        Factory::getApplication()->triggerEvent('onAfterGetMyAccountMenu', array(&$items, &$active));
        return $items;
    }
}
