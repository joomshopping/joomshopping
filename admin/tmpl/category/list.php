<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
$categories = $this->categories;
//print_r($categories); die();
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
<div class="row">
    <div class="col-md-12">
        <div id="j-main-container" class="j-main-container">
        <form action="index.php?option=com_jshopping&controller=categories" method="post" enctype="multipart/form-data" name="adminForm" id="adminForm">
        <?php print $this->tmp_html_start?>        
        
        <div class="js-stools clearfix">
            <div class="js-stools-container-bar">
                <div class="btn-toolbar" role="toolbar">
                    <?php print $this->tmp_html_filter?>
                    <div class="btn-group">
                        <div class="input-group"> 
                            <div class="js-stools-field-filter">                          
                                <input name="text_search" id="text_search" value="<?php echo htmlspecialchars($text_search);?>" class="form-control" placeholder="<?php print JText::_('JSHOP_SEARCH')?>" type="text">
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
            <th width="200" align="left">
              <?php echo \JHTML::_('grid.sort', 'JSHOP_TITLE', 'name', $this->filter_order_Dir, $this->filter_order); ?>
            </th>
            <?php print $this->tmp_html_col_after_title?>
            <th align="left">
              <?php echo \JHTML::_('grid.sort', 'JSHOP_DESCRIPTION', 'description', $this->filter_order_Dir, $this->filter_order); ?>
            </th>
            <th width="80" align="left">
              <?php echo JText::_('JSHOP_CATEGORY_PRODUCTS')?>
            </th>
            <th width="50" class="center">
              <?php echo JText::_('JSHOP_PUBLISH')?>
            </th>
            <th width="50" class="center">
                <?php echo JText::_('JSHOP_EDIT')?>
            </th>
            <th width="50" class="center">
                <?php echo JText::_('JSHOP_DELETE')?>
            </th>
            <th width="50" class="center">
                <?php echo \JHTML::_( 'grid.sort', 'JSHOP_ID', 'id', $this->filter_order_Dir, $this->filter_order); ?>
            </th>
        </tr>
        </thead>
        <tbody <?php if ($saveOrder) :?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($this->filter_order_Dir); ?>" data-nested="true"<?php endif; ?>>
        <?php foreach($categories as $category) { ?>
        <tr class="row<?php echo $i % 2; ?>" data-draggable-group="<?php print $category->category_parent_id?>"
            item-id="<?php echo $category->category_id; ?>" parents="<?php echo $category->parentsStr; ?>" level="<?php print $category->level?>">
            <td class="order text-center d-none d-md-table-cell">
                <span class="sortable-handler <?php if (!$saveOrder) echo 'inactive';?>">
                    <span class="icon-ellipsis-v" aria-hidden="true"></span>
                </span>
                <?php if ($saveOrder){ ?>
                    <input type="text" class="hidden" name="order[]" value="<?php echo $category->ordering; ?>">
                <?php } ?>
            </td>
            <td>
             <?php echo \JHTML::_('grid.id', $i, $category->category_id);?>
            </td>
            <td>
             <?php print $category->space; ?><a href = "index.php?option=com_jshopping&controller=categories&task=edit&category_id=<?php echo $category->category_id; ?>"><?php echo $category->name;?></a>
            </td>
            <?php print $category->tmp_html_col_after_title?>
            <td>
             <?php echo $category->short_description;?>
            </td>
            <td align="center">
             <?php if (isset($this->countproducts[$category->category_id])){?>
             <a href="index.php?option=com_jshopping&controller=products&category_id=<?php echo $category->category_id?>">
               (<?php print intval($this->countproducts[$category->category_id]);?>) <img src="components/com_jshopping/images/tree.gif" border="0" />
             </a>
             <?php }else{?>
             (0)
             <?php }?>
            </td>
            <td class="center">
             <?php echo \JHTML::_('jgrid.published', $category->category_publish, $i);?>
            </td>
            <td class="center">
                <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=categories&task=edit&category_id=<?php print $category->category_id?>'>
                    <i class="icon-edit"></i>
                </a>
            </td>
            <td class="center">
                <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=categories&task=remove&cid[]=<?php print $category->category_id?>' onclick="return confirm('<?php print JText::_('JSHOP_DELETE')?>');">
                    <i class="icon-delete"></i>
                </a>
            </td>
            <td class="center">
            <?php print $category->category_id?>
            </td>
        </tr>
        <?php $i++; } ?>
        </tbody>
        <tfoot>
        <tr>
            <?php print $this->tmp_html_col_before_td_foot?>
            <td colspan = "12">
                <div class = "jshop_list_footer"><?php echo $pageNav->getListFooter(); ?></div>
                <div class = "jshop_limit_box"><?php echo $pageNav->getLimitBox(); ?></div>
            </td>
            <?php print $this->tmp_html_col_after_td_foot?>
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