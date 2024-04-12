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
<div id="product_extra_fields" class="tab-pane">
    <div class="col100" id="extra_fields_space">
    <?php print $this->tmpl_extra_fields;?>
    <?php $pkey='plugin_template_extrafields'; if ($this->$pkey){ print $this->$pkey;}?>
    </div>
    <div class="clr"></div>
</div>