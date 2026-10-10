<?php
/**
* @version      5.10.0 10.10.2026
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$icons = array(0 => 'fa-shopping-cart', 1 => 'fa-sign-in-alt', 2 => 'fa-map-marker-alt', 3 => 'fa-credit-card', 4 => 'fa-truck', 5 => 'fa-check-circle');
?>
<div class="jshop" id="jshop_menu_order">
    <?php foreach($this->steps as $k=>$step){?>
      <div class="jshop_order_step <?php print $this->cssclass[$k]?>">
        <?php if (isset($icons[$k])){?><span class="fas <?php print $icons[$k]?>" aria-hidden="true"></span><?php }?>
        <?php print $step;?>
      </div>
    <?php }?>
</div>
