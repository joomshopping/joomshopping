<?php 
/**
* @version      5.0.7 02.09.2022
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die;
?>
<?php print $this->tmp_html_start?>
<table>
<tr>
 <td width="40%" style="vertical-align:top">
    <div id="cpanel">
        <?php \JSHelperAdmin::displayMainPanelIco(); ?>
    </div>
 </td>
 <td width="60%" style="vertical-align:top">
    <div class="contacts">
        <div class="jslogo"><img src="components/com_jshopping/images/joomshopping.png"></div>
        <div class="firm">by MAXXmarketing GmbH</div>
        <div class="info mb-3">
            <div class="img"><img src="components/com_jshopping/images/tel.png"></div>
            <div>
                <div>Tel. 0049(0)89-92 92 86-0</div>
                <div>Fax. 0049(0)89-92 92 86-75</div>
            </div>
        </div>
        <div class="info">
            <div class="img"><img src="components/com_jshopping/images/mail.png"></div>
            <div>
                <div>Web. <a href="http://www.joomshopping.com/" target="_blank">www.joomshopping.com</a></div>
                <div><a href="mailto:info@joomshopping.com">info@joomshopping.com</a></div>
            </div>
        </div>
    </div>
</td>
</tr>
</table>
<?php print $this->tmp_html_end?>