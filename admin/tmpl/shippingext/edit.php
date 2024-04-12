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
<form action="index.php?option=com_jshopping&controller=shippingextprice" method="post" name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>
<div class="col100">
<fieldset class="adminform">
    <table class="admintable" width="100%" >
   	<tr>
     	<td class="key" width="30%">
       		<?php echo JText::_('JSHOP_PUBLISH')?>
     	</td>
     	<td>
            <input type="hidden" name="published" value="0" />
       		<input type="checkbox" name="published" value="1" <?php if ($row->published) echo 'checked="checked"'?> />
     	</td>
   	</tr>    
   	<tr>
     	<td class="key">
       		<?php echo JText::_('JSHOP_TITLE')?>*
     	</td>
     	<td>
       		<input type="text" class="inputbox form-control" name="name" value="<?php echo $row->name?>" />
     	</td>
   	</tr>
    <tr>
         <td class="key">
               <?php echo JText::_('JSHOP_DESCRIPTION')?>
         </td>
         <td>
            <textarea class="form-control" name="description" cols="40" rows="5"><?php echo $row->description?></textarea>               
         </td>
       </tr>
    <tr>
         <td class="key">
            <?php echo JText::_('JSHOP_SHIPPINGS')?>
         </td>
         <td>
            <?php foreach($this->list_shippings as $shipping){?>
                <div style="padding:5px 0px;">
                    <input type="hidden" name="shipping[<?php print $shipping->shipping_id?>]" value="0">
                    <input type="checkbox" name="shipping[<?php print $shipping->shipping_id?>]" value="1" <?php if (!isset($this->shippings_conects[$shipping->shipping_id]) || $this->shippings_conects[$shipping->shipping_id]!=="0") print "checked"?>>
                    <?php print $shipping->name;?>
                </div>
            <?php }?>
         </td>
    </tr>
    <?php        
        $row->exec->showConfigForm($row->getParams(), $row, $this);
    ?>
   <?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
</table>
</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="task" value="" />
<input type="hidden" name="id" value="<?php echo (int)$row->id?>" />
<?php print $this->tmp_html_end?>
</form>
</div>