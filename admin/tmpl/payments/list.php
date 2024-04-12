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
$count = count($rows);
$i = 0;
$saveOrder = $this->filter_order_Dir=="asc" && $this->filter_order=="payment_ordering";
if ($saveOrder){
    $saveOrderingUrl = 'index.php?option=com_jshopping&controller=payments&task=saveorder&tmpl=component&ajax=1';
	Joomla\CMS\HTML\HTMLHelper::_('draggablelist.draggable');
}
?>
<div class="row">
<div class="col-md-12">
<div id="j-main-container" class="j-main-container">
    <?php JSHelperAdmin::displaySubmenuOptions();?>
    <form action="index.php?option=com_jshopping&controller=payments" method="post" name="adminForm" id="adminForm">
    <?php print $this->tmp_html_start?>
    <table class="table table-striped" width="70%">
    <thead>
      <tr>
        <th scope="col" style="width:1%" class="text-center d-none d-md-table-cell">
            <?php echo \JHTML::_('grid.sort', $this->filter_order!='payment_ordering' ? '#' : '', 'payment_ordering', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
        <th width="20">
          <input type="checkbox" name="checkall-toggle" value="" title="<?php echo \JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
        </th>
        <th align="left">
          <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_TITLE'), 'name', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
        <th width="12%" align="left">
          <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_CODE'), 'payment_code', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
        <th width="15%" align="left">
          <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_ALIAS'), 'payment_class', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
        <?php echo $this->tmp_extra_column_headers?>
        <th width = "15%" align = "left">
            <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_SCRIPT_NAME'), 'scriptname', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
        <th width="50" class="center">
          <?php echo JText::_('JSHOP_PUBLISH')?>
        </th>
        <th width="50" class="center">
            <?php print JText::_('JSHOP_EDIT')?>
        </th>
        <th width="40" class="center">
            <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_ID'), 'payment_id', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
      </tr>
    </thead>
    <tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($this->filter_order_Dir); ?>" data-nested="false"<?php endif; ?>>
    <?php foreach($rows as $row){?>
    <tr class="row<?php echo $i % 2; ?>" data-draggable-group="1" item-id="<?php echo $row->payment_id; ?>" parents="" level="1">
        <td class="order text-center d-none d-md-table-cell">
            <span class="sortable-handler <?php if (!$saveOrder) echo 'inactive';?>">
                <span class="icon-ellipsis-v" aria-hidden="true"></span>
            </span>
            <?php if ($saveOrder){ ?>
                <input type="text" class="hidden" name="order[]" value="<?php echo $row->payment_ordering; ?>">
            <?php } ?>
        </td>
       <td>
         <?php echo \JHTML::_('grid.id', $i, $row->payment_id);?>
       </td>
       <td>
         <a title="<?php echo JText::_('JSHOP_EDIT_PAYMENT')?>" href="index.php?option=com_jshopping&controller=payments&task=edit&payment_id=<?php echo $row->payment_id; ?>"><?php echo $row->name;?></a>
       </td>
       <td>
         <?php echo $row->payment_code;?>
       </td>
       <td>
         <?php echo $row->payment_class;?>
       </td>
       <?php echo $row->tmp_extra_column_cells?>
       <td>
         <?php echo $row->scriptname;?>
       </td>
       <td class="center">
         <?php echo \JHTML::_('jgrid.published', $row->payment_publish, $i);?>
       </td>
       <td class="center">
            <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=payments&task=edit&payment_id=<?php print $row->payment_id?>'>
                <i class="icon-edit"></i>
            </a>
       </td>
       <td class="center">
            <?php print $row->payment_id;?>
       </td>
      </tr>
    <?php
    $i++;
    }
    ?>
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