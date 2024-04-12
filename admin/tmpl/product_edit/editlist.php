<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
$jshopConfig=$this->config;
$lists=$this->lists;
?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=products" method="post" name="adminForm" id="adminForm" id="item-form">
<ul class="joomla-tabs nav nav-tabs">    
    <li class="nav-item"><a href="#main-page" class="nav-link active" data-toggle="tab"><?php echo JText::_('JSHOP_INFO_PRODUCT')?></a></li>
    <?php if ($jshopConfig->admin_show_product_extra_field) {?>
    <li class="nav-item"><a href="#product_extra_fields" class="nav-link" data-toggle="tab"><?php echo JText::_('JSHOP_EXTRA_FIELDS')?></a></li>
    <?php } ?>
</ul>

<div id="editdata-document" class="tab-content">
<div id="main-page" class="tab-pane active">
<div class="col100">
<table class="admintable" width="90%">
  <tr>
    <td class="key" style="width:180px;">
         <?php echo JText::_('JSHOP_PUBLISH')?>
    </td>
    <td>
        <?php print $this->lists['product_publish'];?>
    </td>
  </tr>
  <tr>
   <td class="key" style="width:180px;">
     <?php echo JText::_('JSHOP_ACCESS')?>
   </td>
   <td>
     <?php print $this->lists['access'];?>
   </td>
 </tr>     
 <tr>
   <td class="key">
     <?php echo JText::_('JSHOP_PRODUCT_PRICE')?>
   </td>
   <td>
     <?php echo $this->lists['price_mod_price'];?>
     <input type="text" class = "form-control" name="product_price" value="" />
     <?php echo $this->lists['currency'];?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo JText::_('JSHOP_OLD_PRICE')?>
   </td>
   <td>
    <span id='foldprice'><?php echo $this->lists['price_mod_old_price'];?>
    <input type = "text" class = "form-control" name = "product_old_price" value = "" />
    <span style="width:5px;"></span>
    </span>
    <input type="checkbox" name="use_old_val_price" value="1" onclick="jshopAdmin.shfoldprice(this.checked)"> <?php print JText::_('JSHOP_USE_OLD_VALUE_PRICE')?>
   </td>
 </tr>
 <?php if ($jshopConfig->admin_show_product_bay_price) { ?>
 <tr>
   <td class="key">
     <?php echo JText::_('JSHOP_PRODUCT_BUY_PRICE')?>
   </td>
   <td>
     <input type="text" class = "form-control" name="product_buy_price" value="" />
   </td>
 </tr>
 <?php } ?>
 
 <tr>
   <td class="key">
     <?php echo JText::_('JSHOP_PRODUCT_WEIGHT')?>
   </td>
   <td>
     <input type="text" class = "form-control" name="product_weight" value="" /> <?php print \JSHelper::sprintUnitWeight();?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo JText::_('JSHOP_QUANTITY_PRODUCT')?>
   </td>
   <td>
     <div id="block_enter_prod_qty" style="padding-bottom:2px;">
         <input type="text" class = "form-control" name="product_quantity" id="product_quantity" value="" />
     </div>
     <div>         
        <input type="checkbox" name="unlimited" value="1" onclick="jshopAdmin.ShowHideEnterProdQty(this.checked)" /> <?php print JText::_('JSHOP_UNLIMITED')?>
     </div>         
   </td>
 </tr>
 <?php if ($jshopConfig->use_different_templates_cat_prod) { ?>
 <tr>
   <td class="key">
     <?php echo JText::_('JSHOP_TEMPLATE_PRODUCT')?>
   </td>
   <td>
     <?php echo $lists['templates'];?>
   </td>
 </tr>
 <?php } ?>
 
 <?php if (!$this->withouttax){?>
 <tr>     
   <td class="key">
     <?php echo JText::_('JSHOP_NAME_TAX')?>*
   </td>
   <td>
     <?php echo $lists['tax'];?>
   </td>
 </tr>
 <?php }?>
 <tr>
   <td class="key">
     <?php echo JText::_('JSHOP_NAME_MANUFACTURER')?>
   </td>
   <td>
     <?php echo $lists['manufacturers'];?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo JText::_('JSHOP_CATEGORIES')?>*
   </td>
   <td>
     <?php echo $lists['categories'];?>
   </td>
 </tr>
 <?php if ($jshopConfig->admin_show_vendors && $this->display_vendor_select) { ?>
 <tr>
   <td class="key">
     <?php echo JText::_('JSHOP_VENDOR')?>
   </td>
   <td>
     <?php echo $lists['vendors'];?>
   </td>
 </tr>
 <?php }?>
 
 <?php if ($jshopConfig->admin_show_delivery_time) { ?>
 <tr>
   <td class="key">
     <?php echo JText::_('JSHOP_DELIVERY_TIME')?>
   </td>
   <td>
     <?php echo $lists['deliverytimes'];?>
   </td>
 </tr>
 <?php }?>
 
 <?php if ($jshopConfig->admin_show_product_labels) { ?>
 <tr>
   <td class="key">
     <?php echo JText::_('JSHOP_LABEL')?>
   </td>
   <td>
     <?php echo $lists['labels'];?>
   </td>
 </tr>
 <?php }?>
 
 <?php if ($jshopConfig->admin_show_product_basic_price) { ?>
 <tr>
   <td class="key"><br/><?php echo JText::_('JSHOP_BASIC_PRICE')?></td>
 </tr>
 <tr>
   <td class="key">
     <?php echo JText::_('JSHOP_WEIGHT_VOLUME_UNITS')?>
   </td>
   <td>
     <input type="text" class = "form-control" name="weight_volume_units" value="" />
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo JText::_('JSHOP_UNIT_MEASURE')?>
   </td>
   <td>
     <?php echo $lists['basic_price_units'];?>
   </td>
 </tr>
 <?php }?>
 <?php $pkey='etemplatevar'; if ($this->$pkey){ print $this->$pkey;}?>
</table>
</div>
</div>
<?php if ($jshopConfig->admin_show_product_extra_field) include(dirname(__FILE__)."/extrafields.php"); ?>
</div>
<input type="hidden" name="task" value="">
<?php foreach($this->cid as $cid){?>
<input type="hidden" name="cid[]" value="<?php print $cid?>">
<?php }?>
</form>
</div>
<script type="text/javascript">
function shfoldprice(checked){
    if (checked){
        jQuery("#foldprice").hide();
    }else{
        jQuery("#foldprice").show();
    }
}
</script>