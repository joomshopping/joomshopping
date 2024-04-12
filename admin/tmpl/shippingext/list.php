<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
$rows=$this->rows;
?>
<div class="row">
<div class="col-md-12">
<div id="j-main-container" class="j-main-container">
<?php JSHelperAdmin::displaySubmenuOptions("shippings");?>
<form action="index.php?option=com_jshopping&controller=shippingextprice" method="post" name="adminForm" id="adminForm">
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
    <th align="left" width="300">
      <?php echo JText::_('JSHOP_TITLE')?>
    </th>
    <th>
        <?php echo JText::_('JSHOP_DESCRIPTION')?>
    </th>
    <th>
      <?php echo JText::_('JSHOP_ORDERING')?>
    </th>
    <th width="30">
      <?php echo JText::_('JSHOP_PUBLISH')?>
    </th>
    <th width="50">
        <?php echo JText::_('JSHOP_CONFIG')?>
    </th>
    <th width="50">
        <?php echo JText::_('JSHOP_DELETE')?>
    </th>
    <th width="40">
        <?php echo JText::_('JSHOP_ID')?>
    </th>
  </tr>
</thead>  
<?php
$count=count($rows);
foreach($rows as $i=>$row){?>
<tr class="row<?php echo $i % 2;?>">
   <td>
     <?php echo $i+1;?>
   </td>
   <td>     
     <?php echo \JHTML::_('grid.id', $i, $row->id); ?>
   </td>
   <td>     
        <?php echo $row->name;?>     
   </td>
   <td>
        <?php echo $row->description;?>
   </td>
   <td class="order" style="width:80px;">
    <span><?php if ($i != 0) echo \JHTML::_('jgrid.orderUp', $i, "orderup");?></span>
    <span><?php if ($i != $count - 1) echo \JHTML::_('jgrid.orderDown', $i, "orderdown");?></span>
   </td>
   <td class="center">     
     <?php echo \JHTML::_('jgrid.published', $row->published, $i);?>
   </td>
   <td class="center">
        <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=shippingextprice&task=edit&id=<?php print $row->id;?>'>
            <i class="icon-edit"></i>
        </a>
   </td>
   <td class="center">
    <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=shippingextprice&task=remove&id=<?php print $row->id?>' onclick="return confirm('<?php print JText::_('JSHOP_DELETE')?>')">
        <i class="icon-delete"></i>
    </a>
   </td>
   <td class="center">
    <?php print $row->id;?>
   </td>
  </tr>
<?php $i++; } ?>
</table>

<input type="hidden" name="task" value="" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
<?php print $this->tmp_html_end?>
</form>
</div>
</div>
</div>
<script>
jQuery(function(){
	jshopAdmin.setMainMenuActive('<?php print JURI::base()?>index.php?option=com_jshopping&controller=other');
});
</script>