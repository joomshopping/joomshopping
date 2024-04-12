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
\JHTML::_('bootstrap.tooltip');
$lists=$this->lists;
?>
<div class="row">
<div class="col-md-12">
<div id="j-main-container" class="j-main-container">
<?php \JSHelperAdmin::displaySubmenuConfigs('checkout');?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=config" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<?php print $this->tmp_html_start?>
<input type="hidden" name="task" value="">
<input type="hidden" name="tab" value="7">

<div class="col100">
<fieldset class="adminform">
    <legend><?php echo JText::_('JSHOP_CHECKOUT')?></legend>
<table class="admintable table-striped">

<tr>
    <td class="key" style="width:280px!important;">
        <?php echo JText::_('JSHOP_DEFAULT_ORDER_STATUS')?>
    </td>
    <td>
        <?php echo $lists['status']; ?>
    </td>
</tr>
<tr>
    <td class="key">
      <?php echo JText::_('JSHOP_NEXT_ORDER_NUMBER')?>
    </td>
    <td>
      <input type="text" name="next_order_number" class = "form-control" value="" /> (<?php echo $jshopConfig->next_order_number?>)
    </td>
</tr>
<?php if (!$jshopConfig->without_shipping){?>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_HIDE_SHIPPING_STEP')?>
    </td>
    <td>
        <input type="hidden" name="hide_shipping_step" value="0">
        <input type="checkbox" name="hide_shipping_step" value="1" <?php if ($jshopConfig->hide_shipping_step) echo 'checked="checked"';?> />
    </td>
</tr>
<?php }?>
<?php if (!$jshopConfig->without_payment){?>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_HIDE_PAYMENT_STEP')?>
    </td>
    <td>
        <input type="hidden" name="hide_payment_step" value="0">
        <input type="checkbox" name="hide_payment_step" value="1" <?php if ($jshopConfig->hide_payment_step) echo 'checked="checked"';?> />
    </td>
</tr>
<?php }?>
<?php if (!$jshopConfig->without_shipping){?>
<tr>
    <td class="key">
      <?php echo JText::_('JSHOP_NULL_SIHPPING')?>
    </td>
    <td>
      <input type="text" name="summ_null_shipping" class = "form-control" value="<?php echo $jshopConfig->summ_null_shipping;?>" /> <?php print $this->currency_code;?>
      <?php echo \JSHelperAdmin::tooltip(JText::_('JSHOP_NULL_SIHPPING_INFO'));?>
    </td>
</tr>
<?php }?>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_ORDER_SEND_PDF_CLIENT')?>
    </td>
    <td>
        <input type="hidden" name="order_send_pdf_client" value="0">
        <input type="checkbox" name="order_send_pdf_client" value="1" <?php if ($jshopConfig->order_send_pdf_client) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_ORDER_SEND_PDF_ADMIN')?>
    </td>
    <td>
        <input type="hidden" name="order_send_pdf_admin" value="0">
        <input type="checkbox" name="order_send_pdf_admin" value="1" <?php if ($jshopConfig->order_send_pdf_admin) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SENT_INVOICE_MANUALLY')?>
    </td>
    <td>
        <input type="hidden" name="send_invoice_manually" value="0">
        <input type="checkbox" name="send_invoice_manually" value="1" <?php if ($jshopConfig->send_invoice_manually) echo 'checked="checked"';?> />
    </td>
</tr>
<?php if ($jshopConfig->tax){?>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_HIDE_TAX')?>
    </td>
    <td>
        <input type="hidden" name="hide_tax" value="0">
        <input type="checkbox" name="hide_tax" value="1" <?php if ($jshopConfig->hide_tax) echo 'checked="checked"';?> />
    </td>
</tr>
<?php }?>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_DISPLAY_REGISTRATION_FORM_ON_LOGIN_PAGE')?>
    </td>
    <td>
        <input type="hidden" name="show_registerform_in_logintemplate" value="0">
        <input type="checkbox" name="show_registerform_in_logintemplate" value="1" <?php if ($jshopConfig->show_registerform_in_logintemplate) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_EXT_MENU_CHECKOUT_STEP')?>
    </td>
    <td>
        <input type="hidden" name="ext_menu_checkout_step" value="0">
        <input type="checkbox" name="ext_menu_checkout_step" value="1" <?php if ($jshopConfig->ext_menu_checkout_step) echo 'checked="checked"';?> />
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SORTING_COUNTRY_IN_ALPHABET')?>
    </td>
    <td>
        <input type="hidden" name="sorting_country_in_alphabet" value="0">
        <input type="checkbox" name="sorting_country_in_alphabet" value="1" <?php if ($jshopConfig->sorting_country_in_alphabet) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_DEFAULT_COUNTRY')?>
    </td>
    <td>
        <?php echo $lists['default_country']; ?>
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SEQUENCE_STEP')?>
    </td>
    <td>
        <?php echo $lists['step_4_3']; ?>
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_WEIGHT_PRODUCT')?>
    </td>
    <td>
        <input type="hidden" name="show_weight_order" value="0">
        <input type="checkbox" name="show_weight_order" value="1" <?php if ($jshopConfig->show_weight_order) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_MANUFACTURER')?>
    </td>
    <td>
        <input type="hidden" name="show_manufacturer_in_cart" value="0" />
        <input type="checkbox" name="show_manufacturer_in_cart" value="1" <?php if ($jshopConfig->show_manufacturer_in_cart) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_EAN_PRODUCT')?>
    </td>
    <td>
        <input type="hidden" name="show_product_code_in_cart" value="0">
        <input type="checkbox" name="show_product_code_in_cart" value="1" <?php if ($jshopConfig->show_product_code_in_cart) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_MANUFACTURER_CODE')?>
    </td>
    <td>
        <input type="hidden" name="manufacturer_code_in_cart" value="0" />
        <input type="checkbox" name="manufacturer_code_in_cart" value="1" <?php if ($jshopConfig->manufacturer_code_in_cart) echo 'checked="checked"';?> />
    </td>
</tr>
<?php if ($jshopConfig->admin_show_product_basic_price){?>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_BASIC_PRICE')?>
    </td>
    <td>
        <input type="hidden" name="cart_basic_price_show" value="0" />
        <input type="checkbox" name="cart_basic_price_show" value="1" <?php if ($jshopConfig->cart_basic_price_show) echo 'checked="checked"';?> />
    </td>
</tr>
<?php }?>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_DISCOUNT_USE_FULL_SUM')?>
    </td>
    <td>
        <input type="hidden" name="discount_use_full_sum" value="0">
        <input type="checkbox" name="discount_use_full_sum" value="1" <?php if ($jshopConfig->discount_use_full_sum) echo 'checked="checked"';?> />
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_CALCULE_TAX_AFTER_DISCOUNT')?>
    </td>
    <td>
        <input type="hidden" name="calcule_tax_after_discount" value="0">
        <input type="checkbox" name="calcule_tax_after_discount" value="1" <?php if ($jshopConfig->calcule_tax_after_discount) echo 'checked="checked"';?> />
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_CART_ALL_STEP_CHECKOUT')?>
    </td>
    <td>
        <input type="hidden" name="show_cart_all_step_checkout" value="0">
        <input type="checkbox" name="show_cart_all_step_checkout" value="1" <?php if ($jshopConfig->show_cart_all_step_checkout) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_NOT_REDIRECT_IN_CART_AFTER_BUY')?>
    </td>
    <td>
        <input type="hidden" name="not_redirect_in_cart_after_buy" value="0">
        <input type="checkbox" name="not_redirect_in_cart_after_buy" value="1" <?php if ($jshopConfig->not_redirect_in_cart_after_buy) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_NOT_REDIRECT_IN_WISHLIST_AFTER_BUY')?>
    </td>
    <td>
        <input type="hidden" name="not_redirect_in_wishlist_after_buy" value="0" />
        <input type="checkbox" name="not_redirect_in_wishlist_after_buy" value="1" <?php if ($jshopConfig->not_redirect_in_wishlist_after_buy) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_CLIENT_ALLOW_CANCEL_ORDER')?>
    </td>
    <td>
        <input type="hidden" name="client_allow_cancel_order" value="0">
        <input type="checkbox" name="client_allow_cancel_order" value="1" <?php if ($jshopConfig->client_allow_cancel_order) echo 'checked="checked"';?> />
    </td>
</tr>

<?php if ($jshopConfig->admin_show_vendors){?>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_MESSAGE_OF_ORDER_VENDOR')?>
    </td>
    <td>
        <?php echo $lists['vendor_order_message_type']; ?>
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_ADMIN_NOT_SEND_EMAIL_ORDER_IF_SEND_VENDOR')?>
    </td>
    <td>
        <input type="hidden" name="admin_not_send_email_order_vendor_order" value="0">
        <input type="checkbox" name="admin_not_send_email_order_vendor_order" value="1" <?php if ($jshopConfig->admin_not_send_email_order_vendor_order) echo 'checked="checked"';?> />
    </td>
</tr>
<?php }?>

<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_USE_DECIMAL_QTY')?>
    </td>
    <td>
        <input type="hidden" name="use_decimal_qty" value="0" />
        <input type="checkbox" name="use_decimal_qty" value="1" <?php if ($jshopConfig->use_decimal_qty) echo 'checked="checked"';?> />
    </td>
</tr>
<?php if ($jshopConfig->admin_show_delivery_time){?>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_DELIVERY_ORDER_DEPENDS_DELIVERY_PRODUCT')?>
    </td>
    <td>
        <input type="hidden" name="delivery_order_depends_delivery_product" value="0" />
        <input type="checkbox" name="delivery_order_depends_delivery_product" value="1" <?php if ($jshopConfig->delivery_order_depends_delivery_product) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_DELIVERY_TIME')?>
    </td>
    <td>
        <input type="hidden" name="show_delivery_time_checkout" value="0" />
        <input type="checkbox" name="show_delivery_time_checkout" value="1" <?php if ($jshopConfig->show_delivery_time_checkout) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_DELIVERY_TIME')." (".JText::_('JSHOP_PRODUCT').")"?>
    </td>
    <td>
        <input type="hidden" name="show_delivery_time_step5" value="0" />
        <input type="checkbox" name="show_delivery_time_step5" value="1" <?php if ($jshopConfig->show_delivery_time_step5) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_OC_display_delivery_time_for_product_in_order_mail')?>
    </td>
    <td>
        <input type="hidden" name="display_delivery_time_for_product_in_order_mail" value="0" />
        <input type="checkbox" name="display_delivery_time_for_product_in_order_mail" value="1" <?php if ($jshopConfig->display_delivery_time_for_product_in_order_mail) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_DELIVERY_DATE')?>
    </td>
    <td>
        <input type="hidden" name="show_delivery_date" value="0" />
        <input type="checkbox" name="show_delivery_date" value="1" <?php if ($jshopConfig->show_delivery_date) echo 'checked="checked"';?> />
    </td>
</tr>
<?php }?>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_INVOICE_DATE')?>
    </td>
    <td>
        <input type="hidden" name="date_invoice_in_invoice" value="0" />
        <input type="checkbox" name="date_invoice_in_invoice" value="1" <?php if ($jshopConfig->date_invoice_in_invoice) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_WEIGHT_IN_INVOICE')?>
    </td>
    <td>
        <input type="hidden" name="weight_in_invoice" value="0" />
        <input type="checkbox" name="weight_in_invoice" value="1" <?php if ($jshopConfig->weight_in_invoice) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_SHIPPING_IN_INVOICE')?>
    </td>
    <td>
        <input type="hidden" name="shipping_in_invoice" value="0" />
        <input type="checkbox" name="shipping_in_invoice" value="1" <?php if ($jshopConfig->shipping_in_invoice) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_PAYMENT_IN_INVOICE')?>
    </td>
    <td>
        <input type="hidden" name="payment_in_invoice" value="0" />
        <input type="checkbox" name="payment_in_invoice" value="1" <?php if ($jshopConfig->payment_in_invoice) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_USER_NUMBER_IN_INVOICE')?>
    </td>
    <td>
        <input type="hidden" name="user_number_in_invoice" value="0" />
        <input type="checkbox" name="user_number_in_invoice" value="1" <?php if ($jshopConfig->user_number_in_invoice) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_AGB')?>
    </td>
    <td>
        <input type="hidden" name="display_agb" value="0" />
        <input type="checkbox" name="display_agb" value="1" <?php if ($jshopConfig->display_agb) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_RETURN_POLICY_IN_EMAIL_ORDER')?> (<?php print JText::_('JSHOP_URL')?>)
    </td>
    <td>
        <input type="hidden" name="show_return_policy_in_email_order" value="0">
        <input type="checkbox" name="show_return_policy_in_email_order" value="1" <?php if ($jshopConfig->show_return_policy_in_email_order) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_RETURN_POLICY_IN_EMAIL_ORDER')?> (<?php print JText::_('JSHOP_TEXT')?>)
    </td>
    <td>
        <input type="hidden" name="show_return_policy_text_in_email_order" value="0" />
        <input type="checkbox" name="show_return_policy_text_in_email_order" value="1" <?php if ($jshopConfig->show_return_policy_text_in_email_order) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_SHOW_RETURN_POLICY_IN_PDF')?>
    </td>
    <td>
        <input type="hidden" name="show_return_policy_text_in_pdf" value="0" />
        <input type="checkbox" name="show_return_policy_text_in_pdf" value="1" <?php if ($jshopConfig->show_return_policy_text_in_pdf) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_RETURN_POLICY_FOR_PRODUCT')?>
    </td>
    <td>
        <input type="hidden" name="return_policy_for_product" value="0" />
        <input type="checkbox" name="return_policy_for_product" value="1" <?php if ($jshopConfig->return_policy_for_product) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_NORETURN_ALL_PRODUCT')?>
    </td>
    <td>
        <input type="hidden" name="no_return_all" value="0" />
        <input type="checkbox" name="no_return_all" value="1" <?php if ($jshopConfig->no_return_all) echo 'checked="checked"';?> />
    </td>
</tr>
<tr>
    <td class="key">
      <?php echo JText::_('JSHOP_ERROR_MAX_COUNT_ORDER_ONE_PRODUCT')?>
    </td>
    <td>
      <input type="text" name="max_count_order_one_product" class = "form-control" value="<?php echo $jshopConfig->max_count_order_one_product;?>" />
    </td>
</tr>

<tr>
    <td class="key">
      <?php echo JText::_('JSHOP_ERROR_MIN_COUNT_ORDER_ONE_PRODUCT')?>
    </td>
    <td>
      <input type="text" name="min_count_order_one_product" class = "form-control" value="<?php echo $jshopConfig->min_count_order_one_product;?>" />
    </td>
</tr>

<tr>
    <td class="key">
      <?php echo JText::_('JSHOP_ERROR_MAX_SUM_ORDER')?>
    </td>
    <td>
      <input type="text" name="max_price_order" class = "form-control" value="<?php echo $jshopConfig->max_price_order;?>" />
    </td>
</tr>

<tr>
    <td class="key">
      <?php echo JText::_('JSHOP_ERROR_MIN_SUM_ORDER')?>
    </td>
    <td>
      <input type="text" name="min_price_order" class = "form-control" value="<?php echo $jshopConfig->min_price_order;?>" />
    </td>
</tr>
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