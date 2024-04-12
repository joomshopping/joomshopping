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
    <h1><?php print JText::_('JSHOP_LOGOUT')?></h1>
    <?php print $this->checkout_navigator?>
    
    <input type="button" class="btn button" value="<?php print JText::_('JSHOP_LOGOUT')?>" onclick="location.href='<?php print \JSHelper::SEFLink("index.php?option=com_jshopping&controller=user&task=logout"); ?>'" />
</div>