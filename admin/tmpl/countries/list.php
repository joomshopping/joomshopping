<?php
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

/**
* @version      5.6.0 15.09.2018
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

<div id="j-main-container" class="j-main-container">
    <?php HelperAdmin::displaySubmenuOptions();?>
    <form action="index.php?option=com_jshopping&controller=countries" method="post" name="adminForm" id="adminForm">

    <?php print $this->tmp_html_start?>

    <div class="js-filters">
        <?php print $this->tmp_html_filter?>
        <div>
            <?php print $this->filter;?>
        </div>
        <div>
            <input name="text_search" id="text_search" value="<?php echo htmlspecialchars($this->text_search);?>"
                class="form-control" placeholder="<?php print Text::_('JSHOP_SEARCH')?>" type="text">
        </div>
        <div>          
            <button type="submit" class="btn btn-primary hasTooltip" title="<?php print Text::_('JSHOP_SEARCH')?>">
                <span class="icon-search" aria-hidden="true"></span>
            </button>                
        </div>
        <div>
            <button type="button" class="btn btn-primary js-stools-btn-clear"><?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?></button>
        </div>
        <?php print $this->tmp_html_filter_end?>      
    </div>

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
          <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_COUNTRY'), 'name', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
        <th width="90">
          <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_CODE'), 'country_code', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
        <th width="90">
          <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_CODE'). '2', 'country_code_2', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
        <th width="50" class="center">
          <?php echo Text::_('JSHOP_PUBLISH')?>
        </th>
        <th width="50" class="center">
            <?php print Text::_('JSHOP_EDIT')?>
        </th>
        <th width="50" class="center">
          <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_ID'), 'country_id', $this->filter_order_Dir, $this->filter_order); ?>
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
         <?php echo HTMLHelper::_('grid.id', $i, $row->country_id);?>
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
         <?php echo HTMLHelper::_('jgrid.published', $row->country_publish, $i);?>
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