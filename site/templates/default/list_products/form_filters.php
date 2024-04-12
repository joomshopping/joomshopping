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
<form action="<?php print $this->action;?>" method="post" name="sort_count" id="sort_count" class="form-horizontal">
<div class="form_sort_count">
<?php if ($this->config->show_sort_product || $this->config->show_count_select_products) : ?>
<div class="block_sorting_count_to_page">
    <?php if ($this->config->show_sort_product) : ?>
        <div class="control-group box_products_sorting d-flex">
            <div class="control-label mt-2">
                <?php print JText::_('JSHOP_ORDER_BY');?>:
            </div>
            <div class="controls">
                <div class="input-group">
                    <?php echo $this->sorting?>
                    <span class="icon-arrow" id="submit_product_list_filter_sort_dir">
						<?php if ($this->orderby==1){?>
						<span class="icon-arrow-down"></span>
						<?php }else{?>
						<span class="icon-arrow-up"></span>
						<?php }?>
                    </span>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($this->config->show_count_select_products) : ?>
        <div class="control-group box_products_count_to_page d-flex">
            <div class="control-label mt-2">
                <?php print JText::_('JSHOP_DISPLAY_NUMBER').": "; ?>
            </div>
            <div class="controls">
                <?php echo $this->product_count?>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if ($this->config->show_product_list_filters && $this->filter_show) : ?>

    <?php if ($this->config->show_sort_product || $this->config->show_count_select_products) : ?>
        <div class="margin_filter"></div>
    <?php endif; ?>
    
	<div class="filter_mob_head" id="filter_mob_head">
		<span class="icon-play-2"></span>
		<?php print JText::_('JSHOP_FILTERS')?>
	</div>
    <div class="jshop filters">
        <div class="box_cat_man d-flex">
            <?php if ($this->filter_show_category) : ?>
                <div class = "control-group box_category d-flex">
                    <div class = "control-label mt-2">
                        <?php print JText::_('JSHOP_CATEGORY').": "; ?>
                    </div>
                    <div class = "controls"><?php echo $this->categorys_sel?></div>
                </div>
            <?php endif; ?>
            <?php if ($this->filter_show_manufacturer) : ?>
                <div class="control-group box_manufacrurer d-flex">
                    <div class="control-label mt-2">
                        <?php print JText::_('JSHOP_MANUFACTURER').": "; ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->manufacuturers_sel; ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php print $this->_tmp_ext_filter_box;?>
        </div>
        
        <?php if (\JSHelper::getDisplayPriceShop()) : ?>
            <div class="filter_price d-sm-flex">
                <div class="control-group box_price_from_to d-flex">
                    <div class="control-label mt-2">
                        <?php print JText::_('JSHOP_PRICE')?> (<?php print $this->config->currency_code?>):
                    </div>
                    <div class="controls form-inline d-flex">
                        <span class="input-append">
                            <input type="text" class="input form-control" name="fprice_from" id="price_from" size="7" placeholder="<?php print \JText::_('JSHOP_FROM')?>" value="<?php if ($this->filters['price_from']>0) print $this->filters['price_from']?>" />
                        </span>
                        <span class="price_ftspace mt-2">-</span>
                        <span class="input-append">
                            <input type="text" class="input form-control" name="fprice_to"  id="price_to" size="7" placeholder="<?php print \JText::_('JSHOP_TO')?>" value="<?php if ($this->filters['price_to']>0) print $this->filters['price_to']?>" />
                        </span>
                    </div>

                </div>
                
                <?php print $this->_tmp_ext_filter;?>
                <div class="control-group box_button">
                    <div class="controls d-flex align-items-center">
                    <input type="button" class="btn button btn-primary" id="submit_product_list_filter" value="<?php print JText::_('JSHOP_GO')?>">
                    <span class="clear_filter"><a href="#" class="btn btn-secondary" id="clear_product_list_filter"><?php print JText::_('JSHOP_CLEAR_FILTERS')?></a></span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
</div>
<input type="hidden" name="orderby" id="orderby" value="<?php print $this->orderby?>">
<input type="hidden" name="limitstart" value="0">
</form>