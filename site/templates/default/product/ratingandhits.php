<?php
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Helper\Helper;

/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
?>
<?php if (($this->allow_review && !$this->config->hide_product_rating) || $this->config->show_hits){?>
<div class="block_rating_hits">
    <table>
        <tr>
            <?php if ($this->config->show_hits){?>
                <td><?php print Text::_('JSHOP_HITS')?>: </td>
                <td><?php print $this->product->hits;?></td>
            <?php } ?>
            
            <?php if (($this->allow_review && !$this->config->hide_product_rating) && $this->config->show_hits){?>
                <td> | </td>
            <?php } ?>
            
            <?php if ($this->allow_review && !$this->config->hide_product_rating){?>
                <td>
                    <?php print Text::_('JSHOP_RATING')?>: 
                </td>
                <td>
                    <?php print Helper::showMarkStar($this->product->average_rating);?>                    
                </td>
            <?php } ?>
        </tr>
    </table>
</div>
<?php } ?>