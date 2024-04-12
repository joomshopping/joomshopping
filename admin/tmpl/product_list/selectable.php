<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$rows = $this->rows;
$lists = $this->lists;
$pageNav = $this->pagination;
$text_search = $this->text_search;
$category_id = $this->category_id;
$manufacturer_id = $this->manufacturer_id;
$count = count($rows);
$jsfname = $this->jsfname;
$i = 0;
?>

<form action="index.php?option=com_jshopping&controller=product_list_selectable&tmpl=component" method="post" name="adminForm" id="adminForm">

<?php print $this->tmp_html_start?>

<div class="js-stools clearfix jshop_block_filter">
    <div class="js-stools-container-bar">
        <div class="btn-toolbar" role="toolbar">
            <?php print $this->tmp_html_filter?>

            <div class="btn-group">
                <div class="input-group">
                    <div class="js-stools-field-filter">
                        <?php echo $lists['treecategories'];?>
                    </div>

                    <?php if ($this->config->disable_admin['product_manufacturer'] == 0){?>
                    <div class="js-stools-field-filter">
                        <?php echo $lists['manufacturers'];?>
                    </div>
                    <?php }?>
                    <?php if ($this->show_vendor) : ?>
                        <div class="js-stools-field-filter">
                            <?php echo $lists['vendors'];?>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->config->admin_show_product_labels){?>
                        <div class="js-stools-field-filter">
                            <?php echo $lists['labels']?>
                        </div>
                    <?php }?>

                    <div class="js-stools-field-filter">
                        <?php echo $lists['publish'];?>
                    </div>
                </div>
            </div>

            <div class="btn-group">
                <div class="input-group">
                    <div class="js-stools-field-filter">
                        <input name="text_search" id="text_search" value="<?php echo htmlspecialchars($text_search);?>" class="form-control" placeholder="<?php print JText::_('JSHOP_SEARCH')?>" type="text">
                    </div>
                    <div class="js-stools-field-filter">
                    <span class="input-group-append">
                        <button type="submit" class="btn btn-primary hasTooltip" title="<?php print JText::_('JSHOP_SEARCH')?>">
                            <span class="icon-search" aria-hidden="true"></span>
                        </button>
                    </span>
                    </div>
                </div>
            </div>
            <div class="js-stools-field-filter">
                <button type="button" class="btn btn-primary js-stools-btn-clear"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
            </div>
            <?php print $this->tmp_html_filter_end?>
        </div>
    </div>
</div>

<table class="table table-striped" >
<thead> 
  <tr>
	<th class="title" width ="10">
	  #
	</th>
	<th width="93">
		<?php print JText::_('JSHOP_IMAGE')?>
	</th>
	<th>
	  <?php echo JText::_('JSHOP_TITLE')?>
	</th>
    <?php print $this->tmp_html_col_after_title?>
	<?php if (!$category_id){?>
	<th width="80">
	  <?php echo JText::_('JSHOP_CATEGORY')?>
	</th>
	<?php }?>
	<?php if (!$manufacturer_id){?>
	<th width="80">
	  <?php echo JText::_('JSHOP_MANUFACTURER')?>
	</th>
	<?php }?>
    <?php if ($this->config->disable_admin['product_ean'] == 0){?>
    <th width="80">
        <?php echo JText::_('JSHOP_EAN_PRODUCT')?>
    </th>
    <?php }?>
	<th width="60">
		<?php echo JText::_('JSHOP_PRICE')?>
	</th>
	<th width="60">
		<?php echo JText::_('JSHOP_DATE')?>
	</th>
	<th width="40" class="center">
	  <?php echo JText::_('JSHOP_PUBLISH')?>
	</th>
	<th width="30" class="center">
	  <?php echo JText::_('JSHOP_ID')?>
	</th>
  </tr>
</thead> 
<?php foreach ($rows as $row){?>
  <tr class="row<?php echo $i % 2;?>">
   <td>
	 <?php echo $pageNav->getRowOffset($i);?>
   </td>
   <td>
	<?php if ($row->image){?>
		<a href="#" onclick="window.parent.<?php print $jsfname?>(<?php echo $row->product_id; ?>)">
			<img src="<?php print \JSHelper::getPatchProductImage($row->image, 'thumb', 1)?>" width="90" border="0" />
		</a>
	<?php }?>
   </td>
   <td>
     <b><a href="#" onclick="window.parent.<?php print $jsfname?>(<?php echo $row->product_id; ?>)"><?php echo $row->name;?></a></b>
	 <div><?php echo $row->short_description;?></div>
   </td>
   <?php print $row->tmp_html_col_after_title?>
   <?php if (!$category_id){?>
   <td>
	  <?php echo $row->namescats;?>
   </td>
   <?php }?>
   <?php if (!$manufacturer_id){?>
   <td>
	  <?php echo $row->man_name;?>
   </td>
   <?php }?>
   <?php if ($this->config->disable_admin['product_ean'] == 0){?>
   <td>
    <?php echo $row->ean?>
   </td>
   <?php }?>
   <td>		
    <?php echo \JSHelper::formatprice($row->product_price, JSHelper::sprintCurrency($row->currency_id));?>
   </td>
   <td>
	<?php echo JSHelper::formatdate($row->product_date_added, 1);?>
   </td>
   <td class="center">
    <a class="btn btn-micro">
	    <?php if ($row->product_publish){;?>
            <i class="icon-publish"></i>
        <?php }else{?>
            <i class="icon-unpublish"></i>
        <?php }?>
    </a>
   </td>
   <td class="center">
	 <?php echo $row->product_id; ?>
   </td>
  </tr>
<?php
$i++;
}
?>
 <tfoot>
 <tr>
    <?php print $this->tmp_html_col_before_td_foot?>
	<td colspan="17">
		<div class = "jshop_list_footer"><?php echo $pageNav->getListFooter(); ?></div>
        <div class = "jshop_limit_box"><?php echo $pageNav->getLimitBox(); ?></div>
	</td>
    <?php print $this->tmp_html_col_after_td_foot?>
 </tr>
 </tfoot>   
</table>
<input type="hidden" name="task" value="" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="jsfname" value="<?php print $jsfname?>" />
<?php print $this->tmp_html_end?>
</form>