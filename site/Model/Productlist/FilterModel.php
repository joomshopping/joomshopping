<?php
/**
* @version      5.6.2 19.10.2022
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model\Productlist;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\CMS\Plugin\PluginHelper;
defined('_JEXEC') or die();

class FilterModel{

    function getFilter($contextfilter, $no_filter = array()){
        $app = Factory::getApplication();
        $input = $app->input;
        $jshopConfig = JSFactory::getConfig();

        $category_id = $input->getInt('category_id');
        $manufacturer_id = $input->getInt('manufacturer_id');
        $label_id = $input->getInt('label_id');
        $vendor_id = $input->getInt('vendor_id');
        $price_from = Helper::saveAsPrice($input->getVar('price_from'));
        $price_to = Helper::saveAsPrice($input->getVar('price_to'));

        $categorys = $app->getUserStateFromRequest($contextfilter.'categorys', 'categorys', array());
        $categorys = Helper::filterAllowValue($categorys, "int+");
        $tmpcd = Helper::getListFromStr($input->getVar('category_id'));
        if (is_array($tmpcd) && !$categorys) $categorys = $tmpcd;

        $manufacturers = $app->getUserStateFromRequest($contextfilter.'manufacturers', 'manufacturers', array());
        $manufacturers = Helper::filterAllowValue($manufacturers, "int+");
        $tmp = Helper::getListFromStr($input->getVar('manufacturer_id'));
        if (is_array($tmp) && !$manufacturers) $manufacturers = $tmp;

        $labels = $app->getUserStateFromRequest($contextfilter.'labels', 'labels', array());
        $labels = Helper::filterAllowValue($labels, "int+");
        $tmplb = Helper::getListFromStr($input->getVar('label_id'));
        if (is_array($tmplb) && !$labels) $labels = $tmplb;

        $vendors = $app->getUserStateFromRequest($contextfilter.'vendors', 'vendors', array());
        $vendors = Helper::filterAllowValue($vendors, "int+");
        $tmp = Helper::getListFromStr($input->getVar('vendor_id'));
        if (is_array($tmp) && !$vendors) $vendors = $tmp;

        if ($jshopConfig->admin_show_product_extra_field){
            $extra_fields = $app->getUserStateFromRequest($contextfilter.'extra_fields', 'extra_fields', array());
            $extra_fields = Helper::filterAllowValue($extra_fields, "array_int_k_v+");
            $extra_fields_t = $app->getUserStateFromRequest($contextfilter.'extra_fields_t', 'extra_fields_t', array());
            $extra_fields_t = Helper::filterAllowValue($extra_fields_t, "array_int_k_v_not_empty");
        }
        $fprice_from = $app->getUserStateFromRequest($contextfilter.'fprice_from', 'fprice_from');
        $fprice_from = Helper::saveAsPrice($fprice_from);
        if (!$fprice_from) $fprice_from = $price_from;
        $fprice_to = $app->getUserStateFromRequest($contextfilter.'fprice_to', 'fprice_to');
        $fprice_to = Helper::saveAsPrice($fprice_to);
        if (!$fprice_to) $fprice_to = $price_to;

        $filters = array();
        $filters['categorys'] = $categorys;
        $filters['manufacturers'] = $manufacturers;
        $filters['price_from'] = $fprice_from;
        $filters['price_to'] = $fprice_to;
        $filters['labels'] = $labels;
        $filters['vendors'] = $vendors;
        if ($jshopConfig->admin_show_product_extra_field){
            $filters['extra_fields'] = $extra_fields;
            $filters['extra_fields_t'] = $extra_fields_t;
        }
        if ($category_id && !$filters['categorys']){
            $filters['categorys'][] = $category_id;
        }
        if ($manufacturer_id && !$filters['manufacturers']){
            $filters['manufacturers'][] = $manufacturer_id;
        }
        if ($label_id && !$filters['labels']){
            $filters['labels'][] = $label_id;
        }
        if ($vendor_id && !$filters['vendors']){
            $filters['vendors'][] = $vendor_id;
        }
        if (is_array($filters['vendors'])){
            $main_vendor = JSFactory::getMainVendor();
            foreach($filters['vendors'] as $vid){
                if ($vid == $main_vendor->id){
                    $filters['vendors'][] = 0;
                }
            }
        }
        foreach($no_filter as $filterkey){
            unset($filters[$filterkey]);
        }
        PluginHelper::importPlugin('jshoppingproducts');
        $app->triggerEvent('onAfterGetBuildFilterListProduct', array(&$filters, &$no_filter, &$contextfilter));
    return $filters;
    }

    public function willBeUseFilter($filters, $standartFilterListProduct){
        $res = 0;
        foreach($filters as $k=>$v){
            if (in_array($k, $standartFilterListProduct)){
                continue;
            }
            if (is_numeric($v) && $v>0){
                $res = 1;
            }
            if (is_string($v) && $v!=''){
                $res = 1;
            }
			if (is_array($v) && count($v)>0){
                $res = 1;
            }
        }
        Factory::getApplication()->triggerEvent('onAfterWillBeUseFilterFunc', array(&$filters, &$res));
    return $res;
    }
}