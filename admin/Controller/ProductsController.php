<?php
/**
* @version      5.5.5 31.01.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\Component\Jshopping\Site\Helper\Error as JSError;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\Component\Jshopping\Site\Helper\SelectOptions;
use Joomla\Component\Jshopping\Site\Helper\Request;

defined('_JEXEC') or die();

class ProductsController extends BaseadminController{

    public function init(){
        HelperAdmin::checkAccessController("products");
        HelperAdmin::addSubmenu("products");
    }

    function display($cachable = false, $urlparams = false){
        $app = Factory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $products = JSFactory::getModel("products");
        $id_vendor_cuser = HelperAdmin::getIdVendorForCUser();

        $context = "jshoping.list.admin.product";
        $limit = $app->getUserStateFromRequest($context.'limit', 'limit', $app->getCfg('list_limit'), 'int' );
        $limitstart = $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int' );
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', $jshopConfig->adm_prod_list_default_sorting, 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', $jshopConfig->adm_prod_list_default_sorting_dir, 'cmd');

        if (isset($_GET['category_id']) && $_GET['category_id']==="0"){
            $app->setUserState($context.'category_id', 0);
            $app->setUserState($context.'manufacturer_id', 0);
			$app->setUserState($context.'vendor_id', -1);
            $app->setUserState($context.'label_id', 0);
            $app->setUserState($context.'publish', 0);
            $app->setUserState($context.'text_search', '');
        }

        $category_id = $app->getUserStateFromRequest($context.'category_id', 'category_id', 0, 'int');
        $manufacturer_id = $app->getUserStateFromRequest($context.'manufacturer_id', 'manufacturer_id', 0, 'int');
		$vendor_id = $app->getUserStateFromRequest($context.'vendor_id', 'vendor_id', -1, 'int');
        $label_id = $app->getUserStateFromRequest($context.'label_id', 'label_id', 0, 'int');
        $publish = $app->getUserStateFromRequest($context.'publish', 'publish', 0, 'int');
        $text_search = $app->getUserStateFromRequest($context.'text_search', 'text_search', '');
        if ($category_id && $filter_order=='category'){
            $filter_order = 'product_id';
        }

        $filter = array("category_id"=>$category_id, "manufacturer_id"=>$manufacturer_id, "vendor_id"=>$vendor_id, "label_id"=>$label_id, "publish"=>$publish, "text_search"=>$text_search);
        if ($id_vendor_cuser){
            $filter["vendor_id"] = $id_vendor_cuser;
        }

        $show_vendor = $jshopConfig->admin_show_vendors;
        if ($id_vendor_cuser){
            $show_vendor = 0;
        }

        $total = $products->getCountAllProducts($filter);
        $pagination = new Pagination($total, $limitstart, $limit);
        $rows = $products->getAllProducts(
            $filter,
            $pagination->limitstart,
            $pagination->limit,
            $filter_order,
            $filter_order_Dir,
            array(
                'label_image' => 1,
                'vendor_name' => $show_vendor
            )
        );

        if ($show_vendor){
            $lists['vendors'] = HTMLHelper::_('select.genericlist', SelectOptions::getVendors(), 'vendor_id','class="form-select" onchange="document.adminForm.submit();" default-value="-1"', 'id', 'name', $vendor_id);
        }
        $lists['treecategories'] = HTMLHelper::_('select.genericlist', SelectOptions::getCategories(1, 0, 1), 'category_id', 'class="form-select" onchange="document.adminForm.submit();"', 'category_id', 'name', $category_id );
        $lists['manufacturers'] = HTMLHelper::_('select.genericlist', SelectOptions::getManufacturers(), 'manufacturer_id','class="form-select" onchange="document.adminForm.submit();"', 'manufacturer_id', 'name', $manufacturer_id);
        if ($jshopConfig->admin_show_product_labels) {
            $lists['labels'] = HTMLHelper::_('select.genericlist', SelectOptions::getLabels(), 'label_id','style="width: 120px;" class="form-select" onchange="document.adminForm.submit();"','id','name', $label_id);
        }
        $lists['publish'] = HTMLHelper::_('select.genericlist', SelectOptions::getPublish(), 'publish', 'style="width: 120px;" class="form-select" onchange="document.adminForm.submit();"', 'id', 'name', $publish);

        $app->triggerEvent('onBeforeDisplayListProducts', array(&$rows));

        $view = $this->getView("product_list", 'html');
        $view->rows = $rows;
        $view->lists = $lists;
        $view->filter_order = $filter_order;
        $view->filter_order_Dir = $filter_order_Dir;
        $view->category_id = $category_id;
        $view->manufacturer_id = $manufacturer_id;
        $view->pagination = $pagination;
        $view->text_search = $text_search;
        $view->config = $jshopConfig;
        $view->show_vendor = $show_vendor;
        foreach($rows as $row) {
            $row->tmp_html_col_after_title = "";
        }
        $view->tmp_html_start = "";
        $view->tmp_html_filter = "";
        $view->tmp_html_col_after_title = "";
        $view->tmp_html_col_before_td_foot = "";
        $view->tmp_html_col_after_td_foot = "";
        $view->tmp_html_end = "";
		$view->tmp_html_filter_end = '';

        $app->triggerEvent('onBeforeDisplayListProductsView', array(&$view));
        $view->display();
    }

    function edit(){
        $app = Factory::getApplication();
        $app->input->set('hidemainmenu', true);
        $jshopConfig = JSFactory::getConfig();
        $lang = JSFactory::getLang();
        $app->triggerEvent('onLoadEditProduct', array());
        $id_vendor_cuser = HelperAdmin::getIdVendorForCUser();
        $category_id = $this->input->getInt('category_id') ?: $app->getUserStateFromRequest("jshoping.list.admin.product".'category_id', 'category_id', 0, 'int') ;
        $tmpl_extra_fields = null;
        $product_id = $this->input->getInt('product_id');
        $product_attr_id = $this->input->getInt('product_attr_id');

        //parent product
        if ($product_attr_id){
            $product_attr = JSFactory::getTable('productattribut');
            $product_attr->load($product_attr_id);
			if ($product_attr->ext_attribute_product_id){
                $product_id = $product_attr->ext_attribute_product_id;
            }else{
                $product = JSFactory::getTable('product');
                $product->parent_id = $product_attr->product_id;
                $product->store();
                $product_id = $product->product_id;
                $product_attr->ext_attribute_product_id = $product_id;
                $product_attr->store();
            }
        }

        if ($id_vendor_cuser && $product_id){
            HelperAdmin::checkAccessVendorToProduct($id_vendor_cuser, $product_id);
        }

        $products = JSFactory::getModel("products");

        $product = JSFactory::getTable('product');
        $product->load($product_id);
        $_productprice = JSFactory::getTable('productprice');
        $product->product_add_prices = $_productprice->getAddPrices($product_id);
        $product->name = $product->getName();
		$app->triggerEvent('onBeforeDisplayEditProductStart', array(&$product));

        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        $edit = intval($product_id);

        OutputFilter::objectHTMLSafe($product, ENT_QUOTES);

        if (!$product_id){
            $product->product_quantity = 1;
            $product->product_publish = 1;
        }

		$product->product_quantity = floatval($product->product_quantity);

        if ($edit){
            $images = $product->getImages();
            $videos = $product->getVideos();
            $files  = $product->getFiles();
            $categories_select = $product->getCategories();
            $categories_select_list = [];
            foreach($categories_select as $v){
                $categories_select_list[] = $v->category_id;
            }
            $related_products = $products->getRelatedProducts($product_id);
        } else {
            $images = [];
            $videos = [];
            $files = [];
            $categories_select = null;
            $categories_select_list = [];
            if ($category_id) {
                $categories_select_list[] = $category_id;
            }
            $related_products = [];
        }
        if ($jshopConfig->tax){
            $list_tax = SelectOptions::getTaxs();
            $withouttax = 0;
        }else{
            $withouttax = 1;
        }

        $categories = Helper::buildTreeCategory(0,1,0);
        if (count($categories)==0){
            JSError::raiseNotice(0, Text::_('JSHOP_PLEASE_ADD_CATEGORY'));
        }
        $lists['images'] = $images;
        $lists['videos'] = $videos;
        $lists['files'] = $files;

        $manufs = SelectOptions::getManufacturers(2);

        //Attributes
        $_attribut = JSFactory::getModel('attribut');
        $list_all_attributes = $_attribut->getAllAttributes(2, $categories_select_list, null, null, ['not_delete_for_category' => 1]);
        $_attribut_value = JSFactory::getModel('attributvalue');
        $lists['attribs'] = $product->getAttributes();
        $lists['attribs_dep_active'] = $products->getAttribsDependentActiveByAttrList($lists['attribs']);
        $lists['ind_attribs'] = $product->getAttributes2();
        $lists['attribs_indep_active'] = $products->getAttribsInDependentActiveByAttrList($lists['ind_attribs']);
        $lists['attribs_values'] = $_attribut_value->getAllAttributeValues(2);
        $all_attributes = $list_all_attributes['dependent'];
        $attr_hidden_for_category = $products->getAttribsHiddenForCategoryByAttrList($list_all_attributes);
        $lists['attrib_names'] = $products->getAttribsNamesByAttrList($list_all_attributes);
        foreach($attr_hidden_for_category as $_attr_id) {
            if (in_array($_attr_id, $lists['attribs_dep_active']) || in_array($_attr_id, $lists['attribs_indep_active'])) {
                JSError::raiseNotice(0, Text::_('JSHOP_ERROR_CONFIG_ATTRIBUTE').": ".$lists['attrib_names'][$_attr_id]);
            }
        }

        $lists['ind_attribs_gr'] = [];
        foreach($lists['ind_attribs'] as $v){
            $lists['ind_attribs_gr'][$v->attr_id][] = $v;
        }

		foreach ($lists['attribs'] as $key => $attribs){
            $lists['attribs'][$key]->count = floatval($attribs->count);
        }

        $first = [];
        $first[] = HTMLHelper::_('select.option', '0',Text::_('JSHOP_SELECT'), 'value_id','name');

        foreach ($all_attributes as $key => $value){
            $values_for_attribut = $_attribut_value->getAllValues($value->attr_id);
            $all_attributes[$key]->values_select = HTMLHelper::_('select.genericlist', array_merge($first, $values_for_attribut),'value_id['.$value->attr_id.']','class = "inputbox form-select" size = "5" multiple="multiple" id = "value_id_'.$value->attr_id.'"','value_id','name');
            $all_attributes[$key]->values = $values_for_attribut;
        }
        $lists['all_attributes'] = $all_attributes;
        $product_with_attribute = (count($lists['attribs']) > 0);

        //independent attribute
        $all_independent_attributes = $list_all_attributes['independent'];

        $price_modification = SelectOptions::getProductAttributPriceModify();

        foreach ($all_independent_attributes as $key => $value){
            $values_for_attribut = $_attribut_value->getAllValues($value->attr_id);
            $all_independent_attributes[$key]->values_select = HTMLHelper::_('select.genericlist', array_merge($first, $values_for_attribut),'attr_ind_id_tmp_'.$value->attr_id.'','class = "inputbox form-select middle2" ','value_id','name');
            $all_independent_attributes[$key]->values = $values_for_attribut;
            $all_independent_attributes[$key]->price_modification_select = HTMLHelper::_('select.genericlist', $price_modification,'attr_price_mod_tmp_'.$value->attr_id.'','class = "inputbox form-select small3" ','id','name');
            $all_independent_attributes[$key]->submit_button = '<input type="button" class="btn btn-primary" onclick = "jshopAdmin.addAttributValue2('.$value->attr_id.');" value = "'.Text::_('JSHOP_ADD_ATTRIBUT').'" />';
        }
        $lists['all_independent_attributes'] = $all_independent_attributes;
		$lists['dep_attr_button_add'] = '<input type="button" class="btn btn-primary" onclick="jshopAdmin.addAttributValue()" value="'.Text::_('JSHOP_ADD').'" />';
        // End work with attributes and values

        if ($jshopConfig->admin_show_delivery_time){
            $lists['deliverytimes'] = HTMLHelper::_('select.genericlist', SelectOptions::getDeliveryTimes(),'delivery_times_id','class = "inputbox form-select"','id','name',$product->delivery_times_id);
        }

        // units
        $allunits = SelectOptions::getUnits();
        if ($jshopConfig->admin_show_product_basic_price){
            $lists['basic_price_units'] = HTMLHelper::_('select.genericlist', $allunits, 'basic_price_unit_id','class = "inputbox form-select"','id','name',$product->basic_price_unit_id);
        }
        if (!$product->add_price_unit_id) $product->add_price_unit_id = $jshopConfig->product_add_price_default_unit;
        $lists['add_price_units'] = HTMLHelper::_('select.genericlist', $allunits, 'add_price_unit_id','class = "inputbox middle form-select"','id','name', $product->add_price_unit_id);
        //

        if ($jshopConfig->admin_show_product_labels){
            $lists['labels'] = HTMLHelper::_('select.genericlist', SelectOptions::getLabels(2), 'label_id','class = "inputbox form-select"','id','name',$product->label_id);
        }

        $lists['access'] = HTMLHelper::_('select.genericlist', SelectOptions::getAccessGroups(), 'access','class = "inputbox form-select"','id','title', $product->access);

        //currency
        $current_currency = $product->currency_id;
        if (!$current_currency) $current_currency = $jshopConfig->mainCurrency;
        $lists['currency'] = HTMLHelper::_('select.genericlist', SelectOptions::getCurrencies(), 'currency_id','class = "inputbox middle form-select"','currency_id','currency_code', $current_currency);

        // vendors
        $display_vendor_select = 0;
        if ($jshopConfig->admin_show_vendors){
            $lists['vendors'] = HTMLHelper::_('select.genericlist', SelectOptions::getVendors(0), 'vendor_id','class = "inputbox form-select"', 'id', 'name', $product->vendor_id);
            $display_vendor_select = 1;
            if ($id_vendor_cuser > 0) $display_vendor_select = 0;
        }
        //

        //product extra field
        if ($jshopConfig->admin_show_product_extra_field){
			$product->loadExtraFieldsData();
            $tmpl_extra_fields = $this->_getHtmlProductExtraFields($categories_select_list, $product);
        }
        //

        //free attribute
        if ($jshopConfig->admin_show_freeattributes){
            $_freeattributes = JSFactory::getModel("freeattribut");
            $listfreeattributes = $_freeattributes->getAll();
            $activeFreeAttribute = $product->getListFreeAttributes();
            $listIdActiveFreeAttribute = [];
            foreach($activeFreeAttribute as $_obj){
                $listIdActiveFreeAttribute[] = $_obj->id;
            }
            foreach($listfreeattributes as $k=>$v){
                if (in_array($v->id, $listIdActiveFreeAttribute)){
                    $listfreeattributes[$k]->pactive = 1;
                }
            }
        }

        $lists['manufacturers'] = HTMLHelper::_('select.genericlist', $manufs,'product_manufacturer_id','class = "inputbox form-select"','manufacturer_id','name',$product->product_manufacturer_id);

        if ($jshopConfig->tax){
            $tax_value = JSFactory::getModel("taxes")->getValue($product->product_tax_id);
        }else{
            $tax_value = 0;
        }

        if ($product_id){
            $product->product_price = Helper::formatEPrice($product->product_price);
            if ($jshopConfig->display_price_admin==0){
                $product->product_price2 = Helper::formatEPrice($product->product_price / (1 + $tax_value / 100));
            }else{
                $product->product_price2 = Helper::formatEPrice($product->product_price * (1 + $tax_value / 100));
            }
        }else{
            $product->product_price2 = '';
        }

        $category_select_onclick = 'onclick="';
        if ($jshopConfig->admin_show_product_extra_field){
            $category_select_onclick .= 'jshopAdmin.reloadProductExtraField(\''.$product_id.'\', \'\');';
        }
        if ($jshopConfig->product_use_main_category_id) {
            $category_select_onclick .= 'jshopAdmin.reloadSelectMainCategory(this);';
        }
        $category_select_onclick .= '"';

        if ($jshopConfig->tax){
            $lists['tax'] = HTMLHelper::_('select.genericlist', $list_tax,'product_tax_id','class = "inputbox form-select" onchange = "jshopAdmin.updatePrice2('.$jshopConfig->display_price_admin.');"','tax_id','tax_name',$product->product_tax_id);
        }
        $lists['categories'] = HTMLHelper::_('select.genericlist', $categories, 'category_id[]', 'class="inputbox form-select" size="10" multiple = "multiple" '.$category_select_onclick, 'category_id', 'name', $categories_select_list);
        $lists['templates'] = HelperAdmin::getTemplates('product', $product->product_template);

        $_product_option = JSFactory::getTable('productoption');
        $product_options = $_product_option->getProductOptions($product_id);
        $product->product_options = $product_options;

        if ($jshopConfig->return_policy_for_product){
            $_statictext = JSFactory::getModel("statictext");
            $first = [];
            $first[] = HTMLHelper::_('select.option', '0', Text::_('JSHP_STPAGE_return_policy'), 'id', 'alias');
            $statictext_list = $_statictext->getList(1);
            $product_options['return_policy'] = isset($product_options['return_policy']) ? $product_options['return_policy'] : "";
            $lists['return_policy'] = HTMLHelper::_('select.genericlist', array_merge($first, $statictext_list), 'options[return_policy]','class = "inputbox form-select"','id','alias', $product_options['return_policy']);
        }

        $app->triggerEvent('onBeforeDisplayEditProduct', array(&$product, &$related_products, &$lists, &$listfreeattributes, &$tax_value));

        $view=$this->getView("product_edit", 'html');
        $view->setLayout("default");
        $view->product = $product;
        $view->lists = $lists;
        $view->related_products = $related_products;
        $view->edit = $edit;
        $view->product_with_attribute = $product_with_attribute;
        $view->tax_value = $tax_value;
        $view->languages = $languages;
        $view->multilang = $multilang;
        $view->tmpl_extra_fields = $tmpl_extra_fields;
        $view->withouttax = $withouttax;
        $view->display_vendor_select = $display_vendor_select;
        $view->listfreeattributes = $listfreeattributes;
        $view->product_attr_id = $product_attr_id;
		$view->categories_select_list = $categories_select_list;
        $view->currency = $current_currency;
        $view->ind_attr_td_header = "";
        $view->dep_attr_td_header = "";
        $view->dep_attr_td_row_empty = "";
        $view->dep_attr_td_footer = "";
        $view->dep_attr_td_row = [];
        $view->ind_attr_td_row = [];
        $view->ind_attr_td_footer = [];
        foreach($languages as $lang){
            $view->{'plugin_template_description_'.$lang->language} = '';
            $view->{'plugin_template_top_description_'.$lang->language} = '';
        }
        $view->plugin_template_info='';
        $view->plugin_template_attribute='';
        $view->plugin_template_freeattribute='';
        $view->plugin_template_images='';
        $view->plugin_template_related='';
        $view->plugin_template_files='';
        $view->plugin_template_extrafields='';
        $view->plugin_template_video='';
        $app->triggerEvent('onBeforeDisplayEditProductView', array(&$view) );
		$view->display();
    }

    function save(){
        Session::checkToken() or die('Invalid Token');
        $model = JSFactory::getModel("products");
        $post = $model->getPrepareDataSave($this->input);
        if (!$product = $model->save($post)){
            JSError::raiseWarning("100", $model->getError());
            $this->setRedirect("index.php?option=com_jshopping&controller=products&task=edit&product_id=".$post['product_id']);
            return;
        }
        if (intval($product->parent_id)!=0){
            print "<script type='text/javascript'>window.close();</script>";
			return;
        }
        if ($this->getTask()=='apply'){
            $this->setRedirect("index.php?option=com_jshopping&controller=products&task=edit&product_id=".$product->product_id, Text::_('JSHOP_PRODUCT_SAVED'));
        }elseif ($this->getTask()=='save2new') {
            $this->setRedirect($this->getUrlEditItem());
        }else{
            $this->setRedirect("index.php?option=com_jshopping&controller=products", Text::_('JSHOP_PRODUCT_SAVED'));
        }
    }

    function editlist(){
        $cid = $this->input->getVar('cid');
        if (count($cid)==1){
            $this->setRedirect("index.php?option=com_jshopping&controller=products&task=edit&product_id=".$cid[0]);
            return 0;
        }
        $id_vendor_cuser = HelperAdmin::getIdVendorForCUser();
        $jshopConfig = JSFactory::getConfig();

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onLoadEditListProduct', []);

        $product = JSFactory::getTable('product');

        $all_taxes = JSFactory::getModel("taxes")->getAllTaxes();
        $list_tax = SelectOptions::getTaxs(0, 1);
        if (count($all_taxes)==0) $withouttax = 1; else $withouttax = 0;

        $categories = Helper::buildTreeCategory(0,1,0);
        $manufs = SelectOptions::getManufacturers(2, 1);        
        $languages = JSFactory::getModel("languages")->getAllLanguages(1);

        $price_modification = SelectOptions::getProductAttributPriceModify();
        $lists['price_mod_price'] = HTMLHelper::_('select.genericlist', $price_modification,'mod_price','class = "form-select"','id','name');
        $lists['price_mod_old_price'] = HTMLHelper::_('select.genericlist', $price_modification,'mod_old_price','class = "form-select"','id','name');
        if ($jshopConfig->admin_show_delivery_time) {
            $lists['deliverytimes'] = HTMLHelper::_('select.genericlist', SelectOptions::getDeliveryTimes(2, 1),'delivery_times_id','class = "inputbox form-select"','id','name');
        }
        if ($jshopConfig->admin_show_product_basic_price){
            $lists['basic_price_units'] = HTMLHelper::_('select.genericlist', SelectOptions::getUnits(), 'basic_price_unit_id','class = "inputbox form-select"','id','name');
        }
        if ($jshopConfig->admin_show_product_labels) {
            $lists['labels'] = HTMLHelper::_('select.genericlist', SelectOptions::getLabels(2, 1), 'label_id','class = "inputbox form-select"','id','name');
        }
        $lists['access'] = HTMLHelper::_('select.genericlist', SelectOptions::getAccessGroups(0, 1), 'access','class = "inputbox form-select"','id','title');

        //currency
        $current_currency = $product->currency_id;
        if (!$current_currency) $current_currency = $jshopConfig->mainCurrency;
        $lists['currency'] = HTMLHelper::_('select.genericlist', SelectOptions::getCurrencies(), 'currency_id','class = "inputbox form-select"','currency_id','currency_code', $current_currency);

        // vendors
        $display_vendor_select = 0;
        if ($jshopConfig->admin_show_vendors){
            $lists['vendors'] = HTMLHelper::_('select.genericlist', SelectOptions::getVendors('- - -'), 'vendor_id','class = "inputbox form-select"', 'id', 'name');
            $display_vendor_select = 1;
            if ($id_vendor_cuser > 0) $display_vendor_select = 0;
        }

		//extra field
        if ($jshopConfig->admin_show_product_extra_field) {
            $tmpl_extra_fields = $this->_getHtmlProductExtraFields([], null, 'list');
        }
        //

        $lists['product_publish'] = HTMLHelper::_('select.genericlist', SelectOptions::getPublishGroup(), 'product_publish', 'class = "inputbox form-select"', 'value', 'name');
        $lists['manufacturers'] = HTMLHelper::_('select.genericlist', $manufs,'product_manufacturer_id','class = "inputbox form-select"','manufacturer_id','name');
        $lists['tax'] = HTMLHelper::_('select.genericlist', $list_tax,'product_tax_id','class = "inputbox form-select"','tax_id','tax_name');
        $lists['categories'] = HTMLHelper::_('select.genericlist', $categories, 'category_id[]', 'class="inputbox form-select" size="10" multiple = "multiple" ', 'category_id', 'name');
        $lists['templates'] = HelperAdmin::getTemplates('product', "", 1);
        $lists['add_new_related'] = HTMLHelper::_('select.genericlist', SelectOptions::getGroupOptionEditType(), 'add_new_related','class="inputbox form-select"','id','name');

        $view = $this->getView("product_edit", 'html');
        $view->setLayout("editlist");
        $view->set('lists', $lists);
        $view->set('cid', $cid);
        $view->set('config', $jshopConfig);
        $view->set('withouttax', $withouttax);
        $view->set('display_vendor_select', $display_vendor_select);
		$view->set('tmpl_extra_fields', $tmpl_extra_fields);
        $view->languages = $languages;
        $view->set('related_products', []);
        $view->set('etemplatevar', '');
        $dispatcher->triggerEvent('onBeforeDisplayEditListProductView', array(&$view));
        $view->editGroup();
    }

    function savegroup(){
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforSaveListProduct', []);
        $cid = $this->input->getVar('cid');
        $post = $this->input->post->getArray();
        $model = JSFactory::getModel("products");
        foreach($cid as $id){
			$model->productGroupUpdate($id, $post);
        }
        $dispatcher->triggerEvent('onAfterSaveListProductEnd', array($cid, $post) );
        $this->setRedirect("index.php?option=com_jshopping&controller=products", Text::_('JSHOP_PRODUCT_SAVED'));
    }

    function copy(){
        $cid = $this->input->getVar('cid');
        $text = JSFactory::getModel("products")->copyProducts($cid);
        $this->setRedirect("index.php?option=com_jshopping&controller=products", implode("</li><li>",$text));
    }

    function order(){
        $order = $this->input->getVar("order");
        $product_id = $this->input->getInt("product_id");
        $number = $this->input->getInt("number");
        $category_id = $this->input->getInt("category_id");
        JSFactory::getModel("products")->orderProductInCategory($product_id, $category_id, $number, $order);
        $this->setRedirect("index.php?option=com_jshopping&controller=products&category_id=".$category_id);
    }

    function saveorder(){
        $category_id = $this->input->getInt("category_id");
        $cid = $this->input->get('cid', array(), 'array');
        $order = $this->input->get('order', array(), 'array');
        JSFactory::getModel("products")->saveOrderProductInCategory($cid, $order, $category_id);
        if ($this->input->getInt('ajax')) {
            $this->app->close();
        } else {
            $this->setRedirect("index.php?option=com_jshopping&controller=products&category_id=".$category_id);
        }
    }

    function cancel(){
        $this->setRedirect("index.php?option=com_jshopping&controller=products");
    }

    function delete_foto(){
        $image_id = $this->input->getInt("id");
        JSFactory::getModel("products")->deleteImage($image_id);
        die();
    }

    function delete_video(){
        $video_id = $this->input->getInt("id");
        JSFactory::getModel("products")->deleteVideo($video_id);
        die();
    }

    function delete_file(){
        $id = $this->input->getInt("id");
        $type = $this->input->getVar("type");
        print JSFactory::getModel("products")->deleteFile($id, $type);
        die();
    }

    function search_related(){
        $jshopConfig = JSFactory::getConfig();
        $products = JSFactory::getModel("products");

        $text_search = $this->input->getVar("text");
        $limitstart = $this->input->getInt("start");
        $no_id = $this->input->getInt("no_id");
        $limit = $this->input->getInt("limit", $jshopConfig->admin_count_related_search);

        $filter = array("without_product_id"=>$no_id, "text_search"=>$text_search);
        $total = $products->getCountAllProducts($filter);
        $rows = $products->getAllProducts($filter, $limitstart, $limit);
        $page = ceil($total/$limit);

        $view = $this->getView("product_list", 'html');
        $view->setLayout("search_related");
        $view->set('rows', $rows);
        $view->set('config', $jshopConfig);
        $view->set('limit', $limit);
        $view->set('pages', $page);
        $view->set('no_id', $no_id);
		$view->tmp_html_start = '';
        $view->tmp_html_end = '';
        $view->display();
        die();
    }

    function product_extra_fields(){
        $product_id = $this->input->getInt("product_id");
        $cat_id = $this->input->getVar("cat_id");
		$edittype = $this->input->getVar("edittype");
        $product = JSFactory::getTable('product');
        $product->load($product_id);
        $product->loadExtraFieldsData();

        $categorys = array();
        if (is_array($cat_id)){
            foreach($cat_id as $cid){
                $categorys[] = intval($cid);
            }
        }

        print $this->_getHtmlProductExtraFields($categorys, $product, $edittype);
        die();
    }

    function product_extra_fields_hide(){
        $cat_id = $this->input->getVar("cat_id");
        $categorys = array();
        if (is_array($cat_id)){
            foreach($cat_id as $cid){
                $categorys[] = intval($cid);
            }
        }
        $_productfields = JSFactory::getModel("productfields");
        $list = $_productfields->getList(1);
        $ch_active = array_keys($_productfields->getListForCats($categorys));
        $fields = [];
        foreach($list as $v){
            $insert = intval(in_array($v->id, $ch_active));
            $row_class = $insert ? '' : 'hide';
            $fields[] = ['id' => $v->id, 'row_class' => $row_class];
        }
        print json_encode($fields);
        die();
    }

    function _getHtmlProductExtraFields($categorys = [], $product = null, $edittype = '', $hide = 0){
        $jshopConfig = JSFactory::getConfig();
		if ($product === null) $product = new \stdClass;
		$_productfields = JSFactory::getModel("productfields");
        $list = $_productfields->getList(1);
        $ch_active = array_keys($_productfields->getListForCats($categorys));

        $_productfieldvalues = JSFactory::getModel("productfieldvalues");
        $listvalue = $_productfieldvalues->getAllList(10, $jshopConfig->admin_ordering_extra_field_values_in_select);

        $f_option = array();
		if ($edittype == 'list') {
			$f_option[] = HTMLHelper::_('select.option', -1, " - - - ", 'id', 'name');
			$f_option[] = HTMLHelper::_('select.option', 0, ' - '.Text::_('JSHOP_NONE').' - ', 'id', 'name');
		} else {
			$f_option[] = HTMLHelper::_('select.option', 0, " - - - ", 'id', 'name');
		}

        $fields = [];
        foreach($list as $v) {
			if ($edittype == 'list') {
				$insert = 1;
			} else {
                $insert = intval(in_array($v->id, $ch_active));
            }
            $html_hide = $insert ? 0 : 1;
            if (!$hide) {
                $insert = 1;
            }
            if ($edittype == 'list' && $v->type == 2) {
                $insert = 0;
            }
            if ($insert){
                $obj = new \stdClass();
                $obj->id = $v->id;
                $obj->name = $v->name;
                $obj->group = $v->group;
                $obj->groupname = $v->groupname;
                $obj->row_class = $html_hide ? 'hide' : '';
                $name = 'extra_field_'.$v->id;
                if ($v->type == 0) {
                    if ($v->multilist==1){
                        $attr = 'multiple="multiple" size="10" class="form-select" ';
                    }else{
                        $attr = "class = 'form-select' ";
                    }
                    $tmp = [];
                    foreach($listvalue as $lv){
                        if ($lv->field_id==$v->id) $tmp[] = $lv;
                    }
                    $obj->values = HTMLHelper::_('select.genericlist', array_merge($f_option, $tmp), 'productfields['.$name.'][]', $attr, 'id', 'name', explode(',', $product->$name ?? ''));
                    $view = $this->getView("product_edit", 'html');
                    $view->setLayout("extrafields_btn_add");
                    $view->title = Text::_('JSHOP_ADD_NEW_OPTION_FOR').' "'.$v->name.'"';
                    $obj->btn = $view->loadTemplate();
                } elseif ($v->type > 1) {
                    if ($edittype == 'list' && $v->type == 3) {
                        $val = '-1';
                        $val_text = ' - - - ';
                    } else {
                        $val = $product->$name ?? '0';
                        $val_text = isset($product->$name) ? ($listvalue[$product->$name]->name ?? '') : '';
                    }
                    $obj->values = "<input type='hidden' name='".'productfields['.$name.'][]'."' value='".$val."'>";
                    $obj->values .= "<span class='prod_extra_fields_uniq_val'>".$val_text."</span>";
                    $view = $this->getView("product_edit", 'html');
                    $view->setLayout("extrafields_btn_edit");
                    $view->title = Text::_('JSHOP_EDIT').' "'.$v->name.'"';
                    $obj->btn = $view->loadTemplate();
                } else {
                    $obj->values = "<input type='text' class='form-control' name='".$name."' value='".($product->$name ?? '')."' />";
                }
                $fields[] = $obj;
            }
        }
        $view = $this->getView("product_edit", 'html');
        $view->setLayout("extrafields_inner");
        $view->set('fields', $fields);
		$view->set('product', $product);
		$view->set('categorys', $categorys);
		$view->set('edittype', $edittype);
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeLoadTemplateHtmlProductExtraFields', array(&$view));
        return $view->loadTemplate();
    }

    function getfilesale(){
        $id = $this->input->getVar('id');

        $model = JSFactory::getModel('productdownload', 'Site');
        $model->setId($id);
        $file_name = $model->getFile();

        ob_end_clean();
        @set_time_limit(0);
        $model->downloadFile($file_name);
        die();
    }

    function loadproductinfo(){
        $jshopConfig = JSFactory::getConfig();
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onLoadInfoProduct', array());
        $id_vendor_cuser = HelperAdmin::getIdVendorForCUser();
        $product_id = $this->input->getInt('product_id');
        $layout = $this->input->getVar('layout','productinfo_json');
        $display_price = $this->input->getVar('display_price');
        $jshopConfig->setDisplayPriceFront($display_price);

        if ($id_vendor_cuser && $product_id){
            HelperAdmin::checkAccessVendorToProduct($id_vendor_cuser, $product_id);
        }

        $product = JSFactory::getTable('product');
        $product->load($product_id);
        $product->getDescription();
		$count_attributes = count($product->getRequireAttribute());
        $product_price = $product->getPrice();

        $res = array();
        $res['product_id'] = $product->product_id;
		$res['category_id'] = $product->getCategory();
        $res['product_ean'] = $product->product_ean;
        $res['manufacturer_code'] = $product->getManufacturerCode();
        $res['real_ean'] = $product->getRealEan();
        $res['product_price'] = $product_price;
        $res['delivery_times_id'] = $product->delivery_times_id;
        $res['vendor_id'] = Helper::fixRealVendorId($product->vendor_id);
        $res['product_weight'] = $product->product_weight;
        $res['product_tax'] = $product->getTax();
        $res['product_name'] = $product->name;
        $res['count_attributes'] = $count_attributes;
		$res['thumb_image'] = Helper::getPatchProductImage($product->image, 'thumb');

        $view = $this->getView("product_edit", 'html');
        $view->setLayout($layout);
        $view->set('res', $res);
        $view->set('edit', null);
        $view->set('product', $product);
        $dispatcher->triggerEvent('onBeforeDisplayLoadInfoProduct', array(&$view) );
        $view->display();
    die();
    }

	function getvideocode() {
		$video_id = $this->input->getInt('video_id');
		$productvideo = JSFactory::getTable('productvideo');
		$productvideo->load($video_id);

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onAfterLoadVideoCodeForProduct', array(&$productvideo));

		$view=$this->getView('product_edit', 'html');
        $view->setLayout('product_video_code');
        $view->set('code', $productvideo->video_code);

		$dispatcher->triggerEvent('onBeforeDisplayVideoCodeForProduct', array(&$view) );
        $view->display();
		die();
	}

    function getattributes(){
        $jshopConfig = JSFactory::getConfig();
        $product_id = $this->input->getInt('product_id');
        $num = $this->input->getInt('num');
        $admin_load_user_id = $this->input->getInt('admin_load_user_id');
        $id_currency = $this->input->getInt('id_currency');
        $display_price = $this->input->getVar('display_price');
        $jshopConfig->setDisplayPriceFront($display_price);

        $product = JSFactory::getTable('product');
        $product->load($product_id);
        $attributesDatas = $product->getAttributesDatas();
        $product->setAttributeActive($attributesDatas['attributeActive']);
        $attributeValues = $attributesDatas['attributeValues'];

        $attributes = $product->getBuildSelectAttributes($attributeValues, $attributesDatas['attributeSelected'], 1);

        $_attributevalue = JSFactory::getTable('Attributvalue');
        $all_attr_values = $_attributevalue->getAllAttributeValues();

        $product->getExtendsData();

        $urlupdateprice = 'index.php?option=com_jshopping&controller=products&task=ajax_attrib_select_and_price&product_id='.$product_id.'&ajax=1&admin_load_user_id='.$admin_load_user_id.'&id_currency='.$id_currency.'&display_price='.$display_price;

        $view = $this->getView("product_edit", 'html');
        $view->setLayout('product_attribute_select');
        $view->set('attributes', $attributes);
        $view->set('product', $product);
        $view->set('num', $num);
        $view->set('config', $jshopConfig);
        $view->set('image_path', $jshopConfig->live_path.'/images');
        $view->set('noimage', $jshopConfig->noimage);
        $view->set('all_attr_values', $all_attr_values);
        $view->set('urlupdateprice', $urlupdateprice);
        Factory::getApplication()->triggerEvent('onBeforeDisplayGetAttributes', array(&$view) );
        $view->display();
    }

    function ajax_attrib_select_and_price(){
        $request = $this->input->getArray();
        $display_price = $this->input->getVar('display_price');
        JSFactory::getConfig()->setDisplayPriceFront($display_price);
        $product_id = $this->input->getInt('product_id');
        $change_attr = $this->input->getInt('change_attr');
        $qty = Request::getQuantity('qty', 1);
		$attribs = Request::getAttribute('attr');
        $freeattr = Request::getFreeAttribute('freeattr');

        $model = JSFactory::getModel('productajaxrequest', 'Site');
		$model->setData($product_id, $change_attr, $qty, $attribs, $freeattr, $request);
        $model->displayonlyattrtype = 1;
		print $model->getProductDataJson();
		die();
    }

    function get_product_extrafield_value(){
        $product_id = $this->input->getInt("product_id");
        $ef_id = $this->input->getInt("ef_id");
        $ef_val_id = $this->input->getInt("ef_val_id");
        $productfield = JSFactory::getTable('productfield');
        $productfield->load($ef_id);

        if ($ef_val_id) {
            $value = $ef_val_id;
        } else {
            $product = JSFactory::getTable('product');
            $product->load($product_id);
            $ef_data = $product->getExtraFieldsData();
            $value = $ef_data['extra_field_'.$ef_id] ?? '';
        }
        $res = [];
        $res['type'] = $productfield->type;
        if ($productfield->type > 1) {
            $productfieldvalue = JSFactory::getTable('productfieldvalue');
            $productfieldvalue->load($value);            
            $res['id'] = $productfieldvalue->id ?? 0;
            $res['name'] = [];
            $langs = JSFactory::getModel("languages")->getAllLanguages(1);
            foreach($langs as $lang) {
                $res['name'][$lang->language] = $productfieldvalue->{'name_'.$lang->language};
            }            
        } else {
            $res['value'] = $value;
        }
        print json_encode($res);
		die();
    }

}