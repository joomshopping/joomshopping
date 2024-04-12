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
<?php JSHelperAdmin::displaySubmenuOptions();?>
<form action="index.php?option=com_jshopping&controller=importexport" method="post" name="adminForm" id="adminForm">
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
    <th align="left" width="25%">
      <?php echo JText::_('JSHOP_TITLE')?>
    </th>    
    <th align="left">
      <?php echo JText::_('JSHOP_DESCRIPTION')?>
    </th>
    <th width="150" class="center">
        <?php echo JText::_('JSHOP_AUTOMATIC_EXECUTION')?>
    </th>
    <th width="50" class="center">
        <?php echo JText::_('JSHOP_DELETE')?>
    </th>
    <th width="40" class="center">
        <?php echo JText::_('JSHOP_ID')?>
    </th>
  </tr>
</thead>
<?php
$count=count($rows);
foreach($rows as $row){
?>
<tr class="row<?php echo $i % 2;?>">
    <td>
        <?php echo $i+1;?>
    </td>
    <td>
        <?php echo \JHTML::_('grid.id', $i, $row->id);?>
    </td>
    <td>
        <a href="index.php?option=com_jshopping&controller=importexport&task=view&ie_id=<?php echo $row->id; ?>"><?php echo $row->name;?></a>
    </td>
    <td>
        <?php echo $row->description;?>
    </td>
    <td class="center">
        <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=importexport&task=setautomaticexecution&cid=<?php print $row->id?>'>
            <?php if ($row->steptime>0){?>
                <i class="icon-publish"></i>
            <?php }else{ ?>
                <i class="icon-unpublish"></i>
            <?php }?>
        </a>
    </td>
    <td class="center">
        <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=importexport&task=remove&cid=<?php print $row->id?>' onclick="return confirm('<?php print JText::_('JSHOP_DELETE')?>');">
            <i class="icon-delete"></i>
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