<?php
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\CMS\Language\Text;

/**
* @version      5.10.0 10.10.2026
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die;
?>
<?php if (count ($this->demofiles)){?>
    <div class="list_product_demo">
        <table>
            <?php foreach($this->demofiles as $demo){?>
                <tr>
                    <td class="descr"><?php print $demo->demo_descr?></td>            
                    <?php if ($this->config->demo_type == 1) { ?>
                        <td class="download"><a target="_blank" href="<?php print $this->config->demo_product_live_path."/".$demo->demo;?>" onClick="popupWin = window.open('<?php print Helper::SEFLink("index.php?option=com_jshopping&controller=product&task=showmedia&media_id=".$demo->id);?>', 'video', 'width=<?php print $this->config->video_product_width;?>,height=<?php print $this->config->video_product_height;?>,top=0,resizable=no,location=no'); popupWin.focus(); return false;" title="play" aria-label="play"><span class="fas fa-play-circle" aria-hidden="true"></span></a></td>
                    <?php } else { ?>
                        <td class="download"><a target="_blank" href="<?php print $this->config->demo_product_live_path."/".$demo->demo;?>"><span class="fas fa-download" aria-hidden="true"></span> <?php print Text::_('JSHOP_DOWNLOAD')?></a></td>
                    <?php }?>
                </tr>
            <?php }?>
        </table>
    </div>
<?php } ?>