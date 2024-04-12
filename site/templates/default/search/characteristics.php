<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die;
?>
<?php
	$characteristic_displayfields = $this->characteristic_displayfields;
	$characteristic_fields = $this->characteristic_fields;
	$characteristic_fieldvalues = $this->characteristic_fieldvalues;
	$groupname = "";
?>
<?php print $this->tmp_ext_search_html_characteristic_start;?>
<?php if (is_array($characteristic_displayfields) && count($characteristic_displayfields)) : ?>
    <div class="filter_characteristic">
    <?php foreach($characteristic_displayfields as $ch_id) : ?>
        <div class = "control-group">
            <div class = "control-label">
                <?php if ($characteristic_fields[$ch_id]->groupname!=$groupname){ $groupname = $characteristic_fields[$ch_id]->groupname;?>
                    <span class="characteristic_group"><?php print $groupname;?></span>
                <?php }?>
                <span class="characteristic_name"><?php print $characteristic_fields[$ch_id]->name;?></span>
            </div>
            <div class = "controls">
                <?php if ($characteristic_fields[$ch_id]->type==0){?>
                    <input type="hidden" name="extra_fields[<?php print $ch_id?>][]" value="0" />
                    <?php if (is_array($characteristic_fieldvalues[$ch_id])){?>
                        <?php foreach($characteristic_fieldvalues[$ch_id] as $val_id=>$val_name){?>
                            <div class="characteristic_val"><input type="checkbox" name="extra_fields[<?php print $ch_id?>][]" value="<?php print $val_id;?>" <?php if (is_array($extra_fields_active[$ch_id]) && in_array($val_id, $extra_fields_active[$ch_id])) print "checked";?> /> <?php print $val_name;?></div>
                        <?php }?>
                    <?php }?>
                <?php }else{?>
                    <div class="characteristic_val"><input type="text" name="extra_fields[<?php print $ch_id?>]" class="inputbox form-control" /></div>
                <?php }?>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php print $this->tmp_ext_search_html_characteristic_end;?>