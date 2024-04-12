<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$jshopConfig=\JSFactory::getConfig();
?>
<div class="row">
<div class="col-md-12">
<div id="j-main-container" class="j-main-container">
<?php \JSHelperAdmin::displaySubmenuConfigs('catprod');?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=config" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<?php print $this->tmp_html_start?>
<input type="hidden" name="task" value="">
<input type="hidden" name="tab" value="6">

<div class="col100">
<fieldset class="adminform">
    <legend><?php echo JText::_('JSHOP_LIST_PRODUCTS')." / ".JText::_('JSHOP_PRODUCT')?></legend>
<table class="admintable table-striped">
<?php if ($jshopConfig->tax){?>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_TAX')?>
    </td>
    <td>
        <input type="hidden" name="show_tax_in_product" value="0">
        <input type="checkbox" name="show_tax_in_product" value="1" <?php if ($jshopConfig->show_tax_in_product) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_TAX_IN_CART')?>
    </td>
    <td>
        <input type="hidden" name="show_tax_product_in_cart" value="0">
        <input type="checkbox" name="show_tax_product_in_cart" value="1" <?php if ($jshopConfig->show_tax_product_in_cart) echo 'checked="checked"';?> />
    </td>
</tr>
<?php }?>
<tr>
    <td class="key" style="width:220px;">
        <?php echo JText::_('JSHOP_SHOW_PLUS_SHIPPING')?>
    </td>
    <td>
        <input type="hidden" name="show_plus_shipping_in_product" value="0">
        <input type="checkbox" name="show_plus_shipping_in_product" value="1" <?php if ($jshopConfig->show_plus_shipping_in_product) echo 'checked="checked"';?> />
    </td>
</tr>
<?php if ($jshopConfig->stock){?>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_HIDE_PRODUCT_NOT_AVAIBLE_STOCK')?>
    </td>
    <td>
        <input type="hidden" name="hide_product_not_avaible_stock" value="0">
        <input type="checkbox" id="hide_product_not_avaible_stock" name="hide_product_not_avaible_stock" value="1" <?php if ($jshopConfig->hide_product_not_avaible_stock) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_HIDE_BUY_PRODUCT_NOT_AVAIBLE_STOCK')?>
    </td>
    <td>
        <input type="hidden" name="hide_buy_not_avaible_stock" value="0">
        <input type="checkbox" name="hide_buy_not_avaible_stock" value="1" <?php if ($jshopConfig->hide_buy_not_avaible_stock) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_HIDE_HIDE_TEXT_PRODUCT_NOT_AVAILABLE')?>
    </td>
    <td>
        <input type="hidden" name="hide_text_product_not_available" value="0">
        <input type="checkbox" name="hide_text_product_not_available" value="1" <?php if ($jshopConfig->hide_text_product_not_available) echo 'checked="checked"';?> />
    </td>
</tr>
<?php }?>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_DEFAULT_PRICE')?>
    </td>
    <td>
        <input type="hidden" name="product_list_show_price_default" value="0">
        <input type="checkbox" name="product_list_show_price_default" value="1" <?php if ($jshopConfig->product_list_show_price_default) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_HIDE_PRICE_NULL')?>
    </td>
    <td>
        <input type="hidden" name="product_hide_price_null" value="0">
        <input type="checkbox" name="product_hide_price_null" value="1" <?php if ($jshopConfig->product_hide_price_null) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_PRICE')?>
    </td>
    <td>
        <?php print $this->lists['displayprice'];?>
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_DISPLAY_WEIGHT_AS')?>
    </td>
    <td>
        <?php print $this->lists['units'];?>
    </td>
</tr>
	
</table>
</fieldset>
</div>
<div class="clr"></div>


<div class="col100">
<fieldset class="adminform">
    <legend><?php echo JText::_('JSHOP_LIST_PRODUCTS')?></legend>
<table class="admintable table-striped" width="100%" >
<tr>
    <td class="key" style="width:220px;">
        <?php echo JText::_('JSHOP_COUNT_PRODUCTS_PAGE')?>
    </td>
    <td>
        <input type="text" name="count_products_to_page" class="inputbox form-control" id="count_products_to_page" value="<?php echo $jshopConfig->count_products_to_page;?>" />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_COUNT_PRODUCTS_ROW')?>
    </td>
    <td>
        <input type="text" name="count_products_to_row" class="inputbox form-control" id="count_products_to_row" value="<?php echo $jshopConfig->count_products_to_row;?>" />
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_CHANGE_COUNTS_PROD_ROWS_FOR_ALL_CATS')?>
    </td>
    <td>
        <input type="hidden" name="update_count_prod_rows_all_cats" value="0">
        <input type="checkbox" name="update_count_prod_rows_all_cats" value="1">
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_COUNT_CATEGORY_ROW')?>
    </td>
    <td>
        <input type="text" name="count_category_to_row" class="inputbox form-control" id="count_category_to_row" value="<?php echo $jshopConfig->count_category_to_row;?>" />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_COUNT_MANUFACTURER_ROW')?>
    </td>
    <td>
        <input type="text" name="count_manufacturer_to_row" class="inputbox form-control" value="<?php echo $jshopConfig->count_manufacturer_to_row;?>" />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_ORDERING_CATEGORY')?>
    </td>
    <td>
        <?php print $this->lists['category_sorting'];?>
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_MANUFACTURER_SORTING')?>
    </td>
    <td>
        <?php print $this->lists['manufacturer_sorting'];?>
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_PRODUCT_SORTING')?>
    </td>
    <td>
        <?php print $this->lists['product_sorting'];?>
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_PRODUCT_SORTING_DIRECTION')?>
    </td>
    <td>
        <?php print $this->lists['product_sorting_direction'];?>
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_BAY_BUT_IN_CAT')?>
    </td>
    <td>
        <input type="hidden" name="show_buy_in_category" value="0">
        <input type="checkbox" name="show_buy_in_category" value="1" <?php if ($jshopConfig->show_buy_in_category) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_ABILITY_TO_SORT_PRODUCTS')?>
    </td>
    <td>
        <input type="hidden" name="show_sort_product" value="0">
        <input type="checkbox" name="show_sort_product" value="1" <?php if ($jshopConfig->show_sort_product) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_SELECTBOX_COUNT_PRODUCTS_TO_PAGE')?>
    </td>
    <td>
        <input type="hidden" name="show_count_select_products" value="0">
        <input type="checkbox" name="show_count_select_products" value="1" <?php if ($jshopConfig->show_count_select_products) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_FILTERS')?>
    </td>
    <td>
        <input type="hidden" name="show_product_list_filters" value="0">
        <input type="checkbox" name="show_product_list_filters" value="1" <?php if ($jshopConfig->show_product_list_filters) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_WEIGHT_PRODUCT')?>
    </td>
    <td>
        <input type="hidden" name="product_list_show_weight" value="0">
        <input type="checkbox" name="product_list_show_weight" value="1" <?php if ($jshopConfig->product_list_show_weight) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_MANUFACTURER')?>
    </td>
    <td>
        <input type="hidden" name="product_list_show_manufacturer" value="0">
        <input type="checkbox" name="product_list_show_manufacturer" value="1" <?php if ($jshopConfig->product_list_show_manufacturer) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_EAN_PRODUCT')?>
    </td>
    <td>
        <input type="hidden" name="product_list_show_product_code" value="0">
        <input type="checkbox" name="product_list_show_product_code" value="1" <?php if ($jshopConfig->product_list_show_product_code) echo 'checked="checked"';?> />
    </td>
</tr>
<?php if ($jshopConfig->disable_admin['manufacturer_code']==0){?>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_MANUFACTURER_CODE')?>
    </td>
    <td>
        <input type="hidden" name="manufacturer_code_in_product_list" value="0">
        <input type="checkbox" name="manufacturer_code_in_product_list" value="1" <?php if ($jshopConfig->manufacturer_code_in_product_list) echo 'checked="checked"';?> />
    </td>
</tr>
<?php }?>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_MIN_PRICE')?>
    </td>
    <td>
        <input type="hidden" name="product_list_show_min_price" value="0">
        <input type="checkbox" name="product_list_show_min_price" value="1" <?php if ($jshopConfig->product_list_show_min_price) echo 'checked="checked"';?> />
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_PRICE_DESCRIPTION')?>
    </td>
    <td>
        <input type="hidden" name="product_list_show_price_description" value="0">
        <input type="checkbox" name="product_list_show_price_description" value="1" <?php if ($jshopConfig->product_list_show_price_description) echo 'checked="checked"';?> />
    </td>
</tr>

<?php if ($jshopConfig->admin_show_delivery_time){?>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_DELIVERY_TIME')?>
    </td>
    <td>
        <input type="hidden" name="product_list_show_delivery_time" value="0">
        <input type="checkbox" name="product_list_show_delivery_time" value="1" <?php if (isset($jshopConfig->product_list_show_delivery_time) && $jshopConfig->product_list_show_delivery_time) echo 'checked="checked"';?> />
    </td>
</tr>
<?php }?>

<?php if ($jshopConfig->admin_show_vendors){?>
<tr>
    <td class="key">
      <?php echo JText::_('JSHOP_SHOW_VENDOR')?>
    </td>
    <td>
        <input type="hidden" name="product_list_show_vendor" value="0">
        <input type="checkbox" name="product_list_show_vendor" value="1" <?php if ($jshopConfig->product_list_show_vendor) echo 'checked="checked"';?> />
    </td>
</tr>
<?php }?>

<?php if ($jshopConfig->admin_show_product_extra_field){?>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_EXTRA_FIELDS')?>
    </td>
    <td>
        <?php print $this->lists['product_list_display_extra_fields'];?>
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_EXTRA_FIELDS_FILTER')?>
    </td>
    <td>
        <?php print $this->lists['filter_display_extra_fields'];?>
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_EXTRA_FIELDS_CART')?>
    </td>
    <td>
        <?php print $this->lists['cart_display_extra_fields'];?>
    </td>
</tr>
<?php }?>
<?php if ($jshopConfig->stock){?>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_QTY_IN_STOCK')?>
    </td>
    <td>
        <input type="hidden" name="product_list_show_qty_stock" value="0" />
        <input type="checkbox" name="product_list_show_qty_stock" value="1" <?php if ($jshopConfig->product_list_show_qty_stock) echo 'checked="checked"';?> />
    </td>
</tr>
<?php }?>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHORT_DESCR_MULTILINE')?>
    </td>
    <td>
        <input type="hidden" name="display_short_descr_multiline" value="0" />
        <input type="checkbox" name="display_short_descr_multiline" value="1" <?php if ($jshopConfig->display_short_descr_multiline) echo 'checked="checked"';?> />
    </td>
</tr>


<tr>
    <td><b><?php print JText::_('JSHP_SEOPAGE_tophitsproducts')?></b></td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_COUNT_PRODUCTS_PAGE')?>
    </td>
    <td><input type="text" name="count_products_to_page_tophits" class="inputbox form-control middle" value="<?php echo $jshopConfig->count_products_to_page_tophits;?>" /></td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_COUNT_PRODUCTS_ROW')?>
    </td>
    <td><input type="text" name="count_products_to_row_tophits" class="inputbox form-control middle" value="<?php echo $jshopConfig->count_products_to_row_tophits;?>" /></td>
</tr>


<tr>
    <td><b><?php print JText::_('JSHP_SEOPAGE_topratingproducts')?></b></td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_COUNT_PRODUCTS_PAGE')?>
    </td>
    <td><input type="text" name="count_products_to_page_toprating" class="inputbox form-control middle" value="<?php echo $jshopConfig->count_products_to_page_toprating;?>" /></td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_COUNT_PRODUCTS_ROW')?>
    </td>
    <td><input type="text" name="count_products_to_row_toprating" class="inputbox form-control middle" value="<?php echo $jshopConfig->count_products_to_row_toprating;?>" /></td>
</tr>


<tr>
    <td><b><?php print JText::_('JSHP_SEOPAGE_labelproducts')?></b></td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_COUNT_PRODUCTS_PAGE')?>
    </td>
    <td><input type="text" name="count_products_to_page_label" class="inputbox form-control middle" value="<?php echo $jshopConfig->count_products_to_page_label;?>" /></td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_COUNT_PRODUCTS_ROW')?>
    </td>
    <td><input type="text" name="count_products_to_row_label" class="inputbox form-control middle" value="<?php echo $jshopConfig->count_products_to_row_label;?>" /></td>
</tr>


<tr>
    <td><b><?php print JText::_('JSHP_SEOPAGE_bestsellerproducts')?></b></td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_COUNT_PRODUCTS_PAGE')?>
    </td>
    <td><input type="text" name="count_products_to_page_bestseller" class="inputbox form-control middle" value="<?php echo $jshopConfig->count_products_to_page_bestseller;?>" /></td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_COUNT_PRODUCTS_ROW')?>
    </td>
    <td><input type="text" name="count_products_to_row_bestseller" class="inputbox form-control middle" value="<?php echo $jshopConfig->count_products_to_row_bestseller;?>" /></td>
</tr>


<tr>
    <td><b><?php print JText::_('JSHP_SEOPAGE_randomproducts')?></b></td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_COUNT_PRODUCTS_PAGE')?>
    </td>
    <td><input type="text" name="count_products_to_page_random" class="inputbox form-control middle" value="<?php echo $jshopConfig->count_products_to_page_random;?>" /></td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_COUNT_PRODUCTS_ROW')?>
    </td>
    <td><input type="text" name="count_products_to_row_random" class="inputbox form-control middle" value="<?php echo $jshopConfig->count_products_to_row_random;?>" /></td>
</tr>


<tr>
    <td><b><?php print JText::_('JSHP_SEOPAGE_lastproducts')?></b></td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_COUNT_PRODUCTS_PAGE')?>
    </td>
    <td><input type="text" name="count_products_to_page_last" class="inputbox form-control middle" value="<?php echo $jshopConfig->count_products_to_page_last;?>" /></td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_COUNT_PRODUCTS_ROW')?>
    </td>
    <td><input type="text" name="count_products_to_row_last" class="inputbox form-control middle" value="<?php echo $jshopConfig->count_products_to_row_last;?>" /></td>
</tr>

<tr>
    <td><b><?php print JText::_('JSHP_SEOPAGE_search')?></b></td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_COUNT_PRODUCTS_PAGE')?>
    </td>
    <td><input type="text" name="count_products_to_page_search" class="inputbox form-control middle" value="<?php echo $jshopConfig->count_products_to_page_search;?>" /></td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_COUNT_PRODUCTS_ROW')?>
    </td>
    <td><input type="text" name="count_products_to_row_search" class="inputbox form-control middle" value="<?php echo $jshopConfig->count_products_to_row_search;?>" /></td>
</tr>

</table>
    
</fieldset>
</div>
<div class="clr"></div>

<div class="col100">
<fieldset class="adminform">
    <legend><?php echo JText::_('JSHOP_PRODUCT')?></legend>
<table class="admintable table-striped" >

<tr>
    <td class="key" style="width:220px;">
        <?php echo JText::_('JSHOP_SHOW_DEMO_TYPE_AS_MEDIA')?>
    </td>
    <td>
        <input type="hidden" name="demo_type" value="0">
        <input type="checkbox" name="demo_type" value="1" <?php if ($jshopConfig->demo_type) echo 'checked="checked"';?> />
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_MANUFACTURER_LOGO')?>
    </td>
    <td>
        <input type="hidden" name="product_show_manufacturer_logo" value="0">
        <input type="checkbox" name="product_show_manufacturer_logo" value="1" <?php if ($jshopConfig->product_show_manufacturer_logo) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_MANUFACTURER')?>
    </td>
    <td>
        <input type="hidden" name="product_show_manufacturer" value="0" />
        <input type="checkbox" name="product_show_manufacturer" value="1" <?php if ($jshopConfig->product_show_manufacturer) echo 'checked="checked"';?> />
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_WEIGHT_PRODUCT')?>
    </td>
    <td>
        <input type="hidden" name="product_show_weight" value="0">
        <input type="checkbox" name="product_show_weight" value="1" <?php if ($jshopConfig->product_show_weight) echo 'checked="checked"';?> />
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_PRODUCT_ATTRIBUT_FIRST_VALUE_EMPTY')?>
    </td>
    <td>
        <input type="hidden" name="product_attribut_first_value_empty" value="0">
        <input type="checkbox" name="product_attribut_first_value_empty" value="1" <?php if ($jshopConfig->product_attribut_first_value_empty) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_PRODUCT_ATTRIBUT_RADIO_VALUE_DISPLAY_VERTICAL')?>
    </td>
    <td>
        <input type="hidden" name="radio_attr_value_vertical" value="0">
        <input type="checkbox" name="radio_attr_value_vertical" value="1" <?php if ($jshopConfig->radio_attr_value_vertical) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_PRODUCT_ATTRIBUT_ADD_PRICE_DISPLAY')?>
    </td>
    <td>
        <input type="hidden" name="attr_display_addprice" value="0">
        <input type="checkbox" name="attr_display_addprice" value="1" <?php if ($jshopConfig->attr_display_addprice) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_PRODUCT_ATTRIBUT_SORTING')." (".JText::_('JSHOP_DEPENDENT').")"?>
    </td>
    <td>
        <?php print $this->lists['attribut_dep_sorting_in_product'];?>
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_PRODUCT_ATTRIBUT_SORTING')." (".JText::_('JSHOP_INDEPENDENT').")"?>
    </td>
    <td>
        <?php print $this->lists['attribut_nodep_sorting_in_product'];?>
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_HITS')?>
    </td>
    <td>
        <input type="hidden" name="show_hits" value="0">
        <input type="checkbox" name="show_hits" value="1" <?php if ($jshopConfig->show_hits) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_EAN_PRODUCT')?>
    </td>
    <td>
        <input type="hidden" name="show_product_code" value="0">
        <input type="checkbox" name="show_product_code" value="1" <?php if ($jshopConfig->show_product_code) echo 'checked="checked"';?> />
    </td>
</tr>
<?php if ($jshopConfig->disable_admin['manufacturer_code']==0){?>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_MANUFACTURER_CODE')?>
    </td>
    <td>
        <input type="hidden" name="manufacturer_code_in_product_detail" value="0">
        <input type="checkbox" name="manufacturer_code_in_product_detail" value="1" <?php if ($jshopConfig->manufacturer_code_in_product_detail) echo 'checked="checked"';?> />
    </td>
</tr>
<?php }?>
<?php if ($jshopConfig->admin_show_delivery_time){?>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_DELIVERY_TIME')?>
    </td>
    <td>
        <input type="hidden" name="show_delivery_time" value="0">
        <input type="checkbox" name="show_delivery_time" value="1" <?php if ($jshopConfig->show_delivery_time) echo 'checked="checked"';?> />
    </td>
</tr>
<?php }?>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_USE_PLUGIN_CONTENT')?>
    </td>
    <td>
        <input type="hidden" name="use_plugin_content" value="0">
        <input type="checkbox" name="use_plugin_content" value="1" <?php if ($jshopConfig->use_plugin_content) echo 'checked="checked"';?> />
    </td>
</tr>

<tr>
    <td class="key">
      <?php echo JText::_('JSHOP_ALLOW_REVIEW_PRODUCT')?>
    </td>
    <td>
        <input type="hidden" name="allow_reviews_prod" value="0">
        <input type="checkbox" name="allow_reviews_prod" value="1" <?php if ($jshopConfig->allow_reviews_prod) echo 'checked="checked"';?> />
    </td>
</tr> 
<tr>
    <td class="key">
      <?php echo JText::_('JSHOP_ALLOW_REVIEW_ONLY_REGISTERED')?>
    </td>
    <td>
        <input type="hidden" name="allow_reviews_only_registered" value="0">
        <input type="checkbox" name="allow_reviews_only_registered" value="1" <?php if ($jshopConfig->allow_reviews_only_registered) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
      <?php echo JText::_('JSHOP_DISPLAY_REVIEW_WITHOUT_CONFIRM')?>
    </td>
    <td>
        <input type="hidden" name="display_reviews_without_confirm" value="0">
        <input type="checkbox" name="display_reviews_without_confirm" value="1" <?php if ($jshopConfig->display_reviews_without_confirm) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
      <?php echo JText::_('JSHOP_SHOP_BUTTON_BACK')?>
    </td>
    <td>
        <input type="hidden" name="product_show_button_back" value="0">
        <input type="checkbox" name="product_show_button_back" value="1" <?php if ($jshopConfig->product_show_button_back) echo 'checked="checked"';?> />
    </td>
</tr>
<?php if ($jshopConfig->admin_show_vendors){?>
<tr>
    <td class="key">
      <?php echo JText::_('JSHOP_SHOW_VENDOR')?>
    </td>
    <td>
        <input type="hidden" name="product_show_vendor" value="0">
        <input type="checkbox" name="product_show_vendor" value="1" <?php if ($jshopConfig->product_show_vendor) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
      <?php echo JText::_('JSHOP_SHOW_VENDOR_DETAIL')?>
    </td>
    <td>
        <input type="hidden" name="product_show_vendor_detail" value="0">
        <input type="checkbox" name="product_show_vendor_detail" value="1" <?php if ($jshopConfig->product_show_vendor_detail) echo 'checked="checked"';?> />
    </td>
</tr>
<?php }?>

<tr>
    <td class="key">
      <?php echo JText::_('JSHOP_SHOW_BUTTON_PRINT')?>
    </td>
    <td>
        <input type="hidden" name="display_button_print" value="0">
        <input type="checkbox" name="display_button_print" value="1" <?php if ($jshopConfig->display_button_print) echo 'checked="checked"';?> />
    </td>
</tr>
<?php if ($jshopConfig->stock){?>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_QTY_IN_STOCK')?>
    </td>
    <td>
        <input type="hidden" name="product_show_qty_stock" value="0" />
        <input type="checkbox" name="product_show_qty_stock" value="1" <?php if ($jshopConfig->product_show_qty_stock) echo 'checked="checked"';?> />
    </td>
</tr>
<?php }?>
<tr>
    <td class="key">
      <?php echo JText::_('JSHOP_REVIEW_MAX_MARK')?>
    </td>
    <td>
      <input type="text" name="max_mark" class = "form-control" id="max_mark" value="<?php echo $jshopConfig->max_mark?>" />
    </td>
</tr>
<tr>
   <td class="key">
     <?php echo JText::_('JSHOP_PRODUCTS_RELATED_IN_ROW')?>
   </td>
   <td>
     <input type="text" class="inputbox form-control" name="product_count_related_in_row" value="<?php echo $jshopConfig->product_count_related_in_row?>" />
   </td>
</tr>

<?php if ($jshopConfig->admin_show_product_extra_field){?>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_HIDE_EXTRA_FIELDS')?>
    </td>
    <td>
        <?php print $this->lists['product_hide_extra_fields'];?>
    </td>
</tr>
<?php }?>

<?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>  
</table>
    
</fieldset>
</div>
<div class="clr"></div>
<?php print $this->tmp_html_end?>
</form>
</div>
</div>
</div>
</div>