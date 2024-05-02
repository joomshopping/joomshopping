<?php 
/**
* @version      5.4.1 18.04.2024
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die();

$rows=$this->rows;
$i=0;
$saveOrder = ($this->filter_order_Dir=="asc" && $this->filter_order=="ordering");
if ($saveOrder){
    $saveOrderingUrl = 'index.php?option=com_jshopping&controller=orderstatus&task=saveorder&tmpl=component&ajax=1';
	HTMLHelper::_('draggablelist.draggable');
}
?>
<div id="j-main-container" class="j-main-container">
    <?php HelperAdmin::displaySubmenuOptions();?>
    <form action="index.php?option=com_jshopping&controller=orderstatus" method="post" name="adminForm" id="adminForm">
        <?php print $this->tmp_html_start?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col" style="width:1%" class="text-center d-none d-md-table-cell">
                        <?php echo HTMLHelper::_('grid.sort', $this->filter_order!='ordering' ? '#' : '', 'ordering', $this->filter_order_Dir, $this->filter_order); ?>
                    </th>
                    <th width="20">
                        <input type="checkbox" name="checkall-toggle" value=""
                            title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>
                    <th width="200" align="left">
                        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_TITLE'), 'name', $this->filter_order_Dir, $this->filter_order); ?>
                    </th>
                    <th align="left">
                        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_CODE'), 'status_code', $this->filter_order_Dir, $this->filter_order); ?>
                    </th>
                    <th width="50" class="center">
                        <?php echo Text::_('JSHOP_EDIT')?>
                    </th>
                    <th width="40" class="center">
                        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_ID'), 'status_id', $this->filter_order_Dir, $this->filter_order); ?>
                    </th>
                </tr>
            </thead>
            <tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>"
                data-direction="<?php echo strtolower($this->filter_order_Dir); ?>" data-nested="false" <?php endif; ?>>
                <?php foreach($rows as $row){ ?>
                <tr class="row<?php echo $i % 2; ?>" data-draggable-group="1" item-id="<?php echo $row->status_id;?>"
                    parents="" level="1">
                    <td class="order text-center d-none d-md-table-cell">
                        <span class="sortable-handler <?php if (!$saveOrder) echo 'inactive';?>">
                            <span class="icon-ellipsis-v" aria-hidden="true"></span>
                        </span>
                        <?php if ($saveOrder){ ?>
                        <input type="text" class="hidden" name="order[]" value="<?php echo $row->ordering; ?>">
                        <?php } ?>
                    </td>
                    <td>
                        <?php echo HTMLHelper::_('grid.id', $i, $row->status_id);?>
                    </td>
                    <td>
                        <a title="<?php echo Text::_('JSHOP_EDIT_ORDER_STATUS')?>"
                            href="index.php?option=com_jshopping&controller=orderstatus&task=edit&status_id=<?php echo $row->status_id; ?>"><?php echo $row->name;?></a>
                    </td>
                    <td>
                        <?php echo $row->status_code;?>
                    </td>
                    <td class="center">
                        <a class="btn btn-micro btn-nopad"
                            href='index.php?option=com_jshopping&controller=orderstatus&task=edit&status_id=<?php echo $row->status_id; ?>'>
                            <i class="icon-edit"></i>
                        </a>
                    </td>
                    <td class="center">
                        <?php print $row->status_id;?>
                    </td>
                </tr>
                <?php
                $i++;
                }
                ?>
            </tbody>
        </table>

        <input type="hidden" name="filter_order" value="<?php echo $this->filter_order?>">
        <input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir?>">
        <input type="hidden" name="task" value="">
        <input type="hidden" name="hidemainmenu" value="0">
        <input type="hidden" name="boxchecked" value="0">
        <?php print $this->tmp_html_end?>
    </form>
</div>
<script>
jQuery(function() {
    jQuery('ul li a.mm-active').removeClass('mm-active');
    jshopAdmin.setMainMenuActive('<?php print Uri::base()?>index.php?option=com_jshopping&controller=other');
});
</script>