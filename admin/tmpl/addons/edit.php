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
$params=$this->params;
?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=addons" method="post" enctype="multipart/form-data" name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>
<input type="hidden" name="f-id" value="<?php print $this->row->id?>">
<?php if ($this->config_file_exist){
    include($this->config_file_patch);
}?>
<input type="hidden" name="task" value="" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="boxchecked" value="0" />
<?php print $this->tmp_html_end?>
</form>
</div>