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
$pageNav=$this->pageNav;
$i=0;
?>
<div class="row">
<div class="col-md-12">
<div id="j-main-container" class="j-main-container">
<?php JSHelperAdmin::displaySubmenuOptions();?>
<form action="index.php?option=com_jshopping&controller=coupons" method="post" name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>

<div class="js-stools clearfix jshop_block_filter">
    <div class="js-stools-container-bar">
        <div class="btn-toolbar" role="toolbar">
            <?php print $this->tmp_html_filter?>

            <div class="btn-group">
                <div class="input-group">
                    <div class="js-stools-field-filter">
                        <input name="text_search" id="text_search" value="<?php echo htmlspecialchars($this->text_search);?>" class="form-control" placeholder="<?php print JText::_('JSHOP_SEARCH')?>" type="text">
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

<?php
if (isset($this->ext_coupon_html_befor_list)){
    print $this->ext_coupon_html_befor_list;
}
?>

<table class="table table-striped">
<thead>
  <tr>
    <th class="title" width ="10">
      #
    </th>
    <th width="20">
      <input type="checkbox" name="checkall-toggle" value="" title="<?php echo \JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
    </th>
    <th align = "left">
        <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_CODE'), 'C.coupon_code', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width = "200" align = "left">
        <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_VALUE'), 'C.coupon_value', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width = "80">
        <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_START_DATE_COUPON'), 'C.coupon_start_date', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width = "80">
        <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_EXPIRE_DATE_COUPON'), 'C.coupon_expire_date', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width = "80" class="center">
        <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_FINISHED_AFTER_USED'), 'C.finished_after_used', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width = "80" class="center">
        <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_FOR_USER'), 'C.for_user_id', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width = "80" class="center">
        <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_COUPON_USED'), 'C.used', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
	<?php echo $this->tmp_extra_column_headers?>
    <th width = "50" class="center">
        <?php echo JText::_('JSHOP_PUBLISH')?>
    </th>
    <th width = "50" class="center">
        <?php echo JText::_('JSHOP_EDIT')?>
    </th>
    <th width = "40" class="center">
        <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_ID'), 'C.coupon_id', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
  </tr>
</thead>  
<?php
foreach($rows as $row){
    $finished=0; $date=date('Y-m-d');
    if ($row->used) $finished=1;
    if ($row->coupon_expire_date < $date && $row->coupon_expire_date!='0000-00-00' ) $finished=1;
?>
  <tr class="row<?php echo $i % 2;?>" <?php if ($finished) print "style='font-style:italic; color: #999;'"?>>
   <td>
     <?php echo $pageNav->getRowOffset($i);?>
   </td>
   <td>
    <?php echo \JHTML::_('grid.id', $i, $row->coupon_id);?>
   </td>
   <td>
     <a href="index.php?option=com_jshopping&controller=coupons&task=edit&coupon_id=<?php echo $row->coupon_id; ?>"><?php echo $row->coupon_code;?></a>
   </td>
   <td>
     <?php echo $row->coupon_value; ?>
     <?php if ($row->coupon_type==0) print "%"; else print $this->currency;?>
   </td>
   <td>
    <?php if ($row->coupon_start_date!='0000-00-00') print JSHelper::formatdate($row->coupon_start_date);?>
   </td>
   <td>
    <?php if ($row->coupon_expire_date!='0000-00-00')  print JSHelper::formatdate($row->coupon_expire_date);?>
   </td>
   <td class="center">
    <?php if ($row->finished_after_used) print JText::_('JSHOP_YES'); else print JText::_('JSHOP_NO')?>
   </td>
   <td class="center">
    <?php if ($row->for_user_id) print $row->f_name." ".$row->l_name; else print JText::_('JSHOP_ALL')?>
   </td>
   <td class="center">
    <?php if ($row->used) print JText::_('JSHOP_YES'); else print JText::_('JSHOP_NO')?>
   </td>
   <?php echo $row->tmp_extra_column_cells?>
   <td class="center">     
     <?php echo \JHTML::_('jgrid.published', $row->coupon_publish, $i);?>
   </td>
   <td class="center">
        <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=coupons&task=edit&coupon_id=<?php print $row->coupon_id?>'>
            <i class="icon-edit"></i>
        </a>
   </td>
   <td class="center">
     <?php echo $row->coupon_id ?>
   </td>
  </tr>
<?php
$i++;
}
?>
<tfoot>
<tr>	
    <td colspan="<?php echo 12+(int)$this->deltaColspan?>">
		<div class = "jshop_list_footer"><?php echo $pageNav->getListFooter(); ?></div>
        <div class = "jshop_limit_box"><?php echo $pageNav->getLimitBox(); ?></div>
	</td>
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
<script>
jQuery(function(){
	jshopAdmin.setMainMenuActive('<?php print JURI::base()?>index.php?option=com_jshopping&controller=other');
});
</script>