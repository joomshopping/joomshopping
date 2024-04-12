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
<div class="jshop myorders_list" id="comjshop">

    <h1><?php print JText::_('JSHOP_MY_ORDERS')?></h1>
    
    <?php print $this->_tmp_html_before_user_order_list;?>
    
    <?php if (count($this->orders)) {?>
        <?php foreach ($this->orders as $order){?>
            <div class="myorders_block_info">
            
                <div class="order_number">
                    <b><?php print JText::_('JSHOP_ORDER_NUMBER')?>:</b>
                    <span><?php print $order->order_number?></span>
                </div>
				<?php print $order->_tmp_ext_order_number;?>
				
                <div class="order_status">
                    <b><?php print JText::_('JSHOP_ORDER_STATUS')?>:</b>
                    <span><?php print $order->status_name?></span>
                </div>
				<?php print $order->_tmp_ext_status_name;?>
                
                <div class="table_order_list">
                    <div class="row">
                        <div class="col-lg-6 users">
                            <div>
                                <b><?php print JText::_('JSHOP_ORDER_DATE')?>:</b>
                                <span><?php print \JSHelper::formatdate($order->order_date, 0) ?></span>
                            </div>
                            <div>
                                <b><?php print JText::_('JSHOP_EMAIL_BILL_TO')?>:</b>
                                <span><?php print $order->f_name ?> <?php print $order->l_name ?></span>
                            </div>
                            <div>
                                <b><?php print JText::_('JSHOP_EMAIL_SHIP_TO')?>:</b>
                                <span><?php print $order->d_f_name ?> <?php print $order->d_l_name ?></span>
                            </div>
                            <?php print $order->_tmp_ext_user_info;?>
                        </div>
                        <div class="col-lg-3 products">
                            <div>
                                <b><?php print JText::_('JSHOP_PRODUCTS')?>:</b> 
                                <span><?php print $order->count_products ?></span>
                            </div>
                            <div>
                                <span><?php print \JSHelper::formatprice($order->order_total, $order->currency_code)?></span>
                                <?php print $order->_ext_price_html?>
                            </div>
                            <?php print $order->_tmp_ext_prod_info;?>
                        </div>
                        <div class="col-lg-3 buttons">
                            <a class="btn btn-primary" href="<?php print $order->order_href ?>"><?php print JText::_('JSHOP_DETAILS')?></a>
                            <?php print $order->_tmp_ext_but_info;?>
                        </div>
                    </div>
                    <?php print $order->_tmp_ext_row_end;?>
                </div>
            </div>
        <?php } ?>
        
        <div class="myorders_total">
            <span class="name"><?php print JText::_('JSHOP_TOTAL')?>:</span>
            <span class="price"><?php print \JSHelper::formatprice($this->total, \JSHelper::getMainCurrencyCode())?></span>
        </div>
        
    <?php }else{ ?>
        <div class="myorders_no_orders">
            <?php print JText::_('JSHOP_NO_ORDERS')?>
        </div>
    <?php } ?>
    
    <?php print $this->_tmp_html_after_user_order_list;?>
</div>