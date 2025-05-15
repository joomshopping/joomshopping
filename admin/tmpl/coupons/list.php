<?php
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\CMS\Uri\Uri;

/**
* @version      5.6.3 13.03.2025
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

<div id="j-main-container" class="j-main-container">
<?php HelperAdmin::displaySubmenuOptions();?>
<form action="index.php?option=com_jshopping&controller=coupons" method="post" name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>

<div class="js-filters">

    <?php print $this->tmp_html_filter?>

    <div>
        <input name="text_search" id="text_search" value="<?php echo htmlspecialchars($this->text_search);?>" class="form-control" placeholder="<?php print Text::_('JSHOP_SEARCH')?>" type="text">
    </div>
    <div>
        <span class="input-group-append">
            <button type="submit" class="btn btn-primary hasTooltip" title="<?php print Text::_('JSHOP_SEARCH')?>">
                <span class="icon-search" aria-hidden="true"></span>
            </button>                        
        </span>
    </div>

    <div>
        <button type="button" class="btn btn-primary js-stools-btn-clear"><?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?></button>
    </div>
    <?php print $this->tmp_html_filter_end?>

</div>

<?php  print $this->ext_coupon_html_befor_list ?? '';?>

<table class="table table-striped">
<thead>
  <tr>
    <th class="title" width ="10">
      #
    </th>
    <th width="20">
      <input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
    </th>
    <th align = "left">
        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_CODE'), 'C.coupon_code', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width = "200" align = "left">
        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_VALUE'), 'C.coupon_value', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width = "80">
        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_START_DATE_COUPON'), 'C.coupon_start_date', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width = "80">
        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_EXPIRE_DATE_COUPON'), 'C.coupon_expire_date', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width = "80" class="center">
        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_FINISHED_AFTER_USED'), 'C.finished_after_used', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width = "80" class="center">
        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_FOR_USER'), 'C.for_user_id', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width = "80" class="center">
        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_COUPON_USED'), 'C.used', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
	<?php echo $this->tmp_extra_column_headers?>
    <th width = "50" class="center">
        <?php echo Text::_('JSHOP_PUBLISH')?>
    </th>
    <th width = "50" class="center">
        <?php echo Text::_('JSHOP_EDIT')?>
    </th>
    <th width = "40" class="center">
        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_ID'), 'C.coupon_id', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
  </tr>
</thead>  
<?php foreach($rows as $row){ ?>
  <tr class="row<?php echo $i % 2;?>" <?php if ($row->_finished) print "style='font-style:italic; color: #999;'"?>>
   <td>
     <?php echo $pageNav->getRowOffset($i);?>
   </td>
   <td>
    <?php echo HTMLHelper::_('grid.id', $i, $row->coupon_id);?>
   </td>
   <td>
     <a href="index.php?option=com_jshopping&controller=coupons&task=edit&coupon_id=<?php echo $row->coupon_id; ?>"><?php echo $row->coupon_code;?></a>
   </td>
   <td>
     <?php echo $row->coupon_value; ?>
     <?php if ($row->coupon_type==0) print "%"; else print $this->currency;?>
   </td>
   <td>
    <?php print Helper::formatdate($row->coupon_start_date);?>
   </td>
   <td>
    <?php print Helper::formatdate($row->coupon_expire_date);?>
   </td>
   <td class="center">
    <?php if ($row->finished_after_used) print Text::_('JSHOP_YES'); else print Text::_('JSHOP_NO')?>
   </td>
   <td class="center">
    <?php if ($row->for_user_id) print $row->f_name." ".$row->l_name; else print Text::_('JSHOP_ALL')?>
   </td>
   <td class="center">
    <?php if ($row->used) print Text::_('JSHOP_YES'); else print Text::_('JSHOP_NO')?>
   </td>
   <?php echo $row->tmp_extra_column_cells?>
   <td class="center">     
     <?php echo HTMLHelper::_('jgrid.published', $row->coupon_publish, $i);?>
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
</table>

<div class="d-flex justify-content-between align-items-center">
    <div class="jshop_list_footer"><?php echo $pageNav->getListFooter(); ?></div>
    <div class="jshop_limit_box"><?php echo $pageNav->getLimitBox(); ?></div>
</div>

<input type="hidden" name="filter_order" value="<?php echo $this->filter_order?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
<?php print $this->tmp_html_end?>
</form>
</div>
<script>
jQuery(function(){
	jshopAdmin.setMainMenuActive('<?php print Uri::base()?>index.php?option=com_jshopping&controller=other');
});
</script>