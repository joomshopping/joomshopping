<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

print $this->_tmp_maincategory_html_start;
?>
<?php if ($this->params->get('show_page_heading') && $this->params->get('page_heading')){?>
<div class="shophead<?php print $this->params->get('pageclass_sfx');?>">
    <h1><?php print $this->params->get('page_heading')?></h1>
</div>
<?php }?>

<div class="jshop" id="comjshop">
    <div class="category_description">
        <?php print $this->category->description?>
    </div>

    <div class="jshop_list_category">
    <?php if (count($this->categories)) : ?>
        <div class="row-fluid">
        <?php foreach ($this->categories as $k => $category) : ?>        
            <div class="sblock<?php echo $this->count_category_to_row;?> jshop_categ category">
                <div class="image">
                    <a href="<?php print $category->category_link;?>">
                        <img class="jshop_img" src="<?php print $this->image_category_path;?>/<?php if ($category->category_image) print $category->category_image; else print $this->noimage;?>" alt="<?php print htmlspecialchars($category->name);?>" title="<?php print htmlspecialchars($category->name);?>" />
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
        <?php endforeach;?>        
        </div>
    <?php endif; ?>
    </div>
    <?php print $this->_tmp_maincategory_html_end;?>
</div>