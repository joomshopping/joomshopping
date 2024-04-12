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
$saveOrder = 1;
$saveOrderingUrl = 'index.php?option=com_jshopping&controller=attributesgroups&task=saveorder&tmpl=component&ajax=1';
Joomla\CMS\HTML\HTMLHelper::_('draggablelist.draggable');
$i = 0; 
?>
<div class="row">
<div class="col-md-12">
<div id="j-main-container" class="j-main-container">
    <?php JSHelperAdmin::displaySubmenuOptions("attributes");?>
    <form action="index.php?option=com_jshopping&controller=attributesgroups" method="post" name="adminForm" id="adminForm">
    <?php print $this->tmp_html_start?>
    <table class="table table-striped">
    <tr>
        <th scope="col" style="width:1%" class="text-center d-none d-md-table-cell">
            #
        </th>
        <th width="20">
          <input type="checkbox" name="checkall-toggle" value="" title="<?php echo \JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
        </th>
        <th align="left">
          <?php echo JText::_('JSHOP_TITLE')?>
        </th>
        <th width="50" class="center">
            <?php echo JText::_('JSHOP_EDIT')?>
        </th>
        <th width="40" class="center">
            <?php echo JText::_('JSHOP_ID')?>
        </th>
      </tr>
    </thead>
    <tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="asc" data-nested="false"<?php endif; ?>>
    <?php foreach ($rows as $row){?>
    <tr class="row<?php echo $i % 2; ?>" data-draggable-group="1" item-id="<?php echo $row->id; ?>" parents="" level="1">
        <td class="order text-center d-none d-md-table-cell">
            <span class="sortable-handler <?php if (!$saveOrder) echo 'inactive';?>">
                <span class="icon-ellipsis-v" aria-hidden="true"></span>
            </span>
            <?php if ($saveOrder){ ?>
                <input type="text" class="hidden" name="order[]" value="<?php echo $row->ordering; ?>">
            <?php } ?>
        </td>
       <td>
         <?php echo \JHTML::_('grid.id', $i, $row->id);?>
       </td>
       <td>
         <a href="index.php?option=com_jshopping&controller=attributesgroups&task=edit&id=<?php echo $row->id;?>"><?php echo $row->name;?></a>
       </td>
       <td class="center">
            <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=attributesgroups&task=edit&id=<?php print $row->id;?>'>
                <i class="icon-edit"></i>
            </a>
       </td>
       <td class="center">
        <?php print $row->id;?>
       </td>
    </tr>
    <?php
    $i++;
    }?>
    </tbody>
    </table>

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