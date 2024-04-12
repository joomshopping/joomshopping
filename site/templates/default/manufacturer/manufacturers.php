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
<?php if ($this->params->get('show_page_heading') && $this->params->get('page_heading')) : ?>    
    <div class="shophead<?php print $this->params->get('pageclass_sfx');?>">
        <h1><?php print $this->params->get('page_heading')?></h1>
    </div>
<?php endif; ?>

<div class="jshop" id="comjshop">

    <div class="manufacturer_description">
        <?php print $this->manufacturer->description?>
    </div>

    <?php if (count($this->rows)) : ?>
    <div class="jshop_list_manufacturer">   
		<div class="row-fluid">
		<?php foreach($this->rows as $k=>$row) : ?>                
			<div class="sblock<?php echo $this->count_manufacturer_to_row?> jshop_categ manufacturer">
				<div class="image">
					<a href="<?php print $row->link;?>">
						<img class="jshop_img" src="<?php print $this->image_manufs_live_path;?>/<?php if ($row->manufacturer_logo) print $row->manufacturer_logo; else print $this->noimage;?>" alt="<?php print htmlspecialchars($row->name);?>" />
					</a>
				</div>
				<div class="manufacturer_info">
					<div class="manufacturer_name">
						<a class="product_link" href="<?php print $row->link?>">
							<?php print $row->name?>
						</a>
					</div>
					<p class="manufacturer_short_description">
						<?php print $row->short_description?>
					</p>
					<?php if ($row->manufacturer_url != "") : ?>
						<div class="manufacturer_url">
							<a target="_blank" href="<?php print $row->manufacturer_url?>">
								<?php print JText::_('JSHOP_MANUFACTURER_INFO')?>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>                
		<?php endforeach; ?>
		</div>
    </div>
    <?php endif; ?>
</div>