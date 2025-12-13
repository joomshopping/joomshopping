<?php
/**
* @version      5.8.4 17.11.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();
$groupname="";
?>
<table class="admintable list_prod_extrafields" edittype="<?php echo $this->edittype?>">
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
    <?php if ($field->filled ?? 0) {?>
    <td class="filled ps-2">
        <i class="fa fa-check-circle" title="<?php print Text::_('JSHOP_FILLED')?>"></i>
    </td>
    <?php } ?>
</tr>
<?php }?>
</table>