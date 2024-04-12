<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$shippings = $this->rows;
$i = 0;
$saveOrder = $this->filter_order_Dir=="asc" && $this->filter_order=="ordering";
if ($saveOrder){
    $saveOrderingUrl = 'index.php?option=com_jshopping&controller=shippings&task=saveorder&tmpl=component&ajax=1';
	Joomla\CMS\HTML\HTMLHelper::_('draggablelist.draggable');
}
?>
<div class="row">
<div class="col-md-12">
<div id="j-main-container" class="j-main-container">
    <?php JSHelperAdmin::displaySubmenuOptions();?>
    <form action="index.php?option=com_jshopping&controller=shippings" method="post" name="adminForm" id="adminForm">
    <?php print $this->tmp_html_start?>
    <table class="table table-striped">
    <thead>
        <tr>
            <th scope="col" style="width:1%" class="text-center d-none d-md-table-cell">
                <?php echo \JHTML::_('grid.sort', $this->filter_order!='ordering' ? '#' : '', 'ordering', $this->filter_order_Dir, $this->filter_order); ?>
            </th>
            <th width="20">
              <input type="checkbox" name="checkall-toggle" value="" title="<?php echo \JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
            </th>
            <th align="left">
              <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_TITLE'), 'name', $this->filter_order_Dir, $this->filter_order); ?>
            </th>
            <?php echo $this->tmp_extra_column_headers?>
            <th width="320">
                <?php echo JText::_('JSHOP_SHIPPING_PRICES')?>
            </th>
            <th width="30" class="center">
              <?php echo JText::_('JSHOP_PUBLISH')?>
            </th>
            <th width="50" class="center">
                <?php echo JText::_('JSHOP_EDIT')?>
            </th>
            <th width="40" class="center">
                <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_ID'), 'shipping_id', $this->filter_order_Dir, $this->filter_order); ?>
            </th>
        </tr>
    </thead>
    <tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($this->filter_order_Dir); ?>" data-nested="false"<?php endif; ?>>
    <?php foreach($shippings as $i=>$shipping){ ?>
    <tr class="row<?php echo $i % 2; ?>" data-draggable-group="1" item-id="<?php echo $shipping->shipping_id; ?>" parents="" level="1">
        <td class="order text-center d-none d-md-table-cell">
            <span class="sortable-handler <?php if (!$saveOrder) echo 'inactive';?>">
                <span class="icon-ellipsis-v" aria-hidden="true"></span>
            </span>
            <?php if ($saveOrder){ ?>
                <input type="text" class="hidden" name="order[]" value="<?php echo $shipping->ordering; ?>">
            <?php } ?>
        </td>
        <td>
         <?php echo \JHTML::_('grid.id', $i, $shipping->shipping_id);?>
        </td>
        <td>
          <a href="index.php?option=com_jshopping&controller=shippings&task=edit&shipping_id=<?php echo $shipping->shipping_id; ?>">
              <?php if ($shipping->count_shipping_price==0){?>
                 <img src="components/com_jshopping/images/disabled.png" alt="disabled" title="<?php print JText::_('JSHOP_NOT_SET_PRICE')?>" />&nbsp;
             <?php }?>
             <?php echo $shipping->name;?>
          </a>
        </td>
        <?php echo $shipping->tmp_extra_column_cells?>
        <td>
         <a href="index.php?option=com_jshopping&controller=shippingsprices&shipping_id_back=<?php print $shipping->shipping_id;?>" class="btn btn-info"><?php echo JText::_('JSHOP_SHIPPING_PRICES')." (".$shipping->count_shipping_price.")"?> <img src="components/com_jshopping/images/tree.gif" border="0" /></a>
        </td>
        <td class="center">
         <?php echo \JHTML::_('jgrid.published', $shipping->published, $i);?>
        </td>
         <td class="center">
             <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=shippings&task=edit&shipping_id=<?php echo $shipping->shipping_id; ?>'>
                 <i class="icon-edit"></i>
             </a>
        </td>
        <td class="center">
         <?php print $shipping->shipping_id;?>
        </td>
    </tr>
    <?php $i++;} ?>
    </tbody>
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