<?php
/**
* @version      5.6.3 02.05.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Model;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

class OrdersModel extends BaseadminModel{

	public function getListItems(array $filters = [], array $orderBy = [], array $limit = [], array $params = []){
		return $this->getAllOrders($limit['limitstart'] ?? null, $limit['limit'] ?? null, $filters, $orderBy['order'] ?? null, $orderBy['dir'] ?? null);
	}

	public function getCountItems(array $filters = [], array $params = []) {
		return $this->getCountAllOrders($filters);
	}

    function getCountAllOrders($filters) {
        $db = Factory::getDBO();
        $where = $this->_getAllOrdersQueryForFilter($filters);
        if (isset($filters['vendor_id']) && $filters['vendor_id']){
            $query = "SELECT COUNT(distinct O.order_id) FROM `#__jshopping_orders` as O
                  left join `#__jshopping_order_item` as OI on OI.order_id=O.order_id
                  where 1 $where ORDER BY O.order_id DESC";
        }else{
            $query = "SELECT COUNT(O.order_id) FROM `#__jshopping_orders` as O where 1 ".$where;
        }
        $app = Factory::getApplication();
        $app->triggerEvent('onBeforeQueryGetCountAllOrders', array(&$query, &$filters));
        $db->setQuery($query);
        return $db->loadResult();
    }

    function getAllOrders($limitstart, $limit, $filters, $filter_order, $filter_order_Dir) {
        $db = Factory::getDBO(); 
        $where = $this->_getAllOrdersQueryForFilter($filters);
        $order = $filter_order." ".$filter_order_Dir;
        if (isset($filters['vendor_id']) && $filters['vendor_id']){
            $query = "SELECT distinct O.* FROM `#__jshopping_orders` as O
                  left join `#__jshopping_order_item` as OI on OI.order_id=O.order_id
                  where 1 $where ORDER BY ".$order;
        }else{
            $query = "SELECT O.*, V.l_name as v_name, V.f_name as v_fname, concat(O.f_name,' ',O.l_name) as name FROM `#__jshopping_orders` as O
                  left join `#__jshopping_vendors` as V on V.id=O.vendor_id
                  where 1 $where ORDER BY ".$order;
        }
        $app = Factory::getApplication();
        $app->triggerEvent('onBeforeQueryGetAllOrders', array(&$query, &$filters, &$filter_order, &$filter_order_Dir));
		$db->setQuery($query, $limitstart, $limit);
        return $db->loadObjectList();
    }

    function _getAllOrdersQueryForFilter($filters){
        $jshopConfig = JSFactory::getConfig();
        $db = Factory::getDBO();
        $where = "";
        if ($filters['status_id']){
            $where .= " and O.order_status=".$db->q($filters['status_id']);
        }
        if ($filters['user_id']) $where .= " and O.user_id=".$db->q($filters['user_id']);
        if ($filters['text_search']){
            $search = $db->escape($filters['text_search']);
            $where .= " and (
            O.`order_number` like '%".$search."%' 
            or O.`f_name` like '%".$search."%' 
            or O.`l_name` like '%".$search."%' 
            or CONCAT(O.`f_name`, ' ', O.`l_name`) like '%".$search."%' 
            or O.`email` like '%".$search."%' 
            or O.`firma_name` like '%".$search."%' 
            or O.`d_f_name` like '%".$search."%' 
            or O.`d_l_name` like '%".$search."%'
            or CONCAT(O.`d_f_name`, ' ', O.`d_l_name`) like '%".$search."%' 
            or O.`d_firma_name` like '%".$search."%' 
            or O.order_add_info like '%".$search."%'
            ) ";
        }
        $filters['notfinished'] = $filters['notfinished'] ?? 0;
        if ($filters['notfinished'] == 2) $where .= "and O.order_created=0 ";
        if ($filters['notfinished'] < 1) $where .= "and O.order_created=1 ";
        if ($filters['date_from']){
			$date = Helper::getJsDateDB($filters['date_from'], $jshopConfig->store_date_format);
			$where .= ' and O.order_date>="'.$db->escape($date).'" ';
		}
		if ($filters['date_to']){
			$date = Helper::getJsDateDB($filters['date_to'], $jshopConfig->store_date_format);
			$where .= ' and O.order_date<="'.$db->escape($date).' 23:59:59" ';
		}
        if (isset($filters['payment_id']) && $filters['payment_id']){
		    $where .= " and O.payment_method_id=".$db->q($filters['payment_id']);
	    }
	    if (isset($filters['shipping_id']) && $filters['shipping_id']){
		    $where .= " and O.shipping_method_id=".$db->q($filters['shipping_id']);
	    }
        if (isset($filters['vendor_id']) && $filters['vendor_id']){
            $where .= " and OI.vendor_id=".$db->q($filters['vendor_id']);
        }
        return $where;
    }

    function getAllOrderStatus($order = null, $orderDir = null) {
        $db = Factory::getDBO(); 
        $lang = JSFactory::getLang();
        $ordering = "ordering, status_id";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT status_id, status_code, `".$lang->get('name')."` as name, ordering FROM `#__jshopping_order_status` ORDER BY ".$ordering;
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    function getMinYear(){
        $db = Factory::getDBO();
        $query = "SELECT min(order_date) FROM `#__jshopping_orders`";
        $db->setQuery($query);
        $res = substr($db->loadResult(),0, 4);
        if (intval($res)==0) $res = "2010";
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        return $res;
    }
    
    function saveOrderItem($order_id, $post, $old_items){
        $db = Factory::getDBO();
		$jshopConfig = JSFactory::getConfig();
		$dispatcher = Factory::getApplication();
        if (!isset($post['product_name'])) $post['product_name'] = array();

        $edit_order_items = array();
        foreach($post['product_name'] as $k=>$v){
            $order_item_id = intval($post['order_item_id'][$k]);
            $edit_order_items[] = $order_item_id;
            $product = JSFactory::getTable('product');
            $product->load($post['product_id'][$k]);
            $order_item = JSFactory::getTable('orderItem');
            $order_item->order_item_id = $order_item_id;
            $order_item->order_id = $order_id;
            $order_item->product_id = $post['product_id'][$k];
			$order_item->category_id = $post['category_id'][$k];
            $order_item->product_ean = $post['product_ean'][$k] ?? '';
            $order_item->manufacturer_code = $post['manufacturer_code'][$k] ?? '';
            $order_item->real_ean = $post['real_ean'][$k] ?? '';
            $order_item->product_name = $post['product_name'][$k];
            $order_item->product_quantity = Helper::saveAsPrice($post['product_quantity'][$k]);
            $order_item->product_item_price = Helper::saveAsPrice($post['product_item_price'][$k]);
            $order_item->product_tax = Helper::saveAsPrice($post['product_tax'][$k] ?? 0);
            $order_item->product_attributes = $post['product_attributes'][$k] ?? '';
            if (json_decode($post['attributes'][$k])){
                $attribute = (array)json_decode($post['attributes'][$k]);
                $order_item->attributes = serialize($attribute);
            }else{
                $order_item->attributes = $post['attributes'][$k];
            }
            $order_item->product_freeattributes = $post['product_freeattributes'][$k] ?? '';
            $order_item->weight = $post['weight'][$k];
            if (isset($post['delivery_times_id'][$k])){
                $order_item->delivery_times_id = $post['delivery_times_id'][$k];
            }else{
                $order_item->delivery_times_id = 0;
            }
            $order_item->vendor_id = $post['vendor_id'][$k];
            $order_item->thumb_image = $post['thumb_image'][$k];
            if (!$order_item_id){
                $order_item->files = serialize($product->getSaleFiles());
            }
			if ($jshopConfig->admin_show_product_extra_field && count($jshopConfig->getCartDisplayExtraFields())>0){
                $extra_fields = $product->getExtraFields(2);
				$order_item->extra_fields = '';
				foreach($extra_fields as $extra_field){
                    $order_item->extra_fields .= $extra_field['name'].': '.$extra_field['value']."\n";
                }
            }
			$dispatcher->triggerEvent('onBeforeSaveOrderItemAdmin', array(&$order_item, &$post, &$k, &$v));
            $order_item->store();
            unset($order_item);
        }

        foreach($old_items as $k=>$v){
            if (!in_array($v->order_item_id, $edit_order_items)){
                $order_item = JSFactory::getTable('orderItem');
                $order_item->delete($v->order_item_id);
            }
        }
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        return 1;
    }
    
    function getCartProductsFromOrderProducts($items){
        $products = array();
        foreach($items as $k=>$v){
            $prod = [];
            $prod['product_id'] = intval($v['product_id']);
            $prod['quantity'] = floatval($v['product_quantity']);
            $prod['tax'] = floatval($v['product_tax'] ?? 0);
            $prod['product_name'] = $v['product_name'] ?? '';
            $prod['thumb_image'] = $v['thumb_image'] ?? '';
            $prod['ean'] = $v['product_ean'] ?? '';
            $prod['weight'] = $v['weight'] ?? 0;
            $prod['delivery_times_id'] = $v['delivery_times_id'] ?? 0;
            $prod['vendor_id'] = $v['vendor_id'] ?? 0;
            $prod['price'] = floatval($v['product_item_price']);
            $products[] = $prod;
        }
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        return $products;
    }
    
    function loadtaxorder($data_order, $products){
        $jshopConfig = JSFactory::getConfig();
        $jshopConfig->display_price_front_current = $data_order['display_price'] ?? 0;
        $display_price_front_current = $data_order['display_price'] ?? 0;
        $taxes = array();
        $total = 0;
        $AllTaxes = JSFactory::getAllTaxes();
        $id_country = $data_order['d_country'] ?? 0;
        if (!$id_country){
            $id_country = $data_order['country'] ?? 0;
        }
        if (!$id_country){
            $id_country = $jshopConfig->default_country;
        }
        
        // tax product
        foreach($products as $key=>$product){
            $tax = (string)floatval($product['product_tax']);
            $price = floatval($product['product_item_price']) * floatval($product['product_quantity']);
            $SumTax = $taxes[$tax] ?? 0;
            $taxes[$tax] =  $SumTax + floatval(Helper::getPriceTaxValue($price, $tax, $display_price_front_current));
            $total += $price;
        }
        
        $cproducts = $this->getCartProductsFromOrderProducts($products);
        $cart = JSFactory::getModel('cart', 'Site');
        $cart->products = array();
        $cart->loadProductsFromArray($cproducts);
        $cart->loadPriceAndCountProducts();
        
        // payment
        if (floatval($data_order['order_payment'] ?? 0)!=0){
            $price = floatval($data_order['order_payment']);
            $payment_method_id = $data_order['payment_method_id'];
            $paym_method = JSFactory::getTable('paymentmethod');
            $paym_method->load($payment_method_id);
            $paym_method->setCart($cart);
            $payment_taxes = $paym_method->calculateTaxList($price);
            foreach($payment_taxes as $k=>$v){
                $k = (string)floatval($k);
                $SumTax = $taxes[$k] ?? 0;
                $taxes[$k] = $SumTax + floatval($v);
            }
            $total += $price;
        }
        
        //shipping
        $shipping_method = JSFactory::getTable('shippingMethod');
        $sh_pr_method_id = $shipping_method->getShippingPriceId($data_order['shipping_method_id'] ?? 0, $id_country);
        
        $shipping_method_price = JSFactory::getTable('shippingMethodPrice');
        $shipping_method_price->load($sh_pr_method_id);
        
        // tax shipping
        if (floatval($data_order['order_shipping'] ?? 0) > 0){
            $price = floatval($data_order['order_shipping']);
            $shipping_taxes = $shipping_method_price->calculateShippingTaxList($price, $cart);
            foreach($shipping_taxes as $k=>$v){
                $k = (string)floatval($k);
                $SumTax = $taxes[$k] ?? 0;
                $taxes[$k] = $SumTax + floatval($v);
            }
            $total += $price;
        }
        // tax package
        if (floatval($data_order['order_package'] ?? 0) > 0){
            $price = floatval($data_order['order_package']);
            $shipping_taxes = $shipping_method_price->calculatePackageTaxList($price, $cart);
            foreach($shipping_taxes as $k=>$v){
                $k = (string)floatval($k);
                $SumTax = $taxes[$k] ?? 0;
                $taxes[$k] = $SumTax + floatval($v);
            }
            $total += $price;
        }
        
        $taxes_array = array();
        foreach($taxes as $tax=>$value){
            if ($tax>0){
                $taxes_array[] = array('tax'=>$tax, 'value'=>$value);
            }
        }
        
        if (floatval($data_order['order_discount'] ?? 0) > 0 && $jshopConfig->calcule_tax_after_discount){
            $discountPercent = floatval($data_order['order_discount']) / $total;
            foreach($taxes_array as $k=>$v){
                $taxes_array[$k]['value'] = floatval($v['value']) * (1 - $discountPercent);
            }
        }

        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        return $taxes_array;
    }
    
    function loadshippingprice($data_order, $products){
        $jshopConfig = JSFactory::getConfig();
        $jshopConfig->display_price_front_current = $data_order['display_price'] ?? 0;
        $all_currency = JSFactory::getAllCurrency();
        $currency_id = $data_order['currency_id'] ?? 0;
        if ($currency_id){
            $jshopConfig->currency_value = $all_currency[$currency_id]->currency_value;
        }
        
        $id_country = $data_order['d_country'] ?? 0;
        if (!$id_country){
            $id_country = $data_order['country'] ?? 0;
        }
        if (!$id_country){
            $id_country = $jshopConfig->default_country;
        }
        $shipping_method = JSFactory::getTable('shippingMethod');
        $shipping_method_price = JSFactory::getTable('shippingMethodPrice');
        
        $shipping_price_method_id = $shipping_method->getShippingPriceId($data_order['shipping_method_id'], $id_country);
        if (!$shipping_price_method_id){
            return null;
        }
        
        $cproducts = $this->getCartProductsFromOrderProducts($products);
        $cart = JSFactory::getModel('cart', 'Site');
        $cart->products = array();
        $cart->loadProductsFromArray($cproducts);
        $cart->loadPriceAndCountProducts();
        
        $shipping_method_price->load($shipping_price_method_id);
        $prices = $shipping_method_price->calculateSum($cart);
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        foreach($prices as $k=>$v){
            $prices[$k] = Helper::getRoundPriceProduct($v);
        }
        return $prices; 
    }
    
    function loadpaymentprice($data_order, $products){
        $jshopConfig = JSFactory::getConfig();
        $jshopConfig->display_price_front_current = $data_order['display_price'] ?? 0;
        $all_currency = JSFactory::getAllCurrency();
        $currency_id = $data_order['currency_id'] ?? 0;
        if ($currency_id){
            $jshopConfig->currency_value = $all_currency[$currency_id]->currency_value;
        }
        $id_country = $data_order['d_country'] ?? 0;
        if (!$id_country){
            $id_country = $data_order['country'] ?? 0;
        }
        if (!$id_country){
            $id_country = $jshopConfig->default_country;
        }
        $AllTaxes = JSFactory::getAllTaxes();

        $cproducts = $this->getCartProductsFromOrderProducts($products);
        $cart = JSFactory::getModel('cart', 'Site');
        $cart->products = array();
        $cart->loadProductsFromArray($cproducts);
        $cart->loadPriceAndCountProducts();
        
        $paym_method = JSFactory::getTable('paymentmethod');
        $paym_method->load($data_order['payment_method_id']);
        if ($paym_method->price_type==2){
            $total = 0;
            foreach($products as $key=>$product){
                $tax = floatval($product['product_tax']);
                $product_price = floatval($product['product_item_price']) * floatval($product['product_quantity']);
                if ($data_order['display_price']){
                    $product_price = $product_price + $product_price * $tax / 100;    
                }
                $total += $product_price;
            }
            
            $shipping_method = JSFactory::getTable('shippingMethod');
            $sh_pr_method_id = $shipping_method->getShippingPriceId($data_order['shipping_method_id'], $id_country);            
            $shipping_method_price = JSFactory::getTable('shippingMethodPrice');
            $shipping_method_price->load($sh_pr_method_id);
            
            $tax = floatval($AllTaxes[$shipping_method_price->shipping_tax_id] ?? 0);
            $shipping_price = floatval($data_order['order_shipping'] ?? 0);
            if ($data_order['display_price']){
                $shipping_taxes = $shipping_method_price->calculateShippingTaxList($shipping_price, $cart);                
                foreach($shipping_taxes as $k=>$v){
                    $shipping_price = $shipping_price + $v;
                }
            }
            $total += $shipping_price;
            
            $tax = floatval($AllTaxes[$shipping_method_price->package_tax_id] ?? 0);
            $package_price = floatval($data_order['order_package'] ?? 0);
            if ($data_order['display_price']){
                $shipping_taxes = $shipping_method_price->calculatePackageTaxList($package_price, $cart);
                foreach($shipping_taxes as $k=>$v){
                    $package_price = $package_price + $v;   
                }
            }
            $total += $package_price;

            $price = $total * $paym_method->price / 100;
            if ($data_order['display_price'] ?? 0){
                $price = Helper::getPriceCalcParamsTax($price, $paym_method->tax_id, $cart->products);
            }
        }else{
            $price = $paym_method->price * $jshopConfig->currency_value; 
            $price = Helper::getPriceCalcParamsTax($price, $paym_method->tax_id, $cart->products);
        }
        extract(Helper::js_add_trigger(get_defined_vars(), "before"));
        return Helper::getRoundPriceProduct($price);
    }

    function loaddiscountprice($data_order, $products){
        $code = $data_order['coupon_code'] ?? '';
        if ($code == ''){
            return 0;
        }
        $coupon = JSFactory::getTable('coupon');
        $coupon_id = $coupon->getIdFromCode($code);
        if (!$coupon_id){
            return 0;
        }
        $coupon->load($coupon_id);

        $jshopConfig = JSFactory::getConfig();
        $jshopConfig->display_price_front_current = $data_order['display_price'] ?? 0;
        $all_currency = JSFactory::getAllCurrency();
        $currency_id = $data_order['currency_id'] ?? 0;
        if ($currency_id){
            $jshopConfig->currency_value = $all_currency[$currency_id]->currency_value;
        }
        $id_country = $data_order['d_country'] ?? 0;
        if (!$id_country){
            $id_country = $data_order['country'] ?? 0;
        }
        if (!$id_country){
            $id_country = $jshopConfig->default_country;
        }
        $data_order['order_shipping'] = $data_order['order_shipping'] ?? 0;
        $data_order['order_package'] = $data_order['order_package'] ?? 0;
        $data_order['order_payment'] = $data_order['order_payment'] ?? 0;
        $data_order['shipping_method_id'] = $data_order['shipping_method_id'] ?? 0;
        $data_order['payment_method_id'] = $data_order['payment_method_id'] ?? 0;

        $cproducts = $this->getCartProductsFromOrderProducts($products);
        $cart = JSFactory::getModel('cart', 'Site');
        $cart->products = array();
        $cart->loadProductsFromArray($cproducts);
        $cart->loadPriceAndCountProducts();

        $cart->setDisplayItem(1, 1);
        $cart->setShippingPrice($data_order['order_shipping']);
        $cart->setPackagePrice($data_order['order_package']);
        $cart->setPaymentPrice($data_order['order_payment']);
                
        $shipping_method = JSFactory::getTable('shippingMethod');
        $sh_pr_method_id = $shipping_method->getShippingPriceId($data_order['shipping_method_id'], $id_country);
        
        $shipping_method_price = JSFactory::getTable('shippingMethodPrice');
        $shipping_method_price->load($sh_pr_method_id);

        if ($data_order['order_shipping']>0){
            $price = $data_order['order_shipping'];
            $shipping_taxes = $shipping_method_price->calculateShippingTaxList($price, $cart);
            $cart->setShippingTaxList($shipping_taxes);
        }
        if ($data_order['order_package']>0){
            $price = $data_order['order_package'];
            $shipping_taxes = $shipping_method_price->calculatePackageTaxList($price, $cart);
            $cart->setPackageTaxList($shipping_taxes);
        }
        if ($data_order['order_payment']!=0){
            $price = $data_order['order_payment'];
            $payment_method_id = $data_order['payment_method_id'];
            $paym_method = JSFactory::getTable('paymentmethod');
            $paym_method->load($payment_method_id);
            $paym_method->setCart($cart);
            $cart->setPaymentTaxList($paym_method->calculateTaxList($price));
        }

        $cart->setRabatt($coupon->coupon_id, $coupon->coupon_type, $coupon->coupon_value);

        $price = $cart->getDiscountShow();
        $cart->clear();
        return $price;
    }
    
    public function deleteList(array $cid, $msg = 1){
        $app = Factory::getApplication();
        $dispatcher = Factory::getApplication();        
        $dispatcher->triggerEvent('onBeforeRemoveOrder', array(&$cid));
		$order = JSFactory::getTable('order');
        $res = array();
		foreach($cid as $id){
			$order->delete($id);
            $res[$id] = true;
		}
		$dispatcher->triggerEvent('onAfterRemoveOrder', array(&$cid));
        if ($msg){
            $msg = sprintf(Text::_('JSHOP_ORDER_DELETED_ID'), implode(", ", $cid));
            $app->enqueueMessage($msg, 'message');
        }
        return $res;
    }

    public function save(array $post){
        $app = Factory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $file_generete_pdf_order = $jshopConfig->file_generete_pdf_order;
        $dispatcher = Factory::getApplication();
        $order_id = intval($post['order_id']);
		$new_order = !$order_id;
        $order = JSFactory::getTable('order');
        $order->load($order_id);
        $olang = $post['lang'] ?? $order->getLang();
        JSFactory::loadLanguageFile($olang);
		$lang = JSFactory::getLang($olang);
        if (!$order_id){
            $order->user_id = -1;
            $order->order_date = Helper::getJsDate();
            $orderNumber = $jshopConfig->next_order_number;
            $jshopConfig->updateNextOrderNumber();
            $order->order_number = $order->formatOrderNumber($orderNumber);
            $order->order_hash = md5(time().$order->order_total.$order->user_id);
            $order->file_hash = md5(time().$order->order_total.$order->user_id."hashfile");
            $order->ip_address = $_SERVER['REMOTE_ADDR'];
            $order->order_status = $jshopConfig->default_status_order;
        }
        $order_created_prev = $order->order_created;
        if ($order_created_prev && $order->product_stock_removed){
            $update_product_stock = 1;
            $order->changeProductQTYinStock('+');
        }else{
            $update_product_stock = 0;
        }
        $order->order_m_date = Helper::getJsDate();
        $post['birthday'] = Helper::prepareDateBirthdayToSaveDb($post['birthday'] ?? null);
		$post['d_birthday'] = Helper::prepareDateBirthdayToSaveDb($post['d_birthday'] ?? null);
        $post['invoice_date'] = Helper::prepareDateToSaveDb($post['invoice_date'] ?? null);

        if (!$jshopConfig->hide_tax){
            $post['order_tax'] = 0;
            $order_tax_ext = array();
            if (isset($post['tax_percent'])){
                foreach($post['tax_percent'] as $k=>$v){
                    if ($post['tax_percent'][$k]!="" || $post['tax_value'][$k]!=""){
                        $order_tax_ext[number_format($post['tax_percent'][$k],2)] = Helper::saveAsPrice($post['tax_value'][$k]);
                    }
                }
            }
            $post['order_tax_ext'] = serialize($order_tax_ext);
            $post['order_tax'] = number_format(array_sum($order_tax_ext),2);
        }

        $currency = JSFactory::getTable('currency');
        $currency->load($post['currency_id']);
        $post['currency_code'] = $currency->currency_code;
        $post['currency_code_iso'] = $currency->currency_code_iso;
        $post['currency_exchange'] = $currency->currency_value;

        $dispatcher->triggerEvent('onBeforeSaveOrder', array(&$post, &$file_generete_pdf_order, &$order));

        $applyCoupon = $order->applyCoupon($post['coupon_code'] ?? '');
		
		$fields_float = ['order_subtotal', 'order_discount', 'order_shipping', 'order_package', 'order_payment', 'order_total'];
		foreach($fields_float as $v) {
			if (isset($post[$v])) {
				$post[$v] = Helper::saveAsPrice($post[$v]);
			}
		}
        $fields_int = ['title', 'd_title', 'country', 'd_country'];
		foreach($fields_int as $v) {
			if (isset($post[$v]) && $post[$v] == '') {
				$post[$v] = 0;
			}
		}

        $order->bind($post);
		$order->delivery_times_id = $post['order_delivery_times_id'] ?? 0;
        if (isset($post['shipping_method_id']) && isset($post['params'][$post['shipping_method_id']])) {
            $sh_params = $post['params'][$post['shipping_method_id']];
            $order->setShippingParamsByForm($sh_params);
        }
        if (!$order->store()){
            $this->setError(Text::_('JSHOP_ERROR_SAVE_DATABASE')." ".$order->getError());
            return 0;
        }
        $order_id = $order->order_id;
        $order_items = $order->getAllItems();
        $this->saveOrderItem($order_id, $post, $order_items);

        $order->items = null;
        $vendor_id = $order->getVendorIdForItems();
        $order->vendor_id = $vendor_id;
        $order->store();

		$order->items = null;

        if ($update_product_stock){
            $order->changeProductQTYinStock('-');
        }
		
		if ($new_order) {
			$order->saveOrderHistory($order->order_created, '');
		}

        if ($order->order_created==1 && $order_created_prev==0){
			$order->updateProductsInStock(1);
            $dispatcher->triggerEvent('onAdminSaveOrderCreated', array(&$order));
            $checkout = JSFactory::getModel('checkout', 'Site');
            if ($jshopConfig->send_order_email){
                $checkout->sendOrderEmail($order_id, 1);
            }
			if (!$new_order) {
                $order->saveOrderHistory(1, '');
            }
        }elseif($order->order_created==1 && $jshopConfig->generate_pdf){
            $dispatcher->triggerEvent('onAdminSaveOrderPdfResave', array(&$order, &$file_generete_pdf_order));
			$order->load($order_id);
            $order->prepareOrderPrint('', 1);
            $order->generatePdf($file_generete_pdf_order);
		}

        JSFactory::loadAdminLanguageFile();
        if ($post['coupon_code'] != '' && $applyCoupon == 0){
            $app->enqueueMessage(Text::_('JSHOP_ERROR_COUPON_CODE'), 'warning');
        }
        $dispatcher->triggerEvent('onAfterSaveOrder', array(&$order, &$file_generete_pdf_order));
        return $order;
    }
    
}