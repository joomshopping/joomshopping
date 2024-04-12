<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/ 
defined('_JEXEC') or die();
$rows = $this->rows;
?>
<div class="row">
<div class="col-md-12">
<div id="j-main-container" class="j-main-container">
<?php JSHelperAdmin::displaySubmenuOptions();?>
<form action = "index.php?option=com_jshopping&controller=logs" method = "post" name = "adminForm">
<?php print $this->tmp_html_start?>
<table class="table table-striped">
<thead>
<tr>
    <th class="title" width="10"> # </th>    
    <th align = "left"><?php echo JText::_('JSHOP_TITLE')?></th>
    <th align = "left"><?php echo JText::_('JSHOP_DATE')?></th>
    <th align = "left"><?php echo JText::_('JSHOP_SIZE')?></th>
</tr>
</thead>
<?php $i = 0; ?>
<?php foreach($rows as $file){?>
  <tr class = "row<?php echo $i % 2;?>">
   <td>
     <?php echo $i + 1;?>
   </td>
   <td>    
    <a href = "index.php?option=com_jshopping&controller=logs&task=edit&id=<?php echo $file[0];?>"><?php echo $file[0];?></a>
   </td>
   <td><?php print date('Y-m-d H:i:s', $file[1])?></td>
   <td><?php print $file[2]?></td>
  </tr>
<?php
$i++;
}
?>
</table>
<input type = "hidden" name = "task" value = "" />
<input type = "hidden" name = "hidemainmenu" value = "0" />
<input type = "hidden" name = "boxchecked" value = "0" />
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