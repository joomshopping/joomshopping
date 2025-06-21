<?php
use Joomla\CMS\Language\Text;

/**
 * @version      5.8.0 23.02.2024
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */
defined('_JEXEC') or die();

$row = $this->row;
?>
<div class="jshop_edit">
    <form action="index.php?option=com_jshopping&controller=productfields" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
        <?php print $this->tmp_html_start ?>
        <div class="col100">
            <fieldset class="adminform">
                <table width="100%" class="admintable">
                    <tr>
                        <td class="key">
                            <?php echo Text::_('JSHOP_PUBLISH') ?>
                        </td>
                        <td>
                            <input type="hidden" name="publish" value="0">
                            <input type="checkbox" name="publish" value="1" <?php if ($row->publish) echo 'checked="checked"'?>>
                        </td>
                    </tr>
                    <?php
                    foreach ($this->languages as $lang) {
                        $name = "name_" . $lang->language;
                    ?>
                        <tr>
                            <td class="key" style="width:250px;">
                                <?php echo Text::_('JSHOP_TITLE') ?> <?php if ($this->multilang) print "(" . $lang->lang . ")"; ?>*
                            </td>
                            <td>
                                <input type="text" class="inputbox form-control" id="<?php print $name ?>" name="<?php print $name ?>" value="<?php echo $row->$name; ?>" />
                            </td>
                        </tr>
                    <?php } ?>
                    <?php
                    foreach ($this->languages as $lang) {
                        $description = "description_" . $lang->language;
                    ?>
                        <tr>
                            <td class="key" style="width:250px;">
                                <?php echo Text::_('JSHOP_DESCRIPTION') ?> <?php if ($this->multilang) print "(" . $lang->lang . ")"; ?>
                            </td>
                            <td>
                                <textarea class="inputbox form-control" id="<?php print $description?>" name="<?php print $description ?>"><?php echo $row->$description; ?></textarea>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td class="key">
                            <?php echo Text::_('JSHOP_SHOW_FOR_CATEGORY') ?>*
                        </td>
                        <td>
                            <?php echo $this->lists['allcats']; ?>
                        </td>
                    </tr>
                    <tr id="tr_categorys" <?php if ($row->allcats == "1") print "style='display:none;'"; ?>>
                        <td class="key">
                            <?php echo Text::_('JSHOP_CATEGORIES') ?>*
                        </td>
                        <td>
                            <?php echo $this->lists['categories']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="key">
                            <?php echo Text::_('JSHOP_TYPE') ?>*
                        </td>
                        <td>
                            <?php echo $this->lists['type']; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="key">
                            <?php echo Text::_('JSHOP_GROUP') ?>
                        </td>
                        <td>
                            <?php echo $this->lists['group']; ?>
                        </td>
                    </tr>

                    <?php $pkey = "etemplatevar";
                    if ($this->$pkey) {
                        print $this->$pkey;
                    } ?>
                </table>
            </fieldset>
        </div>
        <div class="clr"></div>

        <input type="hidden" name="task" value="" />
        <input type="hidden" name="f-id" value="<?php echo (int)$row->id ?>" />
        <?php print $this->tmp_html_end ?>
    </form>
</div>