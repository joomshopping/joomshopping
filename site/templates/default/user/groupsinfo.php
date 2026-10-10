<?php
use Joomla\CMS\Language\Text;

/**
* @version      5.10.0 10.10.2026
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
?>
<div class="jshop" id="comjshop">
    <h1><?php print Text::_('JSHOP_USER_GROUPS_INFO')?></h1>

    <?php if (!empty($this->account_menu)){?>
    <div class="row myaccount_layout">
        <div class="col-lg-3">
            <?php include(__DIR__."/menu.php");?>
        </div>
        <div class="col-lg-9">
    <?php }?>
    
    <?php echo $this->_tmpl_start?>
    <table class="groups_list">
    <tr>
        <th class="title"><?php print Text::_('JSHOP_TITLE')?></th> 
        <th class="discount"><?php print Text::_('JSHOP_DISCOUNT')?></th> 
    </tr>
    <?php foreach($this->rows as $row) : ?>
        <tr>
            <td class="title"><?php print $row->name?></td> 
            <td class="discount"><?php print floatval($row->usergroup_discount)?>%</td>
        </tr>
    <?php endforeach; ?>
    </table>
    
    <?php echo $this->_tmpl_end?>
    <?php if (!empty($this->account_menu)){?>
        </div>
    </div>
    <?php }?>
</div>