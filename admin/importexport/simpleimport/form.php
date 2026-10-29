<?php
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

/**
* @version      5.10.0 27.10.2026
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
?>
<form action="index.php?option=com_jshopping&controller=importexport" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<input type="hidden" name="task" value="" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="ie_id" value="<?php print $ie_id;?>" />

<div class="d-flex flex-wrap align-items-center gap-2">
<label for="ie_file"><?php print Text::_('JSHOP_FILE')?> (*.csv):</label>
<input type="file" name="file" id="ie_file" class="form-control w-auto">
<label for="ie_encoding" class="ms-3"><?php print Text::_('JSHOP_ENCODING')?>:</label>
<select name="params[encoding]" id="ie_encoding" class="form-select w-auto">
<?php foreach($this->getEncodings() as $encoding){?>
    <option value="<?php print $encoding?>"<?php if ($encoding == $this->getSafeEncoding($ie_params['encoding'] ?? '')){?> selected<?php }?>><?php print $encoding?></option>
<?php }?>
</select>
</div>

<?php print HTMLHelper::_('form.token');?>
</form>