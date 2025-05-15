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
<div class="p-3">
    <div class="mb-3 alert alert-warning">
        <i class="icon-warning"></i> <?php echo Text::_('JSHOP_UPDATE_ATTENTION_MSG')?>
    </div>
    <div class="j-version">Joomla: <span></span></div>
    <div class="js-version">JoomShopping: <span></span></div>

    <div class="mt-5 d-flex justify-content-center">
        <a class="btn btn-success btn_update" href="" onclick="jQuery('#update_popup').modal('hide');jshopAdmin.ajaxLoadAnimate()">
            <?php echo Text::_('JSHOP_UPDATE')?>
        </a>
    </div>
</div>