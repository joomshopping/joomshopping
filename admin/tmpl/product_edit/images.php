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
<script type="text/javascript">
	jshopAdmin.JSHOP_IMAGE_SELECT = "<?php echo JText::_('JSHOP_IMAGE_SELECT')?>";
</script>

<div id="product_images_tab" class="tab-pane <?php if ($this->product->parent_id!=0){?>active<?php }?>">
    <div class="product_image_list">
    <?php
    $i=0;      
    if (count($lists['images']))
    foreach($lists['images'] as $image){
    ?>          
        <div class="product_image_list_item" id="foto_product_<?php print $image->image_id?>">
            <input type="text" class="form-control middle2" name="old_image_descr[<?php print $image->image_id?>]" placeholder="<?php print JText::_('JSHOP_TITLE')?>" value="<?php print htmlspecialchars($image->name);?>" >            
            <div class="image_block">
                <a target="_blank" href="<?php echo \JSHelper::getPatchProductImage($image->image_name, 'full', 1)?>">
                <img src="<?php echo \JSHelper::getPatchProductImage($image->image_name, 'thumb', 1)?>">
                </a>
            </div>
            <?php print JText::_('JSHOP_ORDERING')?>: <input type="text" class = "form-control small form-control-inline" name="old_image_ordering[<?php print $image->image_id?>]" value="<?php print $image->ordering;?>" class="small" />
            <div style="height:3px;"></div>
            <input type="radio" name="set_main_image" id="set_main_image_<?php echo $image->image_id?>" value="<?php echo $image->image_id?>" <?php if ($row->image == $image->image_name) echo 'checked="checked"';?>/> 
			<label style="min-width: 50px;float:none;" for="set_main_image_<?php echo $image->image_id?>"><?php echo JText::_('JSHOP_SET_MAIN_IMAGE')?></label>
  			<?php if (isset($image->tmp_data_img)) print $image->tmp_data_img?>
            <div class="link_delete_foto">
                <a class="btn btn-mini btn-danger" href="#" onclick="if (confirm('<?php print JText::_('JSHOP_DELETE_IMAGE')?>')) jshopAdmin.deleteFotoProduct('<?php echo $image->image_id?>');return false;">
                    <?php print JText::_('JSHOP_DELETE')?>
                </a>
            </div>
        </div>
    <?php } ?>
   </div>
   <div style="height:10px;"></div>
   <div class="col width-45" style="float:left">
        <fieldset class="adminform">
        <legend><?php echo JText::_('JSHOP_UPLOAD_IMAGE')?></legend>
        <div style="height:4px;"></div>
        <?php for($i=0; $i < $jshopConfig->product_image_upload_count; $i++){?>
        <div style="padding-bottom:10px;">
            <div style="padding-bottom:3px;">
                <input type="text" class = "form-control" name="product_image_descr_<?php print $i;?>" placeholder="<?php print JText::_('JSHOP_TITLE')?>" title="<?php print JText::_('JSHOP_TITLE')?>" />
            </div>
            <div class="product_file_image">
                <input type="file" class="product_image" name="product_image_<?php print $i;?>">
            </div>
            <div class="product_folder_image" style="display:none;">
                <input type="text" class = "form-control form-control-inline" name="product_folder_image_<?php print $i;?>" >
                <input type="button" name="select_image_<?php print $i;?>" value="<?php echo JText::_('JSHOP_IMAGE_SELECT')?>" class="btn btn-primary"
                data-bs-toggle="modal" data-bs-target="#aModal" onclick="jshopAdmin.cElName=<?php echo $i?>">
            </div>
			<input type="checkbox" value="1" name="image_from_folder_<?php print $i;?>" id="image_from_folder_<?php print $i;?>" onclick="jshopAdmin.changeProductField(this);">
			<label for="image_from_folder_<?php print $i;?>">
				<?php print JText::_('JSHOP_IMAGE_SELECT')?>
			</label>
			<?php 
			if (isset($this->plugin_template_images_load)){
				print $this->plugin_template_images_load[$i];
			}
			?>
        </div>
        <?php } ?>        
        </fieldset>
    </div>
    
    <div class="col width-55"  style="float:left">
    
        <fieldset class="adminform">
        <legend><?php echo JText::_('JSHOP_IMAGE_THUMB_SIZE')?></legend>
            <table class="tmiddle"><tr>
            <td><input type="radio" name="size_im_product" id="size_1" checked="checked" onclick="jshopAdmin.setDefaultSize(<?php echo $jshopConfig->image_product_width; ?>,<?php echo $jshopConfig->image_product_height; ?>, 'product')" value="1" /></td>
            <td><label for="size_1" style="margin:0px;"><?php echo JText::_('JSHOP_IMAGE_SIZE_1')?></label></td>
            </tr></table>
            <table class="tmiddle"><tr>
            <td><input type="radio" name="size_im_product" value="3" id="size_3" onclick="jshopAdmin.setOriginalSize('product')" value="3"/></td>
            <td><label for="size_3" style="margin:0px;"><?php echo JText::_('JSHOP_IMAGE_SIZE_3')?></label></td>
            </tr></table>
            <table class="tmiddle"><tr>
            <td><input type="radio" name="size_im_product" id="size_2" onclick="jshopAdmin.setManualSize('product')" value="2" /></td>
            <td><label for="size_2" style="margin:0px;"><?php echo JText::_('JSHOP_IMAGE_SIZE_2')?></label></td>
            <td> <?php echo \JSHelperAdmin::tooltip(JText::_('JSHOP_IMAGE_SIZE_INFO'))?></td>
            </tr></table>            
            <div class="key1"><?php echo JText::_('JSHOP_IMAGE_WIDTH')?></div>
            <div class="value1"><input type="text" class = "form-control" id="product_width_image" name="product_width_image" value="<?php echo $jshopConfig->image_product_width?>" disabled="disabled" /></div>
            <div class="key1"><?php echo JText::_('JSHOP_IMAGE_HEIGHT')?></div>
            <div class="value1"><input type="text" class = "form-control" id="product_height_image" name="product_height_image" value="<?php echo $jshopConfig->image_product_height?>" disabled="disabled" /></div>
        </fieldset>
        
        <fieldset class="adminform">
        <legend><?php echo JText::_('JSHOP_IMAGE_SIZE')?></legend>
            <table class="tmiddle"><tr>
            <td><input type="radio" name="size_full_product" id="size_full_1" onclick="jshopAdmin.setDefaultSize(<?php echo $jshopConfig->image_product_full_width; ?>,<?php echo $jshopConfig->image_product_full_height; ?>, 'product_full')" value="1" checked="checked" /></td>
            <td><label for="size_full_1" style="margin:0px;"><?php echo JText::_('JSHOP_IMAGE_SIZE_1')?></label></td>
            </tr></table>
            <table class="tmiddle"><tr>
            <td><input type="radio" name="size_full_product" id="size_full_3" onclick="jshopAdmin.setFullOriginalSize('product_full')" value="3" /></td>
            <td><label for="size_full_3" style="margin:0px;"><?php echo JText::_('JSHOP_IMAGE_SIZE_3')?></label></td>
            </tr></table>
            <table class="tmiddle"><tr>
            <td><input type="radio" name="size_full_product" id="size_full_2" onclick="jshopAdmin.setFullManualSize('product_full')" value="2"/></td>
            <td><label for="size_full_2" style="margin:0px;"><?php echo JText::_('JSHOP_IMAGE_SIZE_2')?></label></td>
            <td> <?php echo \JSHelperAdmin::tooltip(JText::_('JSHOP_IMAGE_SIZE_INFO'))?></td>
            </tr></table>            
            <div class="key1"><?php echo JText::_('JSHOP_IMAGE_WIDTH')?></div>
            <div class="value1"><input type="text" class = "form-control" id="product_full_width_image" name="product_full_width_image" value="<?php echo $jshopConfig->image_product_full_width; ?>" disabled="disabled" /></div>
            <div class="key1"><?php echo JText::_('JSHOP_IMAGE_HEIGHT')?></div>
            <div class="value1"><input type="text" class = "form-control" id="product_full_height_image" name="product_full_height_image" value="<?php echo $jshopConfig->image_product_full_height; ?>" disabled="disabled" /></div>
        </fieldset>
        
    </div>
    <div class="clr"></div>
    <?php $pkey='plugin_template_images'; if ($this->$pkey){ print $this->$pkey;}?>
    <br/>
    <div class="helpbox">
        <div class="head"><?php echo JText::_('JSHOP_ABOUT_UPLOAD_FILES')?></div>
        <div class="text">
            <?php print JText::_('JSHOP_IMAGE_UPLOAD_EXT_INFO')?><br/>
            <?php print sprintf(JText::_('JSHOP_SIZE_FILES_INFO'), ini_get("upload_max_filesize"), ini_get("post_max_size"));?>
        </div>
    </div>

    <?php print \JHtml::_(
        'bootstrap.renderModal',
        'aModal',
        array(
            'title'       => \JText::_('JSHOP_IMAGE_SELECT'),
            'backdrop'    => 'static',
            'url'         => 'index.php?option=com_jshopping&controller=productimages&task=display&tmpl=component',
            'height'      => '400px',
            'width'       => '800px',
            'bodyHeight'  => 70,
            'modalWidth'  => 80
        )
    );?>

</div>