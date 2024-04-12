<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;

$order = $this->order;
$order_item = $this->order_items;
$config_fields = $this->config_fields;

?>
<script type="text/javascript">
jshopAdmin.admin_show_attributes=<?php print $this->config->admin_show_attributes?>;
jshopAdmin.admin_show_manufacturer_code=<?php print (int)$this->config->manufacturer_code_in_cart?>;
jshopAdmin.admin_show_freeattributes=<?php print $this->config->admin_show_freeattributes?>;
jshopAdmin.admin_order_edit_more = <?php print $this->config->admin_order_edit_more?>;
jshopAdmin.hide_tax = <?php print intval($this->config->hide_tax)?>;
jshopAdmin.lang_load='<?php print JText::_('JSHOP_LOAD')?>';
jshopAdmin.lang_price='<?php print JText::_('JSHOP_PRICE')?>';
jshopAdmin.lang_tax='<?php print JText::_('JSHOP_TAX')?>';
jshopAdmin.lang_weight='<?php print JText::_('JSHOP_PRODUCT_WEIGHT')?>';
jshopAdmin.lang_vendor='<?php print JText::_('JSHOP_VENDOR')?>';
jshopAdmin.cElName = '';
function selectProductBehaviour(pid){
	var currency_id = jQuery('#currency_id').val();
    var display_price = jQuery('#display_price').val();
    var user_id = jQuery('#user_id').val();
    jshopAdmin.loadProductInfoRowOrderItem(pid, jshopAdmin.cElName, currency_id, display_price, user_id, 1);
}
function selectUserBehaviour(uid){
    jQuery('#user_id').val(uid);
    jshopAdmin.updateBillingShippingForUser(uid);
    jQuery('#userModal').modal('hide');
}
jshopAdmin.userinfo_fields = {};
<?php foreach ($config_fields as $k=>$v){
    if ($v['display']) echo "jshopAdmin.userinfo_fields['".$k."']='';";
}?>
jshopAdmin.userinfo_ajax = null;
jshopAdmin.userinfo_link = "<?php print "index.php?option=com_jshopping&controller=users&task=get_userinfo&ajax=1"?>";
</script>
<div class="jshop_edit form-horizontal">
<form action="index.php?option=com_jshopping" method="post" name="adminForm" id="adminForm">
<?php echo \JHTML::_('form.token');?>
<?php print $this->tmp_html_start?>
<?php if (!$this->display_info_only_product){?>

<?php if (!$order->order_created){?>
    <div class="row mb-2">
      <div class="col d-flex">
        <div class="mr-2"><b><?php print JText::_('JSHOP_FINISHED')?>:</b></div>
        <div><input class="va-middle" type="checkbox" name="order_created" value="1"></div>
      </div>
    </div>
<?php }?>

<div class="row mb-2">
    <div class="col d-flex">      
      <div class="mr-2 d-flex align-items-center"><b><?php print JText::_('JSHOP_USER')?>:</b></div>  
      <div class="input-group d-flex" style="max-width: 320px;">
        <?php echo $this->users_list_select;?>
        <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
            <?php echo JText::_('JSHOP_LOAD')?>
        </a>
      </div>
    </div>
</div>


<?php if ($this->config->date_invoice_in_invoice){?>
    <div class="row mb-2">
      <div class="col d-flex"> 
          <div class="mr-2 d-flex align-items-center"><b><?php print JText::_('JSHOP_INVOICE_DATE')?>:</b></div>
          <div class="span10"><?php echo \JHTML::_('calendar', \JSHelper::getDisplayDate($order->invoice_date, $this->config->store_date_format), 'invoice_date', 'invoice_date', $this->config->store_date_format , array('class'=>'inputbox', 'size'=>'25', 'maxlength'=>'19'));?></div>
      </div>
    </div>
<?php }?>

<table class="jshop_address" width="100%">
<tr>
    <td width="50%" valign="top">
        <table width="100%" class="admintable table table-striped">
        <thead>
        <tr>
          <th colspan="2" align="center"><?php print JText::_('JSHOP_BILL_TO') ?></th>
        </tr>
        </thead>
		<?php if ($config_fields['title']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_USER_TITLE')?>:</b></td>
          <td><?php print $this->select_titles?></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['firma_name']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_FIRMA_NAME')?>:</b></td>
          <td><input type="text" name="firma_name" class = "form-control" value="<?php print $order->firma_name?>" /></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['f_name']['display']){?>
        <tr>
          <td width="40%"><b><?php print JText::_('JSHOP_FULL_NAME')?>:</b></td>
          <td width="60%">
              <input type="text" class = "form-control mb-2" name="f_name" value="<?php print $order->f_name?>" />
              <input type="text" class = "form-control mb-2" name="l_name" value="<?php print $order->l_name?>" />
              <input type="text" class = "form-control" name="m_name" value="<?php print $order->m_name?>" />
          </td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['client_type']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_CLIENT_TYPE')?>:</b></td>
          <td><?php print $this->select_client_types;?></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['firma_code']['display']){?>
        <tr id="tr_field_firma_code" <?php if ($config_fields['client_type']['display'] && $order->client_type!="2"){?>style="display:none;"<?php } ?>>
          <td><b><?php print JText::_('JSHOP_FIRMA_CODE')?>:</b></td>
          <td><input type="text" class = "form-control" name="firma_code" value="<?php print $order->firma_code?>" /></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['tax_number']['display']){?>
        <tr id="tr_field_tax_number" <?php if ($config_fields['client_type']['display'] && $order->client_type!="2"){?>style="display:none;"<?php } ?>>
          <td><b><?php print JText::_('JSHOP_VAT_NUMBER')?>:</b></td>
          <td><input type="text" class = "form-control" name="tax_number" value="<?php print $order->tax_number?>" /></td>
        </tr>
        <?php } ?>
		<?php if ($config_fields['birthday']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_BIRTHDAY')?>:</b></td>
          <td><?php echo \JHTML::_('calendar', $order->birthday, 'birthday', 'birthday', $this->config->field_birthday_format, array('class'=>'inputbox middle3', 'size'=>'25', 'maxlength'=>'19'));?></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['street']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_STREET_NR')?>:</b></td>
          <td>
          <input type="text" class = "form-control" name="street" value="<?php print $order->street?>" />
          <?php if ($config_fields['street_nr']['display']){?>
          <input type="text" class = "form-control" name="street_nr" value="<?php print $order->street_nr?>" />
          <?php }?>
          </td>
        </tr>
        <?php } ?>
		<?php if ($config_fields['home']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_FIELD_HOME')?>:</b></td>
          <td><input type="text" class = "form-control" name="home" value="<?php print $order->home?>" /></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['apartment']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_FIELD_APARTMENT')?>:</b></td>
          <td><input type="text" class = "form-control" name="apartment" value="<?php print $order->apartment?>" /></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['city']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_CITY')?>:</b></td>
          <td><input type="text" class = "form-control" name="city" value="<?php print $order->city?>" /></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['state']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_STATE')?>:</b></td>
          <td><input type="text" class = "form-control" name="state" value="<?php print $order->state?>" /></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['zip']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_ZIP')?>:</b></td>
          <td><input type="text" class = "form-control" name="zip" value="<?php print $order->zip?>" /></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['country']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_COUNTRY')?>:</b></td>
          <td><?php print $this->select_countries;?></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['phone']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_TELEFON')?>:</b></td>
          <td><input type="text" class = "form-control" name="phone" value="<?php print $order->phone?>" /></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['mobil_phone']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_MOBIL_PHONE')?>:</b></td>
          <td><input type="text" class = "form-control" name="mobil_phone" value="<?php print $order->mobil_phone?>" /></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['fax']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_FAX')?>:</b></td>
          <td><input type="text" class = "form-control" name="fax" value="<?php print $order->fax?>" /></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['email']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_EMAIL')?>:</b></td>
          <td><input type="text" class = "form-control" name="email" value="<?php print $order->email?>" /></td>
        </tr>
        <?php } ?>
        
        <?php if ($config_fields['ext_field_1']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_EXT_FIELD_1')?>:</b></td>
          <td><input type="text"class = "form-control" name="ext_field_1" value="<?php print $order->ext_field_1?>" /></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['ext_field_2']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_EXT_FIELD_2')?>:</b></td>
          <td><input type="text" class = "form-control" name="ext_field_2" value="<?php print $order->ext_field_2?>" /></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['ext_field_3']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_EXT_FIELD_3')?>:</b></td>
          <td><input type="text" class = "form-control" name="ext_field_3" value="<?php print $order->ext_field_3?>" /></td>
        </tr>
        <?php } ?>
		<?php echo $this->tmp_fields?>
        </table>
    </td>
    <td width="50%"  valign="top">
    <?php if ($this->count_filed_delivery >0) {?>
        <table width="100%" class="admintable table table-striped">
        <thead>
        <tr>
          <th colspan="2" align="center"><?php print JText::_('JSHOP_SHIP_TO')?></th>
        </tr>
        </thead>
		<?php if ($config_fields['d_title']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_USER_TITLE')?>:</b></td>
          <td><?php print $this->select_d_titles?></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['d_firma_name']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_FIRMA_NAME')?>:</b></td>
          <td><input type="text" class = "form-control" name="d_firma_name" value="<?php print $order->d_firma_name?>" /></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['d_f_name']['display']){?>
        <tr>
          <td width="40%"><b><?php print JText::_('JSHOP_FULL_NAME')?>:</b></td>
          <td width="60%">
              <input type="text" class = "form-control mb-2" name="d_f_name" value="<?php print $order->d_f_name?>" />
              <input type="text" class = "form-control mb-2" name="d_l_name" value="<?php print $order->d_l_name?>" />
              <input type="text" class = "form-control" name="d_m_name" value="<?php print $order->d_m_name?>" />
          </td>
        </tr>
        <?php } ?>
		<?php if ($config_fields['d_birthday']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_BIRTHDAY')?>:</b></td>
          <td><?php echo \JHTML::_('calendar', $order->d_birthday, 'd_birthday', 'd_birthday', $this->config->field_birthday_format, array('class'=>'inputbox middle3', 'size'=>'25', 'maxlength'=>'19'));?></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['d_street']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_STREET_NR')?>:</b></td>
           <td>
          <input type="text" class = "form-control" name="d_street" value="<?php print $order->d_street?>" />
          <?php if ($config_fields['d_street_nr']['display']){?>
          <input type="text" class = "form-control" name="d_street_nr" value="<?php print $order->d_street_nr?>" />
          <?php }?>
          </td>
        </tr>
        <?php } ?>
		<?php if ($config_fields['d_home']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_FIELD_HOME')?>:</b></td>
          <td><input type="text" class = "form-control" name="d_home" value="<?php print $order->d_home?>" /></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['d_apartment']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_FIELD_APARTMENT')?>:</b></td>
          <td><input type="text" class = "form-control" name="d_apartment" value="<?php print $order->d_apartment?>" /></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['d_city']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_CITY')?>:</b></td>
          <td><input type="text" class = "form-control" name="d_city" value="<?php print $order->d_city?>" /></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['d_state']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_STATE')?>:</b></td>
          <td><input type="text" class = "form-control" name="d_state" value="<?php print $order->d_state?>" /></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['d_zip']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_ZIP')?>:</b></td>
          <td><input type="text" class = "form-control" name="d_zip" value="<?php print $order->d_zip?>" /></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['d_country']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_COUNTRY')?>:</b></td>
          <td><?php print $this->select_d_countries?></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['d_phone']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_TELEFON')?>:</b></td>
          <td><input type="text" class = "form-control" name="d_phone" value="<?php print $order->d_phone?>" /></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['d_mobil_phone']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_MOBIL_PHONE')?>:</b></td>
          <td><input type="text" class = "form-control" name="d_mobil_phone" value="<?php print $order->d_mobil_phone?>" /></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['d_fax']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_FAX')?>:</b></td>
          <td><input type="text" class = "form-control" name="d_fax" value="<?php print $order->d_fax?>" /></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['d_email']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_EMAIL')?>:</b></td>
          <td><input type="text" class = "form-control" name="d_email" value="<?php print $order->d_email?>" /></td>
        </tr>
        <?php } ?>
        
        <?php if ($config_fields['d_ext_field_1']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_EXT_FIELD_1')?>:</b></td>
          <td><input type="text" class = "form-control" name="d_ext_field_1" value="<?php print $order->d_ext_field_1?>" /></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['d_ext_field_2']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_EXT_FIELD_2')?>:</b></td>
          <td><input type="text" class = "form-control" name="d_ext_field_2" value="<?php print $order->d_ext_field_2?>" /></td>
        </tr>
        <?php } ?>
        <?php if ($config_fields['d_ext_field_3']['display']){?>
        <tr>
          <td><b><?php print JText::_('JSHOP_EXT_FIELD_3')?>:</b></td>
          <td><input type="text" class = "form-control" name="d_ext_field_3" value="<?php print $order->d_ext_field_3?>" /></td>
        </tr>
        <?php } ?>
		<?php echo $this->tmp_d_fields?>
        </table>
    <?php } ?>  
    </td>
</tr>
<?php print $this->_tmp_html_after_customer_info; ?>
</table>
<?php } ?>

<div class="row">
    <div class="col-md-4"><?php print JText::_('JSHOP_CURRENCIES')?>: <?php print $this->select_currency?></div>
    <div class="col-md-4"><?php print JText::_('JSHOP_DISPLAY_PRICE')?>: <?php print $this->display_price_select?></div>
    <div class="col-md-4"><?php print JText::_('JSHOP_LANGUAGE_NAME')?>: <?php print $this->select_language?></div>
</div>
<br/>
<table class="admintable table table-striped" width="100%" id='list_order_items'>
<thead>
<tr>
 <th>
   <?php echo JText::_('JSHOP_NAME_PRODUCT')?>
 </th>
 <th>
   <?php echo JText::_('JSHOP_EAN_PRODUCT')?>
 </th>
 <th>
   <?php echo JText::_('JSHOP_QUANTITY')?>
 </th> 
 <th width="16%">
   <?php echo JText::_('JSHOP_PRICE')?>
 </th>
 <th width="4%">
   <?php echo JText::_('JSHOP_DELETE')?>
 </th>
</tr>
</thead>
<?php $i=0;?>
<?php foreach ($order_item as $item){ $i++; ?>
<tr valign="top" id="order_item_row_<?php echo $i?>">
 <td>
   <input type="text" class = "form-control" name="product_name[<?php echo $i?>]" value="<?php echo $item->product_name?>" size="44" title="<?php print JText::_('JSHOP_TITLE')?>" />
   <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#aModal" onclick="jshopAdmin.cElName=<?php echo $i?>">
    <?php print JText::_('JSHOP_LOAD')?>
   </a>
   <br />
   <?php if ($this->config->manufacturer_code_in_cart){?>
   <input type="text" class = "form-control" name="manufacturer_code[<?php echo $i?>]" value="<?php echo $item->manufacturer_code?>" title="<?php print JText::_('JSHOP_MANUFACTURER_CODE')?>"><br />
   <?php }?>
   <?php if ($this->config->admin_show_attributes){?>
   <textarea rows="2" cols="24" name="product_attributes[<?php echo $i?>]" class = "form-control" title="<?php print JText::_('JSHOP_ATTRIBUTES')?>"><?php print $item->product_attributes?></textarea>   
   <br />
   <?php }?>
   <?php if ($this->config->admin_show_freeattributes){?>
   <textarea rows="2" cols="24" name="product_freeattributes[<?php echo $i?>]" class = "form-control" title="<?php print JText::_('JSHOP_FREE_ATTRIBUTES')?>"><?php print $item->product_freeattributes?></textarea>
   <?php }?>   
   <input type="hidden" name="product_id[<?php echo $i?>]" value="<?php echo $item->product_id?>" />
   <input type="hidden" name="delivery_times_id[<?php echo $i?>]" value="<?php echo $item->delivery_times_id?>" />
   <input type="hidden" name="thumb_image[<?php echo $i?>]" value="<?php echo $item->thumb_image?>" />
   <input type="hidden" name="attributes[<?php echo $i?>]" value="<?php echo $item->attributes?>" />
   <input type="hidden" name="category_id[<?php echo $i?>]" value="<?php echo $item->category_id?>" />
   <?php print $item->_ext_attribute_html;?>
   <?php if ($this->config->admin_order_edit_more){?>
   <div>
   <?php echo JText::_('JSHOP_PRODUCT_WEIGHT')?> <input type="text" class = "form-control" name="weight[<?php echo $i?>]" value="<?php echo $item->weight?>" />
   </div>
   <div>   
   <?php echo JText::_('JSHOP_VENDOR')?> ID <input type="text" class = "form-control" name="vendor_id[<?php echo $i?>]" value="<?php echo $item->vendor_id?>" />
   </div>
   <?php }else{?>
   <input type="hidden" name="weight[<?php echo $i?>]" value="<?php echo $item->weight?>" />
   <input type="hidden" name="vendor_id[<?php echo $i?>]" value="<?php echo $item->vendor_id?>" />
   <?php }?>
 </td>
 <td>
   <input type="text" name="product_ean[<?php echo $i?>]" class="middle form-control" value="<?php echo $item->product_ean?>" />
 </td>
 <td>
   <input type="text"  name="product_quantity[<?php echo $i?>]" class="small3 form-control" value="<?php echo $item->product_quantity?>" onkeyup="jshopAdmin.updateOrderSubtotalValue();"/>
 </td>
 <td>
   <div class="price d-flex align-items-center"><div class="small-title"><?php print JText::_('JSHOP_PRICE')?>:</div> <input class="small3 form-control" type="text" name="product_item_price[<?php echo $i?>]" value="<?php echo $item->product_item_price;?>" onkeyup="jshopAdmin.updateOrderSubtotalValue();"/><?php echo ' '.$order->currency_code;?></div>
   <?php if (!$this->config->hide_tax){?>
   <div class="tax d-flex align-items-center"><div class="small-title"><?php print JText::_('JSHOP_TAX')?>:</div> <input class="small3 form-control " type="text" name="product_tax[<?php echo $i?>]" value="<?php echo $item->product_tax?>" /> %</div>
   <?php }?>
   <input type="hidden" class = "form-control" name="order_item_id[<?php echo $i?>]" value="<?php echo $item->order_item_id?>" />
 </td>
 <td>
    <a class="btn btn-danger" href='#' onclick="jQuery('#order_item_row_<?php echo $i?>').remove();jshopAdmin.updateOrderSubtotalValue();return false;">
        <i class="icon-delete"></i>
    </a>
 </td>
</tr>
<?php }?>
</table>
<div style="text-align:right;padding-top:3px;">
    <input type="button" class="btn btn-primary" value="<?php print JText::_('JSHOP_ADD')." ".JText::_('JSHOP_PRODUCT')?>" onclick="jshopAdmin.addOrderItemRow();">
</div>
<script>jshopAdmin.end_number_order_item=<?php echo $i?>;</script>

<br/>
<table class="table table-striped" width="100%">
<tr class="bold">
 <td class="right">
    <?php echo JText::_('JSHOP_SUBTOTAL')?>
 </td>
 <td class="left">
   <input type="text" class="small3 form-control" name="order_subtotal" value="<?php echo $order->order_subtotal;?>" onkeyup="jshopAdmin.updateOrderTotalValue();"/> <?php echo $order->currency_code;?>
 </td>
</tr>
<?php print $this->_tmp_html_after_subtotal?>
<tr class="bold">
 <td class="right">
   <?php echo JText::_('JSHOP_COUPON_DISCOUNT')." (".JText::_('JSHOP_CODE').")"?>
 </td>
 <td class="left">
    <input type="text" class="small3 form-control" name="coupon_code" value="<?php echo $order->coupon_code?>">
	  <input type="button" class="btn btn-primary" value="<?php print JText::_('JSHOP_CALCULE')?>" onclick="jshopAdmin.order_discount_calculate();">
 </td>
</tr>

<tr class="bold">
 <td class="right">
   <?php echo JText::_('JSHOP_COUPON_DISCOUNT')?>
 </td>
 <td class="left">
   <input type="text" class="small3 form-control" name="order_discount" value="<?php echo $order->order_discount;?>" onkeyup="jshopAdmin.updateOrderTotalValue();"/> <?php echo $order->currency_code;?>
 </td>
</tr>

<?php if (!$this->config->without_shipping){?>
<tr class="bold">
 <td class="right">
    <?php echo JText::_('JSHOP_SHIPPING_PRICE')?>
 </td>
 <td class="left">
    <input type="text"  class="small3 form-control" name="order_shipping" value="<?php echo $order->order_shipping;?>" onkeyup="jshopAdmin.updateOrderTotalValue();"/> <?php echo $order->currency_code;?>
 </td>
</tr>
<tr class="bold">
 <td class = "right">
    <?php echo JText::_('JSHOP_PACKAGE_PRICE')?>
 </td>
 <td class = "left">
    <input type="text" class="small3 form-control" name="order_package" value="<?php echo $order->order_package;?>" onkeyup="jshopAdmin.updateOrderTotalValue();"/> <?php echo $order->currency_code;?>
 </td>
</tr>
<?php }?>
<?php if (!$this->config->without_payment){?>
<tr class="bold">
 <td class="right">
     <?php print ($order->payment_name) ? $order->payment_name : JText::_('JSHOP_PAYMENT')?>
 </td>
 <td class="left">
   <input type="text" class="small3 form-control" name="order_payment" value="<?php echo $order->order_payment?>" onkeyup="jshopAdmin.updateOrderTotalValue();"/> <?php echo $order->currency_code;?>
 </td>
</tr>
<?php }?>

<?php $i=0; if (!$this->config->hide_tax){?>
<?php foreach($order->order_tax_list as $percent=>$value){ $i++;?>
  <tr class="bold">
    <td class="right">
      <?php print \JSHelper::displayTotalCartTaxName($order->display_price);?>
      <input type="text" class="small3 form-control" name="tax_percent[]" value="<?php print $percent?>" /> %
    </td>
    <td class="left">
      <input type="text" class="small3 form-control" name="tax_value[]" value="<?php print $value; ?>" /> <?php print $order->currency_code?>
    </td>
  </tr>
<?php }?>
  <tr class="bold" id='row_button_add_tax'>
    <td></td>
    <td class="left">
    <input type="button" class="btn btn-primary" style="margin-top:5px" value="<?php print JText::_('JSHOP_TAX_CALCULATE')?>" onclick="jshopAdmin.order_tax_calculate();">
    <input type="button" class="btn btn-primary" style="margin-top:5px" value="<?php print JText::_('JSHOP_ADD')." ".JText::_('JSHOP_TAX')?>" onclick="jshopAdmin.addOrderTaxRow();">
    </td>
  </tr>
<?php }?>

<tr class="bold">
 <td class="right">
    <?php echo JText::_('JSHOP_TOTAL')?>
 </td>
 <td class="left" width="20%">
   <input type="text" class="small3 form-control" name="order_total" value="<?php echo $order->order_total;?>" /> <?php echo $order->currency_code;?>
 </td>
</tr>
<?php print $this->_tmp_html_after_total?>
<?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
</table>

<table class="table table-striped">
<thead>
<tr>
    <?php if (!$this->config->without_shipping){?>
    <th width="33%">
    <?php echo JText::_('JSHOP_SHIPPING_INFORMATION')?>
    </th>
    <?php }?>
    <?php if (!$this->config->without_payment){?>
    <th width="33%">
    <?php echo JText::_('JSHOP_PAYMENT_INFORMATION')?>
    </th>
    <?php } ?>
    <?php if ($this->config->show_delivery_time){?>
    <th width="33%">
    <?php echo JText::_('JSHOP_DELIVERY_TIME')?>
    </th>
    <?php } ?>
</tr>
</thead>
<tr>
    <?php if (!$this->config->without_shipping){?>    
    <td valign="top">
    	<div style="padding-bottom:4px;"><?php echo $this->shippings_select?></div>
    	<div><textarea class="form-control" name="shipping_params"><?php echo $order->shipping_params?></textarea></div>
    </td>
    <?php } ?>
    <?php if (!$this->config->without_payment){?>
    <td valign="top">
        <div style="padding-bottom:4px;"><?php print $this->payments_select?></div>
        <div><textarea name="payment_params" class = "form-control"><?php echo $order->payment_params?></textarea></div>
    </td>
    <?php } ?>
    <?php if ($this->config->show_delivery_time){?>
    <td valign="top"><?php echo $this->delivery_time_select?></td>
    <?php } ?>
</tr>
</table>

<input type="hidden" name="js_nolang" value="1" />
<input type="hidden" name="order_id" value="<?php echo (int)$this->order_id;?>" />
<input type="hidden" name="controller" value="orders" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="client_id" value="<?php echo (int)$this->client_id?>" />
<?php print $this->tmp_html_end?>
</form>

<?php print HTMLHelper::_(
    'bootstrap.renderModal',
    'aModal',
    array(
        'title'       => \JText::_('Products'),
        'backdrop'    => 'static',
        'url'         => 'index.php?option=com_jshopping&controller=product_list_selectable&tmpl=component',
        'height'      => '400px',
        'width'       => '800px',
        'bodyHeight'  => 70,
        'modalWidth'  => 80        
    )
);

print HTMLHelper::_(
    'bootstrap.renderModal',
    'userModal',
    array(
        'title'       => \JText::_('Users'),
        'backdrop'    => 'static',
        'url'         => 'index.php?option=com_jshopping&controller=users&tmpl=component&select_user=1',
        'height'      => '400px',
        'width'       => '800px',
        'bodyHeight'  => 70,
        'modalWidth'  => 80,        
    )
);
?>

</div>