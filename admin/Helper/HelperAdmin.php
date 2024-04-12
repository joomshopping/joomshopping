<?php
/**
* @version      5.0.7 31.08.2022
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Helper;

defined('_JEXEC') or die;

class HelperAdmin{

    public static function quickiconButton( $link, $image, $text ){
    $jshopConfig = \JSFactory::getConfig();
    ?>
    <div style="float:left;">
        <div class="icon">
            <a href="<?php echo $link?>">
                <img src="<?php print $jshopConfig->live_admin_path?>images/<?php print $image?>" alt="">
                <span><?php echo $text?></span>
            </a>
        </div>
    </div>
    <?php
    }
	
	public static function tooltip($text) {
		return '<span class="jsTooltip" title="'.htmlspecialchars($text, ENT_COMPAT, 'UTF-8').'"><img src="components/com_jshopping/images/jshop_info_s.png" alt="Tooltip"></span>';  
    }

    public static function btnHome(){
        \Joomla\CMS\Toolbar\Toolbar::getInstance('toolbar')->standardButton('home')
            ->text('JOOMSHOPPING')
            ->task('home')
            ->buttonClass('btn btn-info')
            ->listCheck(false);
    }

    public static function getTemplates($type, $default, $first_empty = 0){
        $name = $type."_template";
        $folder = $type;

        $jshopConfig = \JSFactory::getConfig();
        $temp = array();
        $dir = $jshopConfig->template_path.$jshopConfig->template."/".$folder."/";
        $dh = opendir($dir);
        while (($file = readdir($dh)) !== false) {
            if (preg_match("/".$type."_(.*)\.php/", $file, $matches)){
                $temp[] = $matches[1];
            }
        }
        closedir($dh);
        $list = array();
        if ($first_empty){
            $list[] = \JHTML::_('select.option', -1, "- - -", 'id', 'value');
        }
        foreach($temp as $val){
            $list[] = \JHTML::_('select.option', $val, $val, 'id', 'value');
        }
        
        return \JHTML::_('select.genericlist', $list, $name,'class = "inputbox form-control form-select"','id','value', $default);
    }

    public static function getShopTemplatesSelect($default){
        $jshopConfig = \JSFactory::getConfig();
        $temp = array();
        $dir = $jshopConfig->template_path;
        $dh = opendir($dir);
        while(($file = readdir($dh)) !== false){        
            if (is_dir($dir.$file) && $file!="." && $file!=".." && $file!='addons'){
                $temp[] = $file;
            }
        }
        closedir($dh);
        $list = array();
        foreach($temp as $val){
            $list[] = \JHTML::_('select.option', $val, $val, 'id', 'value');
        }
        return \JHTML::_('select.genericlist', $list, "template",'class = "inputbox form-control form-select"','id','value', $default);
    }

    public static function getFileName($name) {
        // Get Extension
        $ext_file = strtolower(substr($name,strrpos($name,".")));
        // Generate name file
        $name_file = md5(uniqid(rand(),true));
        return $name_file . $ext_file;
    }

    public static function updateCountExtTaxRule(){
        $db = \JFactory::getDBO();
        $query = "SELECT count(id) FROM `#__jshopping_taxes_ext`";
        $db->setQuery($query);
        $count = $db->loadResult();
        
		$config = \JSFactory::getTable('Config');
		$config->load(1);
		$config->use_extend_tax_rule = $count;
		$config->store();
    }

    public static function updateCountConfigDisplayPrice(){
        $db = \JFactory::getDBO();
        $query = "SELECT count(id) FROM `#__jshopping_config_display_prices`";
        $db->setQuery($query);
        $count = $db->loadResult();

		$config = \JSFactory::getTable('Config');
		$config->load(1);
		$config->use_extend_display_price_rule = $count;
		$config->store();
    }

    public static function orderBlocked($order){
        if (!$order->order_created && time()-strtotime($order->order_date)<3600){
            return 1;
        }else{
            return 0;
        }
    }

    public static function addSubmenu($vName){
        $user = \JFactory::getUser();
        
        $adminaccess = $user->authorise('core.admin', 'com_jshopping');
        $installaccess = $user->authorise('core.admin.install', 'com_jshopping');
        
        $menu = array();
        $menu['categories'] = array(\JText::_('JSHOP_MENU_CATEGORIES'), 'index.php?option=com_jshopping&controller=categories&catid=0', $vName == 'categories', 1);
        $menu['products'] = array(\JText::_('JSHOP_MENU_PRODUCTS'), 'index.php?option=com_jshopping&controller=products&category_id=0', $vName == 'products', 1);
        $menu['orders'] = array( \JText::_('JSHOP_MENU_ORDERS'), 'index.php?option=com_jshopping&controller=orders', $vName == 'orders', 1);
        $menu['users'] = array(\JText::_('JSHOP_MENU_CLIENTS'), 'index.php?option=com_jshopping&controller=users', $vName == 'users', 1);
        $menu['other'] = array(\JText::_('JSHOP_MENU_OTHER'), 'index.php?option=com_jshopping&controller=other', $vName == 'other', 1);
        $menu['config'] = array( \JText::_('JSHOP_MENU_CONFIG'), 'index.php?option=com_jshopping&controller=config', $vName == 'config', $adminaccess );
        $menu['update'] = array(\JText::_('JSHOP_PANEL_UPDATE'), 'index.php?option=com_jshopping&controller=update', $vName == 'update', $installaccess );
        $menu['info'] = array(\JText::_('JSHOP_MENU_INFO'), 'index.php?option=com_jshopping&controller=info', $vName == 'info', 1);
        
        \JFactory::getApplication()->triggerEvent('onBeforeAdminMenuDisplay', array(&$menu, &$vName));
        
        foreach($menu as $item){
            if ($item[3]){
                \JHtmlSidebar::addEntry( $item[0], $item[1], $item[2]);
            }
        }
    }

    public static function displayMainPanelIco(){
        $user =  \JFactory::getUser();        
        $adminaccess = $user->authorise('core.admin', 'com_jshopping');
        $installaccess = $user->authorise('core.admin.install', 'com_jshopping');
        
        $menu = array();
        $menu['categories'] = array(\JText::_('JSHOP_MENU_CATEGORIES'), 'index.php?option=com_jshopping&controller=categories&catid=0', 'jshop_categories_b.png', 1);
        $menu['products'] = array(\JText::_('JSHOP_MENU_PRODUCTS'), 'index.php?option=com_jshopping&controller=products&category_id=0', 'jshop_products_b.png', 1);
        $menu['orders'] = array( \JText::_('JSHOP_MENU_ORDERS'), 'index.php?option=com_jshopping&controller=orders', 'jshop_orders_b.png', 1);
        $menu['users'] = array(\JText::_('JSHOP_MENU_CLIENTS'), 'index.php?option=com_jshopping&controller=users', 'jshop_users_b.png', 1);
        $menu['other'] = array(\JText::_('JSHOP_MENU_OTHER'), 'index.php?option=com_jshopping&controller=other', 'jshop_options_b.png', 1);
        $menu['config'] = array( \JText::_('JSHOP_MENU_CONFIG'), 'index.php?option=com_jshopping&controller=config', 'jshop_configuration_b.png', $adminaccess );
        $menu['update'] = array(\JText::_('JSHOP_PANEL_UPDATE'), 'index.php?option=com_jshopping&controller=update', 'jshop_update_b.png', $installaccess );
        $menu['info'] = array(\JText::_('JSHOP_MENU_INFO'), 'index.php?option=com_jshopping&controller=info', 'jshop_info_b.png', 1);    
        
        \JFactory::getApplication()->triggerEvent( 'onBeforeAdminMainPanelIcoDisplay', array(&$menu) );
        
        foreach($menu as $item){
            if ($item[3]){
                self::quickiconButton($item[1], $item[2], $item[0]);            
            }
        }
    }

    public static function displayOptionPanelIco(){
        $jshopConfig = \JSFactory::getConfig();
        $user = \JFactory::getUser();
        $dispatcher = \JFactory::getApplication();    
        $adminaccess = $user->authorise('core.admin', 'com_jshopping');
        
        $menu = array();    
        $menu['manufacturers'] = array(\JText::_('JSHOP_MENU_MANUFACTURERS'), 'index.php?option=com_jshopping&controller=manufacturers', 'jshop_manufacturer_b.png', !$jshopConfig->disable_admin['product_manufacturer']);
        $menu['coupons'] = array(\JText::_('JSHOP_MENU_COUPONS'), 'index.php?option=com_jshopping&controller=coupons', 'jshop_coupons_b.png', $jshopConfig->use_rabatt_code);
        $menu['currencies'] = array(\JText::_('JSHOP_PANEL_CURRENCIES'), 'index.php?option=com_jshopping&controller=currencies', 'jshop_currencies_b.png', !$jshopConfig->disable_admin['currencies']);
        $menu['taxes'] = array(\JText::_('JSHOP_PANEL_TAXES'), 'index.php?option=com_jshopping&controller=taxes', 'jshop_taxes_b.png', $jshopConfig->tax);
        $menu['payments'] = array(\JText::_('JSHOP_PANEL_PAYMENTS'), 'index.php?option=com_jshopping&controller=payments', 'jshop_payments_b.png', ($adminaccess && $jshopConfig->without_payment==0));
        $menu['shippings'] = array(\JText::_('JSHOP_PANEL_SHIPPINGS'), 'index.php?option=com_jshopping&controller=shippings', 'jshop_shipping_b.png', ($adminaccess && $jshopConfig->without_shipping==0));
        $menu['shippingsprices'] = array(\JText::_('JSHOP_PANEL_SHIPPINGS_PRICES'), 'index.php?option=com_jshopping&controller=shippingsprices', 'jshop_shipping_price_b.png', ($adminaccess && $jshopConfig->without_shipping==0));    
        $menu['deliverytimes'] = array(\JText::_('JSHOP_PANEL_DELIVERY_TIME'), 'index.php?option=com_jshopping&controller=deliverytimes', 'jshop_time_delivery_b.png', $jshopConfig->admin_show_delivery_time);
        $menu['orderstatus'] = array(\JText::_('JSHOP_PANEL_ORDER_STATUS'), 'index.php?option=com_jshopping&controller=orderstatus', 'jshop_order_status_b.png', !$jshopConfig->disable_admin['orderstatus']);
        $menu['countries'] = array(\JText::_('JSHOP_PANEL_COUNTRIES'), 'index.php?option=com_jshopping&controller=countries', 'jshop_country_list_b.png', !$jshopConfig->disable_admin['countries']);
        $menu['attributes'] = array(\JText::_('JSHOP_PANEL_ATTRIBUTES'), 'index.php?option=com_jshopping&controller=attributes', 'jshop_attributes_b.png', $jshopConfig->admin_show_attributes);
        $menu['freeattributes'] = array(\JText::_('JSHOP_FREE_ATTRIBUTES'), 'index.php?option=com_jshopping&controller=freeattributes', 'jshop_attributes_b.png', $jshopConfig->admin_show_freeattributes);
        $menu['units'] = array(\JText::_('JSHOP_PANEL_UNITS_MEASURE'), 'index.php?option=com_jshopping&controller=units', 'jshop_unit_b.png', $jshopConfig->admin_show_units);
        $menu['usergroups'] = array(\JText::_('JSHOP_PANEL_USERGROUPS'), 'index.php?option=com_jshopping&controller=usergroups', 'jshop_user_groups_b.png', !$jshopConfig->disable_admin['usergroups']);
        $menu['vendors'] = array(\JText::_('JSHOP_VENDORS'), 'index.php?option=com_jshopping&controller=vendors', 'jshop_vendor_b.png', $jshopConfig->admin_show_vendors && $adminaccess);
        $menu['reviews'] = array(\JText::_('JSHOP_PANEL_REVIEWS'), 'index.php?option=com_jshopping&controller=reviews', 'jshop_reviews_b.png', $jshopConfig->allow_reviews_prod);
        $menu['productlabels'] = array(\JText::_('JSHOP_PANEL_PRODUCT_LABELS'), 'index.php?option=com_jshopping&controller=productlabels', 'jshop_label_b.png', $jshopConfig->admin_show_product_labels);
        $menu['productfields'] = array(\JText::_('JSHOP_PANEL_PRODUCT_EXTRA_FIELDS'), 'index.php?option=com_jshopping&controller=productfields', 'jshop_charac_b.png', $jshopConfig->admin_show_product_extra_field);
        $menu['languages'] = array(\JText::_('JSHOP_PANEL_LANGUAGES'), 'index.php?option=com_jshopping&controller=languages', 'jshop_languages_b.png', $jshopConfig->admin_show_languages && $adminaccess);
        $menu['importexport'] = array(\JText::_('JSHOP_PANEL_IMPORT_EXPORT'), 'index.php?option=com_jshopping&controller=importexport', 'jshop_import_export_b.png', !$jshopConfig->disable_admin['importexport']);
        $menu['addons'] = array(\JText::_('JSHOP_ADDONS'), 'index.php?option=com_jshopping&controller=addons', 'jshop_configuration_b.png', $adminaccess && !$jshopConfig->disable_admin['addons']);
        $menu['statistic'] = array(\JText::_('JSHOP_STATISTIC'), 'index.php?option=com_jshopping&controller=statistic', 'jshop_order_status_b.png', $adminaccess && !$jshopConfig->disable_admin['statistic']);
        $menu['logs'] = array(\JText::_('JSHOP_LOGS'), 'index.php?option=com_jshopping&controller=logs', 'jshop_reviews_b.png', $jshopConfig->shop_mode==1 && $adminaccess);
        
        $dispatcher->triggerEvent('onBeforeAdminOptionPanelIcoDisplay', array(&$menu));
        
        foreach($menu as $item){
            if ($item[3]){
                self::quickiconButton($item[1], $item[2], $item[0]);
            }
        }
    }

    public static function getItemsOptionPanelMenu(){
        $jshopConfig = \JSFactory::getConfig();
        $user = \JFactory::getUser();
        $dispatcher = \JFactory::getApplication();
        $adminaccess = $user->authorise('core.admin', 'com_jshopping');
        
        $menu = array();    
        $menu['manufacturers'] = array(\JText::_('JSHOP_MENU_MANUFACTURERS'), 'index.php?option=com_jshopping&controller=manufacturers', 'jshop_manufacturer_b.png', !$jshopConfig->disable_admin['product_manufacturer']);
        $menu['coupons'] = array(\JText::_('JSHOP_MENU_COUPONS'), 'index.php?option=com_jshopping&controller=coupons', 'jshop_coupons_b.png', $jshopConfig->use_rabatt_code);
        $menu['currencies'] = array(\JText::_('JSHOP_PANEL_CURRENCIES'), 'index.php?option=com_jshopping&controller=currencies', 'jshop_currencies_b.png', !$jshopConfig->disable_admin['currencies']);
        $menu['taxes'] = array(\JText::_('JSHOP_PANEL_TAXES'), 'index.php?option=com_jshopping&controller=taxes', 'jshop_taxes_b.png', $jshopConfig->tax);
        $menu['payments'] = array(\JText::_('JSHOP_PANEL_PAYMENTS'), 'index.php?option=com_jshopping&controller=payments', 'jshop_payments_b.png', ($adminaccess && $jshopConfig->without_payment==0));
        $menu['shippings'] = array(\JText::_('JSHOP_PANEL_SHIPPINGS'), 'index.php?option=com_jshopping&controller=shippings', 'jshop_shipping_b.png', ($adminaccess && $jshopConfig->without_shipping==0));
        $menu['shippingsprices'] = array(\JText::_('JSHOP_PANEL_SHIPPINGS_PRICES'), 'index.php?option=com_jshopping&controller=shippingsprices', 'jshop_shipping_price_b.png', ($adminaccess && $jshopConfig->without_shipping==0));    
        $menu['deliverytimes'] = array(\JText::_('JSHOP_PANEL_DELIVERY_TIME'), 'index.php?option=com_jshopping&controller=deliverytimes', 'jshop_time_delivery_b.png', $jshopConfig->admin_show_delivery_time);
        $menu['orderstatus'] = array(\JText::_('JSHOP_PANEL_ORDER_STATUS'), 'index.php?option=com_jshopping&controller=orderstatus', 'jshop_order_status_b.png', !$jshopConfig->disable_admin['orderstatus']);
        $menu['countries'] = array(\JText::_('JSHOP_PANEL_COUNTRIES'), 'index.php?option=com_jshopping&controller=countries', 'jshop_country_list_b.png', !$jshopConfig->disable_admin['countries']);
        $menu['attributes'] = array(\JText::_('JSHOP_PANEL_ATTRIBUTES'), 'index.php?option=com_jshopping&controller=attributes', 'jshop_attributes_b.png', $jshopConfig->admin_show_attributes);
        $menu['freeattributes'] = array(\JText::_('JSHOP_FREE_ATTRIBUTES'), 'index.php?option=com_jshopping&controller=freeattributes', 'jshop_attributes_b.png', $jshopConfig->admin_show_freeattributes);
        $menu['units'] = array(\JText::_('JSHOP_PANEL_UNITS_MEASURE'), 'index.php?option=com_jshopping&controller=units', 'jshop_unit_b.png', $jshopConfig->admin_show_units);
        $menu['usergroups'] = array(\JText::_('JSHOP_PANEL_USERGROUPS'), 'index.php?option=com_jshopping&controller=usergroups', 'jshop_user_groups_b.png', !$jshopConfig->disable_admin['usergroups']);
        $menu['vendors'] = array(\JText::_('JSHOP_VENDORS'), 'index.php?option=com_jshopping&controller=vendors', 'jshop_vendor_b.png', $jshopConfig->admin_show_vendors && $adminaccess);
        $menu['reviews'] = array(\JText::_('JSHOP_PANEL_REVIEWS'), 'index.php?option=com_jshopping&controller=reviews', 'jshop_reviews_b.png', $jshopConfig->allow_reviews_prod);
        $menu['productlabels'] = array(\JText::_('JSHOP_PANEL_PRODUCT_LABELS'), 'index.php?option=com_jshopping&controller=productlabels', 'jshop_label_b.png', $jshopConfig->admin_show_product_labels);
        $menu['productfields'] = array(\JText::_('JSHOP_PANEL_PRODUCT_EXTRA_FIELDS'), 'index.php?option=com_jshopping&controller=productfields', 'jshop_charac_b.png', $jshopConfig->admin_show_product_extra_field);
        $menu['languages'] = array(\JText::_('JSHOP_PANEL_LANGUAGES'), 'index.php?option=com_jshopping&controller=languages', 'jshop_languages_b.png', $jshopConfig->admin_show_languages && $adminaccess);
        $menu['importexport'] = array(\JText::_('JSHOP_PANEL_IMPORT_EXPORT'), 'index.php?option=com_jshopping&controller=importexport', 'jshop_import_export_b.png', !$jshopConfig->disable_admin['importexport']);
        $menu['addons'] = array(\JText::_('JSHOP_ADDONS'), 'index.php?option=com_jshopping&controller=addons', 'jshop_configuration_b.png', $adminaccess && !$jshopConfig->disable_admin['addons']);
        $menu['statistic'] = array(\JText::_('JSHOP_STATISTIC'), 'index.php?option=com_jshopping&controller=statistic', 'jshop_order_status_b.png', $adminaccess && !$jshopConfig->disable_admin['statistic']);
        $menu['logs'] = array(\JText::_('JSHOP_LOGS'), 'index.php?option=com_jshopping&controller=logs', 'jshop_order_status_b.png', $jshopConfig->shop_mode==1 && $adminaccess);
        
        $dispatcher->triggerEvent( 'onBeforeAdminOptionPanelMenuDisplay', array(&$menu) );
        
        return $menu; 
    }

    public static function displayConfigPanelIco(){
        $jshopConfig = \JSFactory::getConfig();
        $user = \JFactory::getUser();
        $dispatcher = \JFactory::getApplication();
        
        $menu = array();
        $menu['adminfunction'] = array(\JText::_('JSHOP_SHOP_FUNCTION'), 'index.php?option=com_jshopping&controller=config&task=adminfunction', 'jshop_options_b.png', 1);
        $menu['general'] = array(\JText::_('JSHOP_GENERAL_PARAMETERS'), 'index.php?option=com_jshopping&controller=config&task=general', 'jshop_configuration_b.png', 1);
        $menu['catprod'] = array(\JText::_('JSHOP_CAT_PROD'), 'index.php?option=com_jshopping&controller=config&task=catprod', 'jshop_products_b.png', 1);
        $menu['checkout'] = array(\JText::_('JSHOP_CHECKOUT'), 'index.php?option=com_jshopping&controller=config&task=checkout', 'jshop_orders_b.png', 1);
        $menu['fieldregister'] = array(\JText::_('JSHOP_REGISTER_FIELDS'), 'index.php?option=com_jshopping&controller=config&task=fieldregister', 'jshop_country_list_b.png', 1);
        $menu['currency'] = array(\JText::_('JSHOP_CURRENCY_PARAMETERS'), 'index.php?option=com_jshopping&controller=config&task=currency', 'jshop_currencies_b.png', 1);
        $menu['image'] = array(\JText::_('JSHOP_IMAGE_VIDEO_PARAMETERS'), 'index.php?option=com_jshopping&controller=config&task=image', 'jshop_image_video_b.png', 1);
        $menu['statictext'] = array(\JText::_('JSHOP_STATIC_TEXT'), 'index.php?option=com_jshopping&controller=config&task=statictext', 'jshop_mein_page_b.png', 1);
        $menu['seo'] = array(\JText::_('JSHOP_SEO'), 'index.php?option=com_jshopping&controller=config&task=seo', 'jshop_languages_b.png', 1);
        $menu['storeinfo'] = array(\JText::_('JSHOP_STORE_INFO'), 'index.php?option=com_jshopping&controller=config&task=storeinfo', 'jshop_store_info_b.png', 1);
        $menu['otherconfig'] = array(\JText::_('JSHOP_OC'), 'index.php?option=com_jshopping&controller=config&task=otherconfig', 'jshop_reviews_b.png', 1);                
        
        $dispatcher->triggerEvent( 'onBeforeAdminConfigPanelIcoDisplay', array(&$menu) );
        
        foreach($menu as $item){
            if ($item[3]){
                self::quickiconButton($item[1], $item[2], $item[0]);
            }
        }
    }

    public static function getItemsConfigPanelMenu(){
        $jshopConfig = \JSFactory::getConfig();
        $user = \JFactory::getUser();
        $dispatcher = \JFactory::getApplication();
        
        $menu = array();
        $menu['adminfunction'] = array(\JText::_('JSHOP_SHOP_FUNCTION'), 'index.php?option=com_jshopping&controller=config&task=adminfunction', 'jshop_options_b.png', 1);
        $menu['general'] = array(\JText::_('JSHOP_GENERAL_PARAMETERS'), 'index.php?option=com_jshopping&controller=config&task=general', 'jshop_configuration_b.png', 1);
        $menu['catprod'] = array(\JText::_('JSHOP_CAT_PROD'), 'index.php?option=com_jshopping&controller=config&task=catprod', 'jshop_products_b.png', 1);
        $menu['checkout'] = array(\JText::_('JSHOP_CHECKOUT'), 'index.php?option=com_jshopping&controller=config&task=checkout', 'jshop_orders_b.png', 1);
        $menu['fieldregister'] = array(\JText::_('JSHOP_REGISTER_FIELDS'), 'index.php?option=com_jshopping&controller=config&task=fieldregister', 'jshop_country_list_b.png', 1);
        $menu['currency'] = array(\JText::_('JSHOP_CURRENCY_PARAMETERS'), 'index.php?option=com_jshopping&controller=config&task=currency', 'jshop_currencies_b.png', 1);
        $menu['image'] = array(\JText::_('JSHOP_IMAGE_VIDEO_PARAMETERS'), 'index.php?option=com_jshopping&controller=config&task=image', 'jshop_image_video_b.png', 1);
        $menu['statictext'] = array(\JText::_('JSHOP_STATIC_TEXT'), 'index.php?option=com_jshopping&controller=config&task=statictext', 'jshop_mein_page_b.png', 1);
        $menu['seo'] = array(\JText::_('JSHOP_SEO'), 'index.php?option=com_jshopping&controller=config&task=seo', 'jshop_languages_b.png', 1);
        $menu['storeinfo'] = array(\JText::_('JSHOP_STORE_INFO'), 'index.php?option=com_jshopping&controller=config&task=storeinfo', 'jshop_store_info_b.png', 1);
        $menu['otherconfig'] = array(\JText::_('JSHOP_OC'), 'index.php?option=com_jshopping&controller=config&task=otherconfig', 'jshop_reviews_b.png', 1);                
        
        $dispatcher->triggerEvent( 'onBeforeAdminConfigPanelMenuDisplay', array(&$menu) );
        
        return $menu;
    }


    public static function checkAccessController($name){
        $app = \JFactory::getApplication();
        $user = \JFactory::getUser();
        
        $adminaccess = $user->authorise('core.admin', 'com_jshopping');
        $installaccess = $user->authorise('core.admin.install', 'com_jshopping');
        
        $access = array();
        $access["config"] = $user->authorise('core.admin', 'com_jshopping')==1;
        $access["languages"] = $user->authorise('core.admin', 'com_jshopping')==1;
        $access["payments"] = $user->authorise('core.admin', 'com_jshopping')==1;
        $access["shippings"] = $user->authorise('core.admin', 'com_jshopping')==1;
        $access["shippingsprices"] = $user->authorise('core.admin', 'com_jshopping')==1;
        $access["vendors"] = $user->authorise('core.admin', 'com_jshopping')==1;
        $access["statistic"] = $user->authorise('core.admin', 'com_jshopping')==1;
        $access["addons"] = $user->authorise('core.admin', 'com_jshopping')==1;
        $access["logs"] = $user->authorise('core.admin', 'com_jshopping')==1;
        $access["update"] = $user->authorise('core.admin.install', 'com_jshopping')==1;

        \JFactory::getApplication()->triggerEvent('onBeforeAdminCheckAccessController', array(&$access));
        
        if (isset($access[$name]) && !$access[$name]) {
			$app->enqueueMessage(\JText::_('JERROR_ALERTNOAUTHOR'), 'warning');
            $app->redirect('index.php');
            return 0;
        }
    }

    public static function displaySubmenuOptions($active=""){
        include(JPATH_COMPONENT_ADMINISTRATOR."/tmpl/panel/options_submenu.php");
    }

    public static function displaySubmenuConfigs($active=""){
        include(JPATH_COMPONENT_ADMINISTRATOR."/tmpl/config/submenu.php");
    }

    public static function getIdVendorForCUser(){
    static $id;
    $jshopConfig = \JSFactory::getConfig();

        if (!$jshopConfig->admin_show_vendors) return 0;
        if (!isset($id)){
            $user = \JFactory::getUser();
            $adminaccess = $user->authorise('core.admin', 'com_jshopping');
            if ($adminaccess){
                $id = 0;    
            }else{
                $vendors = \JSFactory::getModel("vendors");    
                $id = $vendors->getIdVendorForUserId($user->id);
            }
        }
        return $id; 
    }

    public static function checkAccessVendorToProduct($id_vendor_cuser, $product_id){
        $app = \JFactory::getApplication();
        $product = \JSFactory::getTable('product');
        $product->load($product_id);
        if ($product->vendor_id!=$id_vendor_cuser){
			$app->enqueueMessage(\JText::_('JERROR_ALERTNOAUTHOR'), 'warning');
            $app->redirect('index.php');
            return 0;
        }
    }
	
	public static function SEFLinkFromAdmin($link, $fullurl = 0, $langprefix=''){
		$config =\JFactory::getConfig();
        $liveurlhost = \JURI::getInstance()->toString(array("scheme",'host', 'port'));
		$shop_item_id = \JSHelper::getDefaultItemid($link);
		$app = \Joomla\CMS\Application\CMSApplication::getInstance('site');
		$router = $app::getRouter();
		if (!preg_match('/Itemid=/', $link)){
			if (!preg_match('/\?/', $link)) $sp = "?"; else $sp = "&";
			$link .= $sp."Itemid=".$shop_item_id;
		}
		$uri = $router->build($link);
		$url = $uri->toString();
		$url = str_replace('/administrator', '', $url);
        if ($langprefix!=''){
            if ($config->get('sef_rewrite')){
                $url = "/".$langprefix.$url;
            }else{
                $url = str_replace("index.php", "index.php/".$langprefix, $url);
            }
        }
        if ($fullurl){
            $url = $liveurlhost.$url;
        }
		return $url;
    }
}