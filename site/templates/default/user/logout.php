<?php
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Helper\Helper;

/**
* @version      5.10.0 10.10.2026
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die;
?>
<div class="jshop" id="comjshop">
    <h1><?php print Text::_('JSHOP_LOGOUT')?></h1>
    <?php print $this->checkout_navigator?>
    
    <button type="button" class="btn button" onclick="location.href='<?php print Helper::SEFLink("index.php?option=com_jshopping&controller=user&task=logout"); ?>'">
        <span class="fas fa-sign-out-alt" aria-hidden="true"></span>
        <?php print Text::_('JSHOP_LOGOUT')?>
    </button>
</div>