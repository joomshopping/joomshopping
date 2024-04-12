<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
use Joomla\Component\Jshopping\Site\Helper\SelectOptions;

defined('_JEXEC') or die();

class ConfigController extends BaseadminController{
    
    public function init(){
        $this->registerTask('apply', 'save');
        $this->registerTask('applyseo', 'saveseo');
        $this->registerTask('applystatictext', 'savestatictext');
        \JSHelperAdmin::checkAccessController("config");
        \JSHelperAdmin::addSubmenu("config");        
    }

    function display($cachable = false, $urlparams = false){
        $app = \JFactory::getApplication();
        $jshopConfig = \JSFactory::getConfig();        
        $current_currency = \JSFactory::getTable('currency');
        $current_currency->load($jshopConfig->mainCurrency);
        if ($current_currency->currency_value!=1){
            $app->enqueueMessage(\JText::_('JSHOP_ERROR_MAIN_CURRENCY_VALUE'), 'message');
        }
        $view = $this->getView("panel", 'html');
        $view->setLayout("config");
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $view->displayConfig();
    }
    
    function general(){
	    $jshopConfig = \JSFactory::getConfig();

        $lists['languages'] = \JHTML::_('select.genericlist', \JSHelper::getAllLanguages(), 'defaultLanguage', 'class = "form-control"', 'language', 'name', $jshopConfig->defaultLanguage);
        $display_price_list = SelectOptions::getPriceType();
        $lists['display_price_admin'] = \JHTML::_('select.genericlist', $display_price_list, 'display_price_admin', 'class = "form-control"', 'id', 'name', $jshopConfig->display_price_admin);
        $lists['display_price_front'] = \JHTML::_('select.genericlist', $display_price_list, 'display_price_front', 'class = "form-control"', 'id', 'name', $jshopConfig->display_price_front);
        $lists['template'] = \JSHelperAdmin::getShopTemplatesSelect($jshopConfig->template);

    	$view = $this->getView("config", 'html');
        $view->setLayout("general");
        $view->set('etemplatevar', '');
		$view->set("lists", $lists);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditConfigGeneral', array(&$view));
        $view->display();
    }
    
    function catprod(){
        $jshopConfig = \JSFactory::getConfig();
        
        $displayprice = array();
        $displayprice[] = \JHTML::_('select.option', 0, \JText::_('JSHOP_YES'), 'id', 'value');
        $displayprice[] = \JHTML::_('select.option', 1, \JText::_('JSHOP_NO'), 'id', 'value');
        $displayprice[] = \JHTML::_('select.option', 2, \JText::_('JSHOP_ONLY_REGISTER_USER'), 'id', 'value');
        $lists['displayprice'] = \JHTML::_('select.genericlist', $displayprice, 'displayprice','class = "form-control"','id','value', $jshopConfig->displayprice);
        
        $catsort = array();
        $catsort[] = \JHTML::_('select.option', 1, \JText::_('JSHOP_SORT_MANUAL'), 'id','value');
        $catsort[] = \JHTML::_('select.option', 2, \JText::_('JSHOP_SORT_ALPH'), 'id','value');
        $lists['category_sorting'] = \JHTML::_('select.genericlist', $catsort, 'category_sorting','class = "form-control"','id','value', $jshopConfig->category_sorting);
        $lists['manufacturer_sorting'] = \JHTML::_('select.genericlist', $catsort, 'manufacturer_sorting','class = "form-control"','id','value', $jshopConfig->manufacturer_sorting);
        
        $sortd = array();
        $sortd[] = \JHTML::_('select.option', 0, \JText::_('JSHOP_A_Z'), 'id','value');
        $sortd[] = \JHTML::_('select.option', 1, \JText::_('JSHOP_Z_A'), 'id','value');
        $lists['product_sorting_direction'] = \JHTML::_('select.genericlist', $sortd, 'product_sorting_direction','class = "form-control"','id','value', $jshopConfig->product_sorting_direction);
        
        $opt = array();
        $opt[] = \JHTML::_('select.option', 'V.value_ordering', \JText::_('JSHOP_SORT_MANUAL'), 'id','value');
        $opt[] = \JHTML::_('select.option', 'value_name', \JText::_('JSHOP_SORT_ALPH'), 'id','value');
        $opt[] = \JHTML::_('select.option', 'PA.price', \JText::_('JSHOP_SORT_PRICE'), 'id','value');
        $opt[] = \JHTML::_('select.option', 'PA.ean', \JText::_('JSHOP_EAN_PRODUCT'), 'id','value');
        $opt[] = \JHTML::_('select.option', 'PA.count', \JText::_('JSHOP_QUANTITY_PRODUCT'), 'id','value');
        $opt[] = \JHTML::_('select.option', 'PA.product_attr_id', \JText::_('JSHOP_SPECIFIED_IN_PRODUCT'), 'id','value');
        $lists['attribut_dep_sorting_in_product'] = \JHTML::_('select.genericlist', $opt, 'attribut_dep_sorting_in_product','class = "form-control"','id','value', $jshopConfig->attribut_dep_sorting_in_product);
        
        $opt = array();
        $opt[] = \JHTML::_('select.option', 'V.value_ordering', \JText::_('JSHOP_SORT_MANUAL'), 'id','value');
        $opt[] = \JHTML::_('select.option', 'value_name', \JText::_('JSHOP_SORT_ALPH'), 'id','value');
        $opt[] = \JHTML::_('select.option', 'addprice', \JText::_('JSHOP_SORT_PRICE'), 'id','value');
        $opt[] = \JHTML::_('select.option', 'PA.id', \JText::_('JSHOP_SPECIFIED_IN_PRODUCT'), 'id','value');
        $lists['attribut_nodep_sorting_in_product'] = \JHTML::_('select.genericlist', $opt, 'attribut_nodep_sorting_in_product','class = "form-control"','id','value', $jshopConfig->attribut_nodep_sorting_in_product);        
        
        $select = array();        
        foreach($jshopConfig->sorting_products_name_select as $key => $value){
            $select[] = \JHTML::_('select.option', $key, \JText::_($value), 'id', 'value');
        }
        $lists['product_sorting'] = \JHTML::_('select.genericlist',$select, "product_sorting", 'class = "form-control"', 'id','value', $jshopConfig->product_sorting);
        
        if ($jshopConfig->admin_show_product_extra_field){
            $_productfields = \JSFactory::getModel("productfields");
            $rows = $_productfields->getList();
            $lists['product_list_display_extra_fields'] = \JHTML::_('select.genericlist', $rows, "product_list_display_extra_fields[]", ' class = "form-control" size="10" multiple = "multiple" ', 'id','name', $jshopConfig->getProductListDisplayExtraFields() );
            $lists['filter_display_extra_fields'] = \JHTML::_('select.genericlist', $rows, "filter_display_extra_fields[]", ' class = "form-control" size="10" multiple = "multiple" ', 'id','name', $jshopConfig->getFilterDisplayExtraFields() );
            $lists['product_hide_extra_fields'] = \JHTML::_('select.genericlist', $rows, "product_hide_extra_fields[]", ' class = "form-control" size="10" multiple = "multiple" ', 'id','name', $jshopConfig->getProductHideExtraFields() );
            $lists['cart_display_extra_fields'] = \JHTML::_('select.genericlist', $rows, "cart_display_extra_fields[]", ' class = "form-control" size="10" multiple = "multiple" ', 'id','name', $jshopConfig->getCartDisplayExtraFields() );
        }

        $lists['units'] = \JHTML::_('select.genericlist', SelectOptions::getUnits(), "main_unit_weight", 'class = "form-control"', 'id','name', $jshopConfig->main_unit_weight);        
            
        $view = $this->getView("config", 'html');
        $view->setLayout("categoryproduct");
        $view->set("lists", $lists);
        $view->set('etemplatevar', '');
        $view->sidebar = \JHTMLSidebar::render();
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditConfigCatProd', array(&$view));
        $view->display();
    }
    
    function checkout(){
        $jshopConfig = \JSFactory::getConfig();

        $lists['status'] = \JHTML::_('select.genericlist', SelectOptions::getOrderStatus(), 'default_status_order', 'class = "inputbox form-control"', 'status_id', 'name', $jshopConfig->default_status_order);
        $currency_code = \JSHelper::getMainCurrencyCode();        
        $lists['default_country'] = \JHTML::_('select.genericlist', SelectOptions::getCountrys(3), 'default_country','class = "inputbox form-control"','country_id','name', $jshopConfig->default_country);
        
        $vendor_order_message_type = array();
        $vendor_order_message_type[] = \JHTML::_('select.option', 0, \JText::_('JSHOP_NOT_SEND_MESSAGE'), 'id', 'name' );
        $vendor_order_message_type[] = \JHTML::_('select.option', 1, \JText::_('JSHOP_WE_SEND_MESSAGE'), 'id', 'name' );
        $vendor_order_message_type[] = \JHTML::_('select.option', 2, \JText::_('JSHOP_WE_SEND_ORDER'), 'id', 'name' );
        $vendor_order_message_type[] = \JHTML::_('select.option', 3, \JText::_('JSHOP_WE_ALWAYS_SEND_ORDER'), 'id', 'name' );
        $lists['vendor_order_message_type'] = \JHTML::_('select.genericlist', $vendor_order_message_type, 'vendor_order_message_type','class = "inputbox form-control" size = "1"','id','name', $jshopConfig->vendor_order_message_type);
        
		$option = array();
        $option[] = \JHTML::_('select.option', 0, \JText::_('JSHOP_STEP_3_4'), 'id', 'name');
        $option[] = \JHTML::_('select.option', 1, \JText::_('JSHOP_STEP_4_3'), 'id', 'name');
        $lists['step_4_3'] = \JHTML::_('select.genericlist', $option, 'step_4_3','class = "inputbox form-control"','id','name', $jshopConfig->step_4_3);

        $view = $this->getView("config", 'html');
        $view->setLayout("checkout");
        $view->set("lists", $lists); 
        $view->set("currency_code", $currency_code);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditConfigCheckout', array(&$view));
        $view->display();
    }

    function fieldregister(){
        $jshopConfig = \JSFactory::getConfig();
        $view = $this->getView("config", 'html');
        $view->setLayout("fieldregister");
        $config = new \stdClass();
        include($jshopConfig->path.'config/default_config.php');

        $current_fields = $jshopConfig->getListFieldsRegister();
        $view->set("fields", $fields_client);
        $view->set("current_fields", $current_fields);
        $view->set("fields_sys", $fields_client_sys);
        $view->set('etemplatevar', '');
        $view->sidebar = \JHTMLSidebar::render();
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditConfigFieldRegister', array(&$view));
        $view->display();
    }

    function adminfunction(){
        $jshopConfig = \JSFactory::getConfig();
        $shop_register_type = array();
        $shop_register_type[] = \JHTML::_('select.option', 0, "-", 'id', 'name' );
        $shop_register_type[] = \JHTML::_('select.option', 1, \JText::_('JSHOP_MEYBY_SKIP_REGISTRATION'), 'id', 'name' );
        $shop_register_type[] = \JHTML::_('select.option', 2, \JText::_('JSHOP_WITHOUT_REGISTRATION'), 'id', 'name' );
        $lists['shop_register_type'] = \JHTML::_('select.genericlist', $shop_register_type, 'shop_user_guest','class = "inputbox form-control" size = "1"','id','name', $jshopConfig->shop_user_guest);
        
        $opt = array();
        $opt[] = \JHTML::_('select.option', 0, \JText::_('JSHOP_NORMAL'), 'id', 'name');
        $opt[] = \JHTML::_('select.option', 1, \JText::_('JSHOP_DEVELOPER'), 'id', 'name');
        $lists['shop_mode'] = \JHTML::_('select.genericlist', $opt, 'shop_mode','class = "inputbox form-control"','id','name', $jshopConfig->shop_mode);

        $view = $this->getView("config", 'html');
        $view->setLayout("adminfunction");
        $view->set("lists", $lists);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditConfigAdminFunction', array(&$view));
        $view->display();
    }

    function currency(){
    	$jshopConfig = \JSFactory::getConfig();
		
		$lists['currencies'] = \JHTML::_('select.genericlist', SelectOptions::getCurrencies(), 'mainCurrency','class = "inputbox form-control"','currency_id','currency_code',$jshopConfig->mainCurrency);
		
		$i = 0;
		foreach($jshopConfig->format_currency as $key => $value){
            $currenc[$i] = new \stdClass();
			$currenc[$i]->id_cur = $key;
			$currenc[$i]->format = $value;
			$i++;
		}
		$lists['format_currency'] = \JHTML::_('select.genericlist', $currenc, 'currency_format','class = "inputbox form-control"', 'id_cur', 'format', $jshopConfig->currency_format);
				
        $view = $this->getView("config", 'html');
        $view->setLayout("currency");
		$view->set("lists", $lists);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditConfigCurrency', array(&$view));
        $view->display();
    }
    
    function image(){
        $jshopConfig = \JSFactory::getConfig();
        
        $resize_type = array();
        $resize_type[] = \JHTML::_('select.option', 0, \JText::_('JSHOP_CUT'), 'id', 'name' );
        $resize_type[] = \JHTML::_('select.option', 1, \JText::_('JSHOP_FILL'), 'id', 'name' );
        $resize_type[] = \JHTML::_('select.option', 2, \JText::_('JSHOP_STRETCH'), 'id', 'name' );
        $select_resize_type = \JHTML::_('select.genericlist', $resize_type, 'image_resize_type','class = "inputbox form-control"','id','name', $jshopConfig->image_resize_type);
    	
    	$view = $this->getView("config", 'html');
        $view->setLayout("image");
        $view->set("select_resize_type", $select_resize_type);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditConfigImage', array(&$view));
        $view->display();
    }
    
    function storeinfo(){
        $vendor = \JSFactory::getTable('vendor');
        $vendor->loadMain();
		$lists['countries'] = \JHTML::_('select.genericlist', SelectOptions::getCountrys(3), 'country', 'class = "inputbox form-control"', 'country_id', 'name', $vendor->country);
        
        \JFilterOutput::objectHTMLSafe($vendor, ENT_QUOTES);
        
    	$view = $this->getView("config", 'html');
        $view->setLayout("storeinfo");
        $view->set("lists", $lists); 
		$view->set("vendor", $vendor);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditConfigStoreInfo', array(&$view));
        $view->display();
    }
    
    function save(){
	    $jshopConfig = \JSFactory::getConfig();
		$tab = $this->input->getVar('tab');
		$db = \JFactory::getDBO();
        
        $dispatcher = \JFactory::getApplication();
		$extconf = array('imageheader'=>'header.jpg', 'imagefooter'=>'footer.jpg');	
		$post = $this->input->post->getArray();
        $dispatcher->triggerEvent('onBeforeSaveConfig', array(&$post, &$extconf));
        
        if ($tab == 5){
            $vendor = \JSFactory::getTable('vendor');
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
		    $config = \JSFactory::getTable('Config');
		    $config->id = $jshopConfig->load_id;            
		    if (!$config->bind($post, ['task', 'tab', 'option', 'controller'])) {                
			    \JSError::raiseWarning("",\JText::_('JSHOP_ERROR_BIND'));
			    $this->setRedirect('index.php?option=com_jshopping&controller=config');
			    return 0;
		    }
            
            if ($tab==6 && $jshopConfig->admin_show_product_extra_field){                
                $config->setProductListDisplayExtraFields((array)$post['product_list_display_extra_fields']);
                $config->setFilterDisplayExtraFields((array)$post['filter_display_extra_fields']);
                $config->setProductHideExtraFields((array)$post['product_hide_extra_fields']);
                $config->setCartDisplayExtraFields((array)$post['cart_display_extra_fields']);
            }
		    
		    $config->transformPdfParameters();
		    if (!$config->store()) {                
			    \JSError::raiseWarning("",\JText::_('JSHOP_ERROR_SAVE_DATABASE'));
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
            $count_products_to_page = intval($post['count_products_to_page']);
            $count_products_to_row = intval($post['count_products_to_row']);
            $query = "update `#__jshopping_categories` set `products_page`='".$count_products_to_page."', `products_row`='".$count_products_to_row."'";
            $db->setQuery($query);
            $db->execute();
            $query = "update `#__jshopping_manufacturers` set `products_page`='".$count_products_to_page."', `products_row`='".$count_products_to_row."'";
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
            $this->setRedirect('index.php?option=com_jshopping&controller=config&task='.$task, \JText::_('JSHOP_CONFIG_SUCCESS'));
        }else{
		    $this->setRedirect('index.php?option=com_jshopping&controller=config', \JText::_('JSHOP_CONFIG_SUCCESS'));
        }
    }
    
    function seo(){        
        $rows = \JSFactory::getModel("seo")->getList();
        
        $view = $this->getView("config", 'html');
        $view->setLayout("listseo");
        $view->set('etemplatevar', '');
        $view->set("rows", $rows);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplaySeo', array(&$view));
        $view->displayListSeo();    
    }
    
    function seoedit(){
        \JFactory::getApplication()->input->set('hidemainmenu', true);
        $id = $this->input->getInt("id");
        $seo = \JSFactory::getTable("seo");
        $seo->load($id);
        
        $_lang = \JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;        
        \JFilterOutput::objectHTMLSafe($seo, ENT_QUOTES);
        
        $view=$this->getView("config", 'html');
        $view->setLayout("editseo");        
        $view->set('row', $seo);
        $view->set('etemplatevar', '');
        $view->set('languages', $languages);
        $view->set('multilang', $multilang);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplaySeoEdit', array(&$view));
        $view->displayEditSeo();
    }
    
    function saveseo(){
        $post = $this->input->post->getArray();
        $model = \JSFactory::getModel("seo");
        $seo = $model->save($post);
        if ($this->getTask()=='applyseo'){
            $this->setRedirect('index.php?option=com_jshopping&controller=config&task=seoedit&id='.$seo->id, \JText::_('JSHOP_CONFIG_SUCCESS'));
        }else{
            $this->setRedirect('index.php?option=com_jshopping&controller=config&task=seo', \JText::_('JSHOP_CONFIG_SUCCESS'));
        }
    }
    
    function statictext(){
        $rows = \JSFactory::getModel("statictext")->getList();

        $view = $this->getView("config", 'html');
        $view->setLayout("liststatictext");
        $view->set('etemplatevar', '');
        $view->set("rows", $rows);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayStatisticText', array(&$view)); 
        $view->displayListStatictext();    
    }
    
    function statictextedit(){
        \JFactory::getApplication()->input->set('hidemainmenu', true);
        $id = $this->input->getInt("id");
        
        $statictext = \JSFactory::getTable("statictext");
        $statictext->load($id);
        
        $_lang = \JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;
        
        \JFilterOutput::objectHTMLSafe( $statictext, ENT_QUOTES);
        
        $view = $this->getView("config", 'html');
        $view->setLayout("editstatictext");        
        $view->set('row', $statictext);
        $view->set('languages', $languages);
        $view->set('etemplatevar', '');
        $view->set('multilang', $multilang);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayStatisticTextEdit', array(&$view));
        $view->displayEditStatictext();
    }
    
    function savestatictext(){
        $model = \JSFactory::getModel("statictext");
        $post = $model->getPrepareDataSave($this->input);
        $statictext = $model->save($post);
        if ($this->getTask()=='applystatictext'){
            $this->setRedirect('index.php?option=com_jshopping&controller=config&task=statictextedit&id='.$statictext->id, \JText::_('JSHOP_CONFIG_SUCCESS'));
        }else{            
            $this->setRedirect('index.php?option=com_jshopping&controller=config&task=statictext', \JText::_('JSHOP_CONFIG_SUCCESS'));
        }
    }
    
    function deletestatictext(){
        $id = $this->input->getInt("id");
        \JSFactory::getModel("statictext")->delete($id);
        $this->setRedirect('index.php?option=com_jshopping&controller=config&task=statictext');
    }
    
    function publish(){
        $cid = $this->input->getVar('cid');
        \JSFactory::getModel("statictext")->useReturnPolicy($cid, 1);
        $this->setRedirect('index.php?option=com_jshopping&controller=config&task=statictext');
    }
    
    function unpublish(){
        $cid = $this->input->getVar('cid');
        \JSFactory::getModel("statictext")->useReturnPolicy($cid, 0);
        $this->setRedirect('index.php?option=com_jshopping&controller=config&task=statictext');
    }
    
    function preview_pdf(){
        $dispatcher = \JFactory::getApplication();
		$jshopConfig = \JSFactory::getConfig();
        $jshopConfig->currency_code = "USD";
        $file_generete_pdf_order = $jshopConfig->file_generete_pdf_order;		
        $order = \JSFactory::getTable('order');
        $order->prepareOrderPrint = 1;
        $order->firma_name = "Firma";
        $order->f_name = "Fname";
        $order->l_name = 'Lname';
        $order->street = 'Street';
        $order->zip = "Zip"; 
        $order->city = "City";
        $order->country = "Country";
        $order->order_number = \JSHelper::outputDigit(1, 8);
        $order->order_date = strftime($jshopConfig->store_date_format, time());
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
		$jshopConfig = \JSFactory::getConfig();
        $config = new \stdClass();
		include($jshopConfig->path.'config/default_config.php');
        $tax_rule_for = array();
        $tax_rule_for[] = \JHTML::_('select.option', 0, \JText::_('JSHOP_FIRMA_CLIENT'), 'id', 'name' );
        $tax_rule_for[] = \JHTML::_('select.option', 1, \JText::_('JSHOP_VAT_NUMBER'), 'id', 'name' );
        $lists['tax_rule_for'] = \JHTML::_('select.genericlist', $tax_rule_for, 'ext_tax_rule_for','class = "inputbox form-control"','id','name', $jshopConfig->ext_tax_rule_for);

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
        $view->sidebar = \JHTMLSidebar::render();
		$dispatcher = \JFactory::getApplication();
		$dispatcher->triggerEvent('onBeforeEditConfigOtherConfig', array(&$view));
		$view->display();
	}
    
}