<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
/**
* @version      5.8.2 08.03.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
Text::script('JSHOP_SAVE_FIRST');
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
        <?php if ($this->has_folder_view): ?>
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
        <?php endif; ?>
        <?php if ($this->has_file_js): ?>
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
        <?php endif; ?>
        <?php if ($this->has_file_css): ?>
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
        <?php endif; ?>
        <?php if ($this->has_overrides): ?>
        <tr>
            <td class="key"><?php echo Text::_('JSHOP_FOLDER_OVERRIDES')?></td>
            <td>
                <button type="button" class="btn btn-primary" id="btn-override-files" data-id="<?php echo (int)$this->row->id?>">
                    <span class="icon-new" aria-hidden="true"></span> <?php echo Text::_('JSHOP_CREATE_OVERRIDE')?>
                </button>
            </td>
        </tr>
        <?php endif; ?>

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

<div class="modal fade" id="overrideModal" tabindex="-1" aria-labelledby="overrideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header flex-wrap gap-2">
                <h5 class="modal-title me-auto" id="overrideModalLabel"><?php echo Text::_('JSHOP_CREATE_OVERRIDE')?></h5>
                <div><?php echo Text::_('JSHOP_TEMPLATE')?>:</div>
                <select id="override-template-select" class="form-select form-select-sm" style="width:auto;min-width:200px"></select>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-0">
                    <div class="col-6 ps-3 border-end">
                        <div class="fw-bold mb-2"><?php echo Text::_('JSHOP_SOURCE_FILES')?></div>
                        <div id="override-sources"><div class="text-muted">Loading...</div></div>
                    </div>
                    <div class="col-6 ps-3">
                        <div class="fw-bold mb-2"><?php echo Text::_('JSHOP_OVERRIDE_FILES')?></div>
                        <div id="override-files"><div class="text-muted">Loading...</div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Override modal JS templates -->
<template id="tpl-ovr-loading">
    <div class="text-muted p-2">Loading...</div>
</template>

<template id="tpl-ovr-error">
    <div class="text-danger" data-error-msg></div>
</template>

<template id="tpl-ovr-section">
    <div class="mb-3 px-2" data-section>
        <div class="d-flex align-items-center fw-bold mb-1 small text-uppercase text-muted">
            <span data-section-icon aria-hidden="true"></span>
            <span data-section-title class="flex-grow-1 ms-1"></span>
            <span data-section-btn-all></span>
        </div>
        <div class="font-monospace text-muted mb-1" style="font-size:.75em;word-break:break-all" data-section-path hidden></div>
    </div>
</template>

<template id="tpl-ovr-btn-all">
    <button type="button" class="tbody-icon js-override-all" title="Override all">
        <span class="icon-copy text-danger border-white" aria-hidden="true"></span>
    </button>
</template>

<template id="tpl-ovr-src-row">
    <div class="d-flex align-items-center gap-1 mb-1">
        <span class="icon-file-alt icon-fw text-muted flex-shrink-0" aria-hidden="true"></span>
        <span class="flex-grow-1 font-monospace small text-truncate" data-fname></span>
        <button type="button" class="tbody-icon js-override-one" title="Override">
            <span class="icon-copy text-success border-white" aria-hidden="true"></span>
        </button>
    </div>
</template>

<template id="tpl-ovr-file-row">
    <div class="d-flex align-items-center gap-1 mb-1">
        <span class="icon-file-alt icon-fw text-muted flex-shrink-0" aria-hidden="true"></span>
        <span class="flex-grow-1 font-monospace small text-truncate" data-fname></span>
        <a href="#" target="_blank" class="tbody-icon" data-edit-link hidden>
            <span class="icon-edit text-dark border-white" aria-hidden="true"></span>
        </a>
    </div>
</template>

<template id="tpl-ovr-src-empty">
    <p class="text-muted px-2">No source files.</p>
</template>

<template id="tpl-ovr-files-empty">
    <p class="text-muted px-2">No overrides yet.</p>
</template>

</div>