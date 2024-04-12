<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$rows=$this->rows; $count=count ($rows); $i=0;
$lists=$this->lists;
$saveOrder = $this->filter_order_Dir == "asc" && $this->filter_order == "F.ordering";
if ($saveOrder){
    $saveOrderingUrl = 'index.php?option=com_jshopping&controller=productfields&task=saveorder&tmpl=component&ajax=1';
	Joomla\CMS\HTML\HTMLHelper::_('draggablelist.draggable');
}
?>
<div class="row">
<div class="col-md-12">
<div id="j-main-container" class="j-main-container">
    <?php JSHelperAdmin::displaySubmenuOptions();?>
    <form action="index.php?option=com_jshopping&controller=productfields" method="post" name="adminForm" id="adminForm">

    <?php print $this->tmp_html_start?>

    <div class="js-stools clearfix jshop_block_filter">
        <div class="js-stools-container-bar">
            <div class="btn-toolbar" role="toolbar">
                <?php print $this->tmp_html_filter?>

                <div class="btn-group">
                    <div class="input-group">
                        <div class="js-stools-field-filter">
                            <?php print $lists['group']?>
                        </div>

                        <div class="js-stools-field-filter">
                            <?php print $lists['treecategories']?>
                        </div>
                    </div>
                </div>

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

    <table class="table table-striped">
    <thead>
    <tr>
        <th scope="col" style="width:1%" class="text-center d-none d-md-table-cell">
            <?php echo \JHTML::_('grid.sort', $this->filter_order!='F.ordering' ? '#' : '', 'F.ordering', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
        <th width="20">
          <input type="checkbox" name="checkall-toggle" value="" title="<?php echo \JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
        </th>
        <th width="200" align="left">
          <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_TITLE'), 'name', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
        <th align="left">
          <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_TYPE'), 'F.type', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
        <th align="left">
          <?php echo JText::_('JSHOP_OPTIONS')?>
        </th>
        <th align="left">
          <?php echo JText::_('JSHOP_CATEGORIES')?>
        </th>
        <th align="left">
          <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_GROUP'), 'groupname', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
        <th width="50" class="center">
            <?php echo JText::_('JSHOP_EDIT')?>
        </th>
        <th width="40" class="center">
            <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_ID'), 'id', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
      </tr>
    </thead>
    <tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($this->filter_order_Dir); ?>" data-nested="false"<?php endif; ?>>
    <?php foreach ($rows as $row){ ?>
    <tr class="row<?php echo $i % 2; ?>" data-draggable-group="1" item-id="<?php echo $row->id; ?>" parents="" level="1">
        <td class="order text-center d-none d-md-table-cell">
            <span class="sortable-handler <?php if (!$saveOrder) echo 'inactive';?>">
                <span class="icon-ellipsis-v" aria-hidden="true"></span>
            </span>
            <?php if ($saveOrder){ ?>
                <input type="text" class="hidden" name="order[]" value="<?php echo $row->ordering; /*echo $i + 1;*/ ?>">
            <?php } ?>
        </td>
       <td>
         <?php echo \JHTML::_('grid.id', $i, $row->id);?>
       </td>
       <td>
         <?php if (!$row->count_option && $row->type==0) {?><img src="components/com_jshopping/images/icon-16-denyinactive.png" alt="" /><?php }?>
         <a href="index.php?option=com_jshopping&controller=productfields&task=edit&id=<?php echo $row->id; ?>"><?php echo $row->name;?></a>
       </td>
       <td>
         <?php print $this->types[$row->type];?>
       </td>
       <td>
        <?php if ($row->type==0){?>
         <a href="index.php?option=com_jshopping&controller=productfieldvalues&field_id=<?php echo $row->id?>"><?php echo JText::_('JSHOP_OPTIONS')?></a>
         (<?php if (isset($this->vals[$row->id]) && is_array($this->vals[$row->id])) echo implode(", ", $this->vals[$row->id]);?>)
         <?php }else{?>
            -
         <?php }?>
       </td>
       <td>
        <?php print $row->printcat;?>
       </td>
       <td>
        <?php print $row->groupname;?>
       </td>
       <td class="center">
            <a class="btn btn-micro btn-nopad"  href='index.php?option=com_jshopping&controller=productfields&task=edit&id=<?php print $row->id;?>'>
                <i class="icon-edit"></i>
            </a>
       </td>
       <td class="center">
        <?php print $row->id;?>
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