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
<div class="jshop" id="jshop_menu_order">
    <?php foreach($this->steps as $k=>$step){?>
      <div class="jshop_order_step <?php print $this->cssclass[$k]?>">
        <?php print $step;?>
      </div>
    <?php }?>
</div>