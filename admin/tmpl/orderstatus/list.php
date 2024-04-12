<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
?>
<?php
$rows=$this->rows;
$i=0;
?>
<div class="row">
<div class="col-md-12">
<div id="j-main-container" class="j-main-container">
<?php JSHelperAdmin::displaySubmenuOptions();?>
<form action="index.php?option=com_jshopping&controller=orderstatus" method="post" name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>
<table class="table table-striped">
<thead>
  <tr>
    <th class="title" width ="10">
      #
    </th>
    <th width="20">
	  <input type="checkbox" name="checkall-toggle" value="" title="<?php echo \JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
    </th>
    <th width="200" align="left">
        <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_TITLE'), 'name', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th align="left">
        <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_CODE'), 'status_code', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width="50" class="center">
        <?php echo JText::_('JSHOP_EDIT')?>
    </th>
    <th width="40" class="center">
        <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_ID'), 'status_id', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
  </tr>
</thead>  
<?php foreach($rows as $row){ ?>
  <tr class="row<?php echo $i % 2;?>">
   <td>
     <?php echo $i+1;?>
   </td>
   <td>     
     <?php echo \JHTML::_('grid.id', $i, $row->status_id);?>
   </td>
   <td>
     <a title="<?php echo JText::_('JSHOP_EDIT_ORDER_STATUS')?>" href="index.php?option=com_jshopping&controller=orderstatus&task=edit&status_id=<?php echo $row->status_id; ?>"><?php echo $row->name;?></a>
   </td>
   <td>
     <?php echo $row->status_code;?>
   </td>
	<td class="center">
   	    <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=orderstatus&task=edit&status_id=<?php echo $row->status_id; ?>'>
            <i class="icon-edit"></i>
        </a>
   	</td>
    <td class="center">
        <?php print $row->status_id;;?>
    </td>
  </tr>
<?php
$i++;
}
?>
</table>

<input type="hidden" name="filter_order" value="<?php echo $this->filter_order?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir?>" />
<input type="hidden" name="task" value="<?php echo \JFactory::getApplication()->input->getVar('task', 0)?>" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
<?php print $this->tmp_html_end?>
</form>
</div>
</div>
</div>
<script>
jQuery(function(){
	jQuery('ul li a.mm-active').removeClass('mm-active');
	jshopAdmin.setMainMenuActive('<?php print JURI::base()?>index.php?option=com_jshopping&controller=other');
});
</script>