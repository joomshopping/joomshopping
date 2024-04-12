<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$usergroup=$this->usergroup;
?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=usergroups" method="post" name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>
<div class="col100">
<fieldset class="adminform">
    <table class="admintable" width="100%">
    <?php
    foreach($this->languages as $lang){
    $name = "name_".$lang->language;
    ?>
    <tr>
       <td class="key" width = "20%">
         <?php echo JText::_('JSHOP_TITLE')?> <?php if ($this->multilang) print "(".$lang->lang.")";?>* 
       </td>
       <td>
         <input type = "text" class = "inputbox form-control" name = "<?php print $name?>" value = "<?php echo $usergroup->$name?>" />
       </td>
    </tr>
    <?php } ?>	
    <tr>
    <tr>
        <td class="key">
            <?php echo JText::_('JSHOP_USERGROUP_IS_DEFAULT')?>                
        </td>
        <td>
            <input type="checkbox" name="usergroup_is_default" <?php if ($usergroup->usergroup_is_default) echo 'checked="checked"';?> value="1" />
            <?php echo \JSHelperAdmin::tooltip(JText::_('JSHOP_USERGROUP_IS_DEFAULT_DESCRIPTION'));?>
        </td>
    </tr>
    <tr>
        <td class="key">
            <?php echo JText::_('JSHOP_USERGROUP_DISCOUNT')?>*    
        </td>
        <td>
            <input class="inputbox form-control" type="text" name="usergroup_discount" value="<?php echo $usergroup->usergroup_discount;?>" /> %
        </td>
    </tr>
    <?php
    foreach($this->languages as $lang){
    $name = "description_".$lang->language;
    ?>
	<tr>
		<td class="key">
            <?php echo JText::_('JSHOP_DESCRIPTION')?> <?php if ($this->multilang) print "(".$lang->lang.")";?>
		</td>
        <td>
            <?php 
            $editor = \JEditor::getInstance(\JFactory::getConfig()->get('editor'));
            print $editor->display('description'.$lang->id, $usergroup->$name, '100%', '350', '75', '20');
            ?>
        </td>
    </tr>
    <?php }?>
    <?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
</table>
</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="task" value="save" />
<input type="hidden" name="usergroup_id" value="<?php echo (int)$usergroup->usergroup_id;?>" />
<?php print $this->tmp_html_end?>
</form>
</div>