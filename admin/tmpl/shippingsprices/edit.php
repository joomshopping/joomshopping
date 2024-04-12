<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$row=$this->sh_method_price;
$lists=$this->lists;
$jshopConfig=\JSFactory::getConfig();
?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=shippingsprices&shipping_id_back=<?php echo $this->shipping_id_back;?>" method="post" name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>
<div class="col100">
<fieldset class="adminform">
<table class="admintable" width="100%" >
<tr>
<td class="key">
	<?php echo JText::_('JSHOP_TITLE')?>*
</td>
<td>
	<?php echo $lists['shipping_methods']?>
</td>
</tr>
<tr>
<td class="key">
	<?php echo JText::_('JSHOP_COUNTRY')."*"."<br/><br/><span style='font-weight:normal'>".JText::_('JSHOP_MULTISELECT_INFO')."</span>"; ?>
</td>
<td>
	<?php echo $lists['countries'];?>
</td>
</tr>
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

<tr>
<td class="key">
    <?php echo JText::_('JSHOP_PRICE')?>*
</td>
<td>
    <input type = "text" class = "inputbox form-control" name = "shipping_stand_price" value = "<?php echo $row->shipping_stand_price?>" />
    <?php echo $this->currency->currency_code; ?>
</td>
</tr>
<?php if ($this->config->tax){?>
<tr>
 <td class="key">
    <?php echo JText::_('JSHOP_TAX')?>*
 </td>
 <td>
     <?php echo $lists['taxes']?>
 </td>
</tr>
<?php }?>

<tr>
<td class="key">
    <?php echo JText::_('JSHOP_PACKAGE_PRICE')?>*
</td>
<td>
    <input type = "text" class = "inputbox form-control" name = "package_stand_price" value = "<?php echo $row->package_stand_price?>" />
    <?php echo $this->currency->currency_code; ?>
</td>
</tr>
<?php if ($this->config->tax){?>
<tr>
 <td class="key">
    <?php echo JText::_('JSHOP_PACKAGE_TAX')?>*
 </td>
 <td>
     <?php echo $lists['package_taxes']?>
 </td>
</tr>
<?php }?>

<?php foreach($this->extensions as $extension){
    $extension->exec->showShippingPriceForm($row->getParams(), $extension, $this);
}
?>

<?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
</table>
</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="sh_pr_method_id" value="<?php echo (int)$row->sh_pr_method_id?>" />
<input type="hidden" name="task" value="" />
<?php print $this->tmp_html_end?>
</form>
</div>