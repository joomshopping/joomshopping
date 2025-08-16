<?php
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Helper\Helper;

/**
* @version      5.6.2 15.04.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/ 
defined('_JEXEC') or die();
$product = $this->product;
include(__DIR__."/load.js.php");
?>
<div class="jshop productview" id="comjshop">
    <form class="productfull" name="product" method="post" action="<?php print $this->action?>" enctype="multipart/form-data" autocomplete="off">
    
        <h1><?php print $this->product->name?><?php if ($this->config->show_product_code){?> <span class="jshop_code_prod">(<?php print Text::_('JSHOP_EAN_PRODUCT')?>: <span id="product_code"><?php print $this->product->getEan();?></span>)</span><?php }?></h1>
        
        <?php print $this->_tmp_product_html_start;?>

        <?php if ($this->config->display_button_print) print Helper::printContent();?>
        <?php include(__DIR__."/ratingandhits.php");?>

        <div class="row jshop">
            <div class="col-lg-4">
				<div class="image_middle">
					<?php print $this->_tmp_product_html_before_image;?>
					
					<?php if ($product->label_id){?>
						<div class="product_label">
							<?php if ($product->_label_image){?>
								<img src="<?php print $product->_label_image?>" alt="<?php print htmlspecialchars($product->_label_name)?>" />
							<?php }else{?>
								<span class="label_name"><?php print $product->_label_name;?></span>
							<?php }?>
						</div>
					<?php }?>

					<?php include __DIR__ . '/block_video_middle.php';?>

					<span id='list_product_image_middle'>
                        <?php include __DIR__ . '/block_image_middle.php';?>
					</span>

					<?php print $this->_tmp_product_html_after_image;?>
				</div>

                <div class="image_thumb_list">
                    <?php print $this->_tmp_product_html_before_image_thumb;?>

                    <div id='list_product_image_thumb' class="row-fluid0">
	                    <?php include __DIR__ . '/block_image_thumb.php';?>
                    </div>

                    <?php print $this->_tmp_product_html_after_image_thumb;?>

                    <?php include __DIR__ . '/block_video_thumb.php';?>

                    <?php print $this->_tmp_product_html_after_video;?>
                </div>

                <?php if ($this->config->product_show_manufacturer_logo && $this->product->manufacturer_info->manufacturer_logo!=""){?>
                <div class="manufacturer_logo">
                    <a href="<?php print Helper::SEFLink('index.php?option=com_jshopping&controller=manufacturer&task=view&manufacturer_id='.$this->product->product_manufacturer_id, 1);?>">
                        <img src="<?php print $this->config->image_manufs_live_path."/".$this->product->manufacturer_info->manufacturer_logo?>" alt="<?php print htmlspecialchars($this->product->manufacturer_info->name);?>" title="<?php print htmlspecialchars($this->product->manufacturer_info->name);?>" border="0">
                    </a>
                </div>
                <?php }?>
            </div>
            
            <div class="col-lg-8 jshop_oiproduct">

                <?php if ($this->product->product_url!=""){?>
                    <div class="prod_url">
                        <a target="_blank" href="<?php print $this->product->product_url;?>"><?php print Text::_('JSHOP_READ_MORE')?></a>
                    </div>
                <?php }?>
                <?php if ($this->config->product_show_manufacturer && $this->product->manufacturer_info->name!=""){?>
                    <div class="manufacturer_name">
                        <?php print Text::_('JSHOP_MANUFACTURER')?>: <span><?php print $this->product->manufacturer_info->name?></span>
                    </div>
                <?php }?>

                <?php if ($this->config->manufacturer_code_in_product_detail && $this->product->getManufacturerCode()!=""){?>
                    <div class="manufacturer_code">
                        <?php print Text::_('JSHOP_MANUFACTURER_CODE')?>: <span id="manufacturer_code"><?php print $this->product->getManufacturerCode()?></span>
                    </div>
                <?php }?>
				<?php if ($this->config->real_ean_in_product_detail && $this->product->getRealEan()!=""){?>
                    <div class="real_ean">
                        <?php print Text::_('JSHOP_EAN')?>: <span id="real_ean"><?php print $this->product->getRealEan()?></span>
                    </div>
                <?php }?>

                <?php print $this->_tmp_product_html_before_atributes;?>

                <?php if (isset($this->attributes) && count($this->attributes)) : ?>
                    <div class="jshop_prod_attributes jshop">
                        <?php foreach($this->attributes as $attribut) : ?>
                            <?php if ($attribut->grshow){?>
                                <div>
                                    <span class="attributgr_name"><?php print $attribut->groupname?></span>
                                </div>
                            <?php }?>
                            <div class="row row-attr-<?php print $attribut->attr_id?>">
                                <div class="col-lg-2 attributes_title">
                                    <span class="attributes_name"><?php print $attribut->attr_name?>:</span>
                                    <span class="attributes_description"><?php print $attribut->attr_description;?></span>
                                </div>
                                <div class="col-lg-10">
                                    <span id='block_attr_sel_<?php print $attribut->attr_id?>'>
                                        <?php print $attribut->selects?>
                                    </span>
                                </div>
                            </div>
                            <?php print $attribut->tmp_product_html_after_row ?? ''; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php print $this->_tmp_product_html_after_atributes;?>

                <?php if (isset($this->product->freeattributes) && count($this->product->freeattributes)){?>
                    <div class="prod_free_attribs jshop">
                        <?php foreach($this->product->freeattributes as $freeattribut){?>
                            <div class="row row-free-attr-<?php print $freeattribut->id?>">
                                <div class="col-lg-2 name">
                                    <span class="freeattribut_name"><?php print $freeattribut->name;?></span>
                                    <?php if ($freeattribut->required){?><span>*</span><?php }?>
                                    <span class="freeattribut_description"><?php print $freeattribut->description;?></span>
                                </div>
                                <div class="col-lg-10 field">
                                    <?php print $freeattribut->input_field;?>
                                </div>
                            </div>
                            <?php print $freeattribut->tmp_product_html_after_row ?? ''; ?>
                        <?php }?>
                        <?php if ($this->product->freeattribrequire) {?>
                            <div class="requiredtext">* <?php print Text::_('JSHOP_REQUIRED')?></div>
                        <?php }?>
                    </div>
                <?php }?>

                <?php print $this->_tmp_product_html_after_freeatributes;?>

                <?php if ($this->product->product_is_add_price){?>
                    <div class="price_prod_qty_list_head"><?php print Text::_('JSHOP_PRICE_FOR_QTY')?></div>
                    <table class="price_prod_qty_list">
                        <?php foreach($this->product->product_add_prices as $k=>$add_price){?>
                            <tr>
                                <td class="qty_from" <?php if ($add_price->product_quantity_finish==0){?>colspan="3"<?php } ?>>
                                    <?php if ($add_price->product_quantity_finish==0) print Text::_('JSHOP_FROM')?>
                                    <?php print $add_price->product_quantity_start?>
                                    <?php print $this->product->product_add_price_unit?>
                                </td>

                                <?php if ($add_price->product_quantity_finish > 0){?>
                                    <td class="qty_line"> - </td>
                                <?php } ?>

                                <?php if ($add_price->product_quantity_finish > 0){?>
                                    <td class="qty_to">
                                        <?php print $add_price->product_quantity_finish?> <?php print $this->product->product_add_price_unit?>
                                    </td>
                                <?php } ?>

                                <td class="qty_price" id="pricelist_f_<?php print $add_price->product_quantity_start?>">
                                    <span class="price" id="pricelist_from_<?php print $add_price->product_quantity_start?>">
                                        <?php print Helper::formatprice($add_price->price)?><?php print $add_price->ext_price?>
                                    </span>
                                    <span class="per_piece">/ <?php print $this->product->product_add_price_unit?></span>
                                    <?php if ($this->product->product_basic_price_show){?>
                                        <span class="base">(<span class="price"><?php print Helper::formatprice($add_price->basic_price)?></span> / <span class="bp_name"><?php print $this->product->product_basic_price_unit_name;?></span>)</span>
                                    <?php }?>
                                </td>
                                <?php print $add_price->_tmp_var?>
                            </tr>
                        <?php }?>
                    </table>
                <?php }?>

                <div class="old_price" <?php if ($this->product->product_old_price == 0){?>style="display:none"<?php }?>>
                    <?php print Text::_('JSHOP_OLD_PRICE')?>:
                    <span class="old_price" id="old_price">
                        <?php print Helper::formatprice($this->product->product_old_price)?>
                        <?php print $this->product->_tmp_var_old_price_ext;?>
                    </span>
                </div>

                <?php if ($this->product->product_price_default > 0 && $this->config->product_list_show_price_default){?>
                    <div class="default_price"><?php print Text::_('JSHOP_DEFAULT_PRICE')?>: <span id="pricedefault"><?php print Helper::formatprice($this->product->product_price_default)?></span></div>
                <?php }?>

                <?php print $this->_tmp_product_html_before_price;?>

                <?php if ($this->product->_display_price){?>
                    <div class="prod_price">
                        <?php print Text::_('JSHOP_PRICE')?>:
                        <span id="block_price">
                            <?php print Helper::formatprice($this->product->getPriceCalculate())?>
                            <?php print $this->product->_tmp_var_price_ext;?>
                        </span>
                    </div>
                <?php }?>

                <?php print $this->product->_tmp_var_bottom_price;?>

                <?php if ($this->config->show_tax_in_product && $this->product->product_tax > 0){?>
                    <span class="taxinfo"><?php print Helper::productTaxInfo($this->product->product_tax);?></span>
                <?php }?>

                <?php if ($this->config->show_plus_shipping_in_product && $this->product->_display_price){?>
                    <span class="plusshippinginfo"><?php print sprintf(Text::_('JSHOP_PLUS_SHIPPING'), $this->shippinginfo);?></span>
                <?php }?>

                <?php if ($this->product->delivery_time != ''){?>
                    <div class="deliverytime" <?php if ($product->hide_delivery_time){?>style="display:none"<?php }?>><?php print Text::_('JSHOP_DELIVERY_TIME')?>: <?php print $this->product->delivery_time?></div>
                <?php }?>

                <?php if ($this->config->product_show_weight && $this->product->product_weight > 0){?>
                    <div class="productweight"><?php print Text::_('JSHOP_WEIGHT')?>: <span id="block_weight"><?php print Helper::formatweight($this->product->getWeight())?></span></div>
                <?php }?>

                <?php if ($this->product->product_basic_price_show){?>
                    <div class="prod_base_price"><?php print Text::_('JSHOP_BASIC_PRICE')?>: <span id="block_basic_price"><?php print Helper::formatprice($this->product->product_basic_price_calculate)?></span> / <?php print $this->product->product_basic_price_unit_name;?></div>
                <?php }?>

                <?php print $this->product->_tmp_var_bottom_allprices;?>

                <?php if (is_array($this->product->extra_field)){?>
                    <div class="extra_fields">
                    <?php foreach($this->product->extra_field as $extra_field){?>
                        <?php if ($extra_field['grshow']){?>
                            <div class='block_efg'>
                            <div class='extra_fields_group'><?php print $extra_field['groupname']?></div>
                        <?php }?>

                        <div class="extra_fields_el">
                            <span class="extra_fields_name"><?php print $extra_field['name'];?></span><?php if ($extra_field['description']){?>
                                <span class="extra_fields_description">
                                    <?php print $extra_field['description'];?>
                                </span><?php } ?>:
                            <span class="extra_fields_value">
                                <?php print $extra_field['value'];?>
                            </span>
                        </div>
                        <?php print $extra_field['tmp_product_html_after_row'] ?? ''; ?>
                        <?php if ($extra_field['grshowclose']){?>
                            </div>
                        <?php }?>
                    <?php }?>
                    </div>
                <?php }?>

                <?php print $this->_tmp_product_html_after_ef;?>

                <?php if ($this->product->vendor_info){?>
                    <div class="vendorinfo">
                        <?php print Text::_('JSHOP_VENDOR')?>: <?php print $this->product->vendor_info->shop_name?> (<?php print $this->product->vendor_info->l_name." ".$this->product->vendor_info->f_name;?>),
                        (
                        <?php if ($this->config->product_show_vendor_detail){?><a href="<?php print $this->product->vendor_info->urlinfo?>"><?php print Text::_('JSHOP_ABOUT_VENDOR')?></a>,<?php }?>
                        <a href="<?php print $this->product->vendor_info->urllistproducts?>"><?php print Text::_('JSHOP_VIEW_OTHER_VENDOR_PRODUCTS')?></a> )
                    </div>
                <?php }?>

                <?php if (!$this->config->hide_text_product_not_available){ ?>
                    <?php if ($this->product->getQty() <= 0) {?>
                        <div class="block_available not_available" id="not_available">
                            <?php print $this->available?>
                        </div>
                    <?php } else { ?>
                        <div class="block_available available" id="not_available">
                            <?php print Text::_('JSHOP_PRODUCT_AVAILABLE')?>
                        </div>
                    <?php } ?>
                <?php } ?>

                <?php if ($this->config->product_show_qty_stock){?>
                    <div class="qty_in_stock">
                        <?php print Text::_('JSHOP_QTY_IN_STOCK')?>:
                        <span id="product_qty"><?php print Helper::sprintQtyInStock($this->product->qty_in_stock);?></span>
                    </div>
                <?php }?>

                <?php print $this->_tmp_product_html_before_buttons;?>

                <?php if (!$this->hide_buy){?>
                    <div class="prod_buttons" style="<?php print $this->displaybuttons?>">

                        <div class="prod_qty">
                            <?php print Text::_('JSHOP_QUANTITY')?>:
                        </div>

                        <div class="prod_qty_input">
                            <input type="<?php print $this->prod_qty_input_type?>" name="quantity" id="quantity" oninput="jshop.reloadPrices();" class="inputbox" value="<?php print $this->default_count_product?>" min="0" aria-label="quantity product" ><?php print $this->_tmp_qty_unit;?>
                        </div>

                        <div class="buttons product-buttons">
                            <input type="submit" class="btn btn-success button btn-buy" value="<?php print Text::_('JSHOP_ADD_TO_CART')?>" onclick="jQuery('#to').val('cart');" >

                            <?php if ($this->enable_wishlist){?>
                                <input type="submit" class="btn button btn-wishlist btn-secondary" value="<?php print Text::_('JSHOP_ADD_TO_WISHLIST')?>" onclick="jQuery('#to').val('wishlist');" >
                            <?php }?>

                            <?php print $this->_tmp_product_html_buttons;?>
                        </div>

                        <div id="jshop_image_loading" style="display:none"></div>
                    </div>
                <?php }?>

                <?php print $this->_tmp_product_html_after_buttons;?>

                <input type="hidden" name="to" id='to' value="cart" />
                <input type="hidden" name="product_id" id="product_id" value="<?php print $this->product->product_id?>" />
                <input type="hidden" name="category_id" id="category_id" value="<?php print $this->category_id?>" />
            </div>
        </div>

        <div class="jshop_prod_description">
            <?php print $this->product->description; ?>
        </div>        
    </form>

    <?php print $this->_tmp_product_html_before_demofiles; ?>
    
    <div id="list_product_demofiles"><?php include(__DIR__."/demofiles.php");?></div>
    
    <?php if ($this->config->product_show_button_back){?>
        <div class="button_back">
            <input type="button" class="btn button btn-secondary" value="<?php print Text::_('JSHOP_BACK')?>" onclick="<?php print $this->product->button_back_js_click;?>" />
        </div>
    <?php }?>
    
    <?php
        print $this->_tmp_product_html_before_review;
        include(__DIR__."/review.php");
        
        print $this->_tmp_product_html_before_related;
        include(__DIR__."/related.php");
    ?>
    
    <?php print $this->_tmp_product_html_end;?>
</div>