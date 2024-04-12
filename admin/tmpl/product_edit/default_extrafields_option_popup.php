<table class="w-100">
    <?php foreach($this->languages as $lang) { ?>
        <tr>
            <td class="key">
                <?php print \JText::_('JSHOP_TITLE')." (".$lang->lang.") * ";?>
            </td>
            <td>
                <input type='text' class='new_option form-control w-100' name='new_ef_option_ef_name[<?php print $lang->lang?>]' value='' language="<?php print $lang->language?>">
            </td>
        </tr>
    <?php } ?>
</table>
<input type="hidden" name="new_ef_option_ef_id" value="">
<input type="hidden" name="new_ef_option_ef_val_id" value="">