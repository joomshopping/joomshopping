<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die;
?>
<div class="jshop" id="comjshop">
<h1><?php print JText::_('JSHOP_SEARCH_RESULT')?> <?php if ($this->search) print '"'.$this->search.'"';?></h1>

<?php echo JText::_('JSHOP_NO_SEARCH_RESULTS')?>
</div>