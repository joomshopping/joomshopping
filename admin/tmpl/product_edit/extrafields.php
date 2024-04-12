<?php
/**
* @version      5.3.0 06.12.2023
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
?>
<div id="product_extra_fields" class="tab-pane">
    <?php print $this->tmpl_extra_fields_start ?? '';?>

    <div class="float-end mt-1 ms-2">
        <input type="text" class="form-control prod_extrafields_search" placeholder="<?php print JText::_('JSHOP_SEARCH')?>">
        <label><input type="checkbox" class="prod_extrafields_search_hide_unfilled"> <?php print JText::_('JSHOP_HIDE_UNFILLED')?></label>
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
            'title'       => \JText::_('JSHOP_ADD_NEW_OPTION_FOR'),
            //'backdrop'    => 'static',
            'height'      => '400px',
            'width'       => '800px',
            'bodyHeight'  => 40,
            'modalWidth'  => 50,
            'footer'      => '<button type="button" class="btn btn-primary btn-save">'.\JText::_('JAPPLY').'</button>',
        ),
        $this->loadTemplate($this->_extrafields_option_popup ?? 'extrafields_option_popup')
    );?>
</div>