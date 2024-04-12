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
<div id="product_freeattribute" class="tab-pane">   
   <div class="col100">
   <table class="admintable" width="90%">
   <?php foreach($this->listfreeattributes as $freeattrib){?>
     <tr>
       <td class="key">
         <?php echo $freeattrib->name;?>
       </td>
       <td>
         <input type="checkbox" name="freeattribut[<?php print $freeattrib->id?>]" value="1" <?php if (isset($freeattrib->pactive) && $freeattrib->pactive) echo 'checked="checked"'?> />
       </td>
       <?php if (isset($freeattrib->ext_html)) print $freeattrib->ext_html?>
     </tr>
   <?php }?>
   <?php $pkey='plugin_template_freeattribute'; if ($this->$pkey){ print $this->$pkey;}?>
   </table>
   </div>
   <div class="clr"></div>
</div>