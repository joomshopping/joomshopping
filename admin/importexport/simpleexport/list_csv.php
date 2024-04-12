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
<form action="index.php?option=com_jshopping&controller=importexport" method="post" name="adminForm" id="adminForm">
<input type="hidden" name="task" value="" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="ie_id" value="<?php print $ie_id;?>" />

<?php print \JText::_('JSHOP_FILE_NAME')?>: <input type="text" name="params[filename]" class = "form-control" value="<?php print $ie_params['filename']?>" size="45"><br/>
<br/>
<table class="table table-striped">
<thead>
  <tr>
    <th class="title" width ="10">
      #
    </th>    
    <th align="left">
      <?php echo \JText::_('JSHOP_NAME')?>
    </th>
    <th width="150">
        <?php echo \JText::_('JSHOP_DATE')?>
    </th>    
    <th width="50" class="center">
        <?php echo \JText::_('JSHOP_DELETE')?>
    </th>
  </tr>
</thead>
<?php
$i=0;
foreach($files as $row){
?>
<tr class="row<?php echo $i % 2;?>">
    <td>
        <?php echo $i+1;?>
    </td>    
    <td>
        <a target="_blank" href="<?php print $jshopConfig->importexport_live_path.$_importexport->get('alias')."/".$row; ?>"><?php echo $row;?></a>
    </td>
    <td>
        <?php print date("d.m.Y H:i:s", filemtime($jshopConfig->importexport_path.$_importexport->get('alias')."/".$row)); ?>
    </td>    
    <td class="center">
        <a class="btn btn-micro" href='index.php?option=com_jshopping&controller=importexport&task=filedelete&ie_id=<?php print $ie_id;?>&file=<?php print $row?>' onclick="return confirm('<?php print \JText::_('JSHOP_DELETE')?>');">
            <i class="icon-delete"></i>
        </a>
    </td>
</tr>
<?php
$i++;  
}
?>
</table>


</form>