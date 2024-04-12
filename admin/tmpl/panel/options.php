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
<div class="row">
<div id="j-main-container" class="col-md-12">
    <form action="index.php?option=com_jshopping" method="post" name="adminForm" id="adminForm">
    <?php print $this->tmp_html_start?>
    <div id="cpanel">
        <?php \JSHelperAdmin::displayOptionPanelIco(); ?>
    </div>
    <?php print $this->tmp_html_end?>
    <input type="hidden" name="task">
    </form>
</div>
</div>