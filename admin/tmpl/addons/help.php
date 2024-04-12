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
<form action="index.php?option=com_jshopping&controller=addons" method="post" enctype="multipart/form-data" name="adminForm" id='adminForm'>
<input type="hidden" name="task" value="" />
</form>
1. <?php printf(JText::_('JSHOP_DOWNLOAD_ADDON_FROM_'), '<a href="https://www.webdesigner-profi.de/joomla-webdesign/shop.html" target="_blank">www.webdesigner-profi.de</a>')?><br>
2. <?php print JText::_('JSHOP_INSTALL_JSHOP_INSTALL_UPDATE')?><br>
3. <?php print JText::_('JSHOP_SHOP_OPTION_ADDON_KEY_CONFIG')?>