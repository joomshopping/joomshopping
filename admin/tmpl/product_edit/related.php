<?php
/**
* @version      5.0.0 15.09.2018
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
        <legend><?php echo JText::_('JSHOP_PRODUCT_RELATED')?></legend>
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
                        <a href="index.php?option=com_jshopping&controller=products&task=edit&product_id=<?php print $row_related->product_id;?>"><img src="<?php print \JSHelper::getPatchProductImage($row_related->image, 'thumb', 1)?>" width="90" border="0" /></a>
                        <?php if ($jshopConfig->admin_list_related_show_prod_code){?>
                            <div class="code"><?php print $row_related->ean?></div>
                        <?php }?>
                    </div>
                    
                    <div style="padding-top:5px;"><input type="button" class="btn btn-danger btn-small" value="<?php print JText::_('JSHOP_DELETE')?>" onclick="jshopAdmin.delete_related(<?php print $row_related->product_id;?>)"></div>
                    <input type="hidden" name="related_products[]" value="<?php print $row_related->product_id;?>"/>
                </div>
            </div>
            <?php
            }
            ?>
        </div>
    </fieldset>
    </div>
    <div class="clr"></div>
    <?php $pkey='plugin_template_related'; if ($this->$pkey){ print $this->$pkey;}?>
   <br/>
   <div class="col100">
    <fieldset class="adminform">
        <legend><?php echo JText::_('JSHOP_SEARCH')?></legend>
       <div class="input-group">
            <input type="text" class = "form-control" size="35" id="related_search" value="" />
            &nbsp;
            <input type="button" class="btn btn-primary" value="<?php echo JText::_('JSHOP_SEARCH')?>" onclick="jshopAdmin.releted_product_search(0, '<?php echo $row->product_id?>', 1);" />
        </div>
        <br/>
        <div id="list_for_select_related"></div>
    </fieldset>
    </div>
    <div class="clr"></div>
</div>