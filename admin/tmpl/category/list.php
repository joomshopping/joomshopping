<?php
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

/**
* @version      5.3.0 14.12.203
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
$categories = $this->categories;
$i = 0;
$text_search = $this->text_search;
$count = count($categories); 
$pageNav = $this->pagination;
$saveOrder = $this->filter_order_Dir=="asc" && $this->filter_order=="ordering";
if ($saveOrder){
    $saveOrderingUrl = 'index.php?option=com_jshopping&controller=categories&task=saveorder&tmpl=component&ajax=1';
	Joomla\CMS\HTML\HTMLHelper::_('draggablelist.draggable');
}
?>
<div id="j-main-container" class="j-main-container">
    <form action="index.php?option=com_jshopping&controller=categories" method="post" enctype="multipart/form-data"
        name="adminForm" id="adminForm">
        <?php print $this->tmp_html_start?>

        <div class="js-filters">
            <?php print $this->tmp_html_filter?>
            <div>
                <input name="text_search" id="text_search" value="<?php echo htmlspecialchars($text_search);?>"
                    class="form-control" placeholder="<?php print Text::_('JSHOP_SEARCH')?>" type="text">
            </div>
            <div>
                <button type="submit" class="btn btn-primary hasTooltip" title="<?php print Text::_('JSHOP_SEARCH')?>">
                    <span class="icon-search" aria-hidden="true"></span>
                </button>
            </div>
            <div>
                <button type="button"
                    class="btn btn-primary js-stools-btn-clear"><?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?></button>
            </div>
        </div>

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
                    <th align="left">
                        <?php echo HTMLHelper::_('grid.sort', 'JSHOP_TITLE', 'name', $this->filter_order_Dir, $this->filter_order); ?>
                    </th>
                    <?php print $this->tmp_html_col_after_title?>
                    <th align="left">
                        <?php echo HTMLHelper::_('grid.sort', 'JSHOP_DESCRIPTION', 'description', $this->filter_order_Dir, $this->filter_order); ?>
                    </th>
                    <th width="80" align="left">
                        <?php echo Text::_('JSHOP_CATEGORY_PRODUCTS')?>
                    </th>
                    <th width="50" class="center">
                        <?php echo Text::_('JSHOP_PUBLISH')?>
                    </th>
                    <th width="50" class="center">
                        <?php echo Text::_('JSHOP_EDIT')?>
                    </th>
                    <th width="50" class="center">
                        <?php echo Text::_('JSHOP_DELETE')?>
                    </th>
                    <th width="50" class="center">
                        <?php echo HTMLHelper::_( 'grid.sort', 'JSHOP_ID', 'id', $this->filter_order_Dir, $this->filter_order); ?>
                    </th>
                </tr>
            </thead>
            <tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>"
                data-direction="<?php echo strtolower($this->filter_order_Dir); ?>" data-nested="true" <?php endif; ?>>
                <?php foreach($categories as $category) { ?>
                <tr class="row<?php echo $i % 2; ?>" data-draggable-group="<?php print $category->category_parent_id?>"
                    item-id="<?php echo $category->category_id; ?>" parents="<?php echo $category->parentsStr; ?>"
                    level="<?php print $category->level?>">
                    <td class="order text-center d-none d-md-table-cell">
                        <span class="sortable-handler <?php if (!$saveOrder) echo 'inactive';?>">
                            <span class="icon-ellipsis-v" aria-hidden="true"></span>
                        </span>
                        <?php if ($saveOrder){ ?>
                        <input type="text" class="hidden" name="order[]" value="<?php echo $category->ordering; ?>">
                        <?php } ?>
                    </td>
                    <td>
                        <?php echo HTMLHelper::_('grid.id', $i, $category->category_id);?>
                    </td>
                    <td>
                        <?php print $category->space; ?><a
                            href="index.php?option=com_jshopping&controller=categories&task=edit&category_id=<?php echo $category->category_id; ?>"><?php echo $category->name;?></a>
                    </td>
                    <?php print $category->tmp_html_col_after_title?>
                    <td>
                        <?php echo $category->short_description;?>
                    </td>
                    <td align="center">
                        <?php if (isset($this->countproducts[$category->category_id])){?>
                        <a
                            href="index.php?option=com_jshopping&controller=products&category_id=<?php echo $category->category_id?>">
                            (<?php print intval($this->countproducts[$category->category_id]);?>)
                        </a>
                        <?php }else{?>
                        (0)
                        <?php }?>
                    </td>
                    <td class="center">
                        <?php echo HTMLHelper::_('jgrid.published', $category->category_publish, $i);?>
                    </td>
                    <td class="center">
                        <a class="btn btn-micro btn-nopad"
                            href='index.php?option=com_jshopping&controller=categories&task=edit&category_id=<?php print $category->category_id?>'>
                            <i class="icon-edit"></i>
                        </a>
                    </td>
                    <td class="center">
                        <a class="btn btn-micro btn-nopad"
                            href='index.php?option=com_jshopping&controller=categories&task=remove&cid[]=<?php print $category->category_id?>'
                            onclick="return confirm('<?php print Text::_('JSHOP_DELETE')?>');">
                            <i class="icon-delete"></i>
                        </a>
                    </td>
                    <td class="center">
                        <?php print $category->category_id?>
                    </td>
                </tr>
                <?php $i++; } ?>
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
jQuery(function() {
    jshopAdmin.setMainMenuActive(
        '<?php print Uri::base()?>index.php?option=com_jshopping&controller=categories&catid=0');
});
</script>