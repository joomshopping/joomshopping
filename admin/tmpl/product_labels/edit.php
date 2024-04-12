<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
$row=$this->productLabel;
?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=productlabels" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<?php print $this->tmp_html_start?>
<div class="col100">
<fieldset class="adminform">
<table class="admintable" width="100%">
	<?php 
		foreach($this->languages as $lang){
		$name = "name_".$lang->language;
	?>
	<tr>
		<td class="key">
			<?php echo JText::_('JSHOP_NAME')?> <?php if ($this->multilang) print "(".$lang->lang.")";?>*
		</td>
		<td>
			<input type="text" class="inputbox form-control" name="<?php print $name?>" value="<?php echo $row->$name;?>" />
		</td>
	</tr>
	<?php } ?>
	<tr>
		<td class="key"><?php print JText::_('JSHOP_IMAGE')?></td>
		<td>
			<?php if ($row->image) {?>
				<div id="image_block">
					<div><img src="<?php echo $this->config->image_labels_live_path."/".$row->image?>" alt=""/></div>
					<div style="padding-bottom:5px;" class="link_delete_foto">
                        <a class="btn btn-micro btn-danger" href="#" onclick="if (confirm('<?php print JText::_('JSHOP_DELETE_IMAGE')?>')) jshopAdmin.deleteFotoLabel('<?php echo $row->id?>');return false;">
                            <img src="components/com_jshopping/images/publish_r.png"> <?php print JText::_('JSHOP_DELETE_IMAGE')?>
                        </a>
                    </div>
				</div>
			<?php }?>    
			<input type="file" name="image" />
		</td>
	</tr>
	<?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>    
</table>
</fieldset>
</div>
<div class="clr"></div>
 
<input type="hidden" name="old_image" value="<?php print $row->image;?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="f-id" value="<?php echo (int)$row->id?>" />
<?php print $this->tmp_html_end?>
</form>
</div>