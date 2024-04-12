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
$i=0;
?>
<div class="row">
<div class="col-md-12">
<div id="j-main-container" class="j-main-container">
<?php JSHelperAdmin::displaySubmenuOptions("taxes");?>
<form action="index.php?option=com_jshopping&controller=exttaxes&back_tax_id=<?php print $this->back_tax_id;?>" method="post" name="adminForm" id="adminForm">
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
    <th align="left">
        <?php echo JText::_('JSHOP_TITLE')?>
    </th>
    <th>
        <?php echo JText::_('JSHOP_COUNTRY')?>
    </th>
    <th width="100">
        <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_TAX'), 'ET.tax', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
    <th width="100">
        <?php 
        if ($this->config->ext_tax_rule_for==1) 
            echo \JHTML::_('grid.sort', JText::_('JSHOP_USER_WITH_TAX_ID_TAX'), 'ET.firma_tax', $this->filter_order_Dir, $this->filter_order);
        else
            echo \JHTML::_('grid.sort', JText::_('JSHOP_FIRMA_TAX'), 'ET.firma_tax', $this->filter_order_Dir, $this->filter_order);
        ?>
    </th>
    <th width="50" class="center">
        <?php echo JText::_('JSHOP_EDIT')?>
    </th>
    <th width="40" class="center">
        <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_ID'), 'ET.id', $this->filter_order_Dir, $this->filter_order); ?>
    </th>
  </tr>
</thead>  
<?php foreach($rows as $row){?>
  <tr class="row<?php echo $i % 2;?>">
   <td>
     <?php echo $i+1;?>
   </td>
   <td>     
     <?php echo \JHTML::_('grid.id', $i, $row->id);?>
   </td>
   <td>
     <?php echo $row->tax_name;?>
   </td>
   <td>
    <?php echo $row->countries;?>
   </td>
   <td>
    <?php echo $row->tax;?> %
   </td>
   <td>
    <?php echo $row->firma_tax;?> %
   </td>
   <td class="center">
        <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=exttaxes&task=edit&id=<?php print $row->id?>&back_tax_id=<?php print $this->back_tax_id?>'>
            <i class="icon-edit"></i>
        </a>
   </td>
   <td class="center">
        <?php print $row->id;?>
   </td>
  </tr>
<?php
$i++;
}
?>
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
<script>
jQuery(function(){
	jshopAdmin.setMainMenuActive('<?php print JURI::base()?>index.php?option=com_jshopping&controller=other');
});
</script>