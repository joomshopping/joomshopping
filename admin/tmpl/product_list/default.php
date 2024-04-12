<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$rows=$this->rows;
$lists=$this->lists;
$pageNav=$this->pagination;
$text_search=$this->text_search;
$category_id=$this->category_id;
$manufacturer_id=$this->manufacturer_id;
$count = count($rows);
$i=0;
$saveOrder = ($this->filter_order_Dir=="asc" && $this->filter_order=="ordering" && $category_id);
if ($saveOrder){
    $saveOrderingUrl = 'index.php?option=com_jshopping&controller=products&task=saveorder&category_id='.$category_id.'&tmpl=component&ajax=1';
	Joomla\CMS\HTML\HTMLHelper::_('draggablelist.draggable');
}

?>
<div class="row">
<div class="col-md-12">
<div id="j-main-container" class="j-main-container">
<form action="index.php?option=com_jshopping&controller=products" method="post" name="adminForm" id="adminForm">
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
    <th scope="col" style="width:1%" class="text-center d-none d-md-table-cell">
        <?php if ($category_id){ ?>
        <?php echo \JHTML::_('grid.sort', $this->filter_order!='ordering' ? '#' : '', 'ordering', $this->filter_order_Dir, $this->filter_order); ?>
        <?php }else{ ?>
            #
        <?php }?>
    </th>
    <th width="20">
      <input type="checkbox" name="checkall-toggle" value="" title="<?php echo \JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
    </th>
    <th width="93">
        <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_IMAGE'), 'product_name_image', $this->filter_order_Dir, $this->filter_order)?>
    </th>
    <th>
      <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_TITLE'), 'name', $this->filter_order_Dir, $this->filter_order)?>
    </th>
    <?php print $this->tmp_html_col_after_title?>
    <?php if (!$category_id){?>
    <th width="80">
        <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_CATEGORY'), 'category', $this->filter_order_Dir, $this->filter_order)?>
    </th>
    <?php }?>
    <?php if (!$manufacturer_id && $this->config->disable_admin['product_manufacturer'] == 0){?>
    <th width="80">
        <?php echo \JHTML::_( 'grid.sort', JText::_('JSHOP_MANUFACTURER'), 'manufacturer', $this->filter_order_Dir, $this->filter_order)?>
    </th>
    <?php }?>
    <?php if ($this->show_vendor){?>
    <th width="80">
      <?php echo \JHTML::_( 'grid.sort', JText::_('JSHOP_VENDOR'), 'vendor', $this->filter_order_Dir, $this->filter_order)?>
    </th>
    <?php }?>
    <?php if ($this->config->disable_admin['product_ean']==0 || $this->config->admin_product_list_manufacture_code){?>
    <th width="80">
        <?php echo \JHTML::_( 'grid.sort', JText::_('JSHOP_EAN_PRODUCT'), 'ean', $this->filter_order_Dir, $this->filter_order);?>
    </th>
    <?php }?>
    <?php if ($this->config->stock){?>
    <th width="60">
        <?php echo \JHTML::_( 'grid.sort', JText::_('JSHOP_QUANTITY'), 'qty', $this->filter_order_Dir, $this->filter_order);?>
    </th>
    <?php }?>
    <th width="80">
        <?php echo \JHTML::_( 'grid.sort', JText::_('JSHOP_PRICE'), 'price', $this->filter_order_Dir, $this->filter_order);?>
    </th>
    <th width="40">
        <?php echo \JHTML::_( 'grid.sort', JText::_('JSHOP_HITS'), 'hits', $this->filter_order_Dir, $this->filter_order);?>
    </th>
    <th width="60">
        <?php echo \JHTML::_( 'grid.sort', JText::_('JSHOP_DATE'), 'date', $this->filter_order_Dir, $this->filter_order);?>
    </th>
    <th width="40" class="center">
      <?php echo JText::_('JSHOP_PUBLISH')?>
    </th>    
    <th width="40" class="center">
        <?php echo JText::_('JSHOP_DELETE')?>
    </th>
    <th width="30" class="center">
      <?php echo \JHTML::_( 'grid.sort', JText::_('JSHOP_ID'), 'product_id', $this->filter_order_Dir, $this->filter_order);?>
    </th>
</tr>
</thead>
<tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($this->filter_order_Dir); ?>" data-nested="false"<?php endif; ?>>
<?php foreach($rows as $row){?>
<tr class="row<?php echo $i % 2; ?>" data-draggable-group="1" item-id="<?php echo $row->product_id; ?>" parents="" level="1">
    <td class="order text-center d-none d-md-table-cell">
        <span class="sortable-handler <?php if (!$saveOrder) echo 'inactive';?>">
            <span class="icon-ellipsis-v" aria-hidden="true"></span>
        </span>
        <?php if ($saveOrder){ ?>
            <input type="text" class="hidden" name="order[]" value="<?php echo $row->product_ordering; ?>">
        <?php } ?>
    </td>
   <td>
     <?php echo \JHTML::_('grid.id', $i, $row->product_id);?>
   </td>
   <td>
    <?php if ($row->label_id){?>
        <div class="product_label">
            <?php if (isset($row->_label_image) && $row->_label_image){?>
                <img src="<?php print $row->_label_image?>" width="25" alt="" />
            <?php }else{?>
                <span class="label_name"><?php print $row->_label_name;?></span>
            <?php }?>
        </div>
    <?php }?>
    <?php if ($row->image){?>
        <a href="index.php?option=com_jshopping&controller=products&task=edit&product_id=<?php print $row->product_id?>">
            <img src="<?php print \JSHelper::getPatchProductImage($row->image, 'thumb', 1)?>" width="90" border="0" />
        </a>
    <?php }?>
   </td>
   <td>
     <b><a href="index.php?option=com_jshopping&controller=products&task=edit&product_id=<?php print $row->product_id?>"><?php echo $row->name;?></a></b>
     <div><?php echo $row->short_description;?></div>
   </td>
   <?php print $row->tmp_html_col_after_title?>
   <?php if (!$category_id){?>
   <td>
      <?php echo $row->namescats;?>
   </td>
   <?php }?>
   <?php if (!$manufacturer_id && $this->config->disable_admin['product_manufacturer'] == 0){?>
   <td>
      <?php echo $row->man_name;?>
   </td>
   <?php }?>
   <?php if ($this->show_vendor){?>
   <td>
        <?php echo $row->vendor_name;?>
   </td>
   <?php }?>
   <?php if ($this->config->disable_admin['product_ean']==0 || $this->config->admin_product_list_manufacture_code){?>
   <td>
       <?php echo $row->ean?>
	   <?php if ($this->config->admin_product_list_manufacture_code && $row->manufacturer_code!=''){?>
	   (<?php print $row->manufacturer_code?>)
	   <?php }?>
   </td>
   <?php }?>
   <?php if ($this->config->stock){?>
   <td>
    <?php if ($row->unlimited){
        print JText::_('JSHOP_UNLIMITED');
    }else{
        echo floatval($row->qty);
    }
    ?>
   </td>
   <?php }?>
   <td>
    <?php echo \JSHelper::formatprice($row->product_price, JSHelper::sprintCurrency($row->currency_id));?>
   </td>
   <td>
    <?php echo $row->hits;?>
   </td>
   <td>
    <?php echo JSHelper::formatdate($row->product_date_added, 1);?>
   </td>  
   <td class="center">     
     <?php echo \JHTML::_('jgrid.published', $row->product_publish, $i);?>
   </td>
      
   <td class="center">
    <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=products&task=remove&cid[]=<?php print $row->product_id?>' onclick="return confirm('<?php print JText::_('JSHOP_DELETE')?>')">
        <i class="icon-delete"></i>
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
</tbody>
<tfoot>
<tr>
    <?php print $this->tmp_html_col_before_td_foot?>
    <td colspan="18">
		<div class = "jshop_list_footer"><?php echo $pageNav->getListFooter(); ?></div>
        <div class = "jshop_limit_box"><?php echo $pageNav->getLimitBox(); ?></div>
	</td>
    <?php print $this->tmp_html_col_after_td_foot?>
</tr>
</tfoot>
</table>
<input type="hidden" name="filter_order" value="<?php echo $this->filter_order?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
<?php print $this->tmp_html_end?>
</form>
</div>
</div>
</div>