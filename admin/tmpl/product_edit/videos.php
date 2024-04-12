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
                <a class="btn btn-mini" href="#" onclick="if (confirm('<?php print JText::_('JSHOP_DELETE_VIDEO')?>')) jshopAdmin.deleteVideoProduct('<?php echo $video->video_id?>');return false;">
                    <img src="components/com_jshopping/images/publish_r.png"> <?php print JText::_('JSHOP_DELETE_VIDEO')?>
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
            <td class="key" style="width:250px;"><?php print JText::_('JSHOP_UPLOAD_VIDEO')?></td>
            <td>
            <input type="file" name="product_video_<?php print $i;?>" /><textarea rows="5" cols="22" name="product_video_code_<?php print $i;?>" style="display: none;"></textarea>
			<?php if ($jshopConfig->show_insert_code_in_product_video) { ?>
            <div style="padding-top:3px;"><input type="checkbox" value="1" name="product_insert_code_<?php print $i;?>" id="product_insert_code_<?php print $i;?>" onclick="jshopAdmin.changeVideoFileField(this);"/><label for="product_insert_code_<?php print $i;?>"><?php print JText::_('JSHOP_INSERT_CODE')?></label></div>
			<?php } ?>
            </td>
        </tr>
        <tr>
            <td class="key"><?php print JText::_('JSHOP_UPLOAD_VIDEO_IMAGE')?></td>
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
    <?php $pkey='plugin_template_attribute'; if ($this->$pkey){ print $this->$pkey;}?>
    <div class="helpbox">
        <div class="head"><?php echo JText::_('JSHOP_ABOUT_UPLOAD_FILES')?></div>
        <div class="text">
            <?php print sprintf(JText::_('JSHOP_SIZE_FILES_INFO'), ini_get("upload_max_filesize"), ini_get("post_max_size"));?>
        </div>
    </div>
</div>