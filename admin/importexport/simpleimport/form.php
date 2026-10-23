<?php
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

/**
* @version      5.10.0 18.10.2026
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

<?php print Text::_('JSHOP_FILE')?> (*.csv):
<input type="file" name="file">

<?php print HTMLHelper::_('form.token');?>
</form>