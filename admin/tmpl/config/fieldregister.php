<?php
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\Language\Text;

/**
* @version      5.4.1 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$jshopConfig=JSFactory::getConfig();
HTMLHelper::_('bootstrap.tooltip');
$fields=$this->fields;
$current_fields=$this->current_fields;
?>

<div id="j-main-container" class="j-main-container">
<?php HelperAdmin::displaySubmenuConfigs('fieldregister');?>
<div class="jshop_edit">
<form class="jshopfieldregister" action="index.php?option=com_jshopping&controller=config" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<?php print $this->tmp_html_start?>
<input type="hidden" name="task" value="">
<input type="hidden" name="tab" value="9">

<div class="row">
    <div class="col col-12 col-lg-4">        
        <div class="card">
        <h3 class="card-header bg-primary text-white"><?php echo Text::_('JSHOP_REGISTER')?></h3>
        <div class="card-body">
        <table class="admintable table-striped">
        <tr>
            <th class="key">
                &nbsp;
            </th>
            <th style="padding-right: 5px">
                <?php echo Text::_('JSHOP_DISPLAY')?>
            </th>
            <th>
                <?php echo Text::_('JSHOP_REQUIRE')?>
            </th>
        </tr>
        <?php foreach($fields['register'] as $field){?>
        <tr>
            <td class="key">
                <?php 
                print Text::_("JSHOP_FIELD_".strtoupper($field));
                
                ?>
            </td>
            <td align="center"><input type="checkbox" name="field[register][<?php print $field?>][display]" class="inputbox" value="1" <?php if (isset($current_fields['register'][$field]['display']) && $current_fields['register'][$field]['display']) echo 'checked="checked"';?> <?php if (in_array($field, $this->fields_sys['register'])){?>disabled="disabled"<?php }?> /></td>
            <td align="center"><input type="checkbox" name="field[register][<?php print $field?>][require]" class="inputbox" value="1" <?php if (isset($current_fields['register'][$field]['require']) && $current_fields['register'][$field]['require']) echo 'checked="checked"';?> <?php if (in_array($field, $this->fields_sys['register'])){?>disabled="disabled"<?php }?> /></td>
        </tr>
        <?php } ?>

        </table>
        </div>
        </div>
    </div>

    <div class="col col-12 col-lg-4">        
        <div class="card">
        <h3 class="card-header bg-primary text-white"><?php echo Text::_('JSHOP_CHECKOUT_ADDRESS')?></h3>
        <div class="card-body">
        <table class="admintable table-striped">
        <tr>
            <th class="key">
                &nbsp;
            </th>
            <th style="padding-right: 5px">
                <?php echo Text::_('JSHOP_DISPLAY')?>
            </th>
            <th>
                <?php echo Text::_('JSHOP_REQUIRE')?>
            </th>
        </tr>
        <?php 
        $display_delivery=0;
        foreach($fields['address'] as $field){?>
        <?php if (!$display_delivery && substr($field,0,2)=="d_"){?>
        <tr>
            <td class="key">
                <br><b><?php print Text::_('JSHOP_FIELD_DELIVERY_ADRESS')?></b>
            </td>
        </tr>    
        <?php $display_delivery=1; } ?>
        <tr>
            <td class="key">
                <?php
                $field_c=$field; 
                if (substr($field_c,0,2)=="d_") $field_c=substr($field_c,2,strlen($field_c)-2);
                print Text::_("JSHOP_FIELD_".strtoupper($field_c));        
                ?>
            </td>
            <td align="center"><input type="checkbox" name="field[address][<?php print $field?>][display]" class="inputbox" value="1" <?php if (isset($current_fields['address'][$field]['display']) && $current_fields['address'][$field]['display']) echo 'checked="checked"';?> <?php if (in_array($field, $this->fields_sys['address'])){?>disabled="disabled"<?php }?> /></td>
            <td align="center"><input type="checkbox" name="field[address][<?php print $field?>][require]" class="inputbox" value="1" <?php if (isset($current_fields['address'][$field]['require']) && $current_fields['address'][$field]['require']) echo 'checked="checked"';?> <?php if (in_array($field, $this->fields_sys['address'])){?>disabled="disabled"<?php }?> /></td>
        </tr>
        <?php } ?>

        </table>
        </div>
        </div>
    </div>

    <div class="col col-12 col-lg-4">        
        <div class="card">
        <h3 class="card-header bg-primary text-white"><?php echo Text::_('JSHOP_EDIT_ACCOUNT')?></h3>
        <div class="card-body">
        <table class="admintable table-striped">
        <tr>
            <th class="key">
                &nbsp;
            </th>
            <th style="padding-right: 5px">
                <?php echo Text::_('JSHOP_DISPLAY')?>
            </th>
            <th>
                <?php echo Text::_('JSHOP_REQUIRE')?>
            </th>
        </tr>
        <?php 
        $display_delivery=0;
        foreach($fields['editaccount'] as $field){?>
        <?php if (!$display_delivery && substr($field,0,2)=="d_"){?>
        <tr>
            <td class="key">
                <br><b><?php print Text::_('JSHOP_FIELD_DELIVERY_ADRESS')?></b>
            </td>
        </tr>    
        <?php $display_delivery=1; } ?>
        <tr>
            <td class="key">
                <?php
                $field_c=$field; 
                if (substr($field_c,0,2)=="d_") $field_c=substr($field_c,2,strlen($field_c)-2);
                print  Text::_("JSHOP_FIELD_".strtoupper($field_c));        
                ?>
            </td>
            <td align="center"><input type="checkbox" name="field[editaccount][<?php print $field?>][display]" class="inputbox" value="1" <?php if (isset($current_fields['editaccount'][$field]['display']) && $current_fields['editaccount'][$field]['display']) echo 'checked="checked"';?> <?php if (in_array($field, $this->fields_sys['editaccount'])){?>disabled="disabled"<?php }?> /></td>
            <td align="center"><input type="checkbox" name="field[editaccount][<?php print $field?>][require]" class="inputbox" value="1" <?php if (isset($current_fields['editaccount'][$field]['require']) && $current_fields['editaccount'][$field]['require']) echo 'checked="checked"';?> <?php if (in_array($field, $this->fields_sys['editaccount'])){?>disabled="disabled"<?php }?> /></td>
        </tr>
        <?php } ?>
        <?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>

        </table>
        </div>
        </div>
    </div>

</div>
<?php print $this->tmp_html_end?>
</form>
</div>
</div>