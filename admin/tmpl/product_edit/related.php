<?php
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\CMS\Language\Text;

/**
* @version      5.4.1 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
?>
<div id="product_related" class="tab-pane">
    <div class="col100">
    <fieldset class="adminform">
        <?php if ($this->getLayout() == 'editlist') {?>
        <div class="mb-2 mt-2">
            <?php print $this->lists['add_new_related']?>
        </div>
        <?php } ?>
        <div id="list_related">
        <?php
            foreach($this->related_products as $row_related){
                $prefix_image = 'thumb'; 
                if (!$row_related->image) {
                    $row_related->image = $jshopConfig->noimage;
                    $prefix_image = '';  
                }
            ?>      
            <div class="block_related" id="related_product_<?php print $row_related->product_id;?>">
                <div class="block_related_inner">
                    <div class="name"><?php echo $row_related->name;?> (ID:&nbsp;<?php print $row_related->product_id?>)</div>
                    <div class="image">
                        <a href="index.php?option=com_jshopping&controller=products&task=edit&product_id=<?php print $row_related->product_id;?>"><img src="<?php print Helper::getPatchProductImage($row_related->image, 'thumb', 1)?>" width="90" border="0" /></a>
                        <?php if ($jshopConfig->admin_list_related_show_prod_code){?>
                            <div class="code"><?php print $row_related->ean?></div>
                        <?php }?>
                    </div>
                    
                    <div style="padding-top:5px;"><input type="button" class="btn btn-danger btn-small" value="<?php print Text::_('JSHOP_DELETE')?>" onclick="jshopAdmin.delete_related(<?php print $row_related->product_id;?>)"></div>
                    <input type="hidden" name="related_products[]" value="<?php print $row_related->product_id;?>"/>
                </div>
            </div>
            <?php
            }
            ?>
        </div>
    </fieldset>
    </div>
    <?php $pkey='plugin_template_related'; print $this->$pkey ?? '';?>
    
    <div class="col100 mt-2">
        <fieldset class="adminform">
            <legend><?php echo Text::_('JSHOP_SEARCH')?></legend>
            <div class="mw-600">
                <div class="input-group">
                    <input type="text" class = "form-control" id="related_search" value="">
            
                    <input type="button" class="btn btn-primary" value="<?php echo Text::_('JSHOP_SEARCH')?>" onclick="jshopAdmin.releted_product_search(0, '<?php echo $row->product_id ?? 0;?>', 1);" />
                </div>
            </div>            
            <div class="mt-3" id="list_for_select_related"></div>
        </fieldset>
    </div>    
</div>