<?php
use Joomla\CMS\Language\Text;

/**
* @version      5.4.0 10.04.2014
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
?>
<button type="button" class='btn btn-small btn-primary extrafields_btn_edit' title="<?php print htmlspecialchars($this->title)?>">
	<span class='icon-edit icon-white'></span> 
    <?php print Text::_("JSHOP_EDIT")?>
</button>

<button type="button" class='btn btn-small btn-primary extrafields_btn_clear'>
    <?php print Text::_("JSHOP_CLEAR")?>
</button>