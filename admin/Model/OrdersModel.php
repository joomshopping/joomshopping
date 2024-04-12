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

class OrdersModel extends BaseadminModel{

    function getCountAllOrders($filters) {
        $jshopConfig = \JSFactory::getConfig();
        $db = \JFactory::getDBO();
        $where = "";
        if ($filters['status_id']){
            $where .= " and O.order_status = '".$db->escape($filters['status_id'])."'";
        }
        if($filters['user_id']) $where .= " and O.user_id = '".$db->escape($filters['user_id'])."'";
        if ($filters['text_search']){
            $search = $db->escape($filters['text_search']);
            $where .= " and (O.`order_number` like '%".$search."%' or O.`f_name` like '%".$search."%' or O.`l_name` like '%".$search."%' or O.`email` like '%".$search."%' or O.`firma_name` like '%".$search."%' or O.`d_f_name` like '%".$search."%' or O.`d_l_name` like '%".$search."%' or O.`d_firma_name` like '%".$search."%' or O.order_add_info like '%".$search."%') ";
        }
        if (!$filters['notfinished']) $where .= "and O.order_created='1' ";
        if ($filters['date_from']){
			$date = \JSHelper::getJsDateDB($filters['date_from'], $jshopConfig->field_birthday_format);
			$where .= ' and O.order_date>="'.$db->escape($date).'" ';
		}
		if ($filters['date_to']){
			$date = \JSHelper::getJsDateDB($filters['date_to'], $jshopConfig->field_birthday_format);
			$where .= ' and O.order_date<="'.$db->escape($date).' 23:59:59" ';
		}        
        
        if (isset($filters['vendor_id']) && $filters['vendor_id']){
            $where .= " and OI.vendor_id='".$db->escape($filters['vendor_id'])."'";
            $query = "SELECT COUNT(distinct O.order_id) FROM `#__jshopping_orders` as O
                  left join `#__jshopping_order_item` as OI on OI.order_id=O.order_id
                  where 1 $where ORDER BY O.order_id DESC";
        }else{
            $query = "SELECT COUNT(O.order_id) FROM `#__jshopping_orders` as O where 1 ".$where;
        }
		
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeQueryGetCountAllOrders', array(&$query, &$filters));
        $db->setQuery($query);
        return $db->loadResult();
    }

    function getAllOrders($limitstart, $limit, $filters, $filter_order, $filter_order_Dir) {
        $jshopConfig = \JSFactory::getConfig();
        $db = \JFactory::getDBO(); 
        $where = "";
        if ($filters['status_id']){
            $where .= " and O.order_status = '".$db->escape($filters['status_id'])."'";
        }
        if($filters['user_id']) $where .= " and O.user_id = '".$db->escape($filters['user_id'])."'";
        if ($filters['text_search']){
            $search = $db->escape($filters['text_search']);
            $where .= " and (O.`order_number` like '%".$search."%' or O.`f_name` like '%".$search."%' or O.`l_name` like '%".$search."%' or O.`email` like '%".$search."%' or O.`firma_name` like '%".$search."%' or O.`d_f_name` like '%".$search."%' or O.`d_l_name` like '%".$search."%' or O.`d_firma_name` like '%".$search."%' or O.order_add_info like '%".$search."%') ";
        }
        if (!$filters['notfinished']) $where .= "and O.order_created='1' ";
        if ($filters['date_from']){
			$date = \JSHelper::getJsDateDB($filters['date_from'], $jshopConfig->field_birthday_format);
			$where .= ' and O.order_date>="'.$db->escape($date).'" ';
		}
		if ($filters['date_to']){
			$date = \JSHelper::getJsDateDB($filters['date_to'], $jshopConfig->field_birthday_format);
			$where .= ' and O.order_date<="'.$db->escape($date).' 23:59:59" ';
		}
        
        $order = $filter_order." ".$filter_order_Dir;
        
        if (isset($filters['vendor_id']) && $filters['vendor_id']){
            $where .= " and OI.vendor_id='".$db->escape($filters['vendor_id'])."'";
            $query = "SELECT distinct O.* FROM `#__jshopping_orders` as O
                  left join `#__jshopping_order_item` as OI on OI.order_id=O.order_id
                  where 1 $where ORDER BY ".$order;
        }else{
            $query = "SELECT O.*, V.l_name as v_name, V.f_name as v_fname, concat(O.f_name,' ',O.l_name) as name FROM `#__jshopping_orders` as O
                  left join `#__jshopping_vendors` as V on V.id=O.vendor_id
                  where 1 $where ORDER BY ".$order;
        }
        
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeQueryGetAllOrders', array(&$query, &$filters, &$filter_order, &$filter_order_Dir));
		$db->setQuery($query, $limitstart, $limit);
        return $db->loadObjectList();
    }

    function getAllOrderStatus($order = null, $orderDir = null) {
        $db = \JFactory::getDBO(); 
        $lang = \JSFactory::getLang();
        $ordering = "status_id";
        if ($order && $orderDir){
            $ordering = $order." ".$orderDir;
        }
        $query = "SELECT status_id, status_code, `".$lang->get('name')."` as name FROM `#__jshopping_order_status` ORDER BY ".$ordering;
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        $db->setQuery($query);
        return $db->loadObjectList();
    }
    
    function getMinYear(){
        $db = \JFactory::getDBO();
        $query = "SELECT min(order_date) FROM `#__jshopping_orders`";
        $db->setQuery($query);
        $res = substr($db->loadResult(),0, 4);
        if (intval($res)==0) $res = "2010";
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        return $res;
    }
    
    function saveOrderItem($order_id, $post, $old_items){
        $db = \JFactory::getDBO();
        if (!isset($post['product_name'])) $post['product_name'] = array();

        $edit_order_items = array();
        foreach($post['product_name'] as $k=>$v){
            $order_item_id = intval($post['order_item_id'][$k]);
            $edit_order_items[] = $order_item_id;
            $product = \JSFactory::getTable('product');
            $product->load($post['product_id'][$k]);
            $order_item = \JSFactory::getTable('orderItem');
            $order_item->order_item_id = $order_item_id;
            $order_item->order_id = $order_id;
            $order_item->product_id = $post['product_id'][$k];
			$order_item->category_id = $post['category_id'][$k];
            $order_item->product_ean = $post['product_ean'][$k];
            $order_item->manufacturer_code = $post['manufacturer_code'][$k];
            $order_item->product_name = $post['product_name'][$k];
            $order_item->product_quantity = \JSHelper::saveAsPrice($post['product_quantity'][$k]);
            $order_item->product_item_price = $post['product_item_price'][$k];
            $order_item->product_tax = $post['product_tax'][$k];
            $order_item->product_attributes = $post['product_attributes'][$k];
            if (json_decode($post['attributes'][$k])){
                $attribute = (array)json_decode($post['attributes'][$k]);
                $order_item->attributes = serialize($attribute);
            }else{
                $order_item->attributes = $post['attributes'][$k];
            }
            $order_item->product_freeattributes = isset($post['product_freeattributes'][$k]) ? $post['product_freeattributes'][$k] : '';
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
            $order_item->store();
            unset($order_item);
        }

        foreach($old_items as $k=>$v){
            if (!in_array($v->order_item_id, $edit_order_items)){
                $order_item = \JSFactory::getTable('orderItem');
                $order_item->delete($v->order_item_id);                
            }
        }
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        return 1;
    }
    
    function getCartProductsFromOrderProducts($items){
        $products = array();
        foreach($items as $k=>$v){
            $prod = array();
            $prod['product_id'] = $v['product_id'];
            $prod['quantity'] = $v['product_quantity'];
            $prod['tax'] = $v['product_tax'];
            $prod['product_name'] = $v['product_name'];
            $prod['thumb_image'] = $v['thumb_image'];
            $prod['ean'] = $v['product_ean'];
            $prod['weight'] = $v['weight'];
            $prod['delivery_times_id'] = $v['delivery_times_id'];
            $prod['vendor_id'] = $v['vendor_id'];
            $prod['price'] = $v['product_item_price'];
            $products[] = $prod;
        }
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        return $products;
    }
    
    function loadtaxorder($data_order, $products){
        $jshopConfig = \JSFactory::getConfig();
        $jshopConfig->display_price_front_current = $data_order['display_price'];
        $display_price_front_current = $data_order['display_price'];
        $taxes = array();
        $total = 0;
        $AllTaxes = \JSFactory::getAllTaxes();
        $id_country = $data_order['d_country'];
        if (!$id_country){
            $id_country = $data_order['country'];
        }
        if (!$id_country){
            $id_country = $jshopConfig->default_country;
        }
        
        // tax product
        foreach($products as $key=>$product){
            $tax = (string)floatval($product['product_tax']);
            $price = $product['product_item_price'] * $product['product_quantity'];
            $SumTax = (isset($taxes[$tax]))?$taxes[$tax]:0;
            $taxes[$tax] =  $SumTax + \JSHelper::getPriceTaxValue($price, $tax, $display_price_front_current);
            $total += $price;
        }
        
        $cproducts = $this->getCartProductsFromOrderProducts($products);
        $cart = \JSFactory::getModel('cart', 'Site');
        $cart->products = array();
        $cart->loadProductsFromArray($cproducts);
        $cart->loadPriceAndCountProducts();
        
        // payment
        if ($data_order['order_payment']!=0){
            $price = $data_order['order_payment'];
            $payment_method_id = $data_order['payment_method_id'];
            $paym_method = \JSFactory::getTable('paymentmethod');
            $paym_method->load($payment_method_id);
            $paym_method->setCart($cart);
            $payment_taxes = $paym_method->calculateTaxList($price);            
            foreach($payment_taxes as $k=>$v){
                $k = (string)floatval($k);
                $SumTax = (isset($taxes[$k]))?$taxes[$k]:0;
                $taxes[$k] = $SumTax + $v;
            }
            $total += $price;
        }
        
        //shipping
        $shipping_method = \JSFactory::getTable('shippingMethod');
        $sh_pr_method_id = $shipping_method->getShippingPriceId($data_order['shipping_method_id'], $id_country);
        
        $shipping_method_price = \JSFactory::getTable('shippingMethodPrice');
        $shipping_method_price->load($sh_pr_method_id);
        
        // tax shipping
        if ($data_order['order_shipping']>0){
            $price = $data_order['order_shipping'];            
            $shipping_taxes = $shipping_method_price->calculateShippingTaxList($price, $cart);
            foreach($shipping_taxes as $k=>$v){
                $k = (string)floatval($k);
                $SumTax = (isset($taxes[$k]))?$taxes[$k]:0;
                $taxes[$k] = $SumTax + $v;
            }
            $total += $price;
        }
        // tax package
        if ($data_order['order_package']>0){
            $price = $data_order['order_package'];
            $shipping_taxes = $shipping_method_price->calculatePackageTaxList($price, $cart);
            foreach($shipping_taxes as $k=>$v){
                $k = (string)floatval($k);
                $SumTax = (isset($taxes[$k]))?$taxes[$k]:0;
                $taxes[$k] = $SumTax + $v;
            }
            $total += $price;
        }
        
        $taxes_array = array();
        foreach($taxes as $tax=>$value){
            if ($tax>0){
                $taxes_array[] = array('tax'=>$tax, 'value'=>$value);
            }
        }
        
        if ($data_order['order_discount'] > 0 && $jshopConfig->calcule_tax_after_discount){
            $discountPercent = $data_order['order_discount'] / $total;
            foreach($taxes_array as $k=>$v){
                $taxes_array[$k]['value'] = $v['value'] * (1 - $discountPercent);
            }
        }

        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        return $taxes_array;
    }
    
    function loadshippingprice($data_order, $products){
        $jshopConfig = \JSFactory::getConfig();
        $jshopConfig->display_price_front_current = $data_order['display_price'];
        $all_currency = \JSFactory::getAllCurrency();
        $currency_id = $data_order['currency_id'];
        if ($currency_id){
            $jshopConfig->currency_value = $all_currency[$currency_id]->currency_value;
        }
        
        $id_country = $data_order['d_country'];
        if (!$id_country){
            $id_country = $data_order['country'];
        }
        if (!$id_country){
            $id_country = $jshopConfig->default_country;
        }
        $shipping_method = \JSFactory::getTable('shippingMethod');
        $shipping_method_price = \JSFactory::getTable('shippingMethodPrice');
        
        $shipping_price_method_id = $shipping_method->getShippingPriceId($data_order['shipping_method_id'], $id_country);
        if (!$shipping_price_method_id){
            return null;
        }
        
        $cproducts = $this->getCartProductsFromOrderProducts($products);
        $cart = \JSFactory::getModel('cart', 'Site');
        $cart->products = array();
        $cart->loadProductsFromArray($cproducts);
        $cart->loadPriceAndCountProducts();
        
        $shipping_method_price->load($shipping_price_method_id);            
        $prices = $shipping_method_price->calculateSum($cart);
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        foreach($prices as $k=>$v){
            $prices[$k] = \JSHelper::getRoundPriceProduct($v);
        }
        return $prices; 
    }
    
    function loadpaymentprice($data_order, $products){
        $jshopConfig = \JSFactory::getConfig();
        $jshopConfig->display_price_front_current = $data_order['display_price'];
        $all_currency = \JSFactory::getAllCurrency();
        $currency_id = $data_order['currency_id'];
        if ($currency_id){
            $jshopConfig->currency_value = $all_currency[$currency_id]->currency_value;
        }
        $id_country = $data_order['d_country'];
        if (!$id_country){
            $id_country = $data_order['country'];
        }
        $AllTaxes = \JSFactory::getAllTaxes();

        $cproducts = $this->getCartProductsFromOrderProducts($products);
        $cart = \JSFactory::getModel('cart', 'Site');
        $cart->products = array();
        $cart->loadProductsFromArray($cproducts);
        $cart->loadPriceAndCountProducts();
        
        $paym_method = \JSFactory::getTable('paymentmethod');
        $paym_method->load($data_order['payment_method_id']);
        if ($paym_method->price_type==2){
            $total = 0;
            foreach($products as $key=>$product){
                $tax = floatval($product['product_tax']);
                $product_price = $product['product_item_price'] * $product['product_quantity'];
                if ($data_order['display_price']){
                    $product_price = $product_price + $product_price * $tax / 100;    
                }
                $total += $product_price;
            }
            
            $shipping_method = \JSFactory::getTable('shippingMethod');
            $sh_pr_method_id = $shipping_method->getShippingPriceId($data_order['shipping_method_id'], $id_country);            
            $shipping_method_price = \JSFactory::getTable('shippingMethodPrice');
            $shipping_method_price->load($sh_pr_method_id);
            
            $tax = floatval($AllTaxes[$shipping_method_price->shipping_tax_id]);
            $shipping_price = $data_order['order_shipping'];
            if ($data_order['display_price']){
                $shipping_taxes = $shipping_method_price->calculateShippingTaxList($shipping_price, $cart);                
                foreach($shipping_taxes as $k=>$v){
                    $shipping_price = $shipping_price + $v;    
                }                
            }
            $total += $shipping_price;
            
            $tax = floatval($AllTaxes[$shipping_method_price->package_tax_id]);
            $package_price = $data_order['order_package'];
            if ($data_order['display_price']){
                $shipping_taxes = $shipping_method_price->calculatePackageTaxList($package_price, $cart);
                foreach($shipping_taxes as $k=>$v){
                    $package_price = $package_price + $v;    
                }                
            }
            $total += $package_price;

            $price = $total * $paym_method->price / 100;
            if ($data_order['display_price']){
                $price = \JSHelper::getPriceCalcParamsTax($price, $paym_method->tax_id, $cart->products);
            }
        }else{
            $price = $paym_method->price * $jshopConfig->currency_value; 
            $price = \JSHelper::getPriceCalcParamsTax($price, $paym_method->tax_id, $cart->products);
        }
        extract(\JSHelper::js_add_trigger(get_defined_vars(), "before"));
        return \JSHelper::getRoundPriceProduct($price);
    }

    function loaddiscountprice($data_order, $products){
        $code = $data_order['coupon_code'];
        if ($code == ''){
            return 0;
        }
        $coupon = \JSFactory::getTable('coupon');
        $coupon_id = $coupon->getIdFromCode($code);
        if (!$coupon_id){
            return 0;
        }
        $coupon->load($coupon_id);

        $jshopConfig = \JSFactory::getConfig();
        $jshopConfig->display_price_front_current = $data_order['display_price'];
        $all_currency = \JSFactory::getAllCurrency();
        $currency_id = $data_order['currency_id'];
        if ($currency_id){
            $jshopConfig->currency_value = $all_currency[$currency_id]->currency_value;
        }
        $id_country = $data_order['d_country'];
        if (!$id_country){
            $id_country = $data_order['country'];
        }

        $cproducts = $this->getCartProductsFromOrderProducts($products);
        $cart = \JSFactory::getModel('cart', 'Site');
        $cart->products = array();
        $cart->loadProductsFromArray($cproducts);
        $cart->loadPriceAndCountProducts();

        $cart->setDisplayItem(1, 1);
        $cart->setShippingPrice($data_order['order_shipping']);
        $cart->setPackagePrice($data_order['order_package']);
        $cart->setPaymentPrice($data_order['order_payment']);
        
        $shipping_method = \JSFactory::getTable('shippingMethod');
        $sh_pr_method_id = $shipping_method->getShippingPriceId($data_order['shipping_method_id'], $id_country);
        
        $shipping_method_price = \JSFactory::getTable('shippingMethodPrice');
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
            $paym_method = \JSFactory::getTable('paymentmethod');
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
        $app = \JFactory::getApplication();
        $dispatcher = \JFactory::getApplication();        
        $dispatcher->triggerEvent('onBeforeRemoveOrder', array(&$cid));
		$order = \JSFactory::getTable('order');
        $res = array();
		foreach($cid as $id){
			$order->delete($id);
            $res[$id] = true;
		}
		$dispatcher->triggerEvent('onAfterRemoveOrder', array(&$cid));
        if ($msg){
            $msg = sprintf(\JText::_('JSHOP_ORDER_DELETED_ID'), implode(", ", $cid));
            $app->enqueueMessage($msg, 'message');
        }
        return $res;
    }

    public function save(array $post){
        $app = \JFactory::getApplication();
        $jshopConfig = \JSFactory::getConfig();
        $file_generete_pdf_order = $jshopConfig->file_generete_pdf_order;
        $dispatcher = \JFactory::getApplication();
        $order_id = intval($post['order_id']);
        $order = \JSFactory::getTable('order');
        $order->load($order_id);
        if (!$order_id){
            $order->user_id = -1;
            $order->order_date = \JSHelper::getJsDate();
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
        $order->order_m_date = \JSHelper::getJsDate();
        if (isset($post['birthday']) && $post['birthday']) $post['birthday'] = \JSHelper::getJsDateDB($post['birthday'], $jshopConfig->field_birthday_format);
        if (isset($post['d_birthday']) && $post['d_birthday']) $post['d_birthday'] = \JSHelper::getJsDateDB($post['d_birthday'], $jshopConfig->field_birthday_format);
		if (isset($post['invoice_date']) && $post['invoice_date']) $post['invoice_date'] = \JSHelper::getJsDateDB($post['invoice_date'], $jshopConfig->store_date_format);

        if (!$jshopConfig->hide_tax){
            $post['order_tax'] = 0;
            $order_tax_ext = array();
            if (isset($post['tax_percent'])){
                foreach($post['tax_percent'] as $k=>$v){
                    if ($post['tax_percent'][$k]!="" || $post['tax_value'][$k]!=""){
                        $order_tax_ext[number_format($post['tax_percent'][$k],2)] = $post['tax_value'][$k];
                    }
                }
            }
            $post['order_tax_ext'] = serialize($order_tax_ext);
            $post['order_tax'] = number_format(array_sum($order_tax_ext),2);
        }

        $currency = \JSFactory::getTable('currency');
        $currency->load($post['currency_id']);
        $post['currency_code'] = $currency->currency_code;
        $post['currency_code_iso'] = $currency->currency_code_iso;
        $post['currency_exchange'] = $currency->currency_value;

        $dispatcher->triggerEvent('onBeforeSaveOrder', array(&$post, &$file_generete_pdf_order, &$order));

        $applyCoupon = $order->applyCoupon($post['coupon_code']);

        $order->bind($post);
		$order->delivery_times_id = $post['order_delivery_times_id'];
        $order->store();
        $order_id = $order->order_id;
        $order_items = $order->getAllItems();
        $this->saveOrderItem($order_id, $post, $order_items);

        $order->items = null;
        $vendor_id = $order->getVendorIdForItems();
        $order->vendor_id = $vendor_id;
        $order->store();

        \JSFactory::loadLanguageFile($order->getLang());
		$lang = \JSFactory::getLang($order->getLang());
		$order->items = null;

        if ($update_product_stock){
            $order->changeProductQTYinStock('-');
        }

        if ($order->order_created==1 && $order_created_prev==0){
			$order->updateProductsInStock(1);
            $dispatcher->triggerEvent('onAdminSaveOrderCreated', array(&$order));
            $checkout = \JSFactory::getModel('checkout', 'Site');
            if ($jshopConfig->send_order_email){
                $checkout->sendOrderEmail($order_id, 1);
            }
        }elseif($order->order_created==1 && $jshopConfig->generate_pdf){
			$order->load($order_id);
            $order->prepareOrderPrint('', 1);
            $order->generatePdf($file_generete_pdf_order);
		}

        \JSFactory::loadAdminLanguageFile();
        if ($post['coupon_code'] != '' && $applyCoupon == 0){
            $app->enqueueMessage(\JText::_('JSHOP_ERROR_COUPON_CODE'), 'warning');
        }
        $dispatcher->triggerEvent('onAfterSaveOrder', array(&$order, &$file_generete_pdf_order));
        return $order;
    }
    
}