<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
$jshopConfig = \JSFactory::getConfig();
\JHTML::_('bootstrap.tooltip');
?>
<div class="row">
<div class="col-md-12">
<div id="j-main-container" class="j-main-container">
<?php \JSHelperAdmin::displaySubmenuConfigs('image');?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=config" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<?php print $this->tmp_html_start?>
<input type="hidden" name="task" value="">
<input type="hidden" name="tab" value="3">

<div class="col100">
<fieldset class="adminform">
    <legend><?php echo JText::_('JSHOP_IMAGE_VIDEO_PARAMETERS')?></legend>
<table class="admintable table-striped">
  <tr>
    <td class="key" style="width:200px;">
      <?php echo JText::_('JSHOP_IMAGE_CATEGORY_WIDTH')?>
    </td>
    <td>
      <input type="text" name="image_category_width" class = "form-control" id="image_category_width" value ="<?php echo $jshopConfig->image_category_width?>" />
      <?php echo \JSHelperAdmin::tooltip(JText::_('JSHOP_IMAGE_SIZE_INFO')) ?>
    </td>
    <td>
    </td>
  </tr>
  <tr>
    <td class="key">
      <?php echo JText::_('JSHOP_IMAGE_CATEGORY_HEIGHT')?>
    </td>
    <td>
      <input type="text" name="image_category_height" class = "form-control" id="image_category_height" value ="<?php echo $jshopConfig->image_category_height?>" />
      <?php echo \JSHelperAdmin::tooltip(JText::_('JSHOP_IMAGE_SIZE_INFO')) ?>
    </td>
    <td>
    </td>
  </tr>
  <tr><td></td></tr>
  <tr>
    <td class="key">
      <?php echo JText::_('JSHOP_IMAGE_PRODUCT_THUMB_WIDTH')?>
    </td>
    <td>
      <input type="text" name="image_product_width" class = "form-control" id="image_product_width" value ="<?php echo $jshopConfig->image_product_width?>" />
      <?php echo \JSHelperAdmin::tooltip(JText::_('JSHOP_IMAGE_SIZE_INFO')) ?>
    </td>
    <td>
    </td>
  </tr>
  <tr>
    <td class="key">
      <?php echo JText::_('JSHOP_IMAGE_PRODUCT_THUMB_HEIGHT')?>
    </td>
    <td>
      <input type="text" name="image_product_height" class = "form-control" id="image_product_height" value ="<?php echo $jshopConfig->image_product_height?>" />
      <?php echo \JSHelperAdmin::tooltip(JText::_('JSHOP_IMAGE_SIZE_INFO')) ?>
    </td>
  </tr>
  <tr><td></td></tr>
  <tr>
    <td class="key">
      <?php echo JText::_('JSHOP_IMAGE_PRODUCT_FULL_WIDTH')?>
    </td>
    <td>
      <input type="text" name="image_product_full_width" class = "form-control" id="image_product_full_width" value ="<?php echo $jshopConfig->image_product_full_width?>" />
      <?php echo \JSHelperAdmin::tooltip(JText::_('JSHOP_IMAGE_SIZE_INFO')) ?>
    </td>
  </tr>
  <tr>
    <td class="key">
      <?php echo JText::_('JSHOP_IMAGE_PRODUCT_FULL_HEIGHT')?>
    </td>
    <td>
      <input type="text" name="image_product_full_height" class = "form-control" id="image_product_full_height" value ="<?php echo $jshopConfig->image_product_full_height?>" />
      <?php echo \JSHelperAdmin::tooltip(JText::_('JSHOP_IMAGE_SIZE_INFO'));?>
    </td>
    <td>
    </td>
  </tr>
  <tr><td></td></tr>
  <tr>
    <td class="key">
      <?php echo JText::_('JSHOP_IMAGE_PRODUCT_ORIGINAL_WIDTH')?>
    </td>
    <td>
      <input type="text" name="image_product_original_width" class = "form-control" value="<?php echo $jshopConfig->image_product_original_width?>" />
      <?php echo \JSHelperAdmin::tooltip(JText::_('JSHOP_IMAGE_SIZE_INFO'));?>
    </td>
  </tr>
  <tr>
    <td class="key">
      <?php echo JText::_('JSHOP_IMAGE_PRODUCT_ORIGINAL_HEIGHT')?>
    </td>
    <td>
      <input type="text" name="image_product_original_height" class = "form-control" value="<?php echo $jshopConfig->image_product_original_height?>" />
      <?php echo \JSHelperAdmin::tooltip(JText::_('JSHOP_IMAGE_SIZE_INFO'));?>
    </td>
    <td>
    </td>
  </tr>  
  <tr>
    <td class="key">
      <?php echo JText::_('JSHOP_IMAGE_RESIZE_TYPE')?>
    </td>
    <td>
      <?php print $this->select_resize_type;?>
    </td>
    <td>
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_OC_image_quality')?>
    </td>
    <td>
        <input type="text" name="image_quality" class = "form-control" value ="<?php echo $jshopConfig->image_quality?>" />
    </td>
</tr>

<tr>
    <td class="key">
        <?php echo JText::_('JSHOP_OC_image_fill_color')?>
    </td>
    <td>
        <input type="text" name="image_fill_color" class = "form-control" value ="<?php echo $jshopConfig->image_fill_color?>" />
    </td>
</tr>
<?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
</table>
</<fieldset>
</div>
<div class="clr"></div>

<div class="col100">
<fieldset class="adminform">
    <legend><?php echo JText::_('JSHOP_PRODUCT_VIDEOS')?></legend>
<table class="admintable table-striped">
<tr>
    <td class="key" style="width:200px;">
      <?php echo JText::_('JSHOP_VIDEO_PRODUCT_WIDTH')?>
    </td>
    <td>
      <input type="text" name="video_product_width" class = "form-control" value ="<?php echo $jshopConfig->video_product_width?>" />      
    </td>
</tr>
<tr>
    <td class="key">
      <?php echo JText::_('JSHOP_VIDEO_PRODUCT_HEIGHT')?>
    </td>
    <td>
      <input type="text" name="video_product_height" class = "form-control" value ="<?php echo $jshopConfig->video_product_height?>" />
    </td>    
</tr>
<tr>
    <td class="key">
      <?php echo JText::_('JSHOP_VIDEO_HTML5_TYPE')?>
    </td>
    <td>
      <input type="text" name="video_html5_type" class = "form-control" value ="<?php echo $jshopConfig->video_html5_type?>" />
      <?php echo \JSHelperAdmin::tooltip("video/mp4, video/webm, video/ogg");?>
    </td>    
</tr>

<?php $pkey="etemplatevar2";if (isset($this->$pkey)){print $this->$pkey;}?>
</table>
</fieldset>
</div>
<div class="clr"></div>
<?php print $this->tmp_html_end?>
</form>
</div>
</div>
</div>
</div>