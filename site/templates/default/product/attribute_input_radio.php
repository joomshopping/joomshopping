<?php
/**
* @version      5.3.5 08.03.2024
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die();

foreach($this->options as $el){?>
<?php $id = 'jshop_attr_id'.$this->attr_id.$el->val_id;?>
<?php if ($el->val_id == $this->active) $sel = ' checked="checked"'; else $sel = '';?>
<span class="input_type_radio">
    <label>
        <input type="radio" name="jshop_attr_id[<?php print $this->attr_id?>]" id="<?php print $id?>" value="<?php print $el->val_id?>" onclick="jshop.setAttrValue('<?php print $this->attr_id?>', this.value);" <?php print $sel?>>
        <span class='radio_attr_label'>
            <?php if ($el->image){?><img src="<?php print $this->config->image_attributes_live_path."/".$el->image?>" alt=''><?php }?><?php echo $el->_tmp_var_image_ext ?? '' ?>
            <span><?php print $el->value_name?></span>
        </span>
    </label>
</span>
<?php if ($this->config->radio_attr_value_vertical){?><br><?php }?>
<?php }?>