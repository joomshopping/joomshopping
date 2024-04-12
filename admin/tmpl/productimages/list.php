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
<form action="index.php?option=com_jshopping&controller=productimages&task=display&tmpl=component" method="post">
<div class="js-stools clearfix jshop_block_filter">
    <div class="js-stools-container-bar">
        <div class="btn-toolbar" role="toolbar">

            <div class="btn-group mr-2">
                <div class="input-group">
                    <input name="filter" value="<?php echo htmlspecialchars($this->filter);?>" class="form-control" placeholder="<?php print JText::_('JSHOP_SEARCH')?>" type="text">
                    <span class="input-group-append">
                        <button type="submit" class="btn btn-primary hasTooltip" title="<?php print JText::_('JSHOP_SEARCH')?>">
                            <span class="icon-search" aria-hidden="true"></span>
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="images_list">
    <?php foreach($this->list as $file){?>
        <div class="one_image">
            <table>
			<tr><td align="center" valign="middle"><div>
                <a href="#" onclick="parent.jshopAdmin.setImageFromFolder('<?php print $file?>');return false;">
                    <img title="<?php print $file?>" src="<?php print $this->config->image_product_live_path?>/thumb_<?php print $file?>">
                </a>
				</div></td></tr>
				<tr><td valign="bottom" align="center"><div>
                <a href="#" onclick="parent.jshopAdmin.setImageFromFolder('<?php print $file?>');return false;">
                    <?php print $file?>
                </a>
			</div></td></tr>
            </table>
		</div>
    <?php }?>

    <div style="clear: both"></div>
</div>
</form>