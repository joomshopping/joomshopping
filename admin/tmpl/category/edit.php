<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$row = $this->category;
$lists = $this->lists;
$jshopConfig = $this->config;
?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=categories" method="post" enctype="multipart/form-data" name="adminForm" id="adminForm">
<?php echo \JHTML::_('form.token');?>
<?php print $this->tmp_html_start?>
    <ul class="joomla-tabs nav nav-tabs">
        <?php $i=0; foreach($this->languages as $lang){ $i++;?>
        <li class="nav-item">
            <a class="nav-link <?php if ($i==1){?>active<?php }?>" href="#<?php print $lang->language.'-page'?>" data-toggle="tab">
                <?php echo JText::_('JSHOP_DESCRIPTION')?><?php if ($this->multilang){?> (<?php print $lang->lang?>)<img class="tab_image" src="components/com_jshopping/images/flags/<?php print $lang->lang?>.gif" /><?php }?>
            </a>
        </li>
        <?php }?>
        <li class="nav-item"><a class="nav-link" href="#main-page" data-toggle="tab"><?php echo JText::_('JSHOP_MAIN_PARAMETERS')?></a></li>
        <li class="nav-item"><a class="nav-link" href="#image" data-toggle="tab"><?php echo JText::_('JSHOP_IMAGE')?></a></li>
    </ul>
    <div id="editdata-document" class="tab-content">
    <?php
    $i=0;
    foreach($this->languages as $lang){
       $i++;
       $name="name_".$lang->language;
       $alias="alias_".$lang->language;
       $description="description_".$lang->language;
       $short_description="short_description_".$lang->language;
       $meta_title="meta_title_".$lang->language;
       $meta_keyword="meta_keyword_".$lang->language;
       $meta_description="meta_description_".$lang->language;
     ?>
     <div id="<?php print $lang->language.'-page'?>" class="tab-pane<?php if ($i==1){?> active<?php }?>">
     <div class="col100">
     <table class="admintable">
       <tr>
         <td class="key" style="width:180px;">
           <?php echo JText::_('JSHOP_TITLE')?>*
         </td>
         <td>
           <input type="text" class="inputbox wide form-control" name="<?php print $name?>" value="<?php print $row->$name?>" />
         </td>
       </tr>
       <tr>
         <td class="key">
           <?php echo JText::_('JSHOP_ALIAS')?>
         </td>
         <td>
           <input type="text" class="inputbox wide form-control" name="<?php print $alias?>" value="<?php print $row->$alias?>" />
         </td>
       </tr>
       <tr>
         <td  class="key">
           <?php echo JText::_('JSHOP_SHORT_DESCRIPTION')?>
         </td>
         <td>
           <textarea name="<?php print $short_description;?>" class="wide form-control" rows="5"><?php echo $row->$short_description ?></textarea>
         </td>
       </tr>
       <tr>
         <td  class="key">
           <?php echo JText::_('JSHOP_DESCRIPTION')?>
         </td>
         <td>
           <?php
              $editor = \JEditor::getInstance(\JFactory::getConfig()->get('editor'));
              print $editor->display('description'.$lang->id, $row->$description , '100%', '350', '75', '20' ) ;
           ?>
         </td>
       </tr>
       <tr>
         <td  class="key">
           <?php echo JText::_('JSHOP_META_TITLE')?>
         </td>
         <td>
           <input type="text" class="inputbox form-control w100" name="<?php print $meta_title?>" value="<?php print $row->$meta_title?>" />
         </td>
       </tr>
       <tr>
         <td  class="key">
           <?php echo JText::_('JSHOP_META_DESCRIPTION')?>
         </td>
         <td>
           <input type="text" class="inputbox form-control w100" name="<?php print $meta_description;?>" value="<?php print $row->$meta_description;?>" />
         </td>
       </tr>
       <tr>
         <td  class="key">
           <?php echo JText::_('JSHOP_META_KEYWORDS')?>
         </td>
         <td>
           <input type="text" class="inputbox form-control w100" name="<?php print $meta_keyword?>" value="<?php print $row->$meta_keyword;?>" />
         </td>
       </tr>
       <?php $pkey = 'plugin_template_description_'.$lang->language; if (isset($this->$pkey)){ print $this->$pkey;}?>
     </table>
     </div>
     <div class="clr"></div>
     </div>     
   <?php }?>
   
   <div id="main-page" class="tab-pane">
     <div class="col100">
     <table class="admintable" >
       <tr>
         <td class="key" style="width:200px;">
           <?php echo JText::_('JSHOP_PUBLISH')?>
         </td>
         <td>
           <input type="checkbox" name="category_publish" value="1" <?php if ($row->category_publish) echo 'checked="checked"'?> />
         </td>
       </tr>
       <tr>
       <td class="key">
         <?php echo JText::_('JSHOP_ACCESS')?>*
       </td>
       <td>
         <?php print $this->lists['access'];?>
       </td>
     </tr>
       <tr>
         <td  class="key">
           <?php echo JText::_('JSHOP_ORDERING_CATEGORY')?>
         </td>
         <td id="ordering">
           <?php echo $lists['onelevel']?>
         </td>
       </tr>
              
       <?php if ($jshopConfig->use_different_templates_cat_prod) { ?>
       <tr>
         <td  class="key">
           <?php echo JText::_('JSHOP_TEMPLATE_CATEGORY')?>
         </td>
         <td>
           <?php echo $lists['templates']?>
         </td>
       </tr>
       <?php } ?>
       
       <tr>
         <td  class="key">
           <?php echo JText::_('JSHOP_COUNT_PRODUCTS_PAGE')?>*
         </td>
         <td>
           <input type="text" class="inputbox form-control" id="products_page" name="products_page" value="<?php echo $count_product_page=($row->category_id) ? ($row->products_page) : ($jshopConfig->count_products_to_page);?>" />
         </td>
       </tr>
       <tr>
         <td  class="key">
           <?php echo JText::_('JSHOP_COUNT_PRODUCTS_ROW')?>*
         </td>
         <td>
           <input type="text" class="inputbox form-control" id="products_row" name="products_row" value="<?php echo $count_product_row=($row->category_id) ? ($row->products_row) : ($jshopConfig->count_products_to_row);?>" />
         </td>
       </tr>
       <tr>
         <td  class="key">
           <?php echo JText::_('JSHOP_PARENT_CATEGORY')?>*
         </td>
         <td>
           <?php echo $lists['treecategories'];?>
         </td>
       </tr>
       <?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>       
     </table>
     </div>
     <div class="clr"></div>
   </div>   
   <div id="image" class="tab-pane">
     <?php if ($row->category_image){ ?>
     <div class="jshop_quote" id="foto_category">
        <div>
            <div><img src="<?php echo $jshopConfig->image_category_live_path . '/' . $row->category_image?>" /></div>
            <div class="link_delete_foto">
                <a class="btn btn-micro btn-danger" href="#" onclick="if (confirm('<?php print JText::_('JSHOP_DELETE_IMAGE')?>')) jshopAdmin.deleteFotoCategory('<?php echo $row->category_id?>');return false;">
                    <?php print JText::_('JSHOP_DELETE_IMAGE')?>
                </a>
            </div>
        </div>
     </div>
     <?php } ?>
     <div class="col100">
     <table class="admintable" >
       <tr>
         <td class="key">
           <?php echo JText::_('JSHOP_IMAGE_SELECT')?>
         </td>
         <td>
           <input type="file" name="category_image" />
         </td>
       </tr>
       <tr>
         <td class="key">
           <?php echo JText::_('JSHOP_IMAGE_THUMB_SIZE')?>
         </td>
         <td>
           <div>
           <input type="radio" name="size_im_category" id="size_1" checked="checked" onclick="jshopAdmin.setDefaultSize(<?php echo $jshopConfig->image_category_width; ?>,<?php echo $jshopConfig->image_category_height; ?>, 'category')" value="1" />
           <label for="size_1"><?php echo JText::_('JSHOP_IMAGE_SIZE_1')?></label>
           <div class="clear"></div>
           </div>
           <div>
           <input type="radio" name="size_im_category" id="size_3" onclick="jshopAdmin.setOriginalSize('category')" value="3"/>
           <label for="size_3"><?php echo JText::_('JSHOP_IMAGE_SIZE_3')?></label>
           <div class="clear"></div>
           </div>
           <div>
           <input type="radio" name="size_im_category" id="size_2" onclick="jshopAdmin.setManualSize('category')" value="2" />
           <label for="size_2"><?php echo JText::_('JSHOP_IMAGE_SIZE_2')?></label> <?php echo \JSHelperAdmin::tooltip(JText::_('JSHOP_IMAGE_SIZE_INFO') );?>
           <div class="clear"></div>
           </div>
         </td>
       </tr>
       <tr>
         <td class="key">
           <?php echo JText::_('JSHOP_IMAGE_WIDTH')?>
         </td>
         <td>
           <input type="text" class = "form-control" id="category_width_image" name="category_width_image" value="<?php echo $jshopConfig->image_category_width?>" disabled="disabled" />
         </td>
       </tr>
       <tr>
         <td class="key">
           <?php echo JText::_('JSHOP_IMAGE_HEIGHT')?>
         </td>
         <td>
           <input type="text" class = "form-control" id="category_height_image" name="category_height_image" value="<?php echo $jshopConfig->image_category_height?>" disabled="disabled" />           
         </td>
       </tr>
     </table>
     <?php $pkey = 'plugin_template_img_'.$lang->language; if (isset($this->$pkey)){ print $this->$pkey;}?>
     </div>
     <div class="clr"></div>
     <br/><br/>
     <div class="helpbox">
        <div class="head"><?php echo JText::_('JSHOP_ABOUT_UPLOAD_FILES')?></div>
        <div class="text">
        	<?php print JText::_('JSHOP_IMAGE_UPLOAD_EXT_INFO')?><br/>
            <?php print sprintf(JText::_('JSHOP_SIZE_FILES_INFO'), ini_get("upload_max_filesize"), ini_get("post_max_size"));?>
        </div>
    </div>
   </div>
   </div>
   <input type="hidden" name="task" value="" />
   <input type="hidden" name="category_id" value="<?php echo (int)$row->category_id?>" />
   <input type="hidden" name="old_image" value="<?php echo $row->category_image;?>" />
   <script type="text/javascript">
     Joomla.submitbutton=function(task){
        if (task == 'save' || task == 'apply'){
            if (!parseInt(jQuery('#products_page').val())){
               alert ('<?php echo JText::_('JSHOP_WRITE_PRODUCTS_PAGE')?>');
               return 0;
             } else if (!parseInt(jQuery('#products_row').val())){
               alert ('<?php echo JText::_('JSHOP_WRITE_PRODUCTS_ROW')?>');
               return 0;
            } else if (jshop.isEmpty(jQuery('#category_width_image').val()) && jshop.isEmpty(jQuery('#category_height_image').val())){
               alert ('<?php echo JText::_('JSHOP_WRITE_SIZE_BAD')?>');
               return 0;
            }
         }
         Joomla.submitform(task, document.getElementById('adminForm'));
     }
   </script>
<?php print $this->tmp_html_end?>
</form>
</div>