<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
$row=$this->payment;
$params=$this->params;
$lists=$this->lists;
?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=payments" method="post" name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>
<ul class="joomla-tabs nav nav-tabs">    
    <li class="nav-item"><a href="#first-tab" class="nav-link active" data-toggle="tab"><?php echo JText::_('JSHOP_PAYMENT_GENERAL')?></a></li>
    <li class="nav-item"><a href="#second-tab" class="nav-link" data-toggle="tab"><?php echo JText::_('JSHOP_PAYMENT_CONFIG')?></a></li>
</ul>
<div id="editdata-document" class="tab-content">
<div id="first-tab" class="tab-pane active">
<div class="col100">
<fieldset class="adminform">
    <table class="admintable" width="100%" >
   <tr>
     <td class="key" width="30%">
       <?php echo JText::_('JSHOP_PUBLISH')?>
     </td>
     <td>
       <input type="checkbox" name="payment_publish" value="1" <?php if ($row->payment_publish) echo 'checked="checked"'?> />
     </td>
   </tr>
   <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_CODE')?>
     </td>
     <td>
       <input type="text" class="inputbox form-control" id="payment_code" name="payment_code" value="<?php echo $row->payment_code;?>" />
     </td>
   </tr>
   <?php
   foreach($this->languages as $lang){
   $field="name_".$lang->language;
   ?>
   <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_TITLE')?> <?php if ($this->multilang) print "(".$lang->lang.")";?>*
     </td>
     <td>
       <input type="text" class="inputbox form-control" id="<?php print $field?>" name="<?php print $field?>" value="<?php echo $row->$field;?>" />
     </td>
   </tr>
   <?php }?>
   <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_ALIAS')?>*
     </td>
     <td>
       <input type="text" class="inputbox form-control" name="payment_class" value="<?php echo $row->payment_class;?>" />
       <?php echo \JSHelperAdmin::tooltip(JText::_('JSHOP_ALIAS_PAYMENT_INFO'));?>
     </td>
   </tr>
   <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_SCRIPT_NAME')?>
     </td>
     <td>       
	   <input type="text" class="inputbox form-control" name="scriptname" value="<?php echo $row->scriptname;?>" <?php if ($this->config->shop_mode==0 && $row->payment_id){?>readonly <?php }?> />
     </td>
   </tr>
   <?php if ($this->config->tax){?>
   <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_SELECT_TAX')?>*
     </td>
     <td>
       <?php echo $lists['tax'];?>
     </td>
   </tr>   
   <?php }?>
   <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_PRICE')?>
     </td>
     <td>
       <input type="text" class="inputbox form-control" name="price" value="<?php echo $row->price;?>" />
       <?php echo $lists['price_type'];?>
     </td>
   </tr>
   <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_IMAGE_URL')?>
     </td>
     <td>
       <input type="text" class="inputbox form-control" name="image" value="<?php echo $row->image;?>" />
     </td>
   </tr>
   <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_TYPE_PAYMENT')?>
     </td>
     <td>
       <?php echo $lists['type_payment'];?>
     </td>
   </tr>
   <?php
   foreach($this->languages as $lang){
   $field="description_".$lang->language;
   ?>
   <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_DESCRIPTION')?> <?php if ($this->multilang) print "(".$lang->lang.")";?>
     </td>
     <td>
       <?php                 
         $editor=\JEditor::getInstance(\JFactory::getConfig()->get('editor'));
         print $editor->display("description".$lang->id,  $row->$field , '100%', '350', '75', '20' ) ;
       ?>
     </td>
   </tr>
   <?php }?>
   <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_SHOW_DESCR_IN_EMAIL')?>
     </td>
     <td>
       <input type="checkbox" name="show_descr_in_email" value="1" <?php if ($row->show_descr_in_email) echo 'checked="checked"'?> />
     </td>
   </tr>
   <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_SHOW_DEFAULT_BANK_IN_BILL')?>
     </td>
     <td>
       <input type="hidden" name="show_bank_in_order" value="0">
       <input type="checkbox" name="show_bank_in_order" value="1" <?php if ($row->show_bank_in_order) echo 'checked="checked"'?> />
     </td>
   </tr>
   <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_DESCRIPTION_IN_BILL')?>
     </td>
     <td>
       <textarea name="order_description" class = "form-control" rows="6" cols="30"><?php print $row->order_description?></textarea>
     </td>
   </tr>
   <?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?> 
 </table>
</fieldset>
</div>
<div class="clr"></div>
</div>
<div id="second-tab" class="tab-pane">
<?php
	if ($lists['html']!=""){	   	
	   	echo $lists['html'];	  	
  	}   	
?>
</div>
</div>
<input type="hidden" name="task" value="" />
<input type="hidden" name="payment_id" value="<?php echo (int)$row->payment_id?>" />
<?php print $this->tmp_html_end?>
</form>
</div>