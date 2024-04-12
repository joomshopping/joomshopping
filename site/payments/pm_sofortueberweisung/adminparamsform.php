<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die;
?>
<div class="col100">
<fieldset class="adminform">
<table class="admintable" width = "100%" >
<tr>
   <td style="width:250px;" class="key">
     <?php echo \JText::_('JSHOP_SOFORTUEBERWEISUNG_USER_ID')?>
   </td>
   <td>
     <input type = "text" class = "inputbox form-control" name = "pm_params[user_id]" size="45" value = "<?php echo $params['user_id']?>" />
   </td>
 </tr>
 <tr>
   <td style="width:250px;" class="key">
     <?php echo \JText::_('JSHOP_SOFORTUEBERWEISUNG_PROJECT_ID')?>
   </td>
   <td>
     <input type = "text" class = "inputbox form-control" name = "pm_params[project_id]" size="45" value = "<?php echo $params['project_id']?>" />
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo \JText::_('JSHOP_SOFORTUEBERWEISUNG_PROJECT_PASSWORD')?>
   </td>
   <td>
     <input type = "text" class = "inputbox form-control" name = "pm_params[project_password]" size="45" value = "<?php echo $params['project_password']?>" />
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo \JText::_('JSHOP_NOFITY_PASSWORD')?>
   </td>
   <td>
     <input type = "text" class = "inputbox form-control" name = "pm_params[notify_password]" size="45" value = "<?php echo $params['notify_password']?>" />
   </td>
 </tr>

 <tr>
   <td class="key">
     <?php echo \JText::_('JSHOP_SOFORTUEBERWEISUNG_TRANSACTION_END')?>
   </td>
   <td>
     <?php
     print \JHTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_end_status]', 'class = "inputbox custom-select" style="display:inline-block; max-width:240px"', 'status_id', 'name', $params['transaction_end_status'] );
     ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo \JText::_('JSHOP_SOFORTUEBERWEISUNG_TRANSACTION_PENDING')?>
   </td>
   <td>
     <?php
     echo \JHTML::_('select.genericlist',$orders->getAllOrderStatus(), 'pm_params[transaction_pending_status]', 'class = "inputbox custom-select" style="display:inline-block; max-width:240px"', 'status_id', 'name', $params['transaction_pending_status']);
     ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo \JText::_('JSHOP_SOFORTUEBERWEISUNG_TRANSACTION_FAILED')?>
   </td>
   <td>
     <?php
     echo \JHTML::_('select.genericlist',$orders->getAllOrderStatus(), 'pm_params[transaction_failed_status]', 'class = "inputbox custom-select" style="display:inline-block; max-width:240px"', 'status_id', 'name', $params['transaction_failed_status']);
     ?>
   </td>
 </tr>
 <tr>
    <td class="key">&nbsp;</td>
 </tr>
 <tr>
   <td class="key">
     <?php echo \JText::_('JSHOP_SOFORTUEBERWEISUNG_RETURN_URL')?>
   </td>
   <td>
     <?php
     print \JURI::getInstance()->toString(['scheme', 'host', 'port']) . '/-USER_VARIABLE_1-';
     ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo \JText::_('JSHOP_SOFORTUEBERWEISUNG_CANCEL_URL')?>
   </td>
   <td>
     <?php
     print \JURI::getInstance()->toString(['scheme', 'host', 'port']) . '/-USER_VARIABLE_2-';
     ?>
   </td>
 </tr>
 <tr>
   <td class="key">
     <?php echo \JText::_('JSHOP_SOFORTUEBERWEISUNG_NOTIFI_URL')?>
   </td>
   <td>
     <?php
     print \JURI::getInstance()->toString(['scheme', 'host', 'port']) . '/-USER_VARIABLE_3-';
     ?>
   </td>
 </tr>

</table>
</fieldset>
</div>
<div class="clr"></div>