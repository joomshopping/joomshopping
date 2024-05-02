<?php
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;

/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
?>

<div id="j-main-container" class="j-main-container">
    <form action="index.php?option=com_jshopping" method="post" name="adminForm" id="adminForm">
    <?php print $this->tmp_html_start?>
    <div id="cpanel">
        <?php HelperAdmin::displayOptionPanelIco(); ?>
    </div>
    <?php print $this->tmp_html_end?>
    <input type="hidden" name="task">
    </form>
</div>
