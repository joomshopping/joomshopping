<?php
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Editor\Editor;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;

/**
* @version      5.6.0 13.03.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
$row=$this->manufacturer;
$edit=$this->edit;
$jshopConfig=JSFactory::getConfig();
?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=manufacturers" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<?php print $this->tmp_html_start?>
    <ul class="joomla-tabs nav nav-tabs">
        <?php $i=0; foreach($this->languages as $lang){ $i++;?>
        <li class="nav-item">
            <a href="#<?php print $lang->language.'-page'?>" class="nav-link <?php if ($i==1){?>active<?php }?>" data-toggle="tab">
                <?php echo Text::_('JSHOP_DESCRIPTION')?><?php if ($this->multilang){?> (<?php print $lang->lang?>)<img class="tab_image" src="components/com_jshopping/images/flags/<?php print $lang->lang?>.gif" /><?php }?>
            </a>
        </li>
        <?php }?>
        <li class="nav-item"><a href="#main-page" class="nav-link" data-toggle="tab"><?php echo Text::_('JSHOP_MAIN_PARAMETERS')?></a></li>
        <li class="nav-item"><a href="#image" class="nav-link" data-toggle="tab"><?php echo Text::_('JSHOP_IMAGE')?></a></li>
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
     <?php echo $this->{'plugin_template_top_description_'.$lang->language} ?? '';?>
       <tr>
         <td class="key">
           <?php echo Text::_('JSHOP_TITLE')?>*
         </td>
         <td>
           <input type="text" class="inputbox form-control wide" name="<?php print $name?>" value="<?php print $row->$name?>" />
         </td>
       </tr>
       <tr>
         <td class="key">
           <?php echo Text::_('JSHOP_ALIAS')?>
         </td>
         <td>
           <input type="text" class="inputbox form-control wide" name="<?php print $alias?>" value="<?php print $row->$alias?>" />
         </td>
       </tr>
       <tr>
         <td  class="key">
           <?php echo Text::_('JSHOP_SHORT_DESCRIPTION')?>
         </td>
         <td>
           <textarea name="<?php print $short_description;?>" class="wide form-control" rows="5"><?php echo $row->$short_description ?></textarea>
         </td>
       </tr>
       <tr>
         <td  class="key">
           <?php echo Text::_('JSHOP_DESCRIPTION')?>
         </td>
         <td>
           <?php
              $editor=Editor::getInstance(Factory::getConfig()->get('editor'));
              print $editor->display('description'.$lang->id, $row->$description , '100%', '350', '75', '20' ) ;              
           ?>
         </td>
       </tr>
       <tr>
         <td  class="key">
           <?php echo Text::_('JSHOP_META_TITLE')?>
         </td>
         <td>
           <input type="text" class="inputbox form-control w100" name="<?php print $meta_title?>" value="<?php print $row->$meta_title?>" />
         </td>
       </tr>
       <tr>
         <td  class="key">
           <?php echo Text::_('JSHOP_META_DESCRIPTION')?>
         </td>
         <td>
           <input type="text" class="inputbox form-control w100" name="<?php print $meta_description;?>" value="<?php print $row->$meta_description;?>" />
         </td>
       </tr>
       <tr>
         <td  class="key">
           <?php echo Text::_('JSHOP_META_KEYWORDS')?>
         </td>
         <td>
           <input type="text" class="inputbox form-control w100" name="<?php print $meta_keyword?>" value="<?php print $row->$meta_keyword;?>" />
         </td>
       </tr>
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
           <?php echo Text::_('JSHOP_PUBLISH')?>
         </td>
         <td>
           <input type="checkbox" class="inputbox" id="manufacturer_publish" name="manufacturer_publish" value="1" <?php if ($row->manufacturer_publish) echo 'checked="checked"'?>  />
         </td>
       </tr>
       <tr>
     <td class="key">
           <?php echo Text::_('JSHOP_URL')?>
         </td>
         <td>
           <input type="text" class="inputbox form-control" id="manufacturer_url" size="40" name="manufacturer_url" value="<?php echo $row->manufacturer_url;?>" />
         </td>
       </tr>
       <tr>
         <td  class="key">
           <?php echo Text::_('JSHOP_COUNT_PRODUCTS_PAGE')?>
         </td>
         <td>
           <input type="text" class="inputbox form-control" id="products_page" name="products_page" value="<?php echo $row->products_page > 0 ? $row->products_page : '';?>">
         </td>
       </tr>
       <tr>
         <td  class="key">
           <?php echo Text::_('JSHOP_COUNT_PRODUCTS_ROW')?>
         </td>
         <td>
           <input type="text" class="inputbox form-control" id="products_row" name="products_row" value="<?php echo $row->products_row > 0 ? $row->products_row : '';?>">
         </td>
       </tr>
       <?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
     </table>
     </div>
     <div class="clr"></div>
   </div>   
   <div id="image" class="tab-pane">
     <?php if ($row->manufacturer_logo){ ?>
     <div class="jshop_quote" id="image_manufacturer">
        <div>
            <div><img src="<?php print $jshopConfig->image_manufs_live_path . '/' . $row->manufacturer_logo?>" /></div>
            <div class="link_delete_foto">
                <a class="btn btn-sm btn-danger" href="#" onclick="if (confirm('<?php print Text::_('JSHOP_DELETE_IMAGE')?>')) jshopAdmin.deleteFotoManufacturer('<?php echo $row->manufacturer_id?>');return false;">
                    <?php print Text::_('JSHOP_DELETE_IMAGE')?>
                </a>
            </div>
        </div>
     </div>
     <?php } ?>
     
     <div class="col100">

     <table class="admintable" >
       <tr>
         <td class="key">
           <?php echo Text::_('JSHOP_IMAGE_SELECT')?>
         </td>
         <td>
           <input type="file" name="manufacturer_logo" />
         </td>
       </tr>
       <?php if ($jshopConfig->product_img_seo) {?>
       <tr>
         <td class="key">
           <?php echo Text::_('JSHOP_IMG_ALT')?>
         </td>
         <td>
          <input type="text" class="inputbox form-control" name="img_alt" value="<?php print $row->img_alt;?>" />
         </td>
       </tr>
       <tr>
         <td class="key">
           <?php echo Text::_('JSHOP_TITLE')?>
         </td>
         <td>
          <input type="text" class="inputbox form-control" name="img_title" value="<?php print $row->img_title;?>" />
         </td>
       </tr>
       <tr>
         <td class="key">
           <?php echo Text::_('JSHOP_IMG_NAME')?>
         </td>
         <td>
          <input type="text" class="inputbox form-control" name="image_name">
         </td>
       </tr>
       <?php } ?>
       <tr>
         <td class="key">
           <?php echo Text::_('JSHOP_IMAGE_THUMB_SIZE')?>
         </td>
         <td>
            <div>
           <input type="radio" name="size_im_category" id="size_1" checked="checked" onclick="jshopAdmin.setDefaultSize(<?php echo $jshopConfig->image_category_width; ?>,<?php echo $jshopConfig->image_category_height; ?>, 'category')" value="1" />
           <label for="size_1"><?php echo Text::_('JSHOP_IMAGE_SIZE_1')?></label>
           <div class="clear"></div>
           </div>
           <div>
           <input type="radio" name="size_im_category" value="3" id="size_3" onclick="jshopAdmin.setOriginalSize('category')" />
           <label for="size_3"><?php echo Text::_('JSHOP_IMAGE_SIZE_3')?></label>
           <div class="clear"></div>
           </div>
           <div>
           <input type="radio" name="size_im_category" id="size_2" onclick="jshopAdmin.setManualSize('category')" value="2" />
           <label for="size_2"><?php echo Text::_('JSHOP_IMAGE_SIZE_2')?></label> <?php echo HelperAdmin::tooltip(Text::_('JSHOP_IMAGE_SIZE_INFO') );?>
           <div class="clear"></div>
           </div>
         </td>
       </tr>
       <tr>
         <td class="key">
           <?php echo Text::_('JSHOP_IMAGE_WIDTH')?>
         </td>
         <td>
           <input type="text" class = "form-control" id="category_width_image" name="category_width_image" value="<?php echo $jshopConfig->image_category_width?>" disabled="disabled" />
         </td>
       </tr>
       <tr>
         <td class="key">
           <?php echo Text::_('JSHOP_IMAGE_HEIGHT')?>
         </td>
         <td>
           <input type="text" class = "form-control" id="category_height_image" name="category_height_image" value="<?php echo $jshopConfig->image_category_height?>" disabled="disabled" />           
         </td>
       </tr>
     </table>

     </div>
     <div class="clr"></div>
     <br/><br/>
     <div class="helpbox">
        <div class="head"><?php echo Text::_('JSHOP_ABOUT_UPLOAD_FILES')?></div>
        <div class="text">
        	<?php print Text::_('JSHOP_IMAGE_UPLOAD_EXT_INFO')?><br/>
            <?php print sprintf(Text::_('JSHOP_SIZE_FILES_INFO'), ini_get("upload_max_filesize"), ini_get("post_max_size"));?>
        </div>
    </div>
   </div>
   </div>
   <script type="text/javascript">   
     Joomla.submitbutton=function(task){        
        if (task == 'save' || task == 'apply'){            
            if (!jQuery('#category_width_image').val() && !jQuery('#category_height_image').val()){
               alert ('<?php echo Text::_('JSHOP_WRITE_SIZE_BAD')?>');
               return 0;
            }
         }
         Joomla.submitform(task, document.getElementById('adminForm'));
     }
   </script>

<input type="hidden" name="old_image" value="<?php echo $row->manufacturer_logo?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="edit" value="<?php echo $edit;?>" />
<input type="hidden" name="manufacturer_id" value="<?php echo (int)$row->manufacturer_id?>" />
<?php print $this->tmp_html_end?>
</form>
</div>