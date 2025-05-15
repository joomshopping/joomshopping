<?php
use Joomla\CMS\Language\Text;

/**
* @version      5.6.3 12.05.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
?>
<?php
$row=$this->row;
?>
<form action="index.php?option=com_jshopping&controller=licensekeyaddon" method="post" id="adminForm">
<?php print $this->tmp_html_start?>
<div class="col100">
<fieldset class="adminform">
<table width="100%" class="admintable">
<tr>
    <td class="key" style="width:250px;">
       <?php echo Text::_('JSHOP_KEY')?>
    </td>
    <td>
        <input type="text" class="inputbox form-control" name="key" value="<?php echo $row->key;?>" size="90"> 
        <?php if ($this->btn_genrate) {?>
        <a href="#" class="btn btn-primary btn-get-api-key" onclick="jshopAdmin.getAddonKey('<?php print $row->alias?>');return false;">
            <?php echo Text::_('JSHOP_GET_ADDON_KEY')?>
        </a>
        <?php }?>
    </td>   
</tr>
</table>
</fieldset>
</div>

<input type="hidden" name="task" value="" />
<input type="hidden" name="f-id" value="<?php print $row->id?>" />
<input type="hidden" name="alias" value="<?php print $row->alias?>" />
<input type="hidden" name="back" value="<?php print $this->back?>" />
<?php print $this->tmp_html_end?>
</form>