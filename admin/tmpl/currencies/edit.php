<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$row=$this->currency;
$lists=$this->lists;
$edit=$this->edit;
?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=currencies" method="post"name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>
<div class="col100">
<fieldset class="adminform">
    <table class="admintable" width="100%" >
   <tr>
     <td class="key" width="30%">
       <?php echo JText::_('JSHOP_PUBLISH')?>
     </td>
     <td>
       <input type="checkbox" name="currency_publish" value="1" <?php if ($row->currency_publish) echo 'checked="checked"'?> />
     </td>
   </tr>
   <tr>
     <td  class="key">
       <?php echo JText::_('JSHOP_ORDERING')?>
     </td>
     <td id="ordering">
       <?php echo $lists['order_currencies']?>
     </td>
   </tr>
   <tr>
     <td  class="key">
       <?php echo JText::_('JSHOP_TITLE')?>*
     </td>
     <td>
       <input type="text" class="inputbox form-control" id="currency_name" name="currency_name" value="<?php echo $row->currency_name;?>" />
     </td>
   </tr>
   <tr>
     <td  class="key">
       <?php echo JText::_('JSHOP_CODE')?>*
     </td>
     <td>
       <input type="text" class="inputbox form-control" id="currency_code" name="currency_code" value="<?php echo $row->currency_code;?>" />
     </td>
   </tr>
   <tr>
     <td  class="key">
       <?php echo JText::_('JSHOP_CODE')." (ISO)";?>*
     </td>
     <td>
       <input type="text" class="inputbox form-control" name="currency_code_iso" value="<?php echo $row->currency_code_iso;?>" />
     </td>
   </tr>
   <tr>
     <td  class="key">
       <?php echo JText::_('JSHOP_CODE')." (".JText::_('JSHOP_NUMERIC').")";?>
     </td>
     <td>
       <input type="text" class="inputbox form-control" name="currency_code_num" value="<?php echo $row->currency_code_num;?>" />
     </td>
   </tr>
   <tr>
     <td  class="key">
       <?php echo JText::_('JSHOP_VALUE_CURRENCY')?>*
     </td>
     <td>
       <input type="text" class="inputbox form-control" id="currency_value" name="currency_value" value="<?php echo $row->currency_value;?>" />
     </td>
   </tr>
   <?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
 </table>
</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="task" value="" />
<input type="hidden" name="edit" value="<?php echo $edit;?>" />
<?php if ($edit) {?>
  <input type="hidden" name="currency_id" value="<?php echo (int)$row->currency_id?>" />
<?php }?>
<?php print $this->tmp_html_end?>
</form>
</div>