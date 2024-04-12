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
<div id="main-page" class="tab-pane">
    <div class="col100">
    <table class="admintable" width="90%">
    <tr>
       <td class="key" style="width:180px;">
         <?php echo JText::_('JSHOP_PUBLISH')?>
       </td>
       <td>
         <input type="checkbox" name="product_publish" id="product_publish" value="1" <?php if ($row->product_publish) echo 'checked="checked"'?> />
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
       <td class="key">
         <?php echo JText::_('JSHOP_PRODUCT_PRICE')?>*
       </td>
       <td>
         <input type="text" name="product_price" class = "form-control" id="product_price" value="<?php echo $row->product_price?>" <?php if (!$this->withouttax){?> onkeyup="jshopAdmin.updatePrice2(<?php print $jshopConfig->display_price_admin;?>)" <?php }?> /> <?php echo $this->lists['currency'];?>
       </td>
    </tr>
    <?php if (!$this->withouttax){?>
    <tr>
       <td class="key">
         <?php if ($jshopConfig->display_price_admin==0) echo JText::_('JSHOP_PRODUCT_NETTO_PRICE'); else echo JText::_('JSHOP_PRODUCT_BRUTTO_PRICE');?>
       </td>
       <td>
         <input type="text" class = "form-control" id="product_price2" value="<?php echo $row->product_price2;?>" onkeyup="jshopAdmin.updatePrice(<?php print $jshopConfig->display_price_admin;?>)" />
       </td>
    </tr>
    <?php }?>
    <?php $pkey='plugin_template_info_price'; if (isset($this->$pkey)){ print $this->$pkey;}?>
    <?php if ($jshopConfig->disable_admin['product_price_per_consignment'] == 0){?>
    <tr>
       <td class="key">
         <?php echo JText::_('JSHOP_PRODUCT_ADD_PRICE')?>
       </td>
       <td>
         <input type="checkbox" name="product_is_add_price"  id="product_is_add_price" value="1" <?php if ($row->product_is_add_price) echo 'checked="checked"';?>  onclick="jshopAdmin.showHideAddPrice()" />
       </td>
    </tr>
    <tr id="tr_add_price">
        <td class="key"><?php echo JText::_('JSHOP_PRODUCT_ADD_PRICE')?></td>
         <td>
            <table style="margin-bottom:0" id="table_add_price" class="table table-striped">
            <thead>
                <tr>
                    <th>
                        <?php echo JText::_('JSHOP_PRODUCT_QUANTITY_START')?>    
                    </th>
                    <th>
                        <?php echo JText::_('JSHOP_PRODUCT_QUANTITY_FINISH')?>    
                    </th>
                    <th>
                        <?php echo JText::_('JSHOP_DISCOUNT')?>
                        <?php if ($jshopConfig->product_price_qty_discount==2){?>
                            (%)
                        <?php }?>
                    </th>
                    <th>
                        <?php echo JText::_('JSHOP_PRODUCT_PRICE')?>
                    </th>                    
                    <th>
                        <?php echo JText::_('JSHOP_DELETE')?>    
                    </th>
                </tr>
                </thead>  
                <tbody>              
                <?php 
                $add_prices=$row->product_add_prices;
                $count=count($add_prices);
                for ($i=0; $i < $count; $i++){
                    if ($jshopConfig->product_price_qty_discount==1){
                        $_add_price=$row->product_price - $add_prices[$i]->discount;
                    }else{
                        $_add_price=$row->product_price - ($row->product_price * $add_prices[$i]->discount / 100);
                    }
                    $_add_price = \JSHelper::formatEPrice($_add_price);
                    ?>
                    <tr id="add_price_<?php print $i?>">
                        <td>
                            <input type="text" class="form-control small3" name="quantity_start[]" id="quantity_start_<?php print $i?>" value="<?php echo $add_prices[$i]->product_quantity_start;?>" />    
                        </td>
                        <td>
                            <input type="text" class="form-control small3" name="quantity_finish[]" id="quantity_finish_<?php print $i?>" value="<?php echo $add_prices[$i]->product_quantity_finish;?>" />    
                        </td>
                        <td>
                            <input type="text" class="form-control small3" name="product_add_discount[]" id="product_add_discount_<?php print $i?>" value="<?php echo $add_prices[$i]->discount;?>" onkeyup="jshopAdmin.productAddPriceupdateValue(<?php print $i?>)" />
                        </td>
                        <td>
                            <input type="text" class="form-control small3" id="product_add_price_<?php print $i?>" value="<?php echo $_add_price;?>" onkeyup="jshopAdmin.productAddPriceupdateDiscount(<?php print $i?>)" />
                        </td>
                        <td align="center">
                            <a class="btn btn-danger" href="#" onclick="jshopAdmin.delete_add_price(<?php print $i?>);return false;">
                                <i class="icon-delete"></i>
                            </a>
                        </td>
                    </tr>
                    <?php
                }
                ?>     
              </tbody>           
            </table>
            <table class="table table-striped">
            <tr>
                <td><?php echo $lists['add_price_units'];?> - <?php echo JText::_('JSHOP_UNIT_MEASURE')?></td>
                <td align="right" width="100">
                    <input class="btn button btn-primary" type="button" name="add_new_price" onclick="jshopAdmin.addNewPrice()" value="<?php echo JText::_('JSHOP_PRODUCT_ADD_PRICE_ADD')?>" />
                </td>
            </tr>
            </table>
            <script type="text/javascript">
            <?php 
            print "jshopAdmin.add_price_num=$i;";
            print "jshopAdmin.config_product_price_qty_discount=".$jshopConfig->product_price_qty_discount.";";
            ?>             
            </script>
        </td>
    </tr>
    <?php }?>
    <?php if ($jshopConfig->disable_admin['product_old_price'] == 0){?>
    <tr>
       <td class="key">
         <?php echo JText::_('JSHOP_OLD_PRICE')?>
       </td>
       <td>
         <input type="text" name="product_old_price" class = "form-control" id="product_old_price" value="<?php echo $row->product_old_price?>" />
       </td>
    </tr>
    <?php }?>
    <?php if ($jshopConfig->admin_show_product_bay_price) { ?>
    <tr>
       <td class="key">
         <?php echo JText::_('JSHOP_PRODUCT_BUY_PRICE')?>
       </td>
       <td>
         <input type="text" name="product_buy_price" class = "form-control" id="product_buy_price" value="<?php echo $row->product_buy_price?>" />
       </td>
    </tr>
    <?php } ?>
    <?php if ($jshopConfig->admin_show_weight){?>
    <tr>
       <td class="key">
         <?php echo JText::_('JSHOP_PRODUCT_WEIGHT')?>
       </td>
       <td>
         <input type="text" name="product_weight" class = "form-control" id="product_weight" value="<?php echo $row->product_weight?>" /> <?php print \JSHelper::sprintUnitWeight();?>
       </td>
    </tr>
	<?php }?>
    <?php if ($jshopConfig->disable_admin['product_ean'] == 0){?>
    <tr>
       <td class="key">
         <?php echo JText::_('JSHOP_EAN_PRODUCT')?>
       </td>
       <td>
         <input type="text" name="product_ean" class = "form-control" id="product_ean" value="<?php echo $row->product_ean?>" onkeyup="jshopAdmin.updateEanForAttrib()"; />
       </td>
    </tr>
    <?php }?>
    <?php if ($jshopConfig->disable_admin['manufacturer_code'] == 0){?>
    <tr>
       <td class="key">
         <?php echo JText::_('JSHOP_MANUFACTURER_CODE')?>
       </td>
       <td>
         <input type="text" name="manufacturer_code" class = "form-control" value="<?php echo $row->manufacturer_code?>" />
       </td>
    </tr>
    <?php }?>
    <?php if ($jshopConfig->stock){?>
    <tr>
       <td class="key">
         <?php echo JText::_('JSHOP_QUANTITY_PRODUCT')?>*
       </td>
       <td>
         <div id="block_enter_prod_qty" style="padding-bottom:2px;<?php if ($row->unlimited) print "display:none;";?>">
             <input type="text" name="product_quantity" class = "form-control" id="product_quantity" value="<?php echo $row->product_quantity?>" <?php if ($this->product_with_attribute){?><?php }?> />
             <?php if ($this->product_with_attribute){ echo \JSHelperAdmin::tooltip(JText::_('JSHOP_INFO_PLEASE_EDIT_AMOUNT_FOR_ATTRIBUTE')); } ?>
         </div>
         <div>         
            <input type="checkbox" name="unlimited"  value="1" onclick="jshopAdmin.ShowHideEnterProdQty(this.checked)" <?php if ($row->unlimited) print "checked";?> /> <?php print JText::_('JSHOP_UNLIMITED')?>
         </div>         
       </td>
    </tr>
    <?php }?>
    <?php if ($jshopConfig->disable_admin['product_url'] == 0){?>
    <tr>
       <td class="key"><?php echo JText::_('JSHOP_URL')?></td>
       <td>
         <input type="text" name="product_url" class = "form-control" id="product_url" value="<?php echo $row->product_url?>" size="80" />
       </td>
    </tr>
    <?php }?>
    <?php if ($jshopConfig->use_different_templates_cat_prod) { ?>
    <tr>
       <td class="key">
         <?php echo JText::_('JSHOP_TEMPLATE_PRODUCT')?>
       </td>
       <td>
         <?php echo $lists['templates'];?>
       </td>
    </tr>
    <?php } ?>
     
    <?php if (!$this->withouttax){?>
    <tr>     
       <td class="key">
         <?php echo JText::_('JSHOP_TAX')?>*
       </td>
       <td>
         <?php echo $lists['tax'];?>
       </td>
    </tr>
    <?php }?>
    <?php if ($jshopConfig->disable_admin['product_manufacturer'] == 0){?>
    <tr>
       <td class="key">
         <?php echo JText::_('JSHOP_NAME_MANUFACTURER')?>
       </td>
       <td>
         <?php echo $lists['manufacturers'];?>
       </td>
    </tr>
    <?php }?>
    <tr>
       <td class="key">
         <?php echo JText::_('JSHOP_CATEGORIES')?>*
       </td>
       <td>
         <?php echo $lists['categories'];?>
       </td>
    </tr>
    <?php if ($jshopConfig->admin_show_vendors && $this->display_vendor_select) { ?>
    <tr>
       <td class="key">
         <?php echo JText::_('JSHOP_VENDOR')?>
       </td>
       <td>
         <?php echo $lists['vendors'];?>
       </td>
    </tr>
    <?php }?>
     
    <?php if ($jshopConfig->admin_show_delivery_time) { ?>
    <tr>
       <td class="key">
         <?php echo JText::_('JSHOP_DELIVERY_TIME')?>
       </td>
       <td>
         <?php echo $lists['deliverytimes'];?>
       </td>
    </tr>
    <?php }?>
     
    <?php if ($jshopConfig->admin_show_product_labels) { ?>
    <tr>
       <td class="key">
         <?php echo JText::_('JSHOP_LABEL')?>
       </td>
       <td>
         <?php echo $lists['labels'];?>
       </td>
    </tr>
    <?php }?>
     
    <?php if ($jshopConfig->admin_show_product_basic_price) { ?>
    <tr>
       <td class="key"><br/><?php echo JText::_('JSHOP_BASIC_PRICE')?></td>
    </tr>
    <tr>
       <td class="key">
         <?php echo JText::_('JSHOP_WEIGHT_VOLUME_UNITS')?>
       </td>
       <td>
         <input type="text" name="weight_volume_units" class = "form-control" value="<?php echo $row->weight_volume_units?>" />
       </td>
    </tr>
    <tr>
       <td class="key">
         <?php echo JText::_('JSHOP_UNIT_MEASURE')?>
       </td>
       <td>
         <?php echo $lists['basic_price_units'];?>
       </td>
    </tr>
    <?php }?>
    <?php if ($jshopConfig->return_policy_for_product){?>
    <tr>
       <td class="key">
         <?php echo JText::_('JSHOP_RETURN_POLICY_FOR_PRODUCT')?>
       </td>
       <td>
         <?php echo $lists['return_policy'];?>
       </td>
    </tr>
    <?php if (!$jshopConfig->no_return_all){?>  
    <tr>
       <td class="key">
         <?php echo JText::_('JSHOP_NO_RETURN')?>
       </td>
       <td>
         <input type="hidden" name="options[no_return]"  value="0" />
         <input type="checkbox" name="options[no_return]" value="1" <?php if ($row->product_options['no_return']) echo 'checked = "checked"';?> />
       </td>
    </tr>
    <?php }?>
    <?php }?>
    <?php $pkey='plugin_template_info'; if ($this->$pkey){ print $this->$pkey;}?>
   </table>
   </div>
   <div class="clr"></div>
</div>