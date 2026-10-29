<?php
use Joomla\CMS\Language\Text;

/**
* @version      5.10.0 27.10.2026
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die;
?>
<div class="jshop" id="comjshop">
<h1><?php print Text::_('JSHOP_SEARCH_RESULT')?> <?php if ($this->search) print '"'.htmlspecialchars($this->search, ENT_QUOTES).'"';?></h1>

<?php echo Text::_('JSHOP_NO_SEARCH_RESULTS')?>
</div>