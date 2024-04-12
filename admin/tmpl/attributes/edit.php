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
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=attributes" method="post" name="adminForm" id="adminForm" enctype = "multipart/form-data">
<?php print $this->tmp_html_start?>
<div class="col100">
<fieldset class="adminform">
<table class="admintable" width="100%" >
    <?php 
    foreach($this->languages as $lang){
    $name="name_".$lang->language;
    ?>
    <tr>
       <td class="key">
         <?php echo JText::_('JSHOP_TITLE')?> <?php if ($this->multilang) print "(".$lang->lang.")";?>* 
       </td>
       <td>
         <input type="text" class="inputbox form-control" name="<?php print $name?>" value="<?php echo $this->attribut->$name?>" />
       </td>
    </tr>
    <?php } ?>
    <?php 
    foreach($this->languages as $lang){
    $description="description_".$lang->language;
    ?>
    <tr>
       <td class="key">
         <?php echo JText::_('JSHOP_DESCRIPTION')?> <?php if ($this->multilang) print "(".$lang->lang.")";?>
       </td>
       <td>
         <input type="text" class="inputbox form-control" name="<?php print $description?>" value="<?php echo $this->attribut->$description?>" />
       </td>
    </tr>
    <?php } ?>
    
    <tr>
        <td class="key">
            <?php echo JText::_('JSHOP_TYPE_ATTRIBUT')?>*
        </td>
        <td>
            <?php echo $this->type_attribut;?>
            <?php echo \JSHelperAdmin::tooltip(JText::_('JSHOP_INFO_TYPE_ATTRIBUT'))?>
        </td>
    </tr>
    
    <tr>
        <td class="key">
            <?php echo JText::_('JSHOP_DEPENDENT')?>*
        </td>
        <td>
             <?php echo $this->dependent_attribut;?>
             <?php echo \JSHelperAdmin::tooltip(JText::_('JSHOP_INFO_DEPENDENT_ATTRIBUT'))?>
        </td>
    </tr>
    <tr>
     <td  class="key">
       <?php echo JText::_('JSHOP_GROUP')?>
     </td>
     <td>
       <?php echo $this->lists['group'];?>
     </td>
    </tr>
    <tr>
     <td  class="key">
       <?php echo JText::_('JSHOP_SHOW_FOR_CATEGORY')?>*
     </td>
     <td>
       <?php echo $this->lists['allcats'];?>
     </td>
   </tr>
   <tr id="tr_categorys" <?php if ($this->attribut->allcats=="1") print "style='display:none;'";?>>
     <td  class="key">
       <?php echo JText::_('JSHOP_CATEGORIES')?>*
     </td>
     <td>
       <?php echo $this->lists['categories'];?>
     </td>
   </tr>
    <?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
</table>
</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="task" value="" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="attr_id" value="<?php echo (int)$this->attribut->attr_id?>" />
<?php print $this->tmp_html_end?>
</form>
</div>