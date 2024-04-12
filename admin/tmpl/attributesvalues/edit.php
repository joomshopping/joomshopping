<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
$attr_id=$this->attr_id; 
?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=attributesvalues&attr_id=<?php echo $attr_id?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<?php print $this->tmp_html_start?>
<div class="col100">
<fieldset class="adminform">
<table class="admintable" width="100%" >
    <?php 
    foreach($this->languages as $lang){
    $field="name_".$lang->language;
    ?>
     <tr>
       <td class="key">
         <?php echo JText::_('JSHOP_NAME_ATTRIBUT_VALUE')?> <?php if ($this->multilang) print "(".$lang->lang.")";?>* 
       </td>
       <td>
         <input type="text" class="inputbox form-control" name="<?php print $field?>" value="<?php echo $this->attributValue->$field?>" />
       </td>
     </tr>
  <?php } ?>
  <tr>
    <td class="key"><?php print JText::_('JSHOP_IMAGE_ATTRIBUT_VALUE')?></td>
    <td>
    <?php if ($this->attributValue->image) {?>
    <div id="image_attrib_value">
        <div><img src="<?php echo $this->config->image_attributes_live_path."/".$this->attributValue->image?>" alt=""/></div>
        <div style="padding-bottom:5px;" class="link_delete_foto">
            <a class="btn btn-micro btn-danger" href="#" onclick="if (confirm('<?php print JText::_('JSHOP_DELETE_IMAGE')?>')) jshopAdmin.deleteFotoAttribValue('<?php echo $this->attributValue->value_id?>');return false;">
                <img src="components/com_jshopping/images/publish_x.png"> <?php print JText::_('JSHOP_DELETE_IMAGE')?></a>
            </div>
    </div>
    <?php }?>
    <div style="clear:both"></div>    
    <input type="file" name="image" />
    </td>
  </tr>
  <?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
</table>
</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="old_image" value="<?php print $this->attributValue->image;?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="value_id" value="<?php echo (int)$this->attributValue->value_id;?>" />
<input type="hidden" name="attr_id" value="<?php echo (int)$attr_id;?>" />
<?php print $this->tmp_html_end?>
</form>
</div>