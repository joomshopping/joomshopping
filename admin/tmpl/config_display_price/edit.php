<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
$row=$this->row;
?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=configdisplayprice" method="post"name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>
<div class="col100">
<fieldset class="adminform">
<table width="100%" class="admintable">
   <tr>
    <td class="key" style="width:250px;">
        <?php echo JText::_('JSHOP_COUNTRY'). "<br/><br/><span style='font-weight:normal'>".JText::_('JSHOP_MULTISELECT_INFO')."</span>"; ?>
    </td>
    <td>
        <?php echo $this->lists['countries'];?>
    </td>
   </tr>
   <tr>
     <td  class="key">
       <?php echo JText::_('JSHOP_DISPLAY_PRICE')?>
     </td>
     <td>
       <?php echo $this->lists['display_price'];?>
     </td>
   </tr>
   <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_DISPLAY_PRICE_FOR_FIRMA')?>
     </td>
     <td>
       <?php echo $this->lists['display_price_firma'];?>
     </td>
   </tr>
   <?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
 </table>
</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="task" value="" />
<input type="hidden" name="id" value="<?php echo $row->id?>" />
<?php print $this->tmp_html_end?>
</form>
</div>