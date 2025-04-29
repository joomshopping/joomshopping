<?php
/**
* @version      5.6.2 15.04.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
?>
<?php foreach($this->videos as $k=>$video){?>
	<?php if ($video->video_code){ ?>
		<div style="display:none" class="video_full" id="hide_video_<?php print $k?>"><?php echo $video->video_code?></div>
	<?php } else { ?>
		<a style="display:none" class="video_full" id="hide_video_<?php print $k?>" href=""></a>
	<?php } ?>
<?php } ?>