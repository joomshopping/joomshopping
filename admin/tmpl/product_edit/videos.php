<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
?>
<div id="product_videos" class="tab-pane">
   <table><tr>
    <?php foreach ($lists['videos'] as $video){ 
		if (!$video->video_preview) $video->video_preview="video.gif";
		$show_video_code=($video->video_code != '') ? 1 : 0;
    ?>
        <td style="padding-right:5px;">
        <div id="video_product_<?php print $video->video_id?>">
            <div style="padding-bottom:5px;">
				<?php if ($show_video_code) { ?>
				<a target="_blank" href="index.php?option=com_jshopping&controller=products&task=getvideocode&video_id=<?php print $video->video_id?>">
				<?php } else { ?>
                <a target="_blank" href="<?php echo $jshopConfig->video_product_live_path."/".$video->video_name;?>">
				<?php } ?>
                    <img width="80" src="<?php echo $jshopConfig->video_product_live_path."/".$video->video_preview ?>" border="0" />
                </a>
            </div>
            <div class="link_delete_foto">
                <a class="btn btn-sm btn-danger" href="#" onclick="if (confirm('<?php print Text::_('JSHOP_DELETE_VIDEO')?>')) jshopAdmin.deleteVideoProduct('<?php echo $video->video_id?>');return false;">
                    <?php print Text::_('JSHOP_DELETE_VIDEO')?>
                </a>
            </div>
        </div>
        </td>
    <?php } ?>
    </tr></table>
    <div class="col100">
    <table class="admintable" >
        <?php for ($i=0; $i < $jshopConfig->product_video_upload_count; $i++){?>
        <tr>
            <td class="key" style="width:250px;">
                <?php print Text::_('JSHOP_UPLOAD_VIDEO')?>
            </td>
            <td>
                <div class="pvf_file">
                    <input type="file" name="product_video_<?php print $i;?>" />
                </div>
                <?php if ($jshopConfig->show_insert_code_in_product_video) { ?>
                    <div class="mt-1 pvf_code">
                        <textarea rows="5" cols="22" name="product_video_code_<?php print $i;?>" style="display: none;"></textarea>                    
                        <div class="pt-1">
                            <label>
                                <input type="checkbox" onclick="jshopAdmin.changeVideoFileField(this, 'code');">
                                <?php print Text::_('JSHOP_INSERT_CODE')?>
                            </label>
                        </div>
                    </div>
                <?php } ?>
                <div class="mt-1 pvf_sel">
                    <div class="product_folder_video" style="display:none;">
                        <input type="text" class="form-control form-control-inline" name="product_folder_video_<?php print $i;?>" >
                        <input type="button" value="<?php echo Text::_('JSHOP_VIDEO_SELECT')?>" class="btn btn-primary"
                        data-bs-toggle="modal" data-bs-target="#videosModal" onclick="jshopAdmin.cElName=<?php echo $i?>">
                    </div>
                    <label>
                        <input type="checkbox" onclick="jshopAdmin.changeVideoFileField(this, 'folder');">
                        <?php print Text::_('JSHOP_VIDEO_SELECT')?>
                    </label>
                </div>
            
            </td>
        </tr>
        <tr>
            <td class="key"><?php print Text::_('JSHOP_UPLOAD_VIDEO_IMAGE')?></td>
            <td><input type="file" name="product_video_preview_<?php print $i;?>" /></td>
        </tr>
        <tr>
            <td style="height:5px;font-size:1px;">&nbsp;</td>
        </tr>
        <?php }?>
    </table>
	<?php if ($jshopConfig->show_insert_code_in_product_video) { ?>
	<script type="text/javascript">
		jshopAdmin.updateAllVideoFileField();
	</script>
	<?php } ?>
    </div>
    <div class="clr"></div>
    <br/>
    <?php $pkey='plugin_template_video'; if ($this->$pkey){ print $this->$pkey;}?>
    <div class="helpbox">
        <div class="head"><?php echo Text::_('JSHOP_ABOUT_UPLOAD_FILES')?></div>
        <div class="text">
            <?php print sprintf(Text::_('JSHOP_SIZE_FILES_INFO'), ini_get("upload_max_filesize"), ini_get("post_max_size"));?>
        </div>
    </div>

    <?php print HTMLHelper::_(
        'bootstrap.renderModal',
        'videosModal',
        array(
            'title'       => Text::_('JSHOP_VIDEO_SELECT'),
            'backdrop'    => 'static',
            'url'         => 'index.php?option=com_jshopping&controller=productimages&task=videos&tmpl=component',
            'height'      => '400px',
            'width'       => '800px',
            'bodyHeight'  => 70,
            'modalWidth'  => 80
        )
    );?>
</div>