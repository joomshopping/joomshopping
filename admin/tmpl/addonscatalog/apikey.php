<?php
use Joomla\CMS\Language\Text;

/**
* @version      5.7.0 12.05.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
?>
<form action="index.php?option=com_jshopping&controller=addonscatalog&task=apikeysave" method="post" id="adminForm">
<?php print $this->tmp_html_start ?? ''?>
<div class="col100">
<fieldset class="adminform">
<table width="100%" class="admintable">
<tr>
    <td class="key" style="width:250px;">
        <?php echo Text::_('JSHOP_API_KEY')?>
    </td>
    <td>
        <input type="text" class="inputbox form-control" name="key" value="<?php echo $this->key ?? '';?>" size="100" />
        <div class="small mt-1">
            <a target="_blank" href="https://www.webdesigner-profi.de/joomla-webdesign/shop/user/apikeys.html">
                <?php echo Text::_('JSHOP_PRESS_FOR_GET_KEY')?>
            </a>
        </div>
    </td>
</tr> 
</table>
</fieldset>
</div>

<input type="hidden" name="task" value="" />
<?php print $this->tmp_html_end ?? '';?>
</form>