<?php
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\CMS\Uri\Uri;

/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
$order=$this->order;
?>
<div class="jshop myorderinfo">
    
    <?php print $this->_tmp_html_start;?>
        
    <?php if ($this->config->order_send_pdf_client && $order->pdf_file && file_exists($this->config->pdf_orders_path."/".$order->pdf_file)) : ?>
        <div class="downlod_order_invoice">
            <a class="btn btn-secondary" target="_blank" href="<?php print $this->config->pdf_orders_live_path."/".$order->pdf_file;?>">
                <?php print Text::_('JSHOP_DOWNLOAD_INVOICE')?>
            </a>
        </div>
    <?php endif; ?>
    
    <div class="order_number">
        <div class="span12">
            <b><?php print Text::_('JSHOP_ORDER_NUMBER')?>:</b> 
            <span><?php print $order->order_number ?></span>
        </div>
    </div>
	<?php print $order->_tmp_ext_order_number;?>
	
    <div class="order_status">
        <div class="span12">
            <b><?php print Text::_('JSHOP_ORDER_STATUS')?>:</b>
            <span><?php print $order->status_name ?></span>
        </div>
    </div>
	<?php print $order->_tmp_ext_status_name;?>
	
    <div class="order_date">
        <div class="span12">
            <b><?php print Text::_('JSHOP_ORDER_DATE')?>:</b>
            <span><?php print Helper::formatdate($order->order_date, 0) ?></span>
        </div>
    </div>
    <div class="order_total">
        <div class="span12">
            <b><?php print Text::_('JSHOP_PRICE_TOTAL')?>:</b>
            <span><?php print Helper::formatprice($order->order_total, $order->currency_code); ?></span>
        </div>
    </div>   
    
    <div class="row userinfo">
        <div class="col-lg-6 userbillinfo">
            <table class="jshop">
                <tr>
                    <td colspan=2><b><?php print Text::_('JSHOP_EMAIL_BILL_TO')?></b></td>
                </tr>
                <?php if ($this->config_fields['firma_name']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_FIRMA_NAME')?>:</td>
                    <td><?php print $this->order->firma_name?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['f_name']['display']){?>
                <tr>
                    <td width="40%"><?php print Text::_('JSHOP_FULL_NAME')?>:</td>
                    <td width="60%"><?php print $this->order->getFullName()?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['client_type']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_CLIENT_TYPE')?>:</td>
                    <td><?php print $this->order->client_type_name;?></td>
                </tr>
                <?php } ?>        
                <?php if ($this->config_fields['firma_code']['display'] && ($this->order->client_type==2 || !$this->config_fields['client_type']['display'])){?>
                <tr>
                    <td><?php print Text::_('JSHOP_FIRMA_CODE')?>:</td>
                    <td><?php print $this->order->firma_code?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['tax_number']['display'] && ($this->order->client_type==2 || !$this->config_fields['client_type']['display'])){?>
                <tr>
                    <td><?php print Text::_('JSHOP_VAT_NUMBER')?>:</td>
                    <td><?php print $this->order->tax_number?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['birthday']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_BIRTHDAY')?>:</td>
                    <td><?php print $this->order->birthday?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['home']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_HOME')?>:</td>
                    <td><?php print $this->order->home?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['apartment']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_APARTMENT')?>:</td>
                    <td><?php print $this->order->apartment?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['street']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_STREET_NR')?>:</td>
                    <td><?php print $this->order->street?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['city']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_CITY')?>:</td>
                    <td><?php print $this->order->city?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['state']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_STATE')?>:</td>
                    <td><?php print $this->order->state?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['zip']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_ZIP')?>:</td>
                    <td><?php print $this->order->zip?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['country']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_COUNTRY')?>:</td>
                    <td><?php print $this->order->country?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['phone']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_TELEFON')?>:</td>
                    <td><?php print $this->order->phone?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['mobil_phone']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_MOBIL_PHONE')?>:</td>
                    <td><?php print $this->order->mobil_phone?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['fax']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_FAX')?>:</td>
                    <td><?php print $this->order->fax?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['email']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_EMAIL')?>:</td>
                    <td><?php print $this->order->email?></td>
                </tr>
                <?php } ?>

                <?php if ($this->config_fields['ext_field_1']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_EXT_FIELD_1')?>:</td>
                    <td><?php print $this->order->ext_field_1?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['ext_field_2']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_EXT_FIELD_2')?>:</td>
                    <td><?php print $this->order->ext_field_2?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['ext_field_3']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_EXT_FIELD_3')?>:</td>
                    <td><?php print $this->order->ext_field_3?></td>
                </tr>
                <?php } ?>
				<?php echo $this->tmp_fields?>
            </table>
        </div>
        <div class="col-lg-6 userdeliveryinfo">
        <?php if ($this->count_filed_delivery >0) {?>
            <table class="jshop userdeliveryinfo">
                <tr>
                    <td colspan="2"><b><?php print Text::_('JSHOP_EMAIL_SHIP_TO')?></b></td>
                </tr>
                <?php if ($this->config_fields['d_firma_name']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_FIRMA_NAME')?>:</td>
                    <td><?php print $this->order->d_firma_name?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['d_f_name']['display']){?>
                <tr>
                    <td width="40%"><?php print Text::_('JSHOP_FULL_NAME')?> </td>
                    <td width="60%"><?php print $this->order->getFullName('d_')?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['d_birthday']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_BIRTHDAY')?>:</td>
                    <td><?php print $this->order->d_birthday?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['d_home']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_HOME')?>:</td>
                    <td><?php print $this->order->d_home?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['d_apartment']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_APARTMENT')?>:</td>
                    <td><?php print $this->order->d_apartment?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['d_street']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_STREET_NR')?>:</td>
                    <td><?php print $this->order->d_street?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['d_city']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_CITY')?>:</td>
                    <td><?php print $this->order->d_city?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['d_state']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_STATE')?>:</td>
                    <td><?php print $this->order->d_state?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['d_zip']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_ZIP')?>:</td>
                    <td><?php print $this->order->d_zip ?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['d_country']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_COUNTRY')?>:</td>
                    <td><?php print $this->order->d_country ?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['d_phone']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_TELEFON')?>:</td>
                    <td><?php print $this->order->d_phone ?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['d_mobil_phone']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_MOBIL_PHONE')?>:</td>
                    <td><?php print $this->order->d_mobil_phone?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['d_fax']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_FAX')?>:</td>
                    <td><?php print $this->order->d_fax ?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['d_email']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_EMAIL')?>:</td>
                    <td><?php print $this->order->d_email ?></td>
                </tr>
                <?php } ?>                            
                <?php if ($this->config_fields['d_ext_field_1']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_EXT_FIELD_1')?>:</td>
                    <td><?php print $this->order->d_ext_field_1?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['d_ext_field_2']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_EXT_FIELD_2')?>:</td>
                    <td><?php print $this->order->d_ext_field_2?></td>
                </tr>
                <?php } ?>
                <?php if ($this->config_fields['d_ext_field_3']['display']){?>
                <tr>
                    <td><?php print Text::_('JSHOP_EXT_FIELD_3')?>:</td>
                    <td><?php print $this->order->d_ext_field_3?></td>
                </tr>
                <?php } ?>
				<?php echo $this->tmp_d_fields?>
            </table>
        <?php } ?>  
        </div>
		<?php print $this->_tmp_html_after_customer_info; ?>
    </div>
    
    <div class="product_head">
        <strong><?php echo Text::_('JSHOP_PRODUCTS')?></strong>
    </div>
    
    <div class="order_items">
        <table class="jshop cart">
        <tr>
            <th class="product_name">
                <?php print Text::_('JSHOP_ITEM')?>
            </th>
            <?php if ($this->config->show_product_code_in_order){?>
                <th class="product_code">
                    <?php print Text::_('JSHOP_EAN_PRODUCT')?>
                </th>
            <?php }?>
            <th class="single_price">
                <?php print Text::_('JSHOP_SINGLEPRICE')?>
            </th>
            <th class="quantity">
                <?php print Text::_('JSHOP_NUMBER')?>
            </th>
            <th class="total_price">
                <?php print Text::_('JSHOP_PRICE_TOTAL')?>
            </th>
            <?php print $this->_tmpl_html_order_items_end?>
        </tr>
        
        <?php
        $i=1; $countprod=count($order->items);
        foreach($order->items as $key_id=>$prod){
            $files=unserialize($prod->files);
            ?>            
            <tr class="jshop_prod_cart <?php if ($i % 2 == 0) print "even"; else print "odd"?>">
                <td class="product_name">
                    <div class="data">
                        <div class="name prodname">
                            <a href="<?php print Helper::SEFLink('index.php?option=com_jshopping&controller=product&task=view&category_id='.$prod->category_id.'&product_id='.$prod->product_id, 1)?>">
								<?php print $prod->product_name?>
							</a>
                        </div>
                        <?php if ($prod->manufacturer!=''){?>
                            <div class="manufacturer">
                                <?php print Text::_('JSHOP_MANUFACTURER')?>: 
                                <span><?php print $prod->manufacturer?></span>
                            </div>
                        <?php }?>
                        <?php if ($this->config->manufacturer_code_in_cart && $prod->manufacturer_code){?>
                            <div class="manufacturer_code"><?php print Text::_('JSHOP_MANUFACTURER_CODE')?>: <span><?php print $prod->manufacturer_code?></span></div>
                        <?php }?>
                        <?php if ($this->config->real_ean_in_cart && $prod->real_ean){?>
                            <div class="real_ean"><?php print Text::_('JSHOP_EAN')?>: <span><?php print $prod->real_ean?></span></div>
                        <?php }?>
                        <div class="attribs">
                            <?php print Helper::sprintAtributeInOrder($prod->product_attributes).Helper::sprintFreeAtributeInOrder($prod->product_freeattributes);?>
                            <div class="extra_fields"><?php print Helper::sprintExtraFiledsInOrder($prod->extra_fields)?></div>
                            <?php print $prod->_ext_attribute_html;?>
                        </div>
                        <?php if (count($files)){?>
                            <div class="filelist">
                                <?php foreach($files as $file){?>
                                    <div class="file">
                                        <span class="descr">
                                            <?php print $file->file_descr?>
                                        </span>
                                        <a class="download" href="<?php print Uri::root()?>index.php?option=com_jshopping&controller=product&task=getfile&oid=<?php print $this->order->order_id?>&id=<?php print $file->id?>&hash=<?php print $this->order->file_hash;?>">
                                            <?php print Text::_('JSHOP_DOWNLOAD')?>
                                        </a>
                                    </div>
                                <?php }?>
                            </div>
                         <?php }?>        
                        <?php print $prod->_ext_file_html;?>
        
                    </div>
                </td>
                <?php if ($this->config->show_product_code_in_order){?>
                    <td class="product_code">
                        <div class="data">
                            <div class="mobile-cart">
                                <?php print Text::_('JSHOP_EAN_PRODUCT')?>:
                            </div>
                            <?php print $prod->product_ean?>
                        </div>
                    </td>
                <?php } ?>
                <td class="single_price">                    
                    <div class="data">
                        <span class="price">
							<?php print Helper::formatprice($prod->product_item_price, $order->currency_code) ?>
						</span>
                        <?php print $prod->_ext_price_html?>
                        
                        <?php if ($this->config->show_tax_product_in_cart && $prod->product_tax>0){?>
                            <span class="taxinfo"><?php print Helper::productTaxInfo($prod->product_tax, $order->display_price);?></span>
                        <?php }?>
                        
                        <?php if ($this->config->cart_basic_price_show && $prod->basicprice>0){?>
                            <div class="basic_price">
                                <?php print Text::_('JSHOP_BASIC_PRICE')?>: 
                                <span><?php print Helper::sprintBasicPrice($prod);?></span>
                            </div>
                        <?php }?>
                        
                    </div>
                </td>
                <td class="quantity">
                    <div class="data">
                        <span class="mobile-cart-inline">
                            <?php print Text::_('JSHOP_NUMBER')?>:
                        </span>
                        <?php print Helper::formatqty($prod->product_quantity);?><?php print $prod->_qty_unit;?>
                    </div>
                </td>
                <td class="total_price">
                    <div class="data">
                        <?php print Helper::formatprice($prod->product_item_price * $prod->product_quantity, $order->currency_code); ?>
                        <?php print $prod->_ext_price_total_html?>
                        <?php if ($this->config->show_tax_product_in_cart && $prod->product_tax>0){?>
                            <span class="taxinfo"><?php print Helper::productTaxInfo($prod->product_tax, $order->display_price);?></span>
                        <?php }?>
                    </div>
                </td>
                <?php print $prod->_tmpl_html_order_items_end?>
            </tr>
        <?php $i++; } ?>
        </table>
    </div>
    
    <?php if ($this->config->show_weight_order){?>  
        <div class="weightorder">
            <?php print Text::_('JSHOP_WEIGHT_PRODUCTS')?>: 
            <span><?php print Helper::formatweight($this->order->weight);?></span>
        </div>
    <?php }?>

    <table class="jshop jshop_subtotal">
        <?php if (!$this->hide_subtotal){?>
            <tr class="subtotal">    
                <td class="name">
                    <?php print Text::_('JSHOP_SUBTOTAL')?>
                </td>
                <td class="value">
                    <?php print Helper::formatprice($order->order_subtotal, $order->currency_code);?>
                    <?php print $this->_tmp_ext_subtotal?>
                </td>
            </tr>
        <?php } ?>
        
        <?php print $this->_tmp_html_after_subtotal?>
        
        <?php if ($order->order_discount > 0){ ?>
            <tr class="discount">
                <td class="name">
                    <?php print Text::_('JSHOP_RABATT_VALUE')?><?php print $this->_tmp_ext_discount_text?>
                </td>
                <td class="value">
                    <?php print Helper::formatprice(-$order->order_discount, $order->currency_code);?>
                    <?php print $this->_tmp_ext_discount?>
                </td>
            </tr>
        <?php } ?>
        
        <?php if (!$this->config->without_shipping || $order->order_shipping > 0){?>
            <tr class="shipping">
                <td class="name">
                    <?php print Text::_('JSHOP_SHIPPING_PRICE')?>
                </td>
                <td class="value">
                    <?php print Helper::formatprice($order->order_shipping, $order->currency_code);?>
                    <?php print $this->_tmp_ext_shipping?>
                </td>
            </tr>
        <?php } ?>
        
        <?php if (!$this->config->without_shipping && ($order->order_package>0 || $this->config->display_null_package_price)){?>
            <tr class="package">
                <td class="name">
                    <?php print Text::_('JSHOP_PACKAGE_PRICE')?>
                </td>
                <td class="value">
                    <?php print Helper::formatprice($order->order_package, $order->currency_code); ?>
                    <?php print $this->_tmp_ext_shipping_package?>
                </td>
            </tr>
        <?php } ?>
        
        <?php if ($this->order->order_payment > 0){?>
            <tr class="payment">
                <td class="name">
                    <?php print $this->order->payment_name;?>
                </td>
                <td class="value">
                    <?php print Helper::formatprice($this->order->order_payment, $order->currency_code);?>
                    <?php print $this->_tmp_ext_payment?>
                </td>
            </tr>
        <?php } ?>
          
        <?php if (!$this->config->hide_tax){ ?>
            <?php foreach($order->order_tax_list as $percent=>$value){?>
                <tr class="tax">
                    <td class="name">
                        <?php print Helper::displayTotalCartTaxName($order->display_price);?>
                        <?php if ($this->show_percent_tax) print Helper::formattax($percent)."%"?>
                    </td>
                    <td class="value">
                        <?php print Helper::formatprice($value, $order->currency_code);?>
                        <?php print $this->_tmp_ext_tax[$percent]?>
                    </td>
                </tr>
            <?php }?>
        <?php }?>
        <tr class="total">
            <td class="name">
                <?php print $this->text_total; ?>
            </td>
            <td class="value">
                <?php print Helper::formatprice($order->order_total, $order->currency_code);?>
                <?php print $this->_tmp_ext_total?>
            </td>
        </tr>
        <?php print $this->_tmp_html_after_total?>
    </table>

    <?php if (!$this->config->without_shipping){?>
        <div class="shipping_block_info">
            <div class="shipping_head">
                <b><?php print Text::_('JSHOP_SHIPPING_INFORMATION')?></b>
            </div>
            
            <div class="shipping_info">
                <?php print nl2br($order->shipping_info ?? '');?>
            </div>
            
            <div class="order_shipping_params">
                <?php print nl2br($order->shipping_params ?? '');?>
            </div>
            
            <?php if ($order->delivery_time_name){?>
                <div class="delivery_time">
                    <?php echo Text::_('JSHOP_DELIVERY_TIME').": ".$order->delivery_time_name?>
                </div>
            <?php }?>
            
            <?php if ($order->delivery_date_f){?>
                <div class="delivery_date">
                    <?php echo Text::_('JSHOP_DELIVERY_DATE').": ".$order->delivery_date_f?>
                </div>
            <?php }?>
			<?php print $this->_tmp_html_shipping_block_info_end;?>
        </div>
    <?php }?>
    
    <?php if (!$this->config->without_payment){?>
        <div class="payment_block_info">
            <div class="payment_head">
                <b><?php print Text::_('JSHOP_PAYMENT_INFORMATION')?></b>
            </div>
            <div class="payment_info">
                <?php print $order->payment_name;?>
            </div>
            <div class="order_payment_params">
                <?php print nl2br($order->payment_params ?? '');?>
                <?php print $order->payment_description;?>
            </div>
        </div>
    <?php }?>

    <?php if ($order->order_add_info){ ?>
        <div class="order_comment">
            <div class="order_comment_head">
                <b><?php print Text::_('JSHOP_ORDER_COMMENT')?></b>
            </div>
            <div class="order_comment_info">
                <?php print $order->order_add_info ?>
            </div>
        </div>
    <?php } ?>
    
    <?php print $this->_tmp_html_after_comment;?>

    <div class="history">
        <div class="history_head">
            <b><?php print Text::_('JSHOP_ORDER_HISTORY')?></b>
        </div>
        <div class="order_history">
            <table>
                <?php foreach($order->history as $history){?>
                    <tr>
                        <td class="date">
                            <?php  print Helper::formatdate($history->status_date_added, 0); ?>
                        </td>
                        <td class="name">
                            <?php print $history->status_name ?>
                        </td>
                        <td class="comment">
                            <?php print nl2br($history->comments)?>
                        </td>
                    </tr>
                <?php } ?>
             </table>
        </div>
    </div>
    
    <?php print $this->_tmp_html_after_history;?>
    
    <?php if ($this->allow_cancel){?>
        <div class="button_cancel">
            <a href="<?php print Helper::SEFLink('index.php?option=com_jshopping&controller=user&task=cancelorder&order_id='.$order->order_id)?>" class="btn btn-secondary">
                <?php print Text::_('JSHOP_CANCEL_ORDER')?>
            </a>
        </div>
    <?php }?>
    
    <?php print $this->_tmp_html_end;?>
</div>