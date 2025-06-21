<?php
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

/**
* @version      5.8.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$rows=$this->rows;
$attr_id=$this->attr_id;
$i=0;
$saveOrder = $this->filter_order_Dir=="asc" && $this->filter_order=="value_ordering";
if ($saveOrder){
    $saveOrderingUrl = 'index.php?option=com_jshopping&controller=attributesvalues&task=saveorder&tmpl=component&ajax=1';
	Joomla\CMS\HTML\HTMLHelper::_('draggablelist.draggable');
}
?>

<div id="j-main-container" class="j-main-container">
    <?php HelperAdmin::displaySubmenuOptions("attributes");?>
    <form action="index.php?option=com_jshopping&controller=attributesvalues&attr_id=<?php echo $attr_id?>" method="post" name="adminForm" id="adminForm">
    <?php print $this->tmp_html_start?>

    <div class="js-filters">
        <?php print $this->tmp_html_filter ?? ''?>        
        <div>
            <input name="filter[text_search]" value="<?php echo htmlspecialchars($this->filter['text_search'] ?? '');?>" class="form-control" placeholder="<?php print Text::_('JSHOP_SEARCH')?>" type="text">
        </div>
        <div>          
            <button type="submit" class="btn btn-primary hasTooltip" title="<?php print Text::_('JSHOP_SEARCH')?>">
                <span class="icon-search" aria-hidden="true"></span>
            </button>                
        </div>
        <div>
            <button type="button" class="btn btn-primary js-stools-btn-clear"><?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?></button>
        </div>
        <?php print $this->tmp_html_filter_end ?? ''?>      
    </div>

    <table class="table table-striped">
    <thead>
    <tr>
        <th scope="col" style="width:1%" class="text-center d-none d-md-table-cell">
            <?php echo HTMLHelper::_('grid.sort', $this->filter_order!='value_ordering' ? '#' : '', 'value_ordering', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
        <th width="20">
          <input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
        </th>
        <th align="left">
          <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_NAME_ATTRIBUT_VALUE'), 'name', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
        <th width="120" align="left">
          <?php echo Text::_('JSHOP_IMAGE_ATTRIBUT_VALUE')?>
        </th>
	    <?php if ($this->config->shop_mode == 1){?>
            <th width="50" class="center">
			    <?php echo Text::_('JSHOP_PRODUCTS')?>
            </th>
	    <?php }?>
        <th width="50" class="center">
            <?php echo Text::_('JSHOP_PUBLISH')?>
        </th>
        <th width="50" class="center">
            <?php echo Text::_('JSHOP_EDIT')?>
        </th>
        <th width="40" class="center">
            <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_ID'), 'value_id', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
    </tr>
    </thead>
    <tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="asc" data-nested="false"<?php endif; ?>>
    <?php foreach ($rows as $row){ ?>
    <tr class="row<?php echo $i % 2; ?>" data-draggable-group="1" item-id="<?php echo $row->value_id; ?>" parents="" level="1">
        <td class="order text-center d-none d-md-table-cell">
            <span class="sortable-handler <?php if (!$saveOrder) echo 'inactive';?>">
                <span class="icon-ellipsis-v" aria-hidden="true"></span>
            </span>
            <?php if ($saveOrder){ ?>
                <input type="text" class="hidden" name="order[]" value="<?php echo $row->value_ordering; ?>">
            <?php } ?>
        </td>
       <td>
         <?php echo HTMLHelper::_('grid.id', $i, $row->value_id);?>
       </td>
       <td>
	       <?php if (!$row->count_products){?><i class="icon icon-exclamation-circle" title="<?php print Text::_('JSHOP_ITEM_NOT_USED')?>"></i><?php }?>
           <a href="index.php?option=com_jshopping&controller=attributesvalues&task=edit&value_id=<?php echo $row->value_id; ?>&attr_id=<?php echo $attr_id?>"><?php echo $row->name;?></a>
       </td>
       <td>
         <?php if ($row->image) {?>
           <img src="<?php echo $this->config->image_attributes_live_path."/".$row->image?>"  alt="" width="20" height="20" />
         <?php }?>
       </td>
        <?php if ($this->config->shop_mode == 1){?>
            <td class="center">
                <?php echo $row->count_products?>
            </td>
        <?php }?>
        <td class="center">
            <?php echo HTMLHelper::_('jgrid.published', $row->publish, $i);?>
        </td>
        <td class="center">
            <a class="btn btn-micro btn-nopad" href="index.php?option=com_jshopping&controller=attributesvalues&task=edit&value_id=<?php echo $row->value_id; ?>&attr_id=<?php echo $attr_id?>">
                <i class="icon-edit"></i>
            </a>
        </td>
        <td class="center">
            <?php print $row->value_id;?>
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
<script>
jQuery(function(){
	jshopAdmin.setMainMenuActive('<?php print Uri::base()?>index.php?option=com_jshopping&controller=other');
});
</script>