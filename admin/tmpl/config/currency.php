<?php
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\Language\Text;

/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$jshopConfig=JSFactory::getConfig();
$lists=$this->lists;
HTMLHelper::_('bootstrap.tooltip');
?>

<div id="j-main-container" class="j-main-container">
<?php HelperAdmin::displaySubmenuConfigs('currency');?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=config" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<?php print $this->tmp_html_start?>
<input type="hidden" name="task" value="">
<input type="hidden" name="tab" value="2">

<div class="card jshop_edit">
<h3 class="card-header bg-primary text-white"><?php echo Text::_('JSHOP_CURRENCY_PARAMETERS')?></h3>
<div class="card-body">
<table class="admintable table-striped">
  <tr>
    <td class="key" >
      <?php echo Text::_('JSHOP_MAIN_CURRENCY')?>
    </td>
    <td>
      <?php echo $lists['currencies'];?>
      &nbsp;&nbsp;
      <a class="btn btn-small btn-info" href="index.php?option=com_jshopping&controller=currencies"><?php print Text::_('JSHOP_LIST_CURRENCY')?></a>
    </td>
  </tr>
  <tr>
    <td class="key" >
      <?php echo Text::_('JSHOP_DECIMAL_COUNT')?>
    </td>
    <td>
      <input type="text" name="decimal_count" class = "form-control" id="decimal_count" value ="<?php echo $jshopConfig->decimal_count?>" />
      <?php echo HelperAdmin::tooltip(Text::_('JSHOP_DECIMAL_COUNT_DESCRIPTION'));?>
    </td>
  </tr>
  <tr>
    <td class="key" >
      <?php echo Text::_('JSHOP_DECIMAL_SYMBOL')?>
    </td>
    <td>
      <input type="text" name="decimal_symbol" class = "form-control" id="decimal_symbol" value ="<?php echo $jshopConfig->decimal_symbol?>" />
      <?php echo HelperAdmin::tooltip(Text::_('JSHOP_DECIMAL_SYMBOL_DESCRIPTION'));?>
    </td>
    <td>
    </td>
  </tr>
  <tr>
    <td class="key" >
      <?php echo Text::_('JSHOP_THOUSAND_SEPARATOR')?>
    </td>
    <td>
      <input type="text" name="thousand_separator" class = "form-control" id="thousand_separator" value ="<?php echo $jshopConfig->thousand_separator?>" />
      <?php echo HelperAdmin::tooltip(Text::_('JSHOP_THOUSAND_SEPARATOR_DESCRIPTION'));?>
    </td>
    <td>
    </td>
  </tr>
  <tr>
    <td class="key" >
      <?php echo Text::_('JSHOP_CURRENCY_FORMAT')?>
    </td>
    <td>
      <?php echo $lists['format_currency']; echo " ".HelperAdmin::tooltip(Text::_('JSHOP_CURRENCY_FORMAT_DESCRIPTION')) ?>
    </td>
    <td>
    </td>
  </tr>
  <?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
</table>
</div>
</div>
<?php print $this->tmp_html_end?>
</form>
</div>
</div>