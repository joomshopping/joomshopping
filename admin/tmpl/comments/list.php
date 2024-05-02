<?php
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$count = count($this->reviews);
$i = 0;
?>

<div id="j-main-container" class="j-main-container">
<?php HelperAdmin::displaySubmenuOptions();?>
<form action="index.php?option=com_jshopping&controller=reviews" method="post" name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>

<div class="js-filters">
    <?php print $this->tmp_html_filter?>

    <div>
        <?php echo $this->categories;?>
    </div>
    <div>
        <?php echo $this->products_select;?>
    </div>

    <div>
        <input name="text_search" id="text_search" value="<?php echo htmlspecialchars($this->text_search);?>" class="form-control" placeholder="<?php print Text::_('JSHOP_SEARCH')?>" type="text">
    </div>
    <div>
        <span class="input-group-append">
            <button type="submit" class="btn btn-primary hasTooltip" title="<?php print Text::_('JSHOP_SEARCH')?>">
                <span class="icon-search" aria-hidden="true"></span>
            </button>                        
        </span>
    </div>

    <div>
        <button type="button" class="btn btn-primary js-stools-btn-clear"><?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?></button>
    </div>
    <?php print $this->tmp_html_filter_end?>
</div>

<table class="table table-striped" >
<thead> 
  <tr>
    <th class="title" width ="10">
      #
    </th>
    <th width="20">
      <input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
    </th>
    <th width = "200" align = "left">
        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_NAME_PRODUCT'), 'name', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th>
        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_USER'), 'pr_rew.user_name', $this->filter_order_Dir, $this->filter_order); ?>
    </th>        
    <th>
        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_EMAIL'), 'pr_rew.user_email', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th align = "left">
        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_PRODUCT_REVIEW'), 'pr_rew.review', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <?php if (!$this->config->hide_product_rating){?>
    <th>
        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_REVIEW_MARK'), 'pr_rew.mark', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <?php }?>
    <th>
        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_DATE'), 'pr_rew.time', $this->filter_order_Dir, $this->filter_order); ?> 
    </th>
    <th>
        <?php echo HTMLHelper::_('grid.sort', 'IP', 'pr_rew.ip', $this->filter_order_Dir, $this->filter_order); ?>
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
    <th width="40" class="center">
        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_ID'), 'pr_rew.review_id', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
	<?php print $this->_tmp_cols_14;?>
  </tr>
</thead> 
<?php foreach ($this->reviews as $row){?>
<tr class="row<?php echo $i % 2;?>">
   <td>
     <?php echo $this->pagination->getRowOffset($i);?>             
   </td>
   <td>         
     <?php echo HTMLHelper::_('grid.id', $i, $row->review_id);?>
   </td>
   <td>
     <?php echo $row->name;?>
   </td>
   <td>
     <?php echo $row->user_name;?>
   </td> 
   <td>
     <?php echo $row->user_email;?>
   </td>     
   <td>
     <?php echo $row->review;?>
   </td>
   <?php if (!$this->config->hide_product_rating){?>
   <td>
     <?php echo $row->mark;?>
   </td>
   <?php }?>
   <td>
     <?php echo $row->dateadd;?>
   </td>
   <td>
     <?php echo $row->ip;?>
   </td>
   <td class="center">
     <?php echo HTMLHelper::_('jgrid.published', $row->publish, $i);?>
   </td> 
   <td class="center">
    <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=reviews&task=edit&cid[]=<?php print $row->review_id?>'>
        <i class="icon-edit"></i>
    </a>
   </td>
   <td class="center">
    <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=reviews&task=remove&cid[]=<?php print $row->review_id?>' onclick="return confirm('<?php print Text::_('JSHOP_DELETE')?>')">
        <i class="icon-delete"></i>
    </a>
   </td>
   <td class="center">
    <?php print $row->review_id;?>
   </td>
   <?php print $row->_tmp_cols_14;?>
</tr>
<?php
$i++;
}
?>
</table>
<div class="d-flex justify-content-between align-items-center">
    <div class="jshop_list_footer"><?php echo $this->pagination->getListFooter(); ?></div>
    <div class="jshop_limit_box"><?php echo $this->pagination->getLimitBox(); ?></div>
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