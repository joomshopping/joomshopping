<?php
/**
* @version      5.0.7 31.08.2022
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
use Joomla\Component\Jshopping\Site\Helper\SelectOptions;
use Joomla\Component\Jshopping\Site\Helper\Selects;


defined('_JEXEC') or die();

class OrdersController extends BaseadminController{

    public function init(){
        $this->registerTask('add', 'edit');
        $this->registerTask('apply', 'save');
        \JSHelperAdmin::checkAccessController("orders");
        if (!$this->input->getVar("js_nolang")){
            \JSHelperAdmin::addSubmenu("orders");
        }  
        \JPluginHelper::importPlugin('jshoppingorder');
    }

    function display($cachable = false, $urlparams = false){
        $jshopConfig = \JSFactory::getConfig();
        $app = \JFactory::getApplication();        
        $context = "jshopping.list.admin.orders";
        $limit = $app->getUserStateFromRequest( $context.'limit', 'limit', $app->getCfg('list_limit'), 'int' );
        $limitstart = $app->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
        $id_vendor_cuser = \JSHelperAdmin::getIdVendorForCUser();
        $client_id = $this->input->getInt('client_id',0);
        
        $status_id = $app->getUserStateFromRequest( $context.'status_id', 'status_id', 0 );
        $date_from = $app->getUserStateFromRequest( $context.'date_from', 'date_from', '');
		$date_to = $app->getUserStateFromRequest( $context.'date_to', 'date_to', '');
        $notfinished = $app->getUserStateFromRequest( $context.'notfinished', 'notfinished', $jshopConfig->order_notfinished_default);
        $text_search = $app->getUserStateFromRequest( $context.'text_search', 'text_search', '' );
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "order_number", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "desc", 'cmd');
  
        $filter = array("status_id"=>$status_id, 'user_id'=>$client_id, "date_from"=>$date_from, "date_to"=>$date_to, "text_search"=>$text_search, 'notfinished'=>$notfinished);        
        if ($id_vendor_cuser){            
            $filter["vendor_id"] = $id_vendor_cuser;
        }
        
        $orders = \JSFactory::getModel("orders");
        
        $total = $orders->getCountAllOrders($filter);        
        jimport('joomla.html.pagination');
        $pageNav = new \JPagination($total, $limitstart, $limit);
        
        $_list_order_status = $orders->getAllOrderStatus();
        $list_order_status = array();
        foreach($_list_order_status as $v){
            $list_order_status[$v->status_id] = $v->name;
        }
        $rows = $orders->getAllOrders($pageNav->limitstart, $pageNav->limit, $filter, $filter_order, $filter_order_Dir);
        
        $lists['status_orders'] = SelectOptions::getOrderStatus();       
        $lists['changestatus'] = \JHTML::_('select.genericlist', SelectOptions::getOrderStatus(1) ,'status_id','class="form-select" style="width: 170px;" ','status_id','name', $status_id);
        $lists['notfinished'] = \JHTML::_('select.genericlist', SelectOptions::getNotFinshed(), 'notfinished','class="form-select" style="width: 170px;" title="'.\JText::_('JSHOP_NOT_FINISHED').'" ','id','name', $notfinished);
        		
		$payments = \JSFactory::getModel("payments");
        $payments_list = $payments->getListNamePaymens(0);
        
        $shippings = \JSFactory::getModel("shippings");
        $shippings_list = $shippings->getListNameShippings(0);
        
        $show_vendor = $jshopConfig->admin_show_vendors;
        if ($id_vendor_cuser) $show_vendor = 0;
        $display_info_only_my_order = 0;
        if ($jshopConfig->admin_show_vendors && $id_vendor_cuser){
            $display_info_only_my_order = 1; 
        }
        
        $total = 0;
        foreach($rows as $k=>$row){
            if ($row->vendor_id>0){
                $vendor_name = $row->v_fname." ".$row->v_name;
            }else{
                $vendor_name = "-";
            }
            $rows[$k]->vendor_name = $vendor_name;
            
            $display_info_order = 1;
            if ($display_info_only_my_order && $id_vendor_cuser!=$row->vendor_id) $display_info_order = 0;
            $rows[$k]->display_info_order = $display_info_order;
            
            $blocked = 0;
            if (\JSHelperAdmin::orderBlocked($row) || !$display_info_order) $blocked = 1;
            $rows[$k]->blocked = $blocked;
			
            $rows[$k]->payment_name = isset($payments_list[$row->payment_method_id]) ? $payments_list[$row->payment_method_id] : '';
            $rows[$k]->shipping_name = isset($shippings_list[$row->shipping_method_id]) ? $shippings_list[$row->shipping_method_id] : '';
			if ($row->currency_exchange==0){
				$row->currency_exchange = 1;
			}
            $total += $row->order_total / $row->currency_exchange;
        }

        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayListOrderAdmin', array(&$rows));

        foreach ($rows as $row) {
            $row->_tmp_ext_info_order_number = "";
            $row->_tmp_cols_1 = "";
            $row->_tmp_cols_after_user = "";
            $row->_tmp_cols_3 = "";
            $row->_tmp_cols_4 = "";
            $row->_tmp_cols_5 = "";
            $row->_tmp_cols_6 = "";
            $row->_tmp_cols_7 = "";
            $row->_tmp_cols_8 = "";
            $row->_tmp_ext_info_status = "";
            $row->_tmp_ext_info_update = "";
            $row->_tmp_ext_info_order_total = "";
        }
		
		$view=$this->getView("orders", 'html');
        $view->setLayout("list");
        $view->set('rows', $rows); 
        $view->set('lists', $lists); 
        $view->set('pageNav', $pageNav); 
        $view->set('text_search', $text_search); 
        $view->set('filter', $filter);        
        $view->set('show_vendor', $show_vendor);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->set('list_order_status', $list_order_status);
        $view->set('client_id', $client_id);
        $view->set('total', $total);
        $view->sidebar = \JHTMLSidebar::render();
        $view->_tmp_order_list_html_end = '';
        $view->tmp_html_start = "";
        $view->tmp_html_filter = "";
        $view->tmp_html_filter_end = "";
        $view->_tmp_cols_1 = "";
        $view->_tmp_cols_after_user = "";
        $view->_tmp_cols_3 = "";
        $view->_tmp_cols_4 = "";
        $view->_tmp_cols_5 = "";
        $view->_tmp_cols_6 = "";
        $view->_tmp_cols_7 = "";
        $view->_tmp_cols_8 = "";
        $view->_tmp_cols_foot_total = "";
        $view->tmp_html_col_before_td_foot = "";
        $view->tmp_html_col_after_td_foot = "";
        $view->tmp_html_end = "";
		$view->deltaColspan0 = 0;
		$view->deltaColspan = 0;
        $dispatcher->triggerEvent('onBeforeShowOrderListView', array(&$view));
		$view->displayList(); 
    }
    
    function show(){
        \JFactory::getApplication()->input->set('hidemainmenu', true);
        $order_id = $this->input->getInt("order_id");
        $jshopConfig = \JSFactory::getConfig();
		
        $orders = \JSFactory::getModel("orders");
        $order = \JSFactory::getTable('order');
        $order->load($order_id);
        
		$order->prepareOrderPrint('order_show');
        
        $id_vendor_cuser = \JSHelperAdmin::getIdVendorForCUser();
        
		$order->loadItemsNewDigitalProducts();
        $order_items = $order->getAllItems();
		
        if ($jshopConfig->admin_show_vendors){
            $tmp_order_vendors = $order->getVendors();
            $order_vendors = array();
            foreach($tmp_order_vendors as $v){
                $order_vendors[$v->id] = $v;
            }
        }

        $lists['status'] = SelectOptions::getOrderStatus();
        
        $tmp_fields = $jshopConfig->getListFieldsRegister();
        $config_fields = $tmp_fields["address"];
        $count_filed_delivery = $jshopConfig->getEnableDeliveryFiledRegistration('address');
        
        $display_info_only_product = 0;
        if ($jshopConfig->admin_show_vendors && $id_vendor_cuser){
            if ($order->vendor_id!=$id_vendor_cuser) $display_info_only_product = 1; 
        }
        
        $display_block_change_order_status = $order->order_created;        
        if ($jshopConfig->admin_show_vendors && $id_vendor_cuser){
            if ($order->vendor_id!=$id_vendor_cuser) $display_block_change_order_status = 0;
            foreach($order_items as $k=>$v){
                if ($v->vendor_id!=$id_vendor_cuser){
                    unset($order_items[$k]);
                }
            }
        }
        		
		$stat_download = $order->getFilesStatDownloads(1);
        
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayOrderAdmin', array(&$order, &$order_items));
        
        $print = $this->input->getInt("print");
        
        $view = $this->getView("orders", 'html');
        $view->setLayout("show");
        $view->set('config', $jshopConfig); 
        $view->set('order', $order); 
        $view->set('order_history', $order->history);
        $view->set('order_items', $order_items); 
        $view->set('lists', $lists); 
        $view->set('print', $print);
        $view->set('config_fields', $config_fields);
        $view->set('count_filed_delivery', $count_filed_delivery);
        $view->set('display_info_only_product', $display_info_only_product);
        $view->set('current_vendor_id', $id_vendor_cuser);
        $view->set('display_block_change_order_status', $display_block_change_order_status);
        $view->_tmp_ext_discount = '';
        $view->_tmp_ext_shipping_package = '';
        $view->tmp_html_start = "";
        $view->tmp_html_info = "";
        $view->tmp_html_table_history_field = "";
        $view->tmp_fields = "";
        $view->tmp_d_fields = "";
        $view->_tmp_html_after_customer_info = "";
        $view->_tmp_ext_subtotal = "";
        $view->_tmp_html_after_subtotal = "";
        $view->_tmp_ext_discount_text = "";
        $view->_tmp_ext_discount = "";
        $view->_tmp_ext_shipping_package = "";
        $view->_tmp_html_after_total = "";
        $view->_ext_end_html = "";
        $view->tmp_html_end = "";
        $view->_update_status_html = "";
        $view->set('stat_download', $stat_download);
        if ($jshopConfig->admin_show_vendors){ 
            $view->set('order_vendors', $order_vendors);
        }
        $dispatcher->triggerEvent('onBeforeShowOrder', array(&$view));
        $view->displayShow();
    }

    function printOrder(){
        $this->input->set("print", 1);
        $this->show();
    }
    
    function update_one_status(){
        $input = $this->input;
        $this->_updateStatus($input->getVar('order_id'),$input->getVar('order_status'),$input->getVar('status_id'),$input->getVar('notify',0),$input->getVar('comments',''),$input->getVar('include',''),1);
    }
    
    function update_status(){
        $input = $this->input;
        $this->_updateStatus($input->getVar('order_id'),$input->getVar('order_status'),$input->getVar('status_id'),$input->getVar('notify',0),$input->getVar('comments',''),$input->getVar('include',''),0);        
    }    
    
    function _updateStatus($order_id, $status, $status_id, $notify, $comments, $include, $view_order){
        $client_id = $this->input->getInt('client_id', 0);
		$sendmessage = $notify;
		
		$model = \JSFactory::getModel('orderchangestatus', 'Site');
		$model->setData($order_id, $status, $sendmessage, $status_id, $notify, $comments, $include, $view_order);
		$model->setAppAdmin(1);
		$model->store();
		
		\JSFactory::loadAdminLanguageFile();
        
        if ($view_order){
            $this->setRedirect("index.php?option=com_jshopping&controller=orders&task=show&order_id=".$order_id, \JText::_('JSHOP_ORDER_STATUS_CHANGED'));
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=orders&client_id=".$client_id, \JText::_('JSHOP_ORDER_STATUS_CHANGED'));
		}
    }
    
    function finish(){
		$dispatcher = \JFactory::getApplication();
		$jshopConfig = \JSFactory::getConfig();
		
        $order_id = $this->input->getInt("order_id");
        $order = \JSFactory::getTable('order');
        $order->load($order_id);
        $order->order_created = 1;
        $dispatcher->triggerEvent('onBeforeAdminFinishOrder', array(&$order));
        $order->store();
		$order->updateProductsInStock(1);
		$order->saveOrderHistory(1, '');
        
        \JSFactory::loadLanguageFile($order->getLang(), true);
		$lang = \JSFactory::getLang($order->getLang());
        $checkout = \JSFactory::getModel('checkout', 'Site');
        if ($jshopConfig->send_order_email){
            $checkout->sendOrderEmail($order_id, 1);
        }
        
        \JSFactory::loadAdminLanguageFile();
        $this->setRedirect("index.php?option=com_jshopping&controller=orders", \JText::_('JSHOP_ORDER_FINISHED'));
    }

    function remove(){
		\JSession::checkToken() or die('Invalid Token');
        $client_id = $this->input->getInt('client_id', 0);
        $cid = (array)$this->input->getVar("cid");
        \JSFactory::getModel("orders")->deleteList($cid);
        $this->setRedirect("index.php?option=com_jshopping&controller=orders&client_id=".$client_id);
    }
    
    function edit(){
        \JFactory::getApplication()->input->set('hidemainmenu', true);
        $app = \JFactory::getApplication();
        $order_id = $this->input->getVar("order_id");
        $client_id = $this->input->getInt('client_id',0);
        $jshopConfig = \JSFactory::getConfig();
        $orders = \JSFactory::getModel("orders");
        $order = \JSFactory::getTable('order');
        $order->load($order_id);        
        
        $id_vendor_cuser = \JSHelperAdmin::getIdVendorForCUser();
        if ($jshopConfig->admin_show_vendors && $id_vendor_cuser){
            if ($order->vendor_id!=$id_vendor_cuser) {
				$app->enqueueMessage(\JText::_('JERROR_ALERTNOAUTHOR'), 'warning');
                $app->redirect('index.php');
                return 0;
            }
        }

        $order_items = $order->getAllItems();
        
        $select_language = \JHTML::_('select.genericlist', SelectOptions::getLanguages(), 'lang', 'class = "inputbox form-control form-select" style="float:none"','language', 'name', $order->lang);
        
		$select_countries = Selects::getCountry($order->country, 'class = "form-control"');
		$select_d_countries = Selects::getCountry($order->d_country, 'class = "inputbox ende form-control"', 'd_country');
		$select_titles = Selects::getTitle($order->title, 'class = "form-control"');
		$select_d_titles = Selects::getTitle($order->d_title, 'class = "inputbox endes form-control"', 'd_title');
		$select_client_types = Selects::getClientType($order->client_type, 'class = "form-control"');

        $order->prepareBirthdayFormat();
        
        $tmp_fields = $jshopConfig->getListFieldsRegister();
        $config_fields = $tmp_fields["address"];
        $count_filed_delivery = $jshopConfig->getEnableDeliveryFiledRegistration('address');
        
		$order->client_type_name = $order->getClientTypeName();
        $order->payment_name = $order->getPaymentName();
        $order->order_tax_list = $order->getTaxExt();
        $order->coupon_code = $order->getCouponCode();
        if (!$order->order_id){
            $order->display_price = $jshopConfig->display_price_front;
        }

        
        $_currency = \JSFactory::getModel("Currencies");
        $currency_list = $_currency->getAllCurrencies();
        $order_currency = 0;
        foreach($currency_list as $k=>$v){
            if ($v->currency_code_iso==$order->currency_code_iso) $order_currency = $v->currency_id;
        }
        $select_currency = \JHTML::_('select.genericlist', $currency_list, 'currency_id','class = "inputbox form-control form-select"','currency_id','currency_code', $order_currency);
        $display_price_select = \JHTML::_('select.genericlist', SelectOptions::getPriceType(), 'display_price', 'class="form-control form-select" onchange="jshopAdmin.updateOrderTotalValue();"', 'id', 'name', $order->display_price);
        $shippings_select = \JHTML::_('select.genericlist', SelectOptions::getShippings(), 'shipping_method_id', 'class="form-control form-select" onchange="jshopAdmin.order_shipping_calculate()"', 'shipping_id', 'name', $order->shipping_method_id);
        $payments_select = \JHTML::_('select.genericlist', SelectOptions::getPayments(), 'payment_method_id', 'class="form-control form-select" onchange="jshopAdmin.order_payment_calculate()"', 'payment_id', 'name', $order->payment_method_id);
        $delivery_time_select = \JHTML::_('select.genericlist', SelectOptions::getDeliveryTimes('- - -'), 'order_delivery_times_id','class = "form-control form-select"', 'id', 'name', $order->delivery_times_id);
        $users_list_select = \JHTML::_('select.genericlist', SelectOptions::getUsers(0, 1), 'user_id', 'class="form-control form-select" onchange="jshopAdmin.updateBillingShippingForUser(this.value);"', 'user_id', 'name', $order->user_id);        
        
        \JSHelper::filterHTMLSafe($order);
        foreach($order_items as $k=>$v){
            \JFilterOutput::objectHTMLSafe($order_items[$k]);
            $v->_ext_attribute_html = "";
        }
		
        $view = $this->getView("orders", 'html');
        $view->setLayout("edit");
        $view->set('config', $jshopConfig); 
        $view->set('order', $order);  
        $view->set('order_items', $order_items); 
        $view->set('config_fields', $config_fields);
        $view->set('etemplatevar', '');
        $view->set('count_filed_delivery', $count_filed_delivery);
        $view->set('order_id',$order_id);
        $view->set('select_countries', $select_countries);
        $view->set('select_d_countries', $select_d_countries);
		$view->set('select_titles', $select_titles);
        $view->set('select_d_titles', $select_d_titles);
        $view->set('select_client_types', $select_client_types);
        $view->set('select_currency', $select_currency);
        $view->set('display_price_select', $display_price_select);
        $view->set('shippings_select', $shippings_select);
        $view->set('payments_select', $payments_select);
        $view->set('select_language', $select_language);
        $view->set('delivery_time_select', $delivery_time_select);
        $view->set('users_list_select', $users_list_select);
        $view->set('client_id', $client_id);
        $view->tmp_html_start = "";
        $view->display_info_only_product = "";
        $view->_tmp_html_after_customer_info = "";
        $view->tmp_fields = "";
        $view->tmp_d_fields = "";
        $view->_ext_attribute_html = "";
        $view->_tmp_html_after_subtotal = "";
        $view->_tmp_html_after_total = "";
        $view->tmp_html_end = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditOrders', array(&$view));
        $view->displayEdit();
    }

    function save(){
		\JSession::checkToken() or die('Invalid Token');
        $client_id = $this->input->getInt('client_id', 0);
        $model = \JSFactory::getModel("orders");
        $post = $model->getPrepareDataSave($this->input);
        $order = $model->save($post);        
        if ($this->getTask()=='apply'){
            $this->setRedirect("index.php?option=com_jshopping&controller=orders&task=edit&order_id=".$order->order_id.'&client_id='.$client_id);
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=orders&client_id=".$client_id);
        }        
    }
    
	function stat_file_download_clear(){        
        $order_id = $this->input->getInt("order_id");
        $order = \JSFactory::getTable('order');
        $order->load($order_id);
        $order->file_stat_downloads = '';
        $order->store();
        $this->setRedirect("index.php?option=com_jshopping&controller=orders&task=show&order_id=".$order_id);
    }
    
    function send(){
        $order_id = $this->input->getInt("order_id");
        $back = $this->input->getVar("back");
        $order = \JSFactory::getTable('order');
        $order->load($order_id);
        \JSFactory::loadLanguageFile($order->getLang());
        \JSFactory::getLang($order->getLang());
        $checkout = \JSFactory::getModel('checkout', 'Site');
        $checkout->sendOrderEmail($order_id, 1);
        \JSFactory::loadAdminLanguageFile();
        if ($back=='orders'){
            $backurl = 'index.php?option=com_jshopping&controller=orders';
        }else{
            $backurl = "index.php?option=com_jshopping&controller=orders&task=show&order_id=".$order_id;
        }
        $this->setRedirect($backurl, \JText::_('JSHOP_MAIL_HAS_BEEN_SENT'));
    }
    
    function transactions(){
        $order_id = $this->input->getInt("order_id");
        $jshopConfig = \JSFactory::getConfig();
        
        $orders = \JSFactory::getModel("orders");
        $order = \JSFactory::getTable('order');
        $order->load($order_id);
        $rows = $order->getListTransactions();
        
        $_list_order_status = $orders->getAllOrderStatus();
        $list_order_status = array();
        foreach($_list_order_status as $v){
            $list_order_status[$v->status_id] = $v->name;
        }
        
        $view = $this->getView("orders", 'html');
        $view->setLayout("transactions");
        $view->set('config', $jshopConfig); 
        $view->set('order', $order);
        $view->set('rows', $rows);
        $view->set('list_order_status', $list_order_status);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeShowOrderTransactions', array(&$view));
        $view->displayTrx();   
    }
    
    function cancel(){
        $client_id = $this->input->getInt('client_id',0);
        $this->setRedirect("index.php?option=com_jshopping&controller=orders&client_id=".$client_id);
    }
    
    function loadtaxorder(){
        $post = $this->input->post->getArray();
        $data_order = (array)$post['data_order'];
        $products = (array)$data_order['product'];

        $orders = \JSFactory::getModel("orders");
        $taxes_array = $orders->loadtaxorder($data_order, $products);
        print json_encode($taxes_array);
        die;
    }
    
    function loadshippingprice(){
        $post = $this->input->post->getArray();
        $data_order = (array)$post['data_order'];
        $products = (array)$data_order['product'];

        $orders = \JSFactory::getModel("orders");
        $prices = $orders->loadshippingprice($data_order, $products);
        print json_encode($prices);
        die;
    }
    
    function loadpaymentprice(){
        $post = $this->input->post->getArray();
        $data_order = (array)$post['data_order'];
        $products = (array)$data_order['product'];

        $orders = \JSFactory::getModel("orders");
        $price = $orders->loadpaymentprice($data_order, $products);
        $prices = array('price'=>$price);
        print json_encode($prices);
        die;
    }

    function loaddiscountprice(){
        $post = $this->input->post->getArray();
        $data_order = (array)$post['data_order'];
        $products = (array)$data_order['product'];

        $orders = \JSFactory::getModel("orders");
        $price = $orders->loaddiscountprice($data_order, $products);
        $prices = array('price'=>$price);
        print json_encode($prices);
        die;
    }
    
}