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
<div id="comjshop">
    <?php print $this->checkout_navigator?>
    <?php print $this->small_cart?>

    <div class="jshop checkout_shipping_block">
        <form id="shipping_form" name="shipping_form" action="<?php print $this->action ?>" method="post" autocomplete="off" enctype="multipart/form-data">
            <?php print $this->_tmp_ext_html_shipping_start?>
            <div id = "table_shippings">
                <?php foreach($this->shipping_methods as $shipping){?>
                    <div class="name">
                        <input type="radio" name="sh_pr_method_id" id="shipping_method_<?php print $shipping->sh_pr_method_id?>" value="<?php print $shipping->sh_pr_method_id ?>" <?php if ($shipping->sh_pr_method_id==$this->active_shipping){ ?>checked="checked"<?php } ?> data-shipping_id="<?php print $shipping->shipping_id?>">
                        <label for = "shipping_method_<?php print $shipping->sh_pr_method_id ?>"><?php
                        if ($shipping->image){
                            ?><span class="shipping_image"><img src="<?php print $shipping->image?>" alt="<?php print htmlspecialchars($shipping->name)?>" /></span><?php
                        }
                        ?><b><?php print $shipping->name?></b>
                        <span class="shipping_price">(<?php print \JSHelper::formatprice($shipping->calculeprice); ?>)</span>
                        </label>
                        
                        <?php if ($this->config->show_list_price_shipping_weight && count($shipping->shipping_price)){ ?>
                            <table class="shipping_weight_to_price">
                                <?php foreach($shipping->shipping_price as $price){?>
                                    <tr>
                                        <td class="weight">
                                            <?php if ($price->shipping_weight_to!=0){?>
                                                <?php print \JSHelper::formatweight($price->shipping_weight_from);?> - <?php print \JSHelper::formatweight($price->shipping_weight_to);?>
                                            <?php }else{ ?>
                                                <?php print JText::_('JSHOP_FROM')." ".\JSHelper::formatweight($price->shipping_weight_from);?>
                                            <?php } ?>
                                        </td>
                                        <td class="price">
                                            <?php print \JSHelper::formatprice($price->shipping_price); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        <?php } ?>
                        
                        <div class="shipping_descr"><?php print $shipping->description?></div>
                        
                        <div id="shipping_form_<?php print $shipping->shipping_id?>" class="shipping_form <?php if ($shipping->sh_pr_method_id==$this->active_shipping) print 'shipping_form_active'?>"><?php print $shipping->form?></div>
                        
                        <?php if ($shipping->delivery){?>
                            <div class="shipping_delivery"><?php print JText::_('JSHOP_DELIVERY_TIME').": ".$shipping->delivery?></div>
                        <?php }?>
                        
                        <?php if ($shipping->delivery_date_f){?>
                            <div class="shipping_delivery_date"><?php print JText::_('JSHOP_DELIVERY_DATE').": ".$shipping->delivery_date_f?></div>
                        <?php }?>      
                    </div>
                <?php } ?>
            </div>

            <?php print $this->_tmp_ext_html_shipping_end?>
            <input type="submit" class="btn btn-success button" value="<?php print JText::_('JSHOP_NEXT')?>">
        </form>
    </div>
</div>