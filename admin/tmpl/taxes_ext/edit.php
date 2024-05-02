<?php
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;

/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$row=$this->tax;
?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=exttaxes&back_tax_id=<?php print $this->back_tax_id;?>" method="post"name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>
<div class="col100">
<fieldset class="adminform">
<table width="100%" class="admintable">
   <tr>
     <td class="key" style="width:250px;">
       <?php echo Text::_('JSHOP_TITLE')?>*
     </td>
     <td>
       <?php print $this->lists['taxes'];?>
     </td>
   </tr>
   <tr>
    <td class="key">
        <?php echo Text::_('JSHOP_COUNTRY')."*<br/><br/><span style='font-weight:normal'>".Text::_('JSHOP_MULTISELECT_INFO')."</span>"; ?>
    </td>
    <td>
        <?php echo $this->lists['countries'];?>
    </td>
   </tr>
   <tr>
     <td  class="key">
       <?php echo Text::_('JSHOP_TAX')?>*
     </td>
     <td>
       <input type="text" class="inputbox form-control" name="tax" value="<?php echo $row->tax;?>" /> %
       <?php echo HelperAdmin::tooltip(Text::_('JSHOP_VALUE_TAX_INFO'));?>
     </td>
   </tr>
   <tr>
     <td class="key">
       <?php 
        if ($this->config->ext_tax_rule_for==1) 
            echo Text::_('JSHOP_USER_WITH_TAX_ID_TAX');
        else
            echo Text::_('JSHOP_FIRMA_TAX');
        ?>*
     </td>
     <td>
       <input type="text" class="inputbox form-control" name="firma_tax" value="<?php echo $row->firma_tax;?>" /> %
       <?php echo HelperAdmin::tooltip(Text::_('JSHOP_VALUE_TAX_INFO'));?>
     </td>
   </tr>
   <?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
 </table>
</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="task" value="" />
<input type="hidden" name="f-id" value="<?php echo (int)$row->id?>" />
<?php print $this->tmp_html_end?>
</form>
</div>