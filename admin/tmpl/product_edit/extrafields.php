<?php
use Joomla\CMS\Language\Text;

/**
* @version      5.4.2 09.05.2024
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
?>
<div id="product_extra_fields" class="tab-pane">
    <?php print $this->tmpl_extra_fields_start ?? '';?>

    <div class="float-end mt-2 ms-2">
        <input type="text" class="form-control prod_extrafields_search" placeholder="<?php print Text::_('JSHOP_SEARCH')?>">
        <label><input type="checkbox" class="prod_extrafields_search_hide_unfilled"> <?php print Text::_('JSHOP_HIDE_UNFILLED')?></label>
        <?php print $this->tmpl_extra_fields_right ?? '';?>
    </div>

    <div class="col100" id="extra_fields_space">
    <?php print $this->tmpl_extra_fields;?>
    <?php print $this->plugin_template_extrafields ?? '';?>
    </div>

    <?php print \Joomla\CMS\HTML\HTMLHelper::_(
        'bootstrap.renderModal',
        'extrafields_option_popup',
        array(
            'modal-dialog-scrollable' => true,
            'title'       => Text::_('JSHOP_ADD_NEW_OPTION_FOR'),
            //'backdrop'    => 'static',
            'height'      => '400px',
            'width'       => '800px',
            'bodyHeight'  => 40,
            'modalWidth'  => 50,
            'footer'      => '<button type="button" class="btn btn-primary btn-save">'.Text::_('JAPPLY').'</button>',
        ),
        $this->loadTemplate($this->_extrafields_option_popup ?? 'extrafields_option_popup')
    );?>
</div>