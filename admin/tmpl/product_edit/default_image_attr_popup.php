<?php
use Joomla\CMS\Language\Text;

/**
* @version      5.9.0 01.12.2015
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
?>
<div class="product_image_attr_list">
    <input type="hidden" id="product_image_attr_popup_id" value="">
    <table class="table">
        <?php foreach($this->lists['list_attribs_active'] as $_attr) { ?>
            <tr>
                <td class="key" width="150">
                    <?php print $_attr['name'];?>:
                </td>
                <td>
                    <div class="values_list">
                        <?php foreach($_attr['vals'] as $_val) {?>
                            <div>                       
                            <label>
                                <input type="checkbox" value="<?php echo $_val['id']?>">
                                <?php echo $_val['name']?> 
                            </label>
                            </div>
                        <?php } ?>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>