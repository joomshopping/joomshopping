<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$shipping_prices=$this->rows;
$i=0;
?>
<div class="row">
<div class="col-md-12">
<div id="j-main-container" class="j-main-container">
<?php JSHelperAdmin::displaySubmenuOptions("shippingsprices");?>
<form name="adminForm" id="adminForm" action="index.php?option=com_jshopping&controller=shippingsprices" method="post">
<?php print $this->tmp_html_start?>
<table class="table table-striped">
<thead>
	<tr class="row<?php echo $i % 2;?>">
    	<th class="title" width ="10">
      		#
    	</th>
    	<th width="20">
	  		<input type="checkbox" name="checkall-toggle" value="" title="<?php echo \JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
    	</th>
    	<th align="left">
      		<?php echo \JHTML::_('grid.sort', JText::_('JSHOP_TITLE'), 'name', $this->filter_order_Dir, $this->filter_order); ?>
    	</th>
        <th>
            <?php echo JText::_('JSHOP_COUNTRIES')?>
        </th>
        <th width="100">
            <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_PRICE'), 'shipping_price.shipping_stand_price', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
    	<th width="70" class="center">
	        <?php echo JText::_('JSHOP_EDIT')?>
	    </th>
        <th width="40" class="center">
            <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_ID'), 'shipping_price.sh_pr_method_id', $this->filter_order_Dir, $this->filter_order); ?>
        </th>
  	</tr>
</thead>
<?php foreach($shipping_prices as $row){?>
<tr>
	<td>
		<?php echo $i + 1;?>
	</td>
	<td>		
        <?php echo \JHTML::_('grid.id', $i, $row->sh_pr_method_id);?>
	</td>
	<td>
		<a href="index.php?option=com_jshopping&controller=shippingsprices&task=edit&sh_pr_method_id=<?php echo $row->sh_pr_method_id?>&shipping_id_back=<?php print $this->shipping_id_back?>"><?php echo $row->name;?></a>
	</td>
    <td>
        <?php print $row->countries; ?>
    </td>
    <td>
        <?php print \JSHelper::formatprice($row->shipping_stand_price);?>
    </td>
	<td class="center">
        <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=shippingsprices&task=edit&sh_pr_method_id=<?php echo $row->sh_pr_method_id?>&shipping_id_back=<?php print $this->shipping_id_back?>'>
            <i class="icon-edit"></i>
        </a>
   </td>
   <td class="center">
    <?php print  $row->sh_pr_method_id;?>
   </td>  
</tr>
<?php $i++;} ?>
</table>

<input type="hidden" name="filter_order" value="<?php echo $this->filter_order?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="shipping_id_back" value="<?php echo $this->shipping_id_back;?>" />
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