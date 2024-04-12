<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined( '_JEXEC' ) or die();

?>
<script type="text/javascript">
    var jshopParams = jshopParams || {};
    <?php if ($this->product->product_quantity >0){?>
    jshopParams.translate_not_available = "<?php print addslashes(JText::_('JSHOP_PRODUCT_NOT_AVAILABLE_THIS_OPTION'))?>";
    <?php }else{?>
    jshopParams.translate_not_available = "<?php print addslashes(JText::_('JSHOP_PRODUCT_NOT_AVAILABLE'))?>";
    <?php }?>
    jshopParams.currency_code = "<?php print $this->config->currency_code;?>";
    jshopParams.format_currency = "<?php print $this->config->format_currency[$this->config->currency_format];?>";
    jshopParams.decimal_count = <?php print $this->config->decimal_count;?>;
    jshopParams.decimal_symbol = "<?php print $this->config->decimal_symbol;?>";
    jshopParams.thousand_separator = "<?php print $this->config->thousand_separator;?>";
    jshopParams.attr_value = new Object();
    jshopParams.attr_list = new Array();
    jshopParams.attr_img = new Object();
    <?php if (count($this->attributes)){?>
        <?php $i=0;foreach($this->attributes as $attribut){?>
        jshopParams.attr_value["<?php print $attribut->attr_id?>"] = "<?php print $attribut->firstval?>";
        jshopParams.attr_list[<?php print $i++;?>] = "<?php print $attribut->attr_id?>";
        <?php } ?>
    <?php } ?>
    <?php foreach($this->all_attr_values as $attrval){ if ($attrval->image){?>jshopParams.attr_img[<?php print $attrval->value_id?>] = "<?php print $attrval->image?>";<?php } }?>
    jshopParams.liveurl = '<?php print JURI::root()?>';
    jshopParams.liveattrpath = '<?php print $this->config->image_attributes_live_path;?>';
    jshopParams.liveproductimgpath = '<?php print $this->config->image_product_live_path;?>';
    jshopParams.liveimgpath = '<?php print $this->config->live_path."images";?>';
    jshopParams.urlupdateprice = '<?php print $this->urlupdateprice;?>';
    <?php if (isset($this->_tmp_product_ext_js)) print $this->_tmp_product_ext_js;?>
</script>

<div class="product_attribute_select" style="padding:20px;">
    <input type="hidden" id="quantity" value="1">
    <input type="hidden" id="product_id" value="<?php print $this->product->product_id?>">
    <input type="hidden" id="pricefloat" value="<?php print $this->product->getPriceCalculate();?>">    
    <div id="block_weight" style="display: none"><?php print $this->product->getWeight();?></div>
    <div id="manufacturer_code" style="display: none"><?php print $this->product->getManufacturerCode();?></div>
    
    <h1><?php print $this->product->name?></h1>
    
    <div class="jshop_code_prod" style="margin-bottom:20px;">
        <?php print JText::_('JSHOP_EAN')?>: <span id="product_code"><?php print $this->product->getEan();?></span>
    </div>
    
    <div class="prod_price" style="margin-bottom:20px;">
        <?php print JText::_('JSHOP_PRICE')?>: 
        <span id="block_price">
            <?php print \JSHelper::formatprice($this->product->getPriceCalculate())?>
        </span>
    </div>
    
    <div class="jshop_prod_attributes jshop">
        <?php foreach($this->attributes as $attribut) : ?>
            <div class = "row">
                <div class="col-2 attributes_title">
                    <span class="attributes_name" id="attr_name_id_<?php print $attribut->attr_id?>"><?php print $attribut->attr_name?>:</span>
                </div>
                <div class = "col-10">
                    <span id='block_attr_sel_<?php print $attribut->attr_id?>'>
                        <?php print $attribut->selects?>
                    </span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <input class="btn btn-primary" type="button" value="<?php print \JText::_('JTOOLBAR_APPLY')?>" onclick="jshopAdmin.loadProductAttributeInfoOrderItem(<?php print $this->num?>)">
    
</div>