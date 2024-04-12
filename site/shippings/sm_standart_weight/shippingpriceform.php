<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die;

$row = $template->sh_method_price;
?>
<tr><td>&nbsp;</td></tr>
<tr>
  <td class="key" style = "text-align:right; vertical-align:top">
    <b><?php echo \JText::_('JSHOP_PRICE_DEPENCED_WEIGHT')?></b>
  </td>
  <td>
    <table class="adminlist" id="table_shipping_weight_price">
    <thead>
       <tr>
         <th>
           <?php echo \JText::_('JSHOP_MINIMAL_WEIGHT')?> (<?php print \JSHelper::sprintUnitWeight();?>)
         </th>
         <th>
           <?php echo \JText::_('JSHOP_MAXIMAL_WEIGHT')?> (<?php print \JSHelper::sprintUnitWeight();?>)
         </th>
         <th>
           <?php echo \JText::_('JSHOP_PRICE')?> (<?php echo $template->currency->currency_code; ?>)
         </th>
         <th>
           <?php echo \JText::_('JSHOP_PACKAGE_PRICE')?> (<?php echo $template->currency->currency_code; ?>)
         </th>         
         <th>
           <?php echo \JText::_('JSHOP_DELETE')?>
         </th>
       </tr>                   
       </thead>
       <?php
       $key = 0;
       foreach ($row->prices as $key=>$value){?>
       <tr id='shipping_weight_price_row_<?php print $key?>'>
         <td>
           <input type = "text" class = "inputbox form-control" name = "shipping_weight_from[]" value = "<?php echo $value->shipping_weight_from;?>" />
         </td>
         <td>
           <input type = "text" class = "inputbox form-control" name = "shipping_weight_to[]" value = "<?php echo $value->shipping_weight_to;?>" />
         </td>
         <td>
           <input type = "text" class = "inputbox form-control" name = "shipping_price[]" value = "<?php echo $value->shipping_price;?>" />
         </td>
         <td>
           <input type = "text" class = "inputbox form-control" name = "shipping_package_price[]" value = "<?php echo $value->shipping_package_price;?>" />
         </td>         
         <td style="text-align:center">
            <a class="btn btn-danger" href="#" onclick="jshopAdmin.delete_shipping_weight_price_row(<?php print $key?>);return false;">
                <i class="icon-delete"></i>
            </a>
         </td>
       </tr>
       <?php }?>    
    </table>
    <table class="adminlist"> 
    <tr>
        <td style="padding-top:5px;" align="right">
            <input type="button" class="btn btn-primary" value="<?php echo \JText::_('JSHOP_ADD_VALUE')?>" onclick = "jshopAdmin.addFieldShPrice();">
        </td>
    </tr>
    </table>
    <script type="text/javascript"> 
        <?php print "jshopAdmin.shipping_weight_price_num = $key;";?>
    </script>
</td>
</tr>