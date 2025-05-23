<?php 
/**
* @version      5.6.2 15.05.2024
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
?>
<?php if (empty($this->_tmp_block_image_thumb)) {?>
	<?php if ( (count($this->images)>1) || (count($this->videos) && count($this->images)) ) {?>
		<?php foreach($this->images as $k=>$image){?>
			<div class="sblock0">
				<img class="jshop_img_thumb" src="<?php print $this->image_product_path?>/<?php print $image->image_thumb?>" alt="<?php print htmlspecialchars($image->img_alt)?>" title="<?php print htmlspecialchars($image->img_title)?>" onclick="jshop.showImage(<?php print $image->image_id?>)" >
			</div>
		<?php }?>
	<?php }?>
<?php } else {?>
	<?php echo $this->_tmp_block_image_thumb;?>
<?php }?>