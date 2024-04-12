<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Model;

defined('_JEXEC') or die;

class ShippingsPricesModel extends BaseadminModel{
    
    protected $nameTable = 'shippingMethodPrice';

    function save(array $post){
        $dispatcher = \JFactory::getApplication();
        $shippings = \JSFactory::getModel('shippings');
		$shipping_pr = \JSFactory::getTable('shippingMethodPrice');
        $post['shipping_stand_price'] = \JSHelper::saveAsPrice($post['shipping_stand_price']);
        $post['package_stand_price'] = \JSHelper::saveAsPrice($post['package_stand_price']);
        $dispatcher->triggerEvent('onBeforeSaveShippingPrice', array(&$post));
        $countries = $post['shipping_countries_id'];
		$shipping_pr->bind($post);
        if( isset($post['sm_params']) ) {
            $shipping_pr->setParams($post['sm_params']);
        } else {
            $shipping_pr->setParams(array());
		}
		if (!$shipping_pr->store() ) {
            $this->setError(\JText::_('JSHOP_ERROR_SAVE_DATABASE').' '.$shipping_pr->getError());
			return 0;
		}
		$shippings->savePrices($shipping_pr->sh_pr_method_id, $post);
		$shippings->saveCountries($shipping_pr->sh_pr_method_id, $countries);
        $dispatcher->triggerEvent('onAfterSaveShippingPrice', array(&$shipping_pr));
        return $shipping_pr;
    }

    function deleteList(array $cid, $msg = 1){
        $app = \JFactory::getApplication();
		$db = \JFactory::getDBO();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeRemoveShippingPrice', array(&$cid));
		foreach($cid as $value) {
			$query = "DELETE FROM `#__jshopping_shipping_method_price`
					  WHERE `sh_pr_method_id` = '" . $db->escape($value) . "'";
			$db->setQuery($query);
			if( $db->execute() ) {
				$query = "DELETE FROM `#__jshopping_shipping_method_price_weight`
						  WHERE `sh_pr_method_id` = '" . $db->escape($value) . "'";
				$db->setQuery($query);
				$db->execute();
				$query = "DELETE FROM `#__jshopping_shipping_method_price_countries`
						  WHERE `sh_pr_method_id` = '" . $db->escape($value) . "'";
				$db->setQuery($query);
				$db->execute();
                
                if ($msg){
                    $app->enqueueMessage(\JText::_('JSHOP_SHIPPING_DELETED'), 'message');
                }
			}
		}
        $dispatcher->triggerEvent('onAfterRemoveShippingPrice', array(&$cid));
    }

}