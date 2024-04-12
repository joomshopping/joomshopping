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
<div id="attribs-page" class="tab-pane">
<?php if ( (count($lists['all_independent_attributes'])+count($lists['all_attributes']))>0 ){?>
    <script type="text/javascript">
        jshopAdmin.lang_error_attribute="<?php print JText::_('JSHOP_ERROR_ADD_ATTRIBUTE')?>";
        jshopAdmin.lang_attribute_exist="<?php print JText::_('JSHOP_ATTRIBUTE_EXIST')?>";
        jshopAdmin.folder_image_attrib="<?php print $jshopConfig->image_attributes_live_path?>";
        jshopAdmin.use_basic_price="<?php print $jshopConfig->admin_show_product_basic_price?>";
        jshopAdmin.use_bay_price="<?php print $jshopConfig->admin_show_product_bay_price?>";
        jshopAdmin.use_stock="<?php print intval($jshopConfig->stock)?>";
		jshopAdmin.use_weight="<?php print (int)$jshopConfig->admin_show_weight?>";
        jshopAdmin.use_product_ean="<?php print (int)!$jshopConfig->disable_admin['product_ean']?>";
        jshopAdmin.use_manufacturer_code="<?php print (int)!$jshopConfig->disable_admin['manufacturer_code']?>";
        jshopAdmin.use_product_old_price="<?php print (int)!$jshopConfig->disable_admin['product_old_price']?>";
        jshopAdmin.attrib_images=new Object();
        <?php foreach($lists['attribs_values'] as $k=>$v){?>
        jshopAdmin.attrib_images[<?php print $v->value_id?>]="<?php print $v->image?>";
        <?php }?>
    </script>
<?php }?>
<?php if (count($lists['all_attributes'])){ ?>
    <script type="text/javascript">
        jshopAdmin.attrib_ids=new Array();
        jshopAdmin.attrib_exist=new Object();
        <?php $i=0; foreach($lists['all_attributes'] as $key=>$value){ ?>
            jshopAdmin.attrib_ids[<?php print $i++;?>]="<?php echo $value->attr_id ?>";
       <?php } ?>
       
       <?php
       $attr_tmp_row_num=0;
       if (count($lists['attribs'])){
           
           foreach($lists['attribs'] as $k=>$v){
               $attr_tmp_row_num++;
               print "jshopAdmin.attrib_exist[".$attr_tmp_row_num."]={};\n";
               foreach($lists['all_attributes'] as $key=>$value){
                    $tmp_field="attr_".$value->attr_id;
                    $tmp_val=$v->$tmp_field;
                    print "jshopAdmin.attrib_exist[".$attr_tmp_row_num."][".$value->attr_id."]='".$tmp_val."';\n";
               }
           
           }
       }
       print "jshopAdmin.attr_tmp_row_num=$attr_tmp_row_num;\n";
       ?>       
       </script>
       
       <table class="table table-striped" id="list_attr_value">
       <thead>
       <tr>
       <?php foreach($lists['all_attributes'] as $key=>$value){ ?>
            <th width="100"><?php echo $value->name?></th>
       <?php } ?>
            <th width="100"><?php print JText::_('JSHOP_PRICE')?></th>
            <?php print $this->dep_attr_td_header?>
			<?php if ($jshopConfig->stock){?>            
                <th width="100"><?php print JText::_('JSHOP_QUANTITY_PRODUCT')?></th>
            <?php }?>
            <?php if ($jshopConfig->disable_admin['product_ean'] == 0){?>
                <th width="100"><?php print JText::_('JSHOP_EAN_PRODUCT')?></th>
            <?php }?>
            <?php if ($jshopConfig->disable_admin['manufacturer_code'] == 0){?>
                <th width="100"><?php print JText::_('JSHOP_MANUFACTURER_CODE')?></th>
            <?php }?>
			<?php if ($jshopConfig->admin_show_weight){?>
                <th width="100"><?php print JText::_('JSHOP_PRODUCT_WEIGHT')?> (<?php print \JSHelper::sprintUnitWeight()?>)</th>
			<?php }?>
            <?php if ($jshopConfig->admin_show_product_basic_price){?>
                <th width="100"><?php print JText::_('JSHOP_WEIGHT_VOLUME_UNITS')?></th>
            <?php }?>
            <?php if ($jshopConfig->disable_admin['product_old_price'] == 0){?>
                <th width="100"><?php print JText::_('JSHOP_OLD_PRICE')?></th>
            <?php }?>
            <?php if ($jshopConfig->admin_show_product_bay_price){?>
                <th width="100"><?php print JText::_('JSHOP_PRODUCT_BUY_PRICE')?></th>
            <?php }?>
            <th></th>
            <th width="50" class="center"><input type='checkbox' id='ch_attr_delete_all' onclick="jshopAdmin.selectAllListAttr(this.checked)"></th>
       </tr>
       </thead>
       <?php       
       if (count($lists['attribs'])){
           $attr_tmp_row_num=0;
           foreach($lists['attribs'] as $k=>$v){
               $attr_tmp_row_num++;
               print "<tr id='attr_row_".$attr_tmp_row_num."'>";
               foreach($lists['all_attributes'] as $key=>$value){
                    $tmp_field="attr_".$value->attr_id;
                    $tmp_val=$v->$tmp_field;
                    $tmp_val_val = isset($lists['attribs_values'][$tmp_val]->name) ? $lists['attribs_values'][$tmp_val]->name : '';
                    $tmp_val_val=$lists['attribs_values'][$tmp_val]->name;
                    $image_="";
                    if (isset($lists['attribs_values'][$tmp_val]->image) && $lists['attribs_values'][$tmp_val]->image!=''){
                        $image_="<img src='".$jshopConfig->image_attributes_live_path."/".$lists['attribs_values'][$tmp_val]->image."' align='left' hspace='5' width='16' height='16' style='margin-right:5px;' class='img_attrib'>";
                    }
                    print "<td><input type='hidden' name='attrib_id[".$value->attr_id."][]' value='".$tmp_val."'>".$image_.$tmp_val_val."</td>";
               }
               print "<td><input type='text' name='attrib_price[]' class = 'form-control' value='".floatval($v->price)."'></td>";
               print isset($this->dep_attr_td_row[$k]) ? $this->dep_attr_td_row[$k] : "";
               if ($jshopConfig->stock){
                print "<td><input type='text' name='attr_count[]' class = 'form-control' value='".$v->count."'></td>";
               }
               if ($jshopConfig->disable_admin['product_ean'] == 0){
                print "<td><input type='text' name='attr_ean[]' class = 'form-control' value='".$v->ean."'></td>";
               }
               if ($jshopConfig->disable_admin['manufacturer_code'] == 0){
                print "<td><input type='text' name='attr_manufacturer_code[]' class = 'form-control' value='".$v->manufacturer_code."'></td>";
               }
			   if ($jshopConfig->admin_show_weight){
				  print "<td><input type='text' name='attr_weight[]' class = 'form-control' value='".$v->weight."'></td>";
			   }
               if ($jshopConfig->admin_show_product_basic_price){
                print "<td><input type='text' name='attr_weight_volume_units[]' class = 'form-control' value='".$v->weight_volume_units."'></td>";
               }
               if ($jshopConfig->disable_admin['product_old_price'] == 0){
                print "<td><input type='text' name='attrib_old_price[]' class = 'form-control' value='".$v->old_price."'></td>";
               }
               if ($jshopConfig->admin_show_product_bay_price){
                  print "<td><input type='text' name='attrib_buy_price[]' class = 'form-control' value='".floatval($v->buy_price)."'></td>";
               }
               print "<td>";
               if ($jshopConfig->use_extend_attribute_data){
                   print "<a class='btn btn-mini' target='_blank' href='index.php?option=com_jshopping&controller=products&task=edit&product_attr_id=".$v->product_attr_id."' onclick='jshopAdmin.editAttributeExtendParams(".$v->product_attr_id.");return false;'>".JText::_('JSHOP_ATTRIBUTE_EXTEND_PARAMS')."</a>";
               }
               print "</td>";
               print "<td class='center'><input type='hidden' name='product_attr_id[]' value='".$v->product_attr_id."'><input type='checkbox' class='ch_attr_delete' value='".$attr_tmp_row_num."'></td>";
               print "</tr>";
           }           
       }
       print "<tr id='attr_row_end'>";
       foreach($lists['all_attributes'] as $key=>$value){
           print "<td></td>";
       }
	   print "<td></td>";
	   print $this->dep_attr_td_row_empty;
       if ($jshopConfig->stock) print "<td></td>";
	   if ($jshopConfig->disable_admin['product_ean'] == 0) print "<td></td>";
       if ($jshopConfig->disable_admin['manufacturer_code'] == 0) print "<td></td>";
	   if ($jshopConfig->admin_show_weight) print "<td></td>";
       if ($jshopConfig->admin_show_product_basic_price) print "<td></td>";
       if ($jshopConfig->disable_admin['product_old_price'] == 0) print "<td></td>";
       if ($jshopConfig->admin_show_product_bay_price) print "<td></td>";              
       print "<td></td>";
       print "<td><input type='button' class='btn btn-danger' value='".JText::_('JSHOP_DELETE')."' onclick='jshopAdmin.deleteListAttr()'></td>";
       print "</tr>";
       ?>
       </table>
       <br/>
       <div class="col width-55">
        <fieldset class="adminform" style="margin-left:0px;">
        <legend><?php echo JText::_('JSHOP_ADD_ATTRIBUT')?></legend>
            <table class="admintable">
            <?php foreach($lists['all_attributes'] as $key=>$value){ ?>
                <tr>
                    <td class="key"><?php echo $value->name?></td>
                    <td><?php echo $value->values_select;?></td>
                </tr>    
            <?php } ?>
            <tr>
                <td class="key"><?php print JText::_('JSHOP_PRICE')?>*</td>
                <td><input type="text" class = "form-control middle2" id="attr_price" value="<?php echo $row->product_price?>"></td>
            </tr>
			<?php print $this->dep_attr_td_footer;?>
            <?php if ($jshopConfig->stock){?>
            <tr>
                <td class="key"><?php print JText::_('JSHOP_QUANTITY_PRODUCT')?>*</td>
                <td><input type="text" class = "form-control middle2" id="attr_count" value="1"></td> 
            </tr>
            <?php }?>
            <?php if ($jshopConfig->disable_admin['product_ean'] == 0){?>
            <tr>
                <td class="key"><?php print JText::_('JSHOP_EAN_PRODUCT')?></td>
                <td><input type="text" class = "form-control middle2" id="attr_ean" value="<?php echo $row->product_ean?>"></td>
            </tr>
            <?php }?>
            <?php if ($jshopConfig->disable_admin['manufacturer_code'] == 0){?>
            <tr>
                <td class="key"><?php print JText::_('JSHOP_MANUFACTURER_CODE')?></td>
                <td><input type="text" class = "form-control middle2" id="attr_manufacturer_code" value="<?php echo $row->manufacturer_code?>"></td>
            </tr>
            <?php }?>
			<?php if ($jshopConfig->admin_show_weight){?>
            <tr>
                <td class="key"><?php print JText::_('JSHOP_PRODUCT_WEIGHT')?></td>
                <td><input type="text" class = "form-control middle2" id="attr_weight" value="<?php echo $row->product_weight?>"> <?php print \JSHelper::sprintUnitWeight();?></td>
            </tr>
			<?php }?>
            <?php if ($jshopConfig->admin_show_product_basic_price){?>
            <tr>
                <td class="key"><?php print JText::_('JSHOP_WEIGHT_VOLUME_UNITS')?></td>
                <td><input type="text" class = "form-control middle2" id="attr_weight_volume_units" value="<?php echo $row->weight_volume_units?>"></td>
            </tr>
            <?php }?>
            <?php if ($jshopConfig->disable_admin['product_old_price'] == 0){?>
            <tr>
                <td class="key"><?php print JText::_('JSHOP_OLD_PRICE')?></td>
                <td><input type="text" class = "form-control middle2" id="attr_old_price" value="<?php echo $row->product_old_price?>"></td>
            </tr>
            <?php }?>
            <?php if ($jshopConfig->admin_show_product_bay_price){?>
            <tr>
                <td class="key"><?php print JText::_('JSHOP_PRODUCT_BUY_PRICE')?></td>
                <td><input type="text" class = "form-control middle2" id="attr_buy_price" value="<?php echo $row->product_buy_price?>"> </td>
            </tr>
            <?php }?>
            <tr>
                <td></td>
                <td>
                <div style="width:130px;text-align:right;">
                <?php print $lists['dep_attr_button_add']?>
                </div>
                </td>
            </tr>            
            </table>
        </fieldset>    
       </div>
       <div class="clr"></div>
       <br/>
   <?php
   }
   
   
   if (count($lists['all_independent_attributes'])){
   ?>
    <?php foreach($lists['all_independent_attributes'] as $ind_attr){?>
        
        <div style="padding-top:20px;">
        <table class="table table-striped" id="list_attr_value_ind_<?php print $ind_attr->attr_id?>">
        <thead>
        <tr>
            <th width="150"><?php print $ind_attr->name?></th>
            <th width="120"><?php print JText::_('JSHOP_PRICE_MODIFICATION')?></th>
            <th width="120"><?php print JText::_('JSHOP_PRICE')?></th>
			<?php print $this->ind_attr_td_header?>
            <th><?php print JText::_('JSHOP_DELETE')?></th>
        </tr>
        </thead>
        <?php 
        if (isset($lists['ind_attribs_gr'][$ind_attr->attr_id]) && is_array($lists['ind_attribs_gr'][$ind_attr->attr_id])){
        foreach($lists['ind_attribs_gr'][$ind_attr->attr_id] as $ind_attr_val){?>
        <tr id='attr_ind_row_<?php print $ind_attr_val->attr_id?>_<?php print $ind_attr_val->attr_value_id?>'>
            <td>
            <?php if ($lists['attribs_values'][$ind_attr_val->attr_value_id]->image!=''){?>
                <img src='<?php print $jshopConfig->image_attributes_live_path."/".$lists['attribs_values'][$ind_attr_val->attr_value_id]->image?>' align='left' hspace='5' width='16' height='16' style='margin-right:5px;' class='img_attrib'>
            <?php }?>
            <input type='hidden' id='attr_ind_<?php print $ind_attr_val->attr_id?>_<?php print $ind_attr_val->attr_value_id?>' name='attrib_ind_id[]' value='<?php print $ind_attr_val->attr_id?>'>
            <input type='hidden' name="attrib_ind_value_id[]" value='<?php print $ind_attr_val->attr_value_id?>'>
            <?php print $lists['attribs_values'][$ind_attr_val->attr_value_id]->name;?>
            </td>
            <td><input type='text' class='small3 form-control' name='attrib_ind_price_mod[]' value='<?php print $ind_attr_val->price_mod?>'></td>
            <td><input type='text' class='small3 form-control' name='attrib_ind_price[]' value='<?php print floatval($ind_attr_val->addprice)?>'></td>
            <?php if (isset($this->ind_attr_td_row[$ind_attr_val->attr_value_id])) print $this->ind_attr_td_row[$ind_attr_val->attr_value_id]?>
            <td><a class="btn btn-danger" href='#' onclick="jQuery('#attr_ind_row_<?php print $ind_attr_val->attr_id?>_<?php print $ind_attr_val->attr_value_id?>').remove();return false;"><i class="icon-delete"></i></a></td>
        </tr>
        <?php }
        }
        ?>
        </table>
        </div>
        
        <div style="padding-top:5px;" class="input-inline">
        <table cellpadding="4" class="table">
        <tr>
            <td width="150"><?php print $ind_attr->values_select;?></td>
            <td width="120"><?php print $ind_attr->price_modification_select;?></td>
            <td width="120"><input type="text" class='small3 form-control' id="attr_ind_price_tmp_<?php print $ind_attr->attr_id?>" value="0"></td>
            <?php if (isset($this->ind_attr_td_footer[$ind_attr->attr_id])) print $this->ind_attr_td_footer[$ind_attr->attr_id]?>
            <td><?php print $ind_attr->submit_button;?></td>
        </tr>
        </table>
        </div>
    <?php }?>
    
   <br/><br/>
   <?php
   }   
   ?>
   <?php $pkey='plugin_template_attribute'; if ($this->$pkey){ print $this->$pkey;}?>
   <a href="index.php?option=com_jshopping&controller=attributes" target="_blank"><img src="components/com_jshopping/images/jshop_attributes_s.png" border='0' align="left" style="margin-right:5px"><?php print JText::_('JSHOP_LIST_ATTRIBUTES')?></a>
</div>