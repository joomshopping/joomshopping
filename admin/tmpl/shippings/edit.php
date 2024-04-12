<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$row=$this->shipping; 
$edit=$this->edit; 
?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=shippings" method="post" name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>
<div class="col100">
<fieldset class="adminform">
    <table class="admintable" width="100%" >
   	<tr>
     	<td class="key" width="30%">
       		<?php echo JText::_('JSHOP_PUBLISH')?>
     	</td>
     	<td>
       		<input type="checkbox" name="published" value="1" <?php if ($row->published) echo 'checked="checked"'?> />
     	</td>
   	</tr>
    <?php 
    foreach($this->languages as $lang){
    $field="name_".$lang->language;
    ?>
   	<tr>
     	<td class="key">
       		<?php echo JText::_('JSHOP_TITLE')?> <?php if ($this->multilang) print "(".$lang->lang.")";?>*
     	</td>
     	<td>
       		<input type="text" class="inputbox form-control" id="<?php print $field?>" name="<?php print $field?>" value="<?php echo $row->$field;?>" />
     	</td>
   	</tr>
    <?php }?>
    <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_ALIAS')?>
     </td>
     <td>
       <input type="text" class="inputbox form-control" name="alias" value="<?php echo $row->alias?>" <?php if ($this->config->shop_mode==0 && $row->shipping_id){?>readonly <?php }?> />
     </td>
    </tr>
    <tr>
        <td class="key">
            <?php echo JText::_('JSHOP_PAYMENTS')?>
        </td>
        <td>
           <?php print $this->lists['payments']?>
        </td>
    </tr>
    <tr>
        <td class="key">
            <?php echo JText::_('JSHOP_IMAGE_URL')?>
        </td>
        <td>
            <input type="text" class="inputbox form-control" name="image" value="<?php echo $row->image;?>" />
        </td>
    </tr>
	<?php print $this->tmp_html_after_image?>
    <?php 
    foreach($this->languages as $lang){
    $field="description_".$lang->language;
    ?>
   	<tr>
     	<td class="key">
       		<?php echo JText::_('JSHOP_DESCRIPTION')?> <?php if ($this->multilang) print "(".$lang->lang.")";?>
     	</td>
     	<td>
       		<?php
                $editor=\JEditor::getInstance(\JFactory::getConfig()->get('editor'));
                print $editor->display('description'.$lang->id,  $row->$field , '100%', '350', '75', '20' ) ;
       		?>
     	</td>
   	</tr>
    <?php }?>
    <?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
</table>
</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="task" value="<?php echo \JFactory::getApplication()->input->getVar('task')?>" />
<input type="hidden" name="edit" value="<?php echo $edit;?>" />
<?php if ($edit) {?>
  <input type="hidden" name="shipping_id" value="<?php echo (int)$row->shipping_id?>" />
<?php }?>
<?php print $this->tmp_html_end?>
</form>
</div>