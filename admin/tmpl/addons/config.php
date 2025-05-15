<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
/**
* @version      5.6.3 08.03.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=addons" method="post" name="adminForm" id="adminForm">
<?php print $this->tmp_html_start ?? ''?>
<div class="col100">
<fieldset class="adminform">
    <table class="admintable" width="100%">
        <tr>
            <td class="key">
                <?php echo Text::_('JSHOP_Alias')?>
            </td>
            <td>
                <?php echo $this->row->alias;?>
            </td>
        </tr>
        <tr>
            <td class="key">
                <?php echo Text::_('JSHOP_DEBUG')?>
            </td>
            <td>
                <?php echo $this->debug_select?>
            </td>
        </tr>
        <tr>
            <td class="key">
                <?php echo Text::_('JSHOP_LOGS')?>
            </td>
            <td>
                <?php print HTMLHelper::_('select.booleanlist', 'config[log]', 'class="inputbox"', $this->config['log'] ?? 0);?>           
            </td>
        </tr>
        <tr>
            <td class="key">
                <?php echo Text::_('JSHOP_FOLDER_OVERRIDES')?> (view)
            </td>
            <td>
                <input type="text" class="form-control w-100" name="config[folder_overrides_view]" value="<?php echo $this->config['folder_overrides_view'] ?? ''?>">
                <div class="small"><?php echo Text::_('JSHOP_DEFAULT').": ".$this->def_folder_view?></div>
                <div class="small"><?php echo Text::_('JSHOP_DEFAULT').' '.Text::_('JSHOP_FOLDER_OVERRIDES').": ".$this->def_overrides_view?></div>
            </td>
        </tr>
        <tr>
            <td class="key">
                <?php echo Text::_('JSHOP_FOLDER_OVERRIDES')?> (js)
            </td>
            <td>
                <input type="text" class="form-control w-100" name="config[folder_overrides_js]" value="<?php echo $this->config['folder_overrides_js'] ?? ''?>">
                <div class="small"><?php echo Text::_('JSHOP_DEFAULT').": ".$this->def_folder_js?></div>
                <div class="small"><?php echo Text::_('JSHOP_DEFAULT').' '.Text::_('JSHOP_FOLDER_OVERRIDES').": ".$this->def_overrides_js?></div>
            </td>
        </tr>
        <tr>
            <td class="key">
                <?php echo Text::_('JSHOP_FOLDER_OVERRIDES')?> (css)
            </td>
            <td>
                <input type="text" class="form-control w-100" name="config[folder_overrides_css]" value="<?php echo $this->config['folder_overrides_css'] ?? ''?>">
                <div class="small"><?php echo Text::_('JSHOP_DEFAULT').": ".$this->def_folder_css?></div>
                <div class="small"><?php echo Text::_('JSHOP_DEFAULT').' '.Text::_('JSHOP_FOLDER_OVERRIDES').": ".$this->def_overrides_css?></div>
            </td>
        </tr>

        <?php if (count($this->tmp_vars)) {?>
        <tr>
            <td class="key">
                <b><?php echo Text::_('JSHOP_ADDON_POSITONS_VARS')?></b>
            </td>
        </tr>
        <?php }?>

        <?php foreach($this->tmp_vars as $k=>$v) {?>
            <tr>
                <td class="key">
                    <?php echo $k?>
                </td>
                <td>
                    <input type="text" class="form-control w-100" name="config[tmp_vars][<?php echo $k?>]" value="<?php echo $v;?>">
                </td>
            </tr>
        <?php }?>

        <?php print $this->etemplatevar ?? '';?>
    </table>
</fieldset>
</div>

<input type="hidden" name="task" value="">
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="f-id" value="<?php print $this->row->id?>">
<?php print $this->tmp_html_end ?? ''?>
</form>
</div>