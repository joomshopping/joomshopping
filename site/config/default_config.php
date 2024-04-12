<?php
/**
* @version      5.2.0 13.05.2023
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$config->load_id = 1;

$config->path = JPATH_ROOT."/components/com_jshopping/";
$config->admin_path = JPATH_ROOT.'/administrator/components/com_jshopping/';

$config->live_path = \JURI::root().'components/com_jshopping/';
$config->live_admin_path = \JURI::root().'administrator/components/com_jshopping/';

$config->log_path = JPATH_ROOT."/components/com_jshopping/log/";

$config->importexport_live_path = $config->live_path."files/importexport/";
$config->importexport_path = $config->path."files/importexport/";

$config->image_category_live_path = $config->live_path."files/img_categories";
$config->image_category_path = $config->path."files/img_categories";

$config->image_product_live_path = $config->live_path."files/img_products";
$config->image_product_path = $config->path."files/img_products";

$config->image_manufs_live_path = $config->live_path."files/img_manufs";
$config->image_manufs_path = $config->path."files/img_manufs";

$config->video_product_live_path = $config->live_path."files/video_products";
$config->video_product_path = $config->path."files/video_products";

$config->demo_product_live_path = $config->live_path."files/demo_products";
$config->demo_product_path = $config->path."files/demo_products";

$config->files_product_live_path = $config->live_path."files/files_products";
$config->files_product_path = $config->path."files/files_products";

$config->pdf_orders_live_path = $config->live_path."files/pdf_orders";
$config->pdf_orders_path = $config->path."files/pdf_orders";

$config->image_attributes_live_path = $config->live_path."files/img_attributes";
$config->image_attributes_path = $config->path."files/img_attributes";

$config->image_labels_live_path = $config->live_path."files/img_labels";
$config->image_labels_path = $config->path."files/img_labels";

$config->image_vendors_live_path = $config->live_path."files/img_vendors";
$config->image_vendors_path = $config->path."files/img_vendors";

$config->template_path = $config->path."templates/";

$config->css_path = $config->path."css/";
$config->css_live_path = $config->live_path."css/";

$config->file_generete_pdf_order = '\\Joomla\\Component\\Jshopping\\Site\\Pdf\\Order';

$config->xml_update_path = "http://www.webdesigner-profi.de/joomla-webdesign/update/update.xml";
$config->updates_site_path = "http://www.webdesigner-profi.de/joomla-webdesign/joomla-shop/downloads/updates.html";
$config->updates_server['sm0'] = "http://www.webdesigner-profi.de/joomla-webdesign/update/sm0";
$config->updates_server['sm1'] = "http://demo.joomshopping.com/demo";
$config->display_updates_version = 1;
$config->noimage = 'noimage.gif';
$config->shippinginfourl = 'index.php?option=com_jshopping&controller=content&task=view&page=shipping';

$config->user_field_client_type = array(
    0=>'JSHOP_REG_SELECT',
    1=>'JSHOP_PRIVAT_CLIENT',
    2=>'JSHOP_FIRMA_CLIENT'
);
$config->user_field_title = array(
    0=>'JSHOP_REG_SELECT',
    1=>'JSHOP_MR',
    2=>'JSHOP_MS'
);
#$config->user_field_title[3] = 'JSHOP_MX';

$config->sorting_products_field_select = array(
    1=>'name',
    2=>'prod.product_price',
    3=>'prod.product_date_added',
    5=>'prod.average_rating',
    6=>'prod.hits',
    4=>'pr_cat.product_ordering'
);
$config->sorting_products_name_select = array(
    1=>'JSHOP_SORT_ALPH',
    2=>'JSHOP_SORT_PRICE',
    3=>'JSHOP_SORT_DATE',
    5=>'JSHOP_SORT_RATING',
    6=>'JSHOP_SORT_POPULAR',
    4=>'JSHOP_SORT_MANUAL'
);

$config->sorting_products_field_s_select = array(
    1=>'name',
    2=>'prod.product_price',
    3=>'prod.product_date_added',
    5=>'prod.average_rating',
    6=>'prod.hits'
);
$config->sorting_products_name_s_select = array(
    1=>'JSHOP_SORT_ALPH',
    2=>'JSHOP_SORT_PRICE',
    3=>'JSHOP_SORT_DATE',
    5=>'JSHOP_SORT_RATING',
    6=>'JSHOP_SORT_POPULAR'
);

$config->format_currency = array(
    '1'=>'00Symb',
    '00 Symb',
    'Symb00',
    'Symb 00'
);
$config->count_product_select = array(
    '5' => 5,
    '10' => 10,
    '15' => 15,
    '20' => 20,
    '25' => 25,
    '50' => 50,
    '99999'=>'JSHOP_ALL'
);
$config->product_attribute_type_template = array(
    1=>'attribute_input_select',
    2=>'attribute_input_radio'
);

$config->payment_status_enable_download_sale_file = [5, 6, 7];
$config->payment_status_return_product_in_stock = [3, 4];
$config->payment_status_for_cancel_client = 3;
$config->payment_status_disable_cancel_client = [7];
$config->payment_status_no_create_order = [3];
$config->payment_status_paid = 6;
$config->order_stock_removed_only_paid_status = 0;
$config->cart_back_to_shop = "list"; //product, list, shop
$config->product_button_back_use_end_list = 0;
$config->display_tax_id_in_pdf = 0;
$config->image_quality = 100;
$config->image_fill_color = 0xffffff;
$config->image_interlace = 1;
$config->product_price_qty_discount = 2; // (1 - price, 2 - percent)
$config->rating_starparts = 2; //star is divided to {2} part
$config->show_list_price_shipping_weight = 0;
$config->product_price_precision = 2;
$config->cart_decimal_qty_precision = 2;
$config->product_add_price_default_unit = 3;
$config->default_frontend_currency = 0;
$config->product_file_upload_via_ftp = 0; //0 - upload file, 1- set name file, 2- {0,1}
$config->product_file_upload_count = 1;
$config->product_image_upload_count = 10;
$config->product_video_upload_count = 3;
$config->image_product_max_size_file = 0; //kb
$config->max_number_download_sale_file = 3; //0 - unlimit
$config->max_day_download_sale_file = 365; //0 - unlimit
$config->show_insert_code_in_product_video = 0;
$config->order_display_new_digital_products = 1;
$config->display_user_groups_info = 1;
$config->display_user_group = 1;
$config->display_delivery_time_for_product_in_order_mail = 1;
$config->show_delivery_time_checkout = 1;
$config->show_delivery_date = 0;
$config->load_jquery_lightbox = 1;
$config->load_javascript = 1;
$config->load_css = 1;
$config->tax = 1;
$config->show_manufacturer_in_cart = 0;
$config->count_products_to_page_tophits = 12;
$config->count_products_to_page_toprating = 12;
$config->count_products_to_page_label = 12;
$config->count_products_to_page_bestseller = 12;
$config->count_products_to_page_random = 12;
$config->count_products_to_page_last = 12;
$config->count_products_to_page_search = 12;
$config->count_products_to_row_tophits = 3;
$config->count_products_to_row_toprating = 3;
$config->count_products_to_row_label = 3;
$config->count_products_to_row_bestseller = 3;
$config->count_products_to_row_random = 3;
$config->count_products_to_row_last = 3;
$config->count_products_to_row_search = 3;
$config->count_manufacturer_to_row = 2;
$config->admin_count_related_search = 20;
$config->date_invoice_in_invoice = 0;
$config->weight_in_invoice = 0;
$config->payment_in_invoice = 0;
$config->shipping_in_invoice = 0;
$config->display_null_package_price = 0;
$config->tax_on_delivery_address = 0;
$config->stock = 1;
$config->display_short_descr_multiline = 0;
$config->price_product_round = 1;
$config->send_order_email = 1;
$config->send_invoice_manually = 0;
$config->display_agb = 1;
$config->check_php_agb = 0;
$config->field_birthday_format = '%d.%m.%Y';
$config->cart_basic_price_show = 0;
$config->list_products_calc_basic_price_from_product_price = 0;
$config->calc_basic_price_from_product_price = 0;
$config->not_update_user_joomla = 0;
$config->update_username_joomla = 0;
$config->step_4_3 = 0;
$config->user_discount_not_apply_prod_old_price = 0;
$config->ordernumberlength = 8;
$config->no_fix_brutoprice_to_tax = 0;
$config->admin_order_edit_more = 0;
$config->return_policy_for_product = 0;
$config->no_return_all = 0;
$config->show_return_policy_text_in_email_order = 0;
$config->show_return_policy_text_in_pdf = 0;
$config->hide_delivery_time_out_of_stock = 0;
$config->attr_display_addprice_all_sign = 0;
$config->formatprice_style_currency_span = 0;
$config->adm_prod_list_default_sorting = 'product_id';
$config->adm_prod_list_default_sorting_dir = 'asc';
$config->get_last_products_order_query = 'prod.product_id';
$config->user_registered_download_sale_file = 0;
$config->multi_charactiristic_separator = ", ";
$config->advert = 1;
$config->hide_weight_in_cart_weight0 = 1;
$config->hide_from_basic_price = 0;
$config->ext_menu_checkout_step = 0;
$config->product_hide_price_null = 0;
$config->admin_show_weight = 1;
$config->frontend_select_class_css = 'inputbox form-control';
$config->frontend_attribute_select_class_css = 'inputbox';
$config->registration_select_class_css = 'inputbox form-control';
$config->frontend_attribute_select_size = 1;
$config->free_shipping_calc_from_total_and_discount = 0;
$config->auto_backup_addon_files = 1;
$config->send_admin_mail_order_status_appadmin = 0;
$config->send_admin_mail_order_status = 1;
$config->checkout_step4_show_error_shipping_config = 1;
$config->category_sorting_direction = 'asc';
$config->manufacturer_sorting_direction = 'asc';
$config->get_vendors_order_query = 'shop_name';
$config->order_notfinished_default = 0;
$config->display_tax_0 = 0;
$config->rating_star_width = 20;
$config->video_html5 = 1;
$config->video_html5_type = '';
$config->audio_html5_type = '';
$config->file_extension_video = array(
    'mp4'
);
$config->file_extension_audio = array(
    'mp3'
);
$config->product_imagename_lowercase = 0;
$config->show_cart_clear = 0;
$config->hide_product_rating = 0;
$config->show_client_id_in_my_account = 0;
$config->orderchangestatus_email_html = 0;
$config->search_form_method = 'post';
$config->admin_list_related_show_prod_code = 1;
$config->manufacturer_code_in_cart = 0;
$config->manufacturer_code_in_product_list = 0;
$config->manufacturer_code_in_product_detail = 0;
$config->product_list_pagination_result_counter = 1;
$config->admin_product_list_manufacture_code = 0;
$config->register_mail_html_format = false;
$config->register_mail_admin_html_format = false;
$config->activation_mail_html_format = false;
$config->activation_mail_admin_html_format = false;
$config->step5_check_coupon = true;
$config->hide_text_product_available = 0;
$config->checkout_step5_show_email = 0;
$config->checkout_step5_show_phone = 0;
$config->productDownloadFilePart8kb = 1;
$config->show_short_descr_insted_of = 1;
$config->allow_image_upload = array('jpeg','jpg','gif','png','webp');
$config->use_summ_for_calcule_payment_with_discount = 0;
$config->product_related_order_by = 'relation.id';
$config->product_img_seo = 0;
$config->product_use_main_category_id = 0;

$config->default_template_block_list_product = 'list_products/list_products.php';
$config->default_template_no_list_product = 'list_products/no_products.php';
$config->default_template_block_form_filter_product = 'list_products/form_filters.php';
$config->default_template_block_pagination_product = 'list_products/block_pagination.php';

$config->load_javascript_bootstrap = 1;
$config->load_javascript_jquery = 1;
$config->file_jquery_media_js = $config->live_path.'js/jquery/jquery.media.js';
$config->file_functions_js = $config->live_path.'js/functions.js';
$config->file_lightbox_js = $config->live_path.'js/jquery/jquery.lightbox.js';
$config->file_lightbox_css = $config->live_path.'css/jquery.lightbox.css';
$config->script_lightbox_init = 'var jshopParams = jshopParams || {};
    jshopParams.initJSlightBox=1;
    jshopParams.liveurl="'.\JURI::root().'";
    jshopParams.txtImage="'.JText::_('JSHOP_IMAGE').'";
    jshopParams.txtOf="'.JText::_('JSHOP_OF').'";';
$config->file_metadata_js = $config->live_path.'js/jquery/jquery.MetaData.js';
$config->file_rating_js = $config->live_path.'js/jquery/jquery.rating.pack.js';
$config->file_rating_css = $config->live_path.'css/jquery.rating.css';

$config->product_search_fields = array(
    'prod.ml:name',
    'prod.ml:short_description',
    'prod.ml:description',
    'prod.product_ean',
    'prod.manufacturer_code'
);

$config->attribut_dep_sorting_in_product = "V.value_ordering"; // (V.value_ordering, value_name, PA.price, PA.ean, PA.count)
$config->attribut_nodep_sorting_in_product = "V.value_ordering"; // (V.value_ordering, value_name, addprice)
$config->new_extra_field_type = 'varchar(100)';

$config->disable_admin = array(
    'product_price_per_consignment' => 0,
    'product_old_price' => 0,
    'product_ean' => 0,
    'manufacturer_code' => 0,
    'product_url' => 0,
    'product_manufacturer' => 0,
    'currencies' => 0,
    'orderstatus' => 0,
    'countries' => 0,
    'usergroups' => 0,
    'importexport' => 0,
    'addons' => 0,
    'statistic' => 0
);

$config->sys_static_text = array(
    'home',
    'manufacturer',
    'agb',
    'return_policy',
    'order_email_descr',
    'order_email_descr_manually',
    'order_email_descr_end',
    'order_finish_descr',
    'shipping',
    'privacy_statement',
    'cart'
);

$other_config = array(
    'auto_backup_addon_files',
    'tax_on_delivery_address',
    "cart_back_to_shop",
    "product_button_back_use_end_list",
    "product_use_main_category_id",
    "display_tax_id_in_pdf",
    "product_price_qty_discount",
    "rating_starparts",
    "show_list_price_shipping_weight",
    "product_price_precision",
    "cart_decimal_qty_precision",
    "default_frontend_currency",
    "product_file_upload_via_ftp",
    "product_file_upload_count",
    "product_image_upload_count",
    "product_video_upload_count",
    "show_insert_code_in_product_video",
    "product_img_seo",
    "max_number_download_sale_file",
    "max_day_download_sale_file",
    "order_display_new_digital_products",
    "display_user_groups_info",
    "display_user_group",
    "load_jquery_lightbox",
    "load_javascript",
    "load_css",
    'list_products_calc_basic_price_from_product_price',
    'hide_from_basic_price','calc_basic_price_from_product_price',
    'user_discount_not_apply_prod_old_price'
);

$other_config_checkbox = array(
    'auto_backup_addon_files',
    'tax_on_delivery_address',
    'product_button_back_use_end_list',
    "product_use_main_category_id",
    "show_list_price_shipping_weight",
    "display_tax_id_in_pdf",
    "show_insert_code_in_product_video",
    "product_img_seo",
    "order_display_new_digital_products",
    "display_user_groups_info",
    "display_user_group",
    "load_jquery_lightbox",
    "load_css",
    "load_javascript",
    'set_old_price_after_group_set_price',
    'list_products_calc_basic_price_from_product_price',
    'hide_from_basic_price',
    'calc_basic_price_from_product_price',
    'user_discount_not_apply_prod_old_price'
);
$other_config_select = array(
    'cart_back_to_shop'=>array(
        'product'=>'product',
        'list'=>'list',
        'shop'=>'shop',
		'home'=>'home'
    ),
    'product_price_qty_discount'=>array(
        '1'=>'price',
        '2'=>'percent'
    ),
    'product_file_upload_via_ftp'=>array(
        0=>'upload_file',
        1=>'set_name_file',
        2=>'upload_file_or_set_name_file'
    )
);

$fields_client_sys = array();
$fields_client_sys['register'][] = "email";

$fields_client = array();
$fields_client['register'][] = "title";
$fields_client['register'][] = "f_name";
$fields_client['register'][] = "l_name";
$fields_client['register'][] = "m_name";
$fields_client['register'][] = "client_type";
$fields_client['register'][] = "firma_name";
$fields_client['register'][] = "firma_code";
$fields_client['register'][] = "tax_number";
$fields_client['register'][] = "email";
$fields_client['register'][] = "email2";
$fields_client['register'][] = "birthday";
$fields_client['register'][] = "home";
$fields_client['register'][] = "apartment";
$fields_client['register'][] = "street";
$fields_client['register'][] = "street_nr";
$fields_client['register'][] = "zip";
$fields_client['register'][] = "city";
$fields_client['register'][] = "state";
$fields_client['register'][] = "country";
$fields_client['register'][] = "phone";
$fields_client['register'][] = "mobil_phone";
$fields_client['register'][] = "fax";
$fields_client['register'][] = "ext_field_1";
$fields_client['register'][] = "ext_field_2";
$fields_client['register'][] = "ext_field_3";
$fields_client['register'][] = "privacy_statement";
$fields_client['register'][] = "u_name";
$fields_client['register'][] = "password";
$fields_client['register'][] = "password_2";

$fields_client_sys['address'] = array();

$fields_client['address'][] = "title";
$fields_client['address'][] = "f_name";
$fields_client['address'][] = "l_name";
$fields_client['address'][] = "m_name";
$fields_client['address'][] = "client_type";
$fields_client['address'][] = "firma_name";
$fields_client['address'][] = "firma_code";
$fields_client['address'][] = "tax_number";
$fields_client['address'][] = "email";
$fields_client['address'][] = "email2";
$fields_client['address'][] = "birthday";
$fields_client['address'][] = "home";
$fields_client['address'][] = "apartment";
$fields_client['address'][] = "street";
$fields_client['address'][] = "street_nr";
$fields_client['address'][] = "zip";
$fields_client['address'][] = "city";
$fields_client['address'][] = "state";
$fields_client['address'][] = "country";
$fields_client['address'][] = "phone";
$fields_client['address'][] = "mobil_phone";
$fields_client['address'][] = "fax";
$fields_client['address'][] = "ext_field_1";
$fields_client['address'][] = "ext_field_2";
$fields_client['address'][] = "ext_field_3";
$fields_client['address'][] = "privacy_statement";

$fields_client['address'][] = "d_title";
$fields_client['address'][] = "d_f_name";
$fields_client['address'][] = "d_l_name";
$fields_client['address'][] = "d_m_name";
$fields_client['address'][] = "d_firma_name";
$fields_client['address'][] = "d_email";
$fields_client['address'][] = "d_birthday";
$fields_client['address'][] = "d_home";
$fields_client['address'][] = "d_apartment";
$fields_client['address'][] = "d_street";
$fields_client['address'][] = "d_street_nr";
$fields_client['address'][] = "d_zip";
$fields_client['address'][] = "d_city";
$fields_client['address'][] = "d_state";
$fields_client['address'][] = "d_country";
$fields_client['address'][] = "d_phone";
$fields_client['address'][] = "d_mobil_phone";
$fields_client['address'][] = "d_fax";
$fields_client['address'][] = "d_ext_field_1";
$fields_client['address'][] = "d_ext_field_2";
$fields_client['address'][] = "d_ext_field_3";

$fields_client_sys['editaccount'] = array();

$fields_client['editaccount'][] = "title";
$fields_client['editaccount'][] = "f_name";
$fields_client['editaccount'][] = "l_name";
$fields_client['editaccount'][] = "m_name";
$fields_client['editaccount'][] = "client_type";
$fields_client['editaccount'][] = "firma_name";
$fields_client['editaccount'][] = "firma_code";
$fields_client['editaccount'][] = "tax_number";
$fields_client['editaccount'][] = "email";
$fields_client['editaccount'][] = "birthday";
$fields_client['editaccount'][] = "home";
$fields_client['editaccount'][] = "apartment";
$fields_client['editaccount'][] = "street";
$fields_client['editaccount'][] = "street_nr";
$fields_client['editaccount'][] = "zip";
$fields_client['editaccount'][] = "city";
$fields_client['editaccount'][] = "state";
$fields_client['editaccount'][] = "country";
$fields_client['editaccount'][] = "phone";
$fields_client['editaccount'][] = "mobil_phone";
$fields_client['editaccount'][] = "fax";
$fields_client['editaccount'][] = "ext_field_1";
$fields_client['editaccount'][] = "ext_field_2";
$fields_client['editaccount'][] = "ext_field_3";
$fields_client['editaccount'][] = "privacy_statement";
$fields_client['editaccount'][] = "password";
$fields_client['editaccount'][] = "password_2";

$fields_client['editaccount'][] = "d_title";
$fields_client['editaccount'][] = "d_f_name";
$fields_client['editaccount'][] = "d_l_name";
$fields_client['editaccount'][] = "d_m_name";
$fields_client['editaccount'][] = "d_firma_name";
$fields_client['editaccount'][] = "d_email";
$fields_client['editaccount'][] = "d_birthday";
$fields_client['editaccount'][] = "d_home";
$fields_client['editaccount'][] = "d_apartment";
$fields_client['editaccount'][] = "d_street";
$fields_client['editaccount'][] = "d_street_nr";
$fields_client['editaccount'][] = "d_zip";
$fields_client['editaccount'][] = "d_city";
$fields_client['editaccount'][] = "d_state";
$fields_client['editaccount'][] = "d_country";
$fields_client['editaccount'][] = "d_phone";
$fields_client['editaccount'][] = "d_mobil_phone";
$fields_client['editaccount'][] = "d_fax";
$fields_client['editaccount'][] = "d_ext_field_1";
$fields_client['editaccount'][] = "d_ext_field_2";
$fields_client['editaccount'][] = "d_ext_field_3";

$config->fields_client_check = [
	'title' => ['int', 'JSHOP_REGWARN_TITLE'],
	'f_name' => ['string', 'JSHOP_REGWARN_NAME'],
	'l_name' => ['string', 'JSHOP_REGWARN_LNAME'],
	'm_name' => ['string', 'JSHOP_REGWARN_MNAME'],
	'firma_name' => ['string', 'JSHOP_REGWARN_FIRMA_NAME'],
	'client_type' => ['int', 'JSHOP_REGWARN_CLIENT_TYPE'],
    'firma_code' => ['string', 'JSHOP_REGWARN_FIRMA_CODE', function($user, $config_fields){return ($user->client_type==2 || !$config_fields['client_type']['display']);}],
	'tax_number' => ['string', 'JSHOP_REGWARN_TAX_NUMBER', function($user, $config_fields){return ($user->client_type==2 || !$config_fields['client_type']['display']);}],
	'email' => ['email', 'JSHOP_REGWARN_MAIL'],
	'email2' => ['email', 'JSHOP_REGWARN_MAIL'],
	'birthday' => ['string', 'JSHOP_REGWARN_BIRTHDAY'],
	'u_name' => ['string', 'JSHOP_REGWARN_UNAME'],
	'password' => ['string', 'JSHOP_REGWARN_PASSWORD'],
	'password2' => ['string', 'JSHOP_REGWARN_PASSWORD_NOT_MATCH'],
	'home' => ['string', 'JSHOP_REGWARN_HOME'],
	'apartment' => ['string', 'JSHOP_REGWARN_APARTMENT'],
	'street' => ['string', 'JSHOP_REGWARN_STREET'],
	'street_nr' => ['string', 'JSHOP_REGWARN_STREET'],
	'zip' => ['string', 'JSHOP_REGWARN_ZIP'],
	'city' => ['string', 'JSHOP_REGWARN_CITY'],
	'state' => ['string', 'JSHOP_REGWARN_STATE'],
	'country' => ['int', 'JSHOP_REGWARN_COUNTRY'],
	'phone' => ['string', 'JSHOP_REGWARN_PHONE'],
	'mobil_phone' => ['string', 'JSHOP_REGWARN_MOBIL_PHONE'],
	'fax' => ['string', 'JSHOP_REGWARN_FAX'],
	'ext_field_1' => ['string', 'JSHOP_REGWARN_EXT_FIELD_1'],
	'ext_field_2' => ['string', 'JSHOP_REGWARN_EXT_FIELD_2'],
	'ext_field_3' => ['string', 'JSHOP_REGWARN_EXT_FIELD_3'],
	'd_title' => ['int', 'JSHOP_REGWARN_TITLE_DELIVERY'],
	'd_f_name' => ['string', 'JSHOP_REGWARN_NAME_DELIVERY'],
	'd_l_name' => ['string', 'JSHOP_REGWARN_LNAME_DELIVERY'],
	'd_m_name' => ['string', 'JSHOP_REGWARN_MNAME_DELIVERY'],
	'd_firma_name' => ['string', 'JSHOP_REGWARN_FIRMA_NAME_DELIVERY'],
	'd_firma_code' => ['string', 'JSHOP_REGWARN_FIRMA_CODE_DELIVERY'],
	'd_tax_number' => ['string', 'JSHOP_REGWARN_TAX_NUMBER_DELIVERY'],
	'd_email' => ['email', 'JSHOP_REGWARN_MAIL_DELIVERY'],
	'd_birthday' => ['string', 'JSHOP_REGWARN_BIRTHDAY_DELIVERY'],
	'd_home' => ['string', 'JSHOP_REGWARN_HOME_DELIVERY'],
	'd_apartment' => ['string', 'JSHOP_REGWARN_APARTMENT_DELIVERY'],
	'd_street' => ['string', 'JSHOP_REGWARN_STREET_DELIVERY'],
    'd_street_nr' => ['string', 'JSHOP_REGWARN_STREET_DELIVERY'],
	'd_zip' => ['string', 'JSHOP_REGWARN_ZIP_DELIVERY'],
	'd_city' => ['string', 'JSHOP_REGWARN_CITY_DELIVERY'],
	'd_state' => ['string', 'JSHOP_REGWARN_STATE_DELIVERY'],
	'd_country' => ['int', 'JSHOP_REGWARN_COUNTRY_DELIVERY'],
	'd_phone' => ['string', 'JSHOP_REGWARN_PHONE_DELIVERY'],
	'd_mobil_phone' => ['string', 'JSHOP_REGWARN_MOBIL_PHONE_DELIVERY'],
	'd_fax' => ['string', 'JSHOP_REGWARN_FAX_DELIVERY'],
	'd_ext_field_1' => ['string', 'JSHOP_REGWARN_EXT_FIELD_1_DELIVERY'],
	'd_ext_field_2' => ['string', 'JSHOP_REGWARN_EXT_FIELD_2_DELIVERY'],
	'd_ext_field_3' => ['string', 'JSHOP_REGWARN_EXT_FIELD_3_DELIVERY'],
	'privacy_statement' => ['int', 'JSHOP_REGWARN_PRIVACY_STATEMENT'],
];

$config->fields_client_only_check = [
	'password', 'password2', 'email2', 'privacy_statement'
];
