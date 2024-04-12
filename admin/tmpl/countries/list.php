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
$pageNav = $this->pageNav;
$i = 0;
$saveOrder = $this->filter_order_Dir=="asc" && $this->filter_order=="ordering";
if ($saveOrder){
    $saveOrderingUrl = 'index.php?option=com_jshopping&controller=countries&task=saveorder&tmpl=component&ajax=1';
	Joomla\CMS\HTML\HTMLHelper::_('draggablelist.draggable');
}
?>
<div class="row">
<div class="col-md-12">
<div id="j-main-container" class="j-main-container">
    <?php JSHelperAdmin::displaySubmenuOptions();?>
    <form action="index.php?option=com_jshopping&controller=countries" method="post" name="adminForm" id="adminForm">

    <?php print $this->tmp_html_start?>

    <div class="js-stools clearfix jshop_block_filter">
        <div class="js-stools-container-bar">
            <div class="btn-toolbar" role="toolbar">
                <?php print $this->tmp_html_filter?>

                <div class="btn-group mr-2">
                    <div class="input-group">
                        <div class="js-stools-field-filter">
                            <label><?php print JText::_('JSHOP_SHOW')?>:</label>
                            <div class="control"><?php print $this->filter;?></div>
                        </div>
                    </div>
                </div>

                <?php print $this->tmp_html_filter_end?>
            </div>
        </div>
    </div>

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
          <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_COUNTRY'), 'name', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
        <th width="90">
          <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_CODE'), 'country_code', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
        <th width="90">
          <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_CODE'). '2', 'country_code_2', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
        <th width="50" class="center">
          <?php echo JText::_('JSHOP_PUBLISH')?>
        </th>
        <th width="50" class="center">
            <?php print JText::_('JSHOP_EDIT')?>
        </th>
        <th width="50" class="center">
          <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_ID'), 'country_id', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
    </tr>
    </thead>
    <tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($this->filter_order_Dir); ?>" data-nested="false"<?php endif; ?>>
    <?php foreach($rows as $row){ ?>
    <tr class="row<?php echo $i % 2; ?>" data-draggable-group="1" item-id="<?php echo $row->country_id; ?>" parents="" level="1">
        <td class="order text-center d-none d-md-table-cell">
            <span class="sortable-handler <?php if (!$saveOrder) echo 'inactive';?>">
                <span class="icon-ellipsis-v" aria-hidden="true"></span>
            </span>
            <?php if ($saveOrder){ ?>
                <input type="text" class="hidden" name="order[]" value="<?php echo $row->ordering; ?>">
            <?php } ?>
        </td>
       <td>
         <?php echo \JHTML::_('grid.id', $i, $row->country_id);?>
       </td>
       <td>
         <a href="index.php?option=com_jshopping&controller=countries&task=edit&country_id=<?php echo $row->country_id; ?>"><?php echo $row->name;?></a>
       </td>
       <td align="center">
         <?php echo $row->country_code;?>
       </td>
       <td align="center">
         <?php echo $row->country_code_2;?>
       </td>
       <td class="center">
         <?php echo \JHTML::_('jgrid.published', $row->country_publish, $i);?>
       </td>
        <td class="center">
            <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=countries&task=edit&country_id=<?php print $row->country_id;?>'>
                <i class="icon-edit"></i>
            </a>
        </td>
        <td class="center">
         <?php echo $row->country_id;?>
       </td>
      </tr>
    <?php
    $i++;
    }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <td colspan="11">
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