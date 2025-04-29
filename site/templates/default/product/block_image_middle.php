<?php
use Joomla\CMS\Language\Text;

/**
* @version      5.6.2 15.04.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
?>
<?php print $this->_tmp_product_html_body_image?>
<?php if (empty($this->_tmp_block_image_middle)) {?>
	<?php if (!count($this->images)){?>
		<img id="main_image" src="<?php print $this->image_product_path?>/<?php print $this->noimage?>" alt="<?php print htmlspecialchars($this->product->name)?>" />
	<?php }?>
	<?php foreach($this->images as $k=>$image){?>
		<a class="lightbox" id="main_image_full_<?php print $image->image_id?>" href="<?php print $this->image_product_path?>/<?php print $image->image_full;?>" <?php if ($k!=0){?>style="display:none"<?php }?> title="<?php print htmlspecialchars($image->img_title)?>">
			<img id = "main_image_<?php print $image->image_id?>" class="image" src="<?php print $this->image_product_path?>/<?php print $image->image_name;?>" alt="<?php print htmlspecialchars($image->img_alt)?>" title="<?php print htmlspecialchars($image->img_title)?>" />
			<div class="text_zoom">
				<span class="icon-zoom-in"></span>
				<?php print Text::_('JSHOP_ZOOM_IMAGE')?>
			</div>
		</a>
	<?php }?>
<?php } else {?>
	<?php echo $this->_tmp_block_image_middle;?>
<?php }?>