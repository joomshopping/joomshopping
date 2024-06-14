<?php
/**
* @version      5.5.0 06.12.2023
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
$groupname="";
?>
<table class="admintable list_prod_extrafields">
<?php foreach($this->fields as $field){ ?>
<?php if ($groupname!=$field->groupname){ $groupname=$field->groupname;?>
<tr class="ef_group" ef_gr_id="<?php print $field->group?>">
    <td><b><?php print $groupname;?></b></td>
</tr>
<?php }?>
<tr extrafieldid="<?php print $field->id?>" extrafield_gr_id="<?php print $field->group?>" class="<?php print $field->row_class?>">
  <td class="key">
    <div class="prod_extrafield_title"><?php echo $field->name;?></div>
  </td>
  <td class="prod_extrafield_values">
    <?php echo $field->values;?>
  </td>
  <td class="prod_extrafield_btn">
    <?php echo $field->btn ?? '';?>
  </td>
</tr>
<?php }?>
</table>