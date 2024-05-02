<?php
/**
 * @version      5.4.1 23.04.2024
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die();
?>

<div id="j-main-container" class="j-main-container">
    <?php print $this->tmp_html_start ?>

    <fieldset class="uploadform option-fieldset options-form">
        <legend><?php echo Text::_('COM_INSTALLER_UPLOAD_PACKAGE_FILE'); ?></legend>
        <form enctype="multipart/form-data" action="index.php?option=com_jshopping&controller=update&task=update" method="post" name="adminForm" id="adminForm">
            <?php echo HTMLHelper::_('form.token'); ?>
            <div class="control-group">
                <label class="control-label">
                    <?php echo Text::_('JSHOP_UPDATE_PACKAGE_FILE') ?>
                </label>
                <div class="controls">
                    <input class="input_box form-control" name="install_path" type="file" size="57" />
                    <input type="hidden" name="installtype" value="package">
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <input class="button btn btn-success" type="submit" value="<?php echo Text::_('JSHOP_INSTALL_AND_UPDATE') ?>">
                </div>
            </div>
            <?php print $this->etemplatevar1 ?? '';?>
        </form>
    </fieldset>

    <fieldset class="uploadform option-fieldset options-form">
        <legend><?php echo Text::_('JSHOP_UPDATE_UPLOAD_FROM_URL_PACKAGE_FILE') ?></legend>
        <form enctype="multipart/form-data" action="index.php?option=com_jshopping&controller=update&task=update" method="post" name="adminForm" id="adminForm">
            <?php echo HTMLHelper::_('form.token'); ?>
            <div class="control-group">
                <label class="control-label">
                    <?php echo Text::_('JSHOP_UPDATE_UPLOAD_FROM_URL_PACKAGE_FILE') ?>
                </label>
                <div class="controls">
                    <input class="input_box inputbox form-control" name="install_path" type="text" value="http://">
                    <input type="hidden" name="installtype" value="url">
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <input class="button btn btn-success" type="submit" value="<?php echo Text::_('JSHOP_INSTALL_AND_UPDATE') ?>">
                </div>
            </div>
            <?php print $this->etemplatevar2 ?? '';?>
        </form>
    </fieldset>

    <fieldset class="uploadform option-fieldset options-form">
        <legend><?php echo Text::_('JSHOP_UPDATE_INSTALL_FROM_FOLDER') ?></legend>
        <form enctype="multipart/form-data" action="index.php?option=com_jshopping&controller=update&task=update" method="post" name="adminForm" id="adminForm">
            <?php echo HTMLHelper::_('form.token'); ?>
            <div class="control-group">
                <label class="control-label">
                    <?php echo Text::_('JSHOP_UPDATE_INSTALL_FROM_FOLDER') ?>
                </label>
                <div class="controls">
                    <input class="input_box inputbox form-control" name="install_path" type="text" value="<?php echo $this->default_folder?>">
                    <input type="hidden" name="installtype" value="folder">
                </div>
            </div>
            <div class="control-group">
                <div class="controls">
                    <input class="button btn btn-success" type="submit" value="<?php echo Text::_('JSHOP_INSTALL_AND_UPDATE') ?>">
                </div>
            </div>
            <?php print $this->etemplatevar3 ?? '';?>
        </form>
    </fieldset>

    <?php if ($this->config->disable_admin['addons_catalog'] == 0) {?>
    <fieldset class="uploadform option-fieldset options-form">
        <legend><?php echo Text::_('JSHOP_UPDATE_INSTALL_FROM_WEB') ?></legend>
        <form enctype="multipart/form-data" action="index.php?option=com_jshopping&controller=update&task=update" method="post" name="adminForm" id="adminForm">
            <?php echo HTMLHelper::_('form.token'); ?>
            <div class="control-group">
                <div class="controls">
                    <a class="button btn btn-success" href="index.php?option=com_jshopping&controller=addons&task=listweb">
                        <?php echo Text::_('JSHOP_ADDONS_CATALOG') ?>
                    </a>
                </div>
            </div>
        </form>
    </fieldset>
    <?php }?>

    <?php print $this->tmp_html_end ?>
</div>