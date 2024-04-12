<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
$jshopConfig=\JSFactory::getConfig();
$rows=$this->rows;
$i=0;
?>
<div class="row">
<div class="col-md-12">
<div id="j-main-container" class="j-main-container">
<?php \JSHelperAdmin::displaySubmenuConfigs('statictext');?>
<form action="index.php?option=com_jshopping&controller=config" method="post" name="adminForm" id="adminForm">
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
      <?php echo JText::_('JSHOP_PAGE')?>
    </th>
    <th width = "150" class="center">
        <?php echo JText::_('JSHOP_USE_FOR_RETURN_POLICY')?>
    </th>
    <th width="50" class="center">
        <?php echo JText::_('JSHOP_EDIT')?>
    </th>
    <th width = "50" class="center">
        <?php echo JText::_('JSHOP_DELETE')?>
    </th>
    <th width="40" class="center">
        <?php echo JText::_('JSHOP_ID')?>
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
    <a href='index.php?option=com_jshopping&controller=config&task=statictextedit&id=<?php print $row->id?>'>    
		<?php if (JText::_('JSHP_STPAGE_'.$row->alias) != 'JSHP_STPAGE_'.$row->alias) print JText::_('JSHP_STPAGE_'.$row->alias); else print $row->alias;?>
    </a>
   </td>
   <td class="center">
     <?php echo \JHTML::_('jgrid.published', $row->use_for_return_policy, $i);?>
   </td>
   <td class="center">
        <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=config&task=statictextedit&id=<?php print $row->id?>'>
            <i class="icon-edit"></i>
        </a>
   </td>
   <td class="center">
   <?php if (!in_array($row->alias, $jshopConfig->sys_static_text)){?>
    <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=config&task=deletestatictext&id=<?php print $row->id?>' onclick="return confirm('<?php print JText::_('JSHOP_DELETE')?>')">
        <i class="icon-delete"></i>
    </a>
    <?php }else{?>
        -
    <?php }?>
   </td>
   <td class="center">
    <?php print $row->id;?>
   </td>
   </tr>
<?php
$i++;
}
?>
<?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
</table>

<input type="hidden" name="task" value="" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
<?php print $this->tmp_html_end?>
</form>
</div>
</div>