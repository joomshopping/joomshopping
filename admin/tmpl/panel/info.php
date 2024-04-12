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
<?php if ($this->sidebar){?>
<div id="j-main-container" class="col-md-12">
<?php }?>
<?php print $this->tmp_html_start?>
<table width="100%" style="background: #EBF0F3;border-radius:10px;">
<tr>
 <td width="50%" valign="top" style="padding:10px;">
    <p style="margin-top:0px;">Anschrift und andere Angaben zum Unternehmen:<br>
    <br>
    <strong>MAXX <em>marketing GmbH</em></strong>
    <br>Karlsplatz 7 (Stachus)<br>
    D-80335 München<br><br>
    Tel: +49 (0)89 - 929286-0<br>
    Fax:+49 (0)89 - 929286-75<br>
    eMail: <strong>
    <a class="link" href="mailto:info@joomshopping.com">info@joomshopping.com</a>
    </strong><br><br>
    </p>
    <p><strong>Steueridentifikationsnummer:<br></strong>
	143/160/40099
    <br><br>
    <strong>Umsatzsteuer Nummer:<br></strong>
	DE221510498
    <br><br>
    </p>
    <p><strong>Geschäftsführer:</strong> 
    <br>Klaus Huber</p>
 </td>
 <td valign="top" style="padding:10px;">
    <div style="padding-left:5px;padding-bottom:30px;">
        <div><img src="components/com_jshopping/images/jshop_logo.jpg" /></div>
        <div style="padding-top:5px;padding-left:5px;font-size:14px;"><?php if (isset($this->data['version'])){?><b>Version <?php print $this->data['version'];}?></b></div>
		<?php if (isset($this->update->text) && $this->update->text && $this->update->link) { ?>
		<div style="padding-left:5px;padding-top:4px;">
			<a href="<?php echo $this->update->link;?>" target="_blank"><?php echo $this->update->text;?></a>
		</div>
		<?php } ?>
        <?php if (isset($this->update->text2) && $this->update->text2 && $this->update->link2){?>
        <div style="padding-left:5px;padding-top:4px;">
            <a href="<?php echo $this->update->link2;?>" onclick="return confirm('<?php echo $this->update->text2;?>')"><?php echo $this->update->text2;?></a>
        </div>
        <?php }?>
    </div>
    <div style="padding-bottom:5px;">
        <img src="components/com_jshopping/images/at.png" align="left" border="0" style="margin-right:10px;">
        <div><b>Web. <a href="http://www.joomshopping.com/" target="_blank" style="color:#000;">www.joomshopping.com</a></b>
        <br><b><a href="mailto:info@joomshopping.com">info@joomshopping.com</a></b></div>
        <br>
    </div>
    <div style="padding-left:4px;padding-bottom:15px;">
        <img src="components/com_jshopping/images/info.png" align="left" border="0" style="margin-right:15px;">
        <div style="padding-top:2px;"><a href="http://www.webdesigner-profi.de/joomla-webdesign/joomla-shop/forum.html" target="_blank" style="color:#000;"><b>Hilfe / Support</b></a></div>
        <br>
    </div>
    <div style="padding-left:4px;">
        <img src="components/com_jshopping/images/shop.png" align="left" border="0" style="margin-right:8px;">
        <div style="padding-top:3px;"><a href="http://www.webdesigner-profi.de/joomla-webdesign/shop" target="_blank" style="color:#000;"><b>JoomShopping extensions</b></a></div>
    </div>
 </td>
</table>
<?php print $this->tmp_html_end?>
<?php if ($this->sidebar){?>
</div>
<?php }?>
</div>