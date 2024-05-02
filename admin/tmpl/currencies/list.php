<?php
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$rows = $this->rows;
$i = 0;
$saveOrder = $this->filter_order_Dir=="asc" && $this->filter_order=="currency_ordering";
if ($saveOrder){
    $saveOrderingUrl = 'index.php?option=com_jshopping&controller=currencies&task=saveorder&tmpl=component&ajax=1';
	Joomla\CMS\HTML\HTMLHelper::_('draggablelist.draggable');
}
?>

<div id="j-main-container" class="j-main-container">
    <?php HelperAdmin::displaySubmenuOptions("currencies");?>
    <form action="index.php?option=com_jshopping&controller=currencies" method="post" name="adminForm" id="adminForm">
    <?php print $this->tmp_html_start?>
    <table class="table table-striped">
    <thead>
        <tr>
            <th scope="col" style="width:1%" class="text-center d-none d-md-table-cell">
                <?php echo HTMLHelper::_('grid.sort', $this->filter_order!='currency_ordering' ? '#' : '', 'currency_ordering', $this->filter_order_Dir, $this->filter_order); ?>
            </th>
            <th width="20">
              <input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
            </th>
            <th align="left">
              <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_TITLE'), 'currency_name', $this->filter_order_Dir, $this->filter_order); ?>
            </th>
            <th width="60" class="center">
                <?php echo Text::_('JSHOP_DEFAULT')?>
            </th>
            <th width="100">
                <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_VALUE_CURRENCY'), 'currency_value', $this->filter_order_Dir, $this->filter_order); ?>
            </th>
            <th width="30" class="center">
              <?php echo Text::_('JSHOP_PUBLISH')?>
            </th>
            <th width="50" class="center">
                <?php print Text::_('JSHOP_EDIT')?>
            </th>
            <th width="40" class="center">
                <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_ID'), 'currency_id', $this->filter_order_Dir, $this->filter_order); ?>
            </th>
        </tr>
    </thead>
    <tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($this->filter_order_Dir); ?>" data-nested="false"<?php endif; ?>>
    <?php foreach($rows as $row){ ?>
    <tr class="row<?php echo $i % 2; ?>" data-draggable-group="1" item-id="<?php echo $row->currency_id; ?>" parents="" level="1">
        <td class="order text-center d-none d-md-table-cell">
            <span class="sortable-handler <?php if (!$saveOrder) echo 'inactive';?>">
                <span class="icon-ellipsis-v" aria-hidden="true"></span>
            </span>
            <?php if ($saveOrder){ ?>
                <input type="text" class="hidden" name="order[]" value="<?php echo $row->currency_ordering; ?>">
            <?php } ?>
        </td>
        <td>
          <?php echo HTMLHelper::_('grid.id', $i, $row->currency_id);?>
        </td>
        <td>
          <a title="<?php echo Text::_('JSHOP_EDIT_CURRENCY')?>" href="index.php?option=com_jshopping&controller=currencies&task=edit&currency_id=<?php echo $row->currency_id; ?>"><?php echo $row->currency_name;?></a>
        </td>
        <td class="center">
          <?php if ($this->config->mainCurrency==$row->currency_id) {?>
             <a class="btn btn-micro btn-nopad">
                 <i class="icon-default"></i>
             </a>
          <?php }?>
        </td>
        <td align="center">
            <?php echo $row->currency_value;?>
        </td>
        <td class="center">
          <?php echo HTMLHelper::_('jgrid.published', $row->currency_publish, $i);?>
        </td>
        <td class="center">
             <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=currencies&task=edit&currency_id=<?php print $row->currency_id?>'>
                 <i class="icon-edit"></i>
             </a>
        </td>
        <td class="center">
             <?php print $row->currency_id;?>
        </td>
    </tr>
    <?php
    $i++;
    }
    ?>
    </tbody>
    </table>

    <input type="hidden" name="filter_order" value="<?php echo $this->filter_order?>" >
    <input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir?>" >
    <input type="hidden" name="task" value="<?php echo Factory::getApplication()->input->getVar('task')?>" >
    <input type="hidden" name="hidemainmenu" value="0" >
    <input type="hidden" name="boxchecked" value="0" >
    <?php print $this->tmp_html_end?>
    </form>
</div>
<script>
jQuery(function(){
	jshopAdmin.setMainMenuActive('<?php print Uri::base()?>index.php?option=com_jshopping&controller=other');
});
</script>