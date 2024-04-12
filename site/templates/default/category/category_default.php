<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

print $this->_tmp_category_html_start;
?>
<div class="jshop" id="comjshop">
    <h1><?php print $this->category->name?></h1>
    <div class="category_description">
        <?php print $this->category->description?>
    </div>

    <div class="jshop_list_category">
    <?php if (count($this->categories)) : ?>
        <div class="jshop list_category">
            <div class="row-fluid">
            <?php foreach($this->categories as $k=>$category) : ?>                
                <div class="sblock<?php echo $this->count_category_to_row;?> jshop_categ category">
                    <div class="image">
                        <a href="<?php print $category->category_link;?>">
                            <img class="jshop_img" src="<?php print $this->image_category_path;?>/<?php if ($category->category_image) print $category->category_image; else print $this->noimage;?>" alt="<?php print htmlspecialchars($category->name)?>" title="<?php print htmlspecialchars($category->name)?>" />
                        </a>
                    </div>
                    <div class="category_info">
                        <div class="category_name">
                            <a class="product_link" href="<?php print $category->category_link?>">
                                <?php print $category->name?>
                            </a>
                        </div>
                        <p class="category_short_description">
                            <?php print $category->short_description?>
                        </p>                       
                    </div>
                </div>                
            <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    </div>
	
	<?php print $this->_tmp_category_html_before_products;?>
        
    <?php include(dirname(__FILE__)."/products.php");?>
	
	<?php print $this->_tmp_category_html_end;?>
</div>