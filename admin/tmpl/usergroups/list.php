<?php
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

/**
* @version      5.6.1 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$rows=$this->rows;
?>

<div id="j-main-container" class="j-main-container">
<?php HelperAdmin::displaySubmenuOptions();?>
<form action="index.php?option=com_jshopping&controller=usergroups" method="post" name="adminForm" id="adminForm">
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
    	<th class="title" width ="10">
      		#
    	</th>
    	<th width="20">
	  		<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
    	</th>
    	<th width="150" align="left">
      		<?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_TITLE'), 'usergroup_name', $this->filter_order_Dir, $this->filter_order); ?>
    	</th>
    	<th align="left">
      		<?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_DESCRIPTION'), 'usergroup_description', $this->filter_order_Dir, $this->filter_order); ?>
    	</th>
        <th width="80">
            <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_USERGROUP_DISCOUNT'), 'usergroup_discount', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
    	<th width="100" class="center">
			<?php echo Text::_('JSHOP_USERGROUP_IS_DEFAULT')?>
		</th>
	    <th width="50" class="center">
	        <?php echo Text::_('JSHOP_EDIT')?>
	    </th>
        <th width="40" class="center">
            <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_ID'), 'usergroup_id', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
  	</tr>
</thead>
<?php $i=0; foreach($rows as $row){?>
<tr class="row<?php echo ($i%2);?>">
	<td>
		<?php echo $i + 1;?>
	</td>
	<td align="center">
        <?php echo HTMLHelper::_('grid.id', $i, $row->usergroup_id);?>
	</td>
	<td>
		<a href="index.php?option=com_jshopping&controller=usergroups&task=edit&usergroup_id=<?php echo $row->usergroup_id;?>"><?php echo $row->usergroup_name; ?></a>
	</td>
	<td>
		<?php echo $row->usergroup_description; ?>
	</td>
    <td>
        <?php print $row->usergroup_discount?> %
    </td>
	<td class="center">
        <a class="btn btn-micro btn-nopad">
        <?php if ($row->usergroup_is_default){?>
            <i class="icon-default"></i>
        <?php }else{?>
            <i class="icon-unfeatured"></i>
        <?php }?>
        </a>
    </td>
	<td class="center">
	    <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=usergroups&task=edit&usergroup_id=<?php print $row->usergroup_id?>'>
            <i class="icon-edit"></i>
        </a>
   </td>
   <td class="center">
    <?php print $row->usergroup_id?>
   </td>
</tr>	
<?php $i++;} ?>
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