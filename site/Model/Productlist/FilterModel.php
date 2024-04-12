<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Model\Productlist;
defined('_JEXEC') or die();

class FilterModel{

    function getFilter($contextfilter, $no_filter = array()){
        $app = \JFactory::getApplication();
        $input = $app->input;
        $jshopConfig = \JSFactory::getConfig();

        $category_id = $input->getInt('category_id');
        $manufacturer_id = $input->getInt('manufacturer_id');
        $label_id = $input->getInt('label_id');
        $vendor_id = $input->getInt('vendor_id');
        $price_from = \JSHelper::saveAsPrice($input->getVar('price_from'));
        $price_to = \JSHelper::saveAsPrice($input->getVar('price_to'));

        $categorys = $app->getUserStateFromRequest($contextfilter.'categorys', 'categorys', array());
        $categorys = \JSHelper::filterAllowValue($categorys, "int+");
        $tmpcd = \JSHelper::getListFromStr($input->getVar('category_id'));
        if (is_array($tmpcd) && !$categorys) $categorys = $tmpcd;

        $manufacturers = $app->getUserStateFromRequest($contextfilter.'manufacturers', 'manufacturers', array());
        $manufacturers = \JSHelper::filterAllowValue($manufacturers, "int+");
        $tmp = \JSHelper::getListFromStr($input->getVar('manufacturer_id'));
        if (is_array($tmp) && !$manufacturers) $manufacturers = $tmp;

        $labels = $app->getUserStateFromRequest($contextfilter.'labels', 'labels', array());
        $labels = \JSHelper::filterAllowValue($labels, "int+");
        $tmplb = \JSHelper::getListFromStr($input->getVar('label_id'));
        if (is_array($tmplb) && !$labels) $labels = $tmplb;

        $vendors = $app->getUserStateFromRequest($contextfilter.'vendors', 'vendors', array());
        $vendors = \JSHelper::filterAllowValue($vendors, "int+");
        $tmp = \JSHelper::getListFromStr($input->getVar('vendor_id'));
        if (is_array($tmp) && !$vendors) $vendors = $tmp;

        if ($jshopConfig->admin_show_product_extra_field){
            $extra_fields = $app->getUserStateFromRequest($contextfilter.'extra_fields', 'extra_fields', array());
            $extra_fields = \JSHelper::filterAllowValue($extra_fields, "array_int_k_v+");
            $extra_fields_t = $app->getUserStateFromRequest($contextfilter.'extra_fields_t', 'extra_fields_t', array());
            $extra_fields_t = \JSHelper::filterAllowValue($extra_fields_t, "array_int_k_v_not_empty");
        }
        $fprice_from = $app->getUserStateFromRequest($contextfilter.'fprice_from', 'fprice_from');
        $fprice_from = \JSHelper::saveAsPrice($fprice_from);
        if (!$fprice_from) $fprice_from = $price_from;
        $fprice_to = $app->getUserStateFromRequest($contextfilter.'fprice_to', 'fprice_to');
        $fprice_to = \JSHelper::saveAsPrice($fprice_to);
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
            $main_vendor = \JSFactory::getMainVendor();
            foreach($filters['vendors'] as $vid){
                if ($vid == $main_vendor->id){
                    $filters['vendors'][] = 0;
                }
            }
        }
        foreach($no_filter as $filterkey){
            unset($filters[$filterkey]);
        }
        \JPluginHelper::importPlugin('jshoppingproducts');
        $app->triggerEvent('afterGetBuildFilterListProduct', array(&$filters));
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
        }
        \JFactory::getApplication()->triggerEvent('onAfterWillBeUseFilterFunc', array(&$filters, &$res));
    return $res;
    }
}