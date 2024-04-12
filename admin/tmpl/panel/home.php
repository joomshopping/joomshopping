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
<?php print $this->tmp_html_start?>
<table>
<tr>
 <td width="40%">
    <div id="cpanel">
    <?php \JSHelperAdmin::displayMainPanelIco(); ?>
    </div>
 </td>
 <td width="60%" style="vertical-align:top">
    <div id="contacts">
        <table cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td><img src="components/com_jshopping/images/jshop_logo.jpg" /></td>
            </tr>
            <tr>
                <td valign="middle">
                    <img src="components/com_jshopping/images/phone.gif" align="left" border="0">
                    <div>Tel. 0049(0)89-92 92 86-0<br>
                     Fax. 0049(0)89-92 92 86-75</div>
                </td>
            </tr>
            <tr>
                <td valign="middle">
                    <img src="components/com_jshopping/images/at.gif" align="left" border="0">
                    <div>Web. <a href="http://www.joomshopping.com/" target="_blank">www.joomshopping.com</a>
                    <br><a href="mailto:info@joomshopping.com">info@joomshopping.com</a></div>
                </td>
            </tr>
        </table>
    </div>             
 </td>
</tr>
</table>
<?php print $this->tmp_html_end?>