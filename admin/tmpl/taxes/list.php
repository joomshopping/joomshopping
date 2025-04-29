<?php
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

/**
* @version      5.6.0 13.03.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$rows = $this->rows;
$i = 0;
$saveOrder = $this->filter_order_Dir=="asc" && $this->filter_order=="ordering";
if ($saveOrder){
    $saveOrderingUrl = 'index.php?option=com_jshopping&controller=taxes&task=saveorder&tmpl=component&ajax=1';
	Joomla\CMS\HTML\HTMLHelper::_('draggablelist.draggable');
}
?>

<div id="j-main-container" class="j-main-container">
<?php HelperAdmin::displaySubmenuOptions();?>
<form action="index.php?option=com_jshopping&controller=taxes" method="post" name="adminForm" id="adminForm">

<?php print $this->tmp_html_start?>

<table class="table table-striped">
<thead>
<tr>
    <th scope="col" style="width:1%" class="text-center d-none d-md-table-cell">
        <?php echo HTMLHelper::_('grid.sort', $this->filter_order!='ordering' ? '#' : '', 'ordering', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width="20">
      <input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
    </th>
    <th align="left">
      <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_TITLE'), 'tax_name', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width="200">
        <?php echo Text::_('JSHOP_EXTENDED_RULE_TAX')?>
    </th>
    <th width="50" class="center">
        <?php echo Text::_('JSHOP_EDIT')?>
    </th>
    <th width="40" class="center">
        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_ID'), 'tax_id', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
</tr>
</thead>
<tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($this->filter_order_Dir); ?>" data-nested="false"<?php endif; ?>>  
<?php foreach($rows as $row){ ?>  
<tr data-draggable-group="1" item-id="<?php echo $row->tax_id; ?>" parents="" level="1">
    <td class="order text-center d-none d-md-table-cell">
        <span class="sortable-handler <?php if (!$saveOrder) echo 'inactive';?>">
            <span class="icon-ellipsis-v" aria-hidden="true"></span>
        </span>
        <?php if ($saveOrder){ ?>
            <input type="text" class="hidden" name="order[]" value="<?php echo $row->ordering; ?>">
        <?php } ?>
    </td>
    <td>
        <?php echo HTMLHelper::_('grid.id', $i, $row->tax_id);?>
    </td>
    <td>
        <a href="index.php?option=com_jshopping&controller=taxes&task=edit&tax_id=<?php echo $row->tax_id; ?>"><?php echo $row->tax_name;?></a> (<?php echo $row->tax_value;?> %)
    </td>
    <td>
    <a class="btn btn-sm btn-info" href="index.php?option=com_jshopping&controller=exttaxes&back_tax_id=<?php echo $row->tax_id; ?>">
        <?php echo Text::_('JSHOP_EXTENDED_RULE_TAX')?>
    </a>
    </td>
    <td class="center">
        <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=taxes&task=edit&tax_id=<?php echo $row->tax_id; ?>'>
            <i class="icon-edit"></i>
        </a>
    </td>
    <td class="center">
        <?php print $row->tax_id;?>
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
<input type="hidden" name="task" value="<?php echo Factory::getApplication()->input->getVar('task')?>" />
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