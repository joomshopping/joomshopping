<?php 
/**
* @version      5.0.7 02.09.2022
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
<div class="aboutus">
    <div class="contacts">
        <div class="jslogo">
            <img src="components/com_jshopping/images/joomshopping.png">
            <div class="version"><?php if (isset($this->data['version'])){?>Version <?php print $this->data['version'];}?></div>
        </div>
        <div class="firm">by MAXXmarketing GmbH</div>        
        <div class="info mb-3">
            <div class="img"><img src="components/com_jshopping/images/mail.png"></div>
            <div>
                <div>Web. <a href="http://www.joomshopping.com/" target="_blank">www.joomshopping.com</a></div>
                <div><a href="mailto:info@joomshopping.com">info@joomshopping.com</a></div>
            </div>
        </div>
        <div class="info mb-3">
            <div class="img"><img src="components/com_jshopping/images/support.png"></div>
            <div>                
                <div><a target="_blank" href="http://www.webdesigner-profi.de/joomla-webdesign/joomla-shop/forum.html">Hilfe / Support</a></div>
            </div>
        </div>
        <div class="info mb-3">
            <div class="img"><img src="components/com_jshopping/images/addons.png"></div>
            <div>                
                <div><a target="_blank" href="http://www.webdesigner-profi.de/joomla-webdesign/shop">JoomShopping extensions</a></div>
            </div>
        </div>
    </div>
    <div class="contacts firminfo">
        <div>Anschrift und andere Angaben zum Unternehmen:</div>
        <br>
        <div class="blue">MAXXmarketing GmbH</div>
        Karlsplatz 7 (Stachus)<br>
        D-80335 München<br>
        <br>
        <span class="blue">Tel:</span> +49 (0)89 - 929286-0<br>
        <span class="blue">Fax:</span>+49 (0)89 - 929286-75<br>        
        <br><br>
        
        <div class="blue">Steueridentifikationsnummer:</div>
        143/160/40099
        <br><br>
        <div class="blue">Umsatzsteuer Nummer:</div>
        DE221510498
        <br><br>        
        <div class="blue">Geschäftsführer:</div>
        Klaus Huber
    </div>
    <?php if (isset($this->installpage)) {?>
    <div class="contacts afinst">
        <div class="info mb-3">
            <div class="img"><img src="components/com_jshopping/images/jshop_products_b.png"></div>
            <div>                
                <div><a href="index.php?option=com_jshopping"><b>JoomShopping</b></a></div>
            </div>
        </div>
        <div class="info mb-3">
            <div class="img"><img src="components/com_jshopping/images/jshop_import_export_b.png"></div>
            <div>                
                <div><a href="index.php?option=com_jshopping&controller=update&task=update&installtype=url&install_url=sm1:demo_products_5.0.0.zip&back=<?php print urldecode("index.php?option=com_jshopping")?>">
                        <b><?php print \JText::_('JSHOP_LOAD_SAMPLE_DATA')?></b>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
<?php print $this->tmp_html_end?>
<?php if ($this->sidebar){?>
</div>
<?php }?>
</div>