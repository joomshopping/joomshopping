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
<?php 
	
	$groupname="";
?>
<table class="admintable" >
<?php foreach($this->fields as $field){ ?>
<?php if ($groupname!=$field->groupname){ $groupname=$field->groupname;?>
<tr>
    <td><b><?php print $groupname;?></b></td>
</tr>
<?php }?>
<tr>
   <td class="key">
     <div style="padding-left:10px;"><?php echo $field->name;?></div>
   </td>
   <td>
     <?php echo $field->values;?>
   </td>
</tr>
<?php }?>
</table>