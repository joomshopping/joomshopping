<?php
/**
* @version      5.6.0 10.03.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\Component\Jshopping\Site\Helper\Error as JSError;
use Joomla\Component\Jshopping\Site\Helper\SelectOptions;

defined('_JEXEC') or die();

class ConfigController extends BaseadminController{
    
    public function init(){
        $this->registerTask('apply', 'save');
        $this->registerTask('applyseo', 'saveseo');
        $this->registerTask('applystatictext', 'savestatictext');
        HelperAdmin::checkAccessController("config");
        HelperAdmin::addSubmenu("config");        
    }

    function display($cachable = false, $urlparams = false){
        $app = Factory::getApplication();
        $jshopConfig = JSFactory::getConfig();        
        $current_currency = JSFactory::getTable('currency');
        $current_currency->load($jshopConfig->mainCurrency);
        if ($current_currency->currency_value!=1){
            $app->enqueueMessage(Text::_('JSHOP_ERROR_MAIN_CURRENCY_VALUE'), 'message');
        }
        $view = $this->getView("panel", 'html');
        $view->setLayout("config");
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";

        $view->displayConfig();
    }
    
    function general(){
	    $jshopConfig = JSFactory::getConfig();

        $lists['languages'] = HTMLHelper::_('select.genericlist', Helper::getAllLanguages(), 'defaultLanguage', 'class = "form-select"', 'language', 'name', $jshopConfig->defaultLanguage);
        $display_price_list = SelectOptions::getPriceType();
        $lists['display_price_admin'] = HTMLHelper::_('select.genericlist', $display_price_list, 'display_price_admin', 'class = "form-select"', 'id', 'name', $jshopConfig->display_price_admin);
        $lists['display_price_front'] = HTMLHelper::_('select.genericlist', $display_price_list, 'display_price_front', 'class = "form-select"', 'id', 'name', $jshopConfig->display_price_front);
        $lists['template'] = HelperAdmin::getShopTemplatesSelect($jshopConfig->template);

    	$view = $this->getView("config", 'html');
        $view->setLayout("general");
        $view->set('etemplatevar', '');
		$view->set("lists", $lists);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditConfigGeneral', array(&$view));
        $view->display();
    }
    
    function catprod(){
        $jshopConfig = JSFactory::getConfig();
        
        $displayprice = array();
        $displayprice[] = HTMLHelper::_('select.option', 0, Text::_('JSHOP_YES'), 'id', 'value');
        $displayprice[] = HTMLHelper::_('select.option', 1, Text::_('JSHOP_NO'), 'id', 'value');
        $displayprice[] = HTMLHelper::_('select.option', 2, Text::_('JSHOP_ONLY_REGISTER_USER'), 'id', 'value');
        $lists['displayprice'] = HTMLHelper::_('select.genericlist', $displayprice, 'displayprice','class = "form-select"','id','value', $jshopConfig->displayprice);
        
        $catsort = array();
        $catsort[] = HTMLHelper::_('select.option', 1, Text::_('JSHOP_SORT_MANUAL'), 'id','value');
        $catsort[] = HTMLHelper::_('select.option', 2, Text::_('JSHOP_SORT_ALPH'), 'id','value');
        $lists['category_sorting'] = HTMLHelper::_('select.genericlist', $catsort, 'category_sorting','class = "form-select"','id','value', $jshopConfig->category_sorting);
        $lists['manufacturer_sorting'] = HTMLHelper::_('select.genericlist', $catsort, 'manufacturer_sorting','class = "form-select"','id','value', $jshopConfig->manufacturer_sorting);
        
        $sortd = SelectOptions::getSortingDirection();
        $lists['product_sorting_direction'] = HTMLHelper::_('select.genericlist', $sortd, 'product_sorting_direction','class = "form-select"','id','value', $jshopConfig->product_sorting_direction);
        
        $opt = array();
        $opt[] = HTMLHelper::_('select.option', 'V.value_ordering', Text::_('JSHOP_SORT_MANUAL'), 'id','value');
        $opt[] = HTMLHelper::_('select.option', 'value_name', Text::_('JSHOP_SORT_ALPH'), 'id','value');
        $opt[] = HTMLHelper::_('select.option', 'PA.price', Text::_('JSHOP_SORT_PRICE'), 'id','value');
        $opt[] = HTMLHelper::_('select.option', 'PA.ean', Text::_('JSHOP_EAN_PRODUCT'), 'id','value');
        $opt[] = HTMLHelper::_('select.option', 'PA.count', Text::_('JSHOP_QUANTITY_PRODUCT'), 'id','value');
        $opt[] = HTMLHelper::_('select.option', 'PA.product_attr_id', Text::_('JSHOP_SPECIFIED_IN_PRODUCT'), 'id','value');
        $lists['attribut_dep_sorting_in_product'] = HTMLHelper::_('select.genericlist', $opt, 'attribut_dep_sorting_in_product','class="form-select"','id','value', $jshopConfig->attribut_dep_sorting_in_product);
        $lists['attribut_dep_sorting_in_product_dir'] = HTMLHelper::_('select.genericlist', $sortd, 'attribut_dep_sorting_in_product_dir','class="form-select"','id','value', $jshopConfig->attribut_dep_sorting_in_product_dir);
        
        $opt = array();
        $opt[] = HTMLHelper::_('select.option', 'V.value_ordering', Text::_('JSHOP_SORT_MANUAL'), 'id','value');
        $opt[] = HTMLHelper::_('select.option', 'value_name', Text::_('JSHOP_SORT_ALPH'), 'id','value');
        $opt[] = HTMLHelper::_('select.option', 'addprice', Text::_('JSHOP_SORT_PRICE'), 'id','value');
        $opt[] = HTMLHelper::_('select.option', 'PA.id', Text::_('JSHOP_SPECIFIED_IN_PRODUCT'), 'id','value');
        $lists['attribut_nodep_sorting_in_product'] = HTMLHelper::_('select.genericlist', $opt, 'attribut_nodep_sorting_in_product','class = "form-select"','id','value', $jshopConfig->attribut_nodep_sorting_in_product);        
        $lists['attribut_nodep_sorting_in_product_dir'] = HTMLHelper::_('select.genericlist', $sortd, 'attribut_nodep_sorting_in_product_dir','class="form-select"','id','value', $jshopConfig->attribut_nodep_sorting_in_product_dir);
        $product_sorting_options = SelectOptions::getProductSorting();
        $lists['product_sorting'] = HTMLHelper::_('select.genericlist', $product_sorting_options, "product_sorting", 'class = "form-select"', 'id', 'name', $jshopConfig->product_sorting);
        
        if ($jshopConfig->admin_show_product_extra_field){
            $_productfields = JSFactory::getModel("productfields");
            $rows = $_productfields->getList();
            $lists['product_list_display_extra_fields'] = HTMLHelper::_('select.genericlist', $rows, "product_list_display_extra_fields[]", ' class = "form-select" size="10" multiple = "multiple" ', 'id','name', $jshopConfig->getProductListDisplayExtraFields() );
            $lists['filter_display_extra_fields'] = HTMLHelper::_('select.genericlist', $rows, "filter_display_extra_fields[]", ' class = "form-select" size="10" multiple = "multiple" ', 'id','name', $jshopConfig->getFilterDisplayExtraFields() );
            $lists['product_hide_extra_fields'] = HTMLHelper::_('select.genericlist', $rows, "product_hide_extra_fields[]", ' class = "form-select" size="10" multiple = "multiple" ', 'id','name', $jshopConfig->getProductHideExtraFields() );
            $lists['cart_display_extra_fields'] = HTMLHelper::_('select.genericlist', $rows, "cart_display_extra_fields[]", ' class = "form-select" size="10" multiple = "multiple" ', 'id','name', $jshopConfig->getCartDisplayExtraFields() );
        }

        $lists['units'] = HTMLHelper::_('select.genericlist', SelectOptions::getUnits(), "main_unit_weight", 'class = "form-select"', 'id','name', $jshopConfig->main_unit_weight);        
            
        $view = $this->getView("config", 'html');
        $view->setLayout("categoryproduct");
        $view->lists = $lists;
        $view->etemplatevar = '';
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $app = Factory::getApplication();
        $app->triggerEvent('onBeforeEditConfigCatProd', array(&$view));
        $view->display();
    }
    
    function checkout(){
        $jshopConfig = JSFactory::getConfig();

        $lists['status'] = HTMLHelper::_('select.genericlist', SelectOptions::getOrderStatus(), 'default_status_order', 'class = "inputbox form-select"', 'status_id', 'name', $jshopConfig->default_status_order);
        $currency_code = Helper::getMainCurrencyCode();        
        $lists['default_country'] = HTMLHelper::_('select.genericlist', SelectOptions::getCountrys(3), 'default_country','class = "inputbox form-select"','country_id','name', $jshopConfig->default_country);
        
        $vendor_order_message_type = array();
        $vendor_order_message_type[] = HTMLHelper::_('select.option', 0, Text::_('JSHOP_NOT_SEND_MESSAGE'), 'id', 'name' );
        $vendor_order_message_type[] = HTMLHelper::_('select.option', 1, Text::_('JSHOP_WE_SEND_MESSAGE'), 'id', 'name' );
        $vendor_order_message_type[] = HTMLHelper::_('select.option', 2, Text::_('JSHOP_WE_SEND_ORDER'), 'id', 'name' );
        $vendor_order_message_type[] = HTMLHelper::_('select.option', 3, Text::_('JSHOP_WE_ALWAYS_SEND_ORDER'), 'id', 'name' );
        $lists['vendor_order_message_type'] = HTMLHelper::_('select.genericlist', $vendor_order_message_type, 'vendor_order_message_type','class = "inputbox form-select" size = "1"','id','name', $jshopConfig->vendor_order_message_type);
        
		$option = array();
        $option[] = HTMLHelper::_('select.option', 0, Text::_('JSHOP_STEP_3_4'), 'id', 'name');
        $option[] = HTMLHelper::_('select.option', 1, Text::_('JSHOP_STEP_4_3'), 'id', 'name');
        $lists['step_4_3'] = HTMLHelper::_('select.genericlist', $option, 'step_4_3','class = "inputbox form-select"','id','name', $jshopConfig->step_4_3);

        $view = $this->getView("config", 'html');
        $view->setLayout("checkout");
        $view->set("lists", $lists); 
        $view->set("currency_code", $currency_code);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditConfigCheckout', array(&$view));
        $view->display();
    }

    function fieldregister(){
        $jshopConfig = JSFactory::getConfig();
        $view = $this->getView("config", 'html');
        $view->setLayout("fieldregister");
        $config = new \stdClass();
        include($jshopConfig->path.'config/default_config.php');

        $current_fields = $jshopConfig->getListFieldsRegister();
        $view->set("fields", $fields_client);
        $view->set("current_fields", $current_fields);
        $view->set("fields_sys", $fields_client_sys);
        $view->set('etemplatevar', '');

        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditConfigFieldRegister', array(&$view));
        $view->display();
    }

    function adminfunction(){
        $jshopConfig = JSFactory::getConfig();
        $shop_register_type = array();
        $shop_register_type[] = HTMLHelper::_('select.option', 0, "-", 'id', 'name' );
        $shop_register_type[] = HTMLHelper::_('select.option', 1, Text::_('JSHOP_MEYBY_SKIP_REGISTRATION'), 'id', 'name' );
        $shop_register_type[] = HTMLHelper::_('select.option', 2, Text::_('JSHOP_WITHOUT_REGISTRATION'), 'id', 'name' );
        $lists['shop_register_type'] = HTMLHelper::_('select.genericlist', $shop_register_type, 'shop_user_guest','class = "inputbox form-select" size = "1"','id','name', $jshopConfig->shop_user_guest);
        
        $opt = array();
        $opt[] = HTMLHelper::_('select.option', 0, Text::_('JSHOP_NORMAL'), 'id', 'name');
        $opt[] = HTMLHelper::_('select.option', 1, Text::_('JSHOP_DEVELOPER'), 'id', 'name');
        $lists['shop_mode'] = HTMLHelper::_('select.genericlist', $opt, 'shop_mode','class = "inputbox form-select"','id','name', $jshopConfig->shop_mode);

        $view = $this->getView("config", 'html');
        $view->setLayout("adminfunction");
        $view->set("lists", $lists);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditConfigAdminFunction', array(&$view));
        $view->display();
    }

    function currency(){
    	$jshopConfig = JSFactory::getConfig();
		
		$lists['currencies'] = HTMLHelper::_('select.genericlist', SelectOptions::getCurrencies(), 'mainCurrency','class = "inputbox form-select"','currency_id','currency_code',$jshopConfig->mainCurrency);
		
		$i = 0;
		foreach($jshopConfig->format_currency as $key => $value){
            $currenc[$i] = new \stdClass();
			$currenc[$i]->id_cur = $key;
			$currenc[$i]->format = $value;
			$i++;
		}
		$lists['format_currency'] = HTMLHelper::_('select.genericlist', $currenc, 'currency_format','class = "inputbox form-select"', 'id_cur', 'format', $jshopConfig->currency_format);
				
        $view = $this->getView("config", 'html');
        $view->setLayout("currency");
		$view->set("lists", $lists);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditConfigCurrency', array(&$view));
        $view->display();
    }
    
    function image(){
        $jshopConfig = JSFactory::getConfig();
        
        $resize_type = array();
        $resize_type[] = HTMLHelper::_('select.option', 0, Text::_('JSHOP_CUT'), 'id', 'name' );
        $resize_type[] = HTMLHelper::_('select.option', 1, Text::_('JSHOP_FILL'), 'id', 'name' );
        $resize_type[] = HTMLHelper::_('select.option', 2, Text::_('JSHOP_STRETCH'), 'id', 'name' );
        $select_resize_type = HTMLHelper::_('select.genericlist', $resize_type, 'image_resize_type','class = "inputbox form-select"','id','name', $jshopConfig->image_resize_type);
		$image_fill_colors = HTMLHelper::_('select.genericlist', $jshopConfig->image_fill_colors, 'image_fill_color','class = "inputbox form-select"', 'id', 'name', $jshopConfig->image_fill_color);
    	
    	$view = $this->getView("config", 'html');
        $view->setLayout("image");
        $view->set("select_resize_type", $select_resize_type);
		$view->set("image_fill_colors", $image_fill_colors);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditConfigImage', array(&$view));
        $view->display();
    }
    
    function storeinfo(){
        $vendor = JSFactory::getTable('vendor');
        $vendor->loadMain();
		$lists['countries'] = HTMLHelper::_('select.genericlist', SelectOptions::getCountrys(3), 'country', 'class = "inputbox form-select"', 'country_id', 'name', $vendor->country);
        
        OutputFilter::objectHTMLSafe($vendor, ENT_QUOTES);
        
    	$view = $this->getView("config", 'html');
        $view->setLayout("storeinfo");
        $view->set("lists", $lists); 
		$view->set("vendor", $vendor);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditConfigStoreInfo', array(&$view));
        $view->display();
    }
    
    function save(){
	    $jshopConfig = JSFactory::getConfig();
		$tab = $this->input->getVar('tab');
		$db = Factory::getDBO();
        
        $dispatcher = Factory::getApplication();
		$extconf = array('imageheader'=>'header.jpg', 'imagefooter'=>'footer.jpg');	
		$post = $this->input->post->getArray();
        $dispatcher->triggerEvent('onBeforeSaveConfig', array(&$post, &$extconf));

        JSFactory::getModel("addondependencies")->clearSystemAddons();
        
        if ($tab == 5){
            $vendor = JSFactory::getTable('vendor');
            $post = $this->input->post->getArray();
            $vendor->id = $post['vendor_id'];
            $vendor->main = 1;
            $vendor->bind($post);
            $vendor->store();
        }
        
        if ($tab == 7){
            if (!$post['next_order_number']){
                unset($post['next_order_number']);
            }            
        }
                
        if ($tab == 8){
            $post['without_shipping'] = intval(!$post['without_shipping']);
            $post['without_payment'] = intval(!$post['without_payment']);
        }
        
        if ($tab == 9){
            $config = new \stdClass();
            include($jshopConfig->path.'config/default_config.php');
                        
            foreach($fields_client_sys as $k=>$v){
                foreach($v as $v2){
                    if (!isset($post['field'][$k][$v2])) $post['field'][$k][$v2] = [];
                    $post['field'][$k][$v2]['require'] = 1;
                    $post['field'][$k][$v2]['display'] = 1;
                }
            }
            foreach($post['field'] as $k=>$v){
                foreach($v as $k2=>$v2){
                    if (!isset($post['field'][$k][$k2])) $post['field'][$k][$k2] = [];
                    if (!isset($post['field'][$k][$k2]['display']) || !$post['field'][$k][$k2]['display']){
                        $post['field'][$k][$k2]['require'] = 0;
                    }
                }
            }

            $post['fields_register'] = serialize($post['field']);
        }
        
        if ($tab != 4){
		    $config = JSFactory::getTable('Config');
		    $config->id = $jshopConfig->load_id;
		    if (!$config->bind($post, ['task', 'tab', 'option', 'controller'])) {
			    JSError::raiseWarning("",Text::_('JSHOP_ERROR_BIND'));
			    $this->setRedirect('index.php?option=com_jshopping&controller=config');
			    return 0;
		    }
            
            if ($tab==6 && $jshopConfig->admin_show_product_extra_field){
                $config->setProductListDisplayExtraFields($post['product_list_display_extra_fields'] ?? []);
                $config->setFilterDisplayExtraFields($post['filter_display_extra_fields'] ?? []);
                $config->setProductHideExtraFields($post['product_hide_extra_fields'] ?? []);
                $config->setCartDisplayExtraFields($post['cart_display_extra_fields'] ?? []);
            }
		    
		    $config->transformPdfParameters();
		    if (!$config->store()) {
			    JSError::raiseWarning("", Text::_('JSHOP_ERROR_SAVE_DATABASE'));
			    $this->setRedirect('index.php?option=com_jshopping&controller=config');
			    return 0;
		    }
            
        }

		if (isset($_FILES['header'])){
			if ($_FILES['header']['size']){
				@unlink($jshopConfig->path."images/".$extconf['imageheader']);
				move_uploaded_file($_FILES['header']['tmp_name'], $jshopConfig->path."images/".$extconf['imageheader']);
			}
		}
	
		if (isset($_FILES['footer'])){
			if ($_FILES['footer']['size']){
				@unlink($jshopConfig->path."images/".$extconf['imagefooter']);
				move_uploaded_file($_FILES['footer']['tmp_name'], $jshopConfig->path."images/".$extconf['imagefooter']);
			}
		}
        
        if (isset($post['update_count_prod_rows_all_cats']) && $tab==6 && $post['update_count_prod_rows_all_cats']){
            $query = "update `#__jshopping_categories` set `products_page`=0, `products_row`=0";
            $db->setQuery($query);
            $db->execute();
            $query = "update `#__jshopping_manufacturers` set `products_page`=0, `products_row`=0";
            $db->setQuery($query);
            $db->execute();
        }

        $dispatcher->triggerEvent('onAfterSaveConfig', array());
        
        if ($this->getTask()=='apply'){
            switch ($tab){
                case 1: $task = "general"; break;
                case 2: $task = "currency"; break;
                case 3: $task = "image"; break;
                case 5: $task = "storeinfo"; break;
                case 6: $task = "catprod"; break;
                case 7: $task = "checkout"; break;
                case 8: $task = "adminfunction"; break;
                case 9: $task = "fieldregister"; break;
				case 10: $task = "otherconfig"; break;
            }
            $this->setRedirect('index.php?option=com_jshopping&controller=config&task='.$task, Text::_('JSHOP_CONFIG_SUCCESS'));
        }else{
		    $this->setRedirect('index.php?option=com_jshopping&controller=config', Text::_('JSHOP_CONFIG_SUCCESS'));
        }
    }
    
    function seo(){        
        $rows = JSFactory::getModel("seo")->getList();
        
        $view = $this->getView("config", 'html');
        $view->setLayout("listseo");
        $view->set('etemplatevar', '');
        $view->set("rows", $rows);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplaySeo', array(&$view));
        $view->displayListSeo();    
    }
    
    function seoedit(){
        Factory::getApplication()->input->set('hidemainmenu', true);
        $id = $this->input->getInt("id");
        $seo = JSFactory::getTable("seo");
        $seo->load($id);
        
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;        
        OutputFilter::objectHTMLSafe($seo, ENT_QUOTES);
        
        $view=$this->getView("config", 'html');
        $view->setLayout("editseo");        
        $view->set('row', $seo);
        $view->set('etemplatevar', '');
        $view->set('languages', $languages);
        $view->set('multilang', $multilang);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplaySeoEdit', array(&$view));
        $view->displayEditSeo();
    }
    
    function saveseo(){
        $post = $this->input->post->getArray();
        $model = JSFactory::getModel("seo");
        $seo = $model->save($post);
        if ($this->getTask()=='applyseo'){
            $this->setRedirect('index.php?option=com_jshopping&controller=config&task=seoedit&id='.$seo->id, Text::_('JSHOP_CONFIG_SUCCESS'));
        }else{
            $this->setRedirect('index.php?option=com_jshopping&controller=config&task=seo', Text::_('JSHOP_CONFIG_SUCCESS'));
        }
    }
    
    function statictext(){
        $rows = JSFactory::getModel("statictext")->getList();

        $view = $this->getView("config", 'html');
        $view->setLayout("liststatictext");
        $view->set('etemplatevar', '');
        $view->set("rows", $rows);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayStatisticText', array(&$view)); 
        $view->displayListStatictext();    
    }
    
    function statictextedit(){
        Factory::getApplication()->input->set('hidemainmenu', true);
        $id = $this->input->getInt("id");
        
        $statictext = JSFactory::getTable("statictext");
        $statictext->load($id);
        
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        
        OutputFilter::objectHTMLSafe( $statictext, ENT_QUOTES);
        
        $view = $this->getView("config", 'html');
        $view->setLayout("editstatictext");        
        $view->set('row', $statictext);
        $view->set('languages', $languages);
        $view->set('etemplatevar', '');
        $view->set('multilang', $multilang);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayStatisticTextEdit', array(&$view));
        $view->displayEditStatictext();
    }
    
    function savestatictext(){
        $model = JSFactory::getModel("statictext");
        $post = $model->getPrepareDataSave($this->input);
        $statictext = $model->save($post);
        if ($this->getTask()=='applystatictext'){
            $this->setRedirect('index.php?option=com_jshopping&controller=config&task=statictextedit&id='.$statictext->id, Text::_('JSHOP_CONFIG_SUCCESS'));
        }else{            
            $this->setRedirect('index.php?option=com_jshopping&controller=config&task=statictext', Text::_('JSHOP_CONFIG_SUCCESS'));
        }
    }
    
    function deletestatictext(){
        $id = $this->input->getInt("id");
        JSFactory::getModel("statictext")->delete($id);
        $this->setRedirect('index.php?option=com_jshopping&controller=config&task=statictext');
    }
    
    function publish(){
        $cid = $this->input->getVar('cid');
        JSFactory::getModel("statictext")->useReturnPolicy($cid, 1);
        $this->setRedirect('index.php?option=com_jshopping&controller=config&task=statictext');
    }
    
    function unpublish(){
        $cid = $this->input->getVar('cid');
        JSFactory::getModel("statictext")->useReturnPolicy($cid, 0);
        $this->setRedirect('index.php?option=com_jshopping&controller=config&task=statictext');
    }
    
    function preview_pdf(){
        $dispatcher = Factory::getApplication();
		$jshopConfig = JSFactory::getConfig();
        $jshopConfig->currency_code = "USD";
        $file_generete_pdf_order = $jshopConfig->file_generete_pdf_order;		
        $order = JSFactory::getTable('order');
        $order->prepareOrderPrint = 1;
        $order->firma_name = "Firma";
        $order->f_name = "Fname";
        $order->l_name = 'Lname';
        $order->street = 'Street';
        $order->zip = "Zip"; 
        $order->city = "City";
        $order->country = "Country";
        $order->order_number = Helper::outputDigit(1, 8);
        $order->order_date = Helper::formatdate(date('Y-m-d'));
        $order->invoice_date = Helper::formatdate(date('Y-m-d'));
        $order->weight = 0;
        $order->products = array();
        $prod = new \stdClass();
        $prod->product_name = "Product name";
        $prod->product_ean = "12345678";
        $prod->product_quantity = 1;
        $prod->product_item_price = 125;
        $prod->product_tax = 19;
        $prod->manufacturer = '';
        $prod->manufacturer_code = '';
        $prod->real_ean = '';
        $prod->product_attributes = '';
        $prod->product_freeattributes = '';
        $prod->delivery_time = '';
        $prod->extra_fields = '';
        $prod->_qty_unit = '';
        $order->products[] = $prod;
        $order->order_subtotal = 125;
        $order->order_shipping = 20;        
        $display_price = $jshopConfig->display_price_front;
        if ($display_price==0){
            $order->display_price = 0;
            $order->order_tax_list = array(19 => 23.15);
            $order->order_total = 145;
        }else{
            $order->display_price = 1;
            $order->order_tax_list = array(19 => 27.55);
            $order->order_total = 172.55;
        }
        $dispatcher->triggerEvent('onBeforeCreateDemoPreviewPdf', array(&$order, &$file_generete_pdf_order));        
		$order->pdf_file = $file_generete_pdf_order::generatePdf($order, $jshopConfig);
		header("Location: ".$jshopConfig->pdf_orders_live_path."/".$order->pdf_file);
		die();
	}
    
	function otherconfig(){
		$jshopConfig = JSFactory::getConfig();
        $config = new \stdClass();
		include($jshopConfig->path.'config/default_config.php');
        $tax_rule_for = array();
        $tax_rule_for[] = HTMLHelper::_('select.option', 0, Text::_('JSHOP_FIRMA_CLIENT'), 'id', 'name' );
        $tax_rule_for[] = HTMLHelper::_('select.option', 1, Text::_('JSHOP_VAT_NUMBER'), 'id', 'name' );
        $lists['tax_rule_for'] = HTMLHelper::_('select.genericlist', $tax_rule_for, 'ext_tax_rule_for','class = "inputbox form-select"','id','name', $jshopConfig->ext_tax_rule_for);

		$view = $this->getView("config", 'html');
		$view->setLayout("otherconfig");
        $view->set("other_config", $other_config);
        $view->set("other_config_checkbox", $other_config_checkbox);
        $view->set("other_config_select", $other_config_select);
        $view->set("config", $jshopConfig);
        $view->set('etemplatevar', '');
		$view->set("lists", $lists);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";

		$dispatcher = Factory::getApplication();
		$dispatcher->triggerEvent('onBeforeEditConfigOtherConfig', array(&$view));
		$view->display();
	}
    
}