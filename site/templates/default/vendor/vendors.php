<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die;
?>
<div class="jshop" id="comjshop">
    <?php if ($this->params->get('show_page_title') && $this->params->get('page_title')) : ?>    
        <div class="componentheading<?php print $this->params->get('pageclass_sfx');?>">
            <?php print $this->params->get('page_title')?>
        </div>
    <?php endif; ?>
    
    <?php if (count($this->rows)) : ?>
    <div class="jshop_list_vendor">
        <div class="row-fluid">
            <?php foreach($this->rows as $k=>$row) : ?>
                <div class="sblock<?php echo $this->count_to_row?> jshop_categ vendor">
                    <div class="image">
                        <a class="product_link" href="<?php print $row->link?>">
                            <img class="jshop_img" src="<?php print $row->logo;?>" alt="<?php print htmlspecialchars($row->shop_name);?>" />
                        </a>                    
                    </div>
                    <div class="vendor_info">
                        <div class="vendor_name">
                            <a class="product_link" href="<?php print $row->link?>">
                                <?php print $row->shop_name?>
                            </a>
                        </div>
                    </div>
                </div>
             <?php endforeach; ?>            
        </div>
        <?php if ($this->display_pagination) : ?>
            <div class="jshop_pagination">
                <div class="pagination"><?php print $this->pagination?></div>
            </div>
        <?php endif;  ?>
    </div>
    <?php endif; ?>
</div>