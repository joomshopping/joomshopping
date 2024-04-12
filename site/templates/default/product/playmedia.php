<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
$width = $this->config->video_product_width;
$height = $this->config->video_product_height;
?>
<html>
	<head>
		<title><?php print $this->description; ?></title>
        <?php print $this->scripts_load?>
	</head>
	<body style = "padding: 0px; margin: 0px;">
        <?php if ($this->file_is_audio){?>
            <div class="file_demo_audio">
                <audio controls autoplay>
                    <source 
                        src="<?php print $this->config->demo_product_live_path.'/'.$this->filename;?>" 
                        <?php if ($this->config->audio_html5_type){?>
                        type='<?php print $this->config->audio_html5_type?>' 
                        <?php }?>
                    />
                </audio>
            </div>
        <?php }else{ ?>
            <div class="file_demo_video">
                <video <?php if ($width){?> width="<?php print $width?>"<?php }?> <?php if ($height){?>height="<?php print $height?>"<?php }?> controls autoplay id = "video">
                    <source 
                        src="<?php print $this->config->demo_product_live_path.'/'.$this->filename;?>" 
                        <?php if ($this->config->video_html5_type){?>
                        type='<?php print $this->config->video_html5_type?>' 
                        <?php }?>
                    />
                </video>
            </div>
		<?php }?>
	</body>
</html>