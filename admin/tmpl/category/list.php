<?php
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

/**
* @version      5.8.4 15.11.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
$categories = $this->categories;
$dtype = (int)($this->dtype ?? 0);
$i = 0;
$count = count($categories); 
$pageNav = $this->pagination;
$saveOrder = $this->filter_order_Dir=="asc" && $this->filter_order=="ordering";
if ($saveOrder){
    $saveOrderingUrl = 'index.php?option=com_jshopping&controller=categories&task=saveorder&tmpl=component&ajax=1';
	Joomla\CMS\HTML\HTMLHelper::_('draggablelist.draggable');
}
?>
<div id="j-main-container" class="j-main-container">
    <?php if (!empty($this->active_name_tree)) { ?>
    <div class="mb-2 cat-try-header">
        <a href="index.php?option=com_jshopping&controller=categories&catid=0"><?php echo Text::_('JSHOP_CATEGORIES'); ?></a>
        <?php foreach ($this->active_name_tree as $catId => $cat) { ?>
        / <a href="index.php?option=com_jshopping&controller=categories&catid=<?php echo (int)$catId; ?>"><?php echo htmlspecialchars($cat); ?></a>
        <?php } ?>
    </div>
    <?php } ?>
    <form action="index.php?option=com_jshopping&controller=categories" method="post" enctype="multipart/form-data"
        name="adminForm" id="adminForm">
        <?php print $this->tmp_html_start?>

        <div class="js-filters">
            <?php print $this->tmp_html_filter?>
            <div>
                <?php print $this->filterinput['publish']?>
            </div>
            <div>
                <input name="filter[text_search]" value="<?php echo htmlspecialchars($this->ifilter['text_search'] ?? '');?>" class="form-control" placeholder="<?php print Text::_('JSHOP_SEARCH')?>" type="text">
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
            <div class="btn-group">
                <button type="button" class="btn btn-outline-primary hasTooltip<?php echo $dtype == 0 ? ' active' : ''; ?>"
                    title="<?php echo Text::_('JSHOP_DISPLAY_TREE'); ?>"
                    onclick="jshopSetDtype(0)">
                    <span class="icon-tree" aria-hidden="true"></span>
                </button>
                <button type="button" class="btn btn-outline-primary hasTooltip<?php echo $dtype == 1 ? ' active' : ''; ?>"
                    title="<?php echo Text::_('JSHOP_DISPLAY_LIST'); ?>"
                    onclick="jshopSetDtype(1)">
                    <span class="icon-list" aria-hidden="true"></span>
                </button>
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
                    <th width="93" align="left">
                        <?php echo Text::_('JSHOP_IMAGE')?>
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
                    item-id="<?php echo $category->category_id; ?>" parents="<?php echo $category->parentsStr ?? ''; ?>"
                    level="<?php print $category->level ?? '';?>">
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
                        <?php if ($category->category_image) { ?>
                        <a href="index.php?option=com_jshopping&controller=categories&task=edit&category_id=<?php echo $category->category_id; ?>">
                            <img src="<?php echo $this->config->image_category_live_path . '/' . $category->category_image ?>" width="60" border="0" />
                        </a>
                        <?php } ?>
                    </td>
                    <td>
                        <?php if ($dtype == 0) print $category->space ?? ''; ?><a
                            href="index.php?option=com_jshopping&controller=categories&task=edit&category_id=<?php echo $category->category_id; ?>"><?php echo $category->name;?></a>
                        <?php if (!empty($category->has_subcat)) { ?>
                        <a class="ms-2 btn btn-primary btn-sm" href="index.php?option=com_jshopping&controller=categories&catid=<?php echo $category->category_id; ?>">
                            <span class="icon-tree" aria-hidden="true"></span>
                        </a>
                        <?php } ?>
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

        <input type="hidden" id="js-dtype" name="dtype" value="<?php echo $dtype; ?>" />
        <input type="hidden" name="filter_order" value="<?php echo $this->filter_order?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir?>" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="hidemainmenu" value="0" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php print $this->tmp_html_end?>
    </form>
</div>
<script>
function jshopSetDtype(v) {
    document.getElementById('js-dtype').value = v;
    document.adminForm.submit();
}
jQuery(function() {
    jshopAdmin.setMainMenuActive(
        '<?php print Uri::base()?>index.php?option=com_jshopping&controller=categories&catid=0');
});
</script>