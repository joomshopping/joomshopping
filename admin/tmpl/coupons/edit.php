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
?>
<?php
	$row=$this->coupon;
	$lists=$this->lists;
	$edit=$this->edit;
?>
<script type="text/javascript">
function selectUserBehaviour(uid){
    jQuery('input[name="for_user_id"]').val(uid);
    jQuery('#userModal').modal('hide');
}
</script>
<div class="jshop_edit">
<div class="col100">
<fieldset class="adminform">
<form action="index.php?option=com_jshopping&controller=coupons" method="post" name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>
<table class="admintable">
   <tr>
     <td class="key" width="30%">
       <?php echo JText::_('JSHOP_PUBLISH')?>
     </td>
     <td>
       <input type="checkbox" name="coupon_publish" value="1" <?php if ($row->coupon_publish) echo 'checked="checked"'?> />
     </td>
   </tr>
   <tr>
     <td  class="key">
       <?php echo JText::_('JSHOP_CODE')?>*
     </td>
     <td>
       <input type="text" class="inputbox form-control" id="coupon_code" name="coupon_code" value="<?php echo $row->coupon_code;?>" />
     </td>
   </tr>
   <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_TYPE_COUPON')?>*
     </td>
     <td>
       <?php echo $lists['coupon_type']; echo \JSHelperAdmin::tooltip( JText::_('JSHOP_COUPON_VALUE_DESCRIPTION'));
       ?>
     </td>
   </tr>
   <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_VALUE')?>*
     </td>
     <td>
       <input type="text" class="inputbox form-control" id="coupon_value" name="coupon_value" value="<?php echo $row->coupon_value;?>" />
       <span id="ctype_percent" <?php if ($row->coupon_type==1) {?>style="display:none"<?php }?>>%</span>
       <span id="ctype_value" <?php if ($row->coupon_type==0) {?>style="display:none"<?php }?>><?php print $this->currency_code?></span>
     </td>
   </tr>
   <tr>
     <td  class="key">
       <?php echo JText::_('JSHOP_START_DATE_COUPON')?>
     </td>
     <td>
       <div class="calblock"><?php echo \JHTML::_('calendar', $row->coupon_start_date, 'coupon_start_date', 'coupon_start_date', '%Y-%m-%d', array('class'=>'inputbox form-control', 'size'=>'25', 'maxlength'=>'19')); ?></div>
     </td>
   </tr>
   <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_EXPIRE_DATE_COUPON')?>
     </td>
     <td>
       <div class="calblock"><?php echo \JHTML::_('calendar', $row->coupon_expire_date, 'coupon_expire_date', 'coupon_expire_date', '%Y-%m-%d', array('class'=>'inputbox form-control', 'size'=>'25', 'maxlength'=>'19')); ?></div>
     </td>
   </tr>
   <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_FOR_USER_ID')?>
     </td>
     <td>
       <input type="text" class="inputbox form-control" name="for_user_id" value="<?php echo $row->for_user_id;?>" />   
       <a class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#userModal">
            <?php echo JText::_('JSHOP_LOAD')?>
        </a>
     </td>
   </tr>
   <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_FINISHED_AFTER_USED')?>
     </td>
     <td>
       <input type="checkbox" name="finished_after_used" value="1" <?php if ($row->finished_after_used) echo 'checked="true"'?> />
     </td>
   </tr>
   <?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
 </table>

<input type="hidden" name="task" value="" />
<input type="hidden" name="edit" value="<?php echo $edit;?>" />
<?php if ($edit) {?>
  <input type="hidden" name="coupon_id" value="<?php echo (int)$row->coupon_id?>" />
<?php }?>
<?php print $this->tmp_html_end?>
</form>
</fieldset>
</div>
<div class="clr"></div>

<?php print HTMLHelper::_(
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
);?>

</div>