<?php
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Session\Session;

/**
* @version      5.10.0 27.10.2026
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

<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
<label for="ie_filename"><?php print Text::_('JSHOP_FILE_NAME')?>:</label>
<input type="text" name="params[filename]" id="ie_filename" class="form-control w-auto" value="<?php print htmlspecialchars($ie_params['filename'] ?? '', ENT_QUOTES)?>" size="45">
<label for="ie_encoding" class="ms-3"><?php print Text::_('JSHOP_ENCODING')?>:</label>
<select name="params[encoding]" id="ie_encoding" class="form-select w-auto">
<?php foreach($this->getEncodings() as $encoding){?>
    <option value="<?php print $encoding?>"<?php if ($encoding == $this->getSafeEncoding($ie_params['encoding'] ?? '')){?> selected<?php }?>><?php print $encoding?></option>
<?php }?>
</select>
</div>
<table class="table table-striped">
<thead>
  <tr>
    <th class="title" width ="10">
      #
    </th>    
    <th align="left">
      <?php echo Text::_('JSHOP_NAME')?>
    </th>
    <th width="150">
        <?php echo Text::_('JSHOP_DATE')?>
    </th>    
    <th width="50" class="center">
        <?php echo Text::_('JSHOP_DELETE')?>
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
        <a target="_blank" href="<?php print $jshopConfig->importexport_live_path.$_importexport->get('alias')."/".rawurlencode($row); ?>"><?php echo htmlspecialchars($row, ENT_QUOTES);?></a>
    </td>
    <td>
        <?php print date("d.m.Y H:i:s", filemtime($jshopConfig->importexport_path.$_importexport->get('alias')."/".$row)); ?>
    </td>    
    <td class="center">
        <a class="btn btn-micro" href='index.php?option=com_jshopping&controller=importexport&task=filedelete&ie_id=<?php print $ie_id;?>&file=<?php print rawurlencode($row)?>&<?php print Session::getFormToken()?>=1' onclick="return confirm('<?php print Text::_('JSHOP_DELETE')?>');">
            <i class="icon-delete"></i>
        </a>
    </td>
</tr>
<?php
$i++;  
}
?>
</table>

<?php print HTMLHelper::_('form.token');?>
</form>