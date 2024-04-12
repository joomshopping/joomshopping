<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
$menu = JSHelperAdmin::getItemsOptionPanelMenu();
?>
<div class="jssubmenu">
    <div class="m">
        <ul id="submenu">
        <?php        
		foreach($menu as $key=>$el){
            if (!$el[3]) continue;
            $class="";
            if ($active){
                if ($key==$active) $class="class='active'";
            }else{
                if ($el[1] == basename($_SERVER['REQUEST_URI'])) $class="class='active'";
            }
        ?>
            <li>
                <a <?php print $class;?> href="<?php print $el[1]?>"><?php print $el[0]?></a>
            </li>
        <?php }?>        
        </ul>    
        <div class="clr"></div>
    </div>
</div>