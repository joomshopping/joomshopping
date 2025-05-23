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

defined('_JEXEC') or die;

class ShippingExtPriceModel extends BaseadminModel{
    
    protected $nameTable = 'shippingext';

	public function getListItems(array $filters = [], array $orderBy = [], array $limit = [], array $params = []){
		return $this->getList($filters['active'] ?? 0);
	}
    
    function getList($active = 0){
        $db = Factory::getDBO();
        $adv_query = "";
        if ($active==1){
            $adv_query = "where `published`='1'";
        }
        $query = "select * from `#__jshopping_shipping_ext_calc` ".$adv_query." order by `ordering`";
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function save(array $post){
        $shippingext = JSFactory::getTable('shippingExt');
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeSaveShippingExtCalc', array(&$post));
        $shippingext->bind($post);
        $shippingext->setShippingMethod($post['shipping']);
        $shippingext->setParams($post['params']);
        if (!$shippingext->store()){
            $this->setError(Text::_('JSHOP_ERROR_SAVE_DATABASE'));
            return 0;
        }
        $dispatcher->triggerEvent('onAfterSaveShippingExtCalc', array(&$shippingext));
        return $shippingext;
    }

    public function delete(&$id) {
        $dispatcher = Factory::getApplication();
        $shippingext = JSFactory::getTable('shippingExt');
        $dispatcher->triggerEvent('onBeforeRemoveShippingExtPrice', array(&$id));
        $shippingext->delete($id);
        $dispatcher->triggerEvent('onAfterRemoveShippingExtPrice', array(&$id));
    }
    
    public function publish(array $cid, $flag){
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforePublishShippingExtPrice', array(&$cid, &$flag));
        $obj = JSFactory::getTable('shippingExt');
        $obj->publish($cid, $flag);
        $dispatcher->triggerEvent('onAfterPublishShippingExtPrice', array(&$cid, &$flag));
    }

}