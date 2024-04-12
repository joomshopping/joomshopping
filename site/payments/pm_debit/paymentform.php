<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
?>
<table>
   <tr>
     <td width="200">
       <?php echo \JText::_('JSHOP_ACCOUNT_HOLDER')?>
     </td>
     <td>
       <input type="text" class="inputbox" name="params[pm_debit][acc_holder]" id="params_pm_debit_acc_holder" value="<?php print $params['acc_holder']?>"/>
     </td>
   </tr>
   <tr>
     <td>
       <?php echo \JText::_('JSHOP_IBAN')?>
     </td>
     <td>
       <input type="text" class="inputbox" name="params[pm_debit][bank_iban]" id="params_pm_debit_bank_iban" value="<?php print $params['bank_iban']?>"/>
     </td>
   </tr>
   <tr>
     <td>
       <?php echo \JText::_('JSHOP_BIC_BIC')?>
     </td>
     <td>
       <input type="text" class="inputbox" name="params[pm_debit][bank_bic]" id="params_pm_debit_bank_bic" value="<?php print $params['bank_bic']?>"/>
     </td>
   </tr>
   <tr>
     <td>
       <?php echo \JText::_('JSHOP_BANK')?>
     </td>
     <td>
       <input type="text" class="inputbox" name="params[pm_debit][bank]" id="params_pm_debit_bank" value="<?php print $params['bank']?>"/>
     </td>
   </tr>
</table>
<script type="text/javascript">
var jshopParams = jshopParams || {};
jshopParams['check_pm_debit'] = function(){
    var ar_focus = new Array();
    var error = 0;
    jshop.unhighlightField('payment_form');
    if (jshop.isEmpty(jQuery("#params_pm_debit_acc_holder").val())) {
        ar_focus[ar_focus.length]="params_pm_debit_acc_holder";
        error=1;
    }
    if (jshop.isEmpty(jQuery("#params_pm_debit_bank_iban").val())) {
        ar_focus[ar_focus.length]="params_pm_debit_bank_iban";
        error=1;
    }
    if (jshop.isEmpty(jQuery("#params_pm_debit_bank").val())) {
        ar_focus[ar_focus.length]="params_pm_debit_bank";
        error=1;
    }
    if (error){
        jQuery('#'+ar_focus[0]).focus();
        for (var i=0; i<ar_focus.length; i++ ){
           jshop.highlightField(ar_focus[i]);
        }
        return false;
    }
    return true;
}
</script>