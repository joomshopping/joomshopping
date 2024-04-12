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
<?php print $product->_tmp_var_start?>
<div class="product productitem_<?php print $product->product_id?>">
    
    <div class="name">
        <a href="<?php print $product->product_link?>">
            <?php print $product->name?>
        </a>
        <?php if ($this->config->product_list_show_product_code){?>
            <span class="jshop_code_prod">(<?php print JText::_('JSHOP_EAN')?>: <span><?php print $product->product_ean;?></span>)</span>
        <?php }?>
    </div>
    
    <div class = "image">
        <?php if ($product->image){?>
            <div class="image_block">
			    <?php print $product->_tmp_var_image_block;?>
                <?php if ($product->label_id){?>
                    <div class="product_label">
                        <?php if ($product->_label_image){?>
                            <img src="<?php print $product->_label_image?>" alt="<?php print htmlspecialchars($product->_label_name)?>" />
                        <?php }else{?>
                            <span class="label_name"><?php print $product->_label_name;?></span>
                        <?php }?>
                    </div>
                <?php }?>
                <a href="<?php print $product->product_link?>">
                    <img class="jshop_img" src="<?php print $product->image?>" alt="<?php print htmlspecialchars($product->name);?>" title="<?php print htmlspecialchars($product->name);?>"  />
                </a>
            </div>
        <?php }?>

        <?php if ($this->allow_review){?>
            <?php if (!$this->config->hide_product_rating){?>
                <div class="review_mark">
                    <?php print \JSHelper::showMarkStar($product->average_rating);?>
                </div>
            <?php }?>
            <div class="count_commentar">
                <?php print sprintf(JText::_('JSHOP_X_COMENTAR'), $product->reviews_count);?>
            </div>
        <?php }?>
        
        <?php print $product->_tmp_var_bottom_foto;?>
    </div>
    
    <div class="oiproduct">
	
		<?php if (!$this->config->hide_text_product_not_available){?>
			<?php if ($product->product_quantity <=0){?>
				<div class="not_available"><?php print JText::_('JSHOP_PRODUCT_NOT_AVAILABLE')?></div>
			<?php }elseif(!$this->config->hide_text_product_available){?>
				<div class="available"><?php print JText::_('JSHOP_PRODUCT_AVAILABLE')?></div>
			<?php }?>
		<?php }?>
        
        <?php if ($product->product_old_price > 0){?>
            <div class="old_price">
                <?php if ($this->config->product_list_show_price_description) print JText::_('JSHOP_OLD_PRICE').': ';?>
                <span><?php print \JSHelper::formatprice($product->product_old_price)?><?php print $product->_tmp_var_old_price_ext?></span>
            </div>
        <?php }?>
        
		<?php print $product->_tmp_var_bottom_old_price;?>
        
        <?php if ($product->product_price_default > 0 && $this->config->product_list_show_price_default){?>
            <div class="default_price">
                <?php print JText::_('JSHOP_DEFAULT_PRICE');?>:
                <span><?php print \JSHelper::formatprice($product->product_price_default)?></span>
            </div>
        <?php }?>
        
        <?php if ($product->_display_price){?>
            <div class = "jshop_price">
                <?php if ($this->config->product_list_show_price_description) print JText::_('JSHOP_PRICE').': ';?>
                <?php if ($product->show_price_from) print JText::_('JSHOP_FROM');?>
                <span><?php print \JSHelper::formatprice($product->product_price);?><?php print $product->_tmp_var_price_ext;?></span>
            </div>
        <?php }?>
        
        <?php print $product->_tmp_var_bottom_price;?>
        
        <div class="price_extra_info">
            <?php if ($this->config->show_tax_in_product && $product->tax > 0){?>
                <span class="taxinfo"><?php print \JSHelper::productTaxInfo($product->tax);?></span>
            <?php }?>
            
            <?php if ($this->config->show_plus_shipping_in_product){?>
                <span class="plusshippinginfo"><?php print sprintf(JText::_('JSHOP_PLUS_SHIPPING'), $this->shippinginfo);?></span>
            <?php }?>
        </div>
        
        <?php if ($product->basic_price_info['price_show']){?>
            <div class="base_price">
                <?php print JText::_('JSHOP_BASIC_PRICE')?>: 
                <?php if ($product->show_price_from && !$this->config->hide_from_basic_price) print JText::_('JSHOP_FROM')?> 
                <span><?php print \JSHelper::formatprice($product->basic_price_info['basic_price'])?> / <?php print $product->basic_price_info['name'];?></span>
            </div>
        <?php }?>
        
        <?php if ($product->manufacturer->name){?>
            <div class="manufacturer_name">
                <?php print JText::_('JSHOP_MANUFACTURER')?>: 
                <span><?php print $product->manufacturer->name?></span>
            </div>
        <?php }?>

        <?php if ($this->config->manufacturer_code_in_product_list && $product->manufacturer_code){?>
            <div class="manufacturer_code">
                <?php print JText::_('JSHOP_MANUFACTURER_CODE')?>:
                <span><?php print $product->manufacturer_code?></span>
            </div>
        <?php }?>
        
        <?php if ($this->config->product_list_show_weight && $product->product_weight > 0){?>
            <div class="productweight">
                <?php print JText::_('JSHOP_WEIGHT')?>: 
                <span><?php print \JSHelper::formatweight($product->product_weight)?></span>
            </div>
        <?php }?>
        
        <?php if ($product->delivery_time != ''){?>
            <div class="deliverytime">
                <?php print JText::_('JSHOP_DELIVERY_TIME')?>: 
                <span><?php print $product->delivery_time?></span>
            </div>
        <?php }?>
        
        <?php if (is_array($product->extra_field)){?>
            <div class="extra_fields">
                <?php foreach($product->extra_field as $extra_field){?>
                    <div>
                        <span class="label-name"><?php print $extra_field['name'];?>:</span>
                        <span class="data"><?php print $extra_field['value'];?></span>
                    </div>
                <?php }?>
            </div>            
        <?php }?>
        
        <?php if ($product->vendor){?>
            <div class="vendorinfo">
                <?php print JText::_('JSHOP_VENDOR')?>: 
                <a href="<?php print $product->vendor->products?>"><?php print $product->vendor->shop_name?></a>
            </div>
        <?php }?>
        
        <?php if ($this->config->product_list_show_qty_stock){?>
            <div class="qty_in_stock">
                <?php print JText::_('JSHOP_QTY_IN_STOCK')?>: 
                <span><?php print \JSHelper::sprintQtyInStock($product->qty_in_stock)?></span>
            </div>
        <?php }?>
        
        <div class="description">
            <?php print $product->short_description?>
        </div>
        
        <?php print $product->_tmp_var_top_buttons;?>
        
        <div class="buttons">
            <?php if ($product->buy_link){?>
                <a class="btn btn-success button_buy" href="<?php print $product->buy_link?>">
                    <?php print JText::_('JSHOP_BUY')?>
                </a>
            <?php }?>
            
            <a class="btn btn-primary button_detail" href="<?php print $product->product_link?>">
                <?php print JText::_('JSHOP_DETAIL')?>
            </a>
            
            <?php print $product->_tmp_var_buttons;?>
        </div>
        
        <?php print $product->_tmp_var_bottom_buttons;?>
        
    </div>
    
</div>
<?php print $product->_tmp_var_end?>