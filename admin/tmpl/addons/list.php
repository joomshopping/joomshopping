<?php 
/**
* @version      5.7.0 29.03.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$rows=$this->rows;
$count=count($rows);
$i=0;
?>

<div id="j-main-container" class="j-main-container">
    <?php HelperAdmin::displaySubmenuOptions();?>
    <form action="index.php?option=com_jshopping&controller=addons" method="post" name="adminForm" id="adminForm">
        <?php print $this->tmp_html_start?>

        <div class="js-filters">
            <?php print $this->tmp_html_filter ?? ''?>
            <div>
                <input name="filter[text_search]" value="<?php echo htmlspecialchars($this->filter['text_search'] ?? '');?>" class="form-control" placeholder="<?php print Text::_('JSHOP_SEARCH')?>" type="text">
            </div>
            <div>
                <button type="submit" class="btn btn-primary hasTooltip" title="<?php print Text::_('JSHOP_SEARCH')?>">
                    <span class="icon-search" aria-hidden="true"></span>
                </button>                
            </div>
            <div>
                <button type="button" class="btn btn-primary js-stools-btn-clear"><?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?></button>
            </div>
            <?php print $this->tmp_html_filter_end ?? ''?>      
        </div>

        <?php if (count($rows) > 0) {?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="title" width="10">#</th>
                    <th width="10">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)">
                    </th>
                    <th align="left">
                        <?php echo Text::_('JSHOP_TITLE')?>
                    </th>
                    <th width="120">
                        <?php echo Text::_('JSHOP_VERSION')?>
                    </th>
                    <?php if ($this->config->disable_admin['addons_catalog'] == 0) {?>
                    <th width="120">
                        <?php echo Text::_('JSHOP_LATEST')?>
                    </th>
                    <?php }?>                    
                    <th width="60" class="center">
                        <?php echo Text::_('JSHOP_KEY')?>
                    </th>
                    <th width="60" class="center">
                        <?php echo Text::_('JSHOP_CONFIG')?>
                    </th>
                    <?php if ($this->config->shop_mode > 0) {?>
                    <th width="50" class="center">
                        <?php echo Text::_('JSHOP_MAINTENANCE')?>
                    </th>
                    <?php } ?>
                    <th width="50" class="center">
                        <?php echo Text::_('JSHOP_PUBLISH')?>
                    </th>
                    <th width="50" class="center">
                        <?php echo Text::_('JSHOP_DELETE')?>
                    </th>
                    <th width="40" class="center">
                        <?php echo Text::_('JSHOP_ID')?>
                    </th>
                </tr>
            </thead>
            <?php foreach($rows as $row){?>
            <tr class="row<?php echo $i % 2;?>">
                <td>
                    <?php echo $i+1;?>
                </td>
                <td>
                    <?php echo HTMLHelper::_('grid.id', $i, $row->id);?>
                </td>
                <td>
                    <?php echo $row->name;?>
                    <?php if ($row->info_file_exist){?>
                        <a href='index.php?option=com_jshopping&controller=addons&task=info&id=<?php print $row->id?>'>
                            <div class="small"><i class="icon-info-circle"></i> <?php echo Text::_('JSHOP_DESCRIPTION')?></div>
                        </a>
                    <?php }?>
                </td>
                <td>
                    <?php if (isset($row->avialable_version_update)) {?>
                        <div class="avialable_version_update">
                            <?php if (isset($row->url_update)) {?>
                                <a class="a_addon_update" xhref="<?php echo $row->url_update?>" j_ver="<?php echo $row->web_addon->last_file->j_ver ?? ''?>"  js_ver="<?php echo $row->web_addon->last_file->js_ver ?? ''?>">
                                    <?php print $row->version?>
                                    <i class="icon-refresh"></i>
                                </a>
                            <?php } else { ?>
                                <?php print $row->version?>
                            <?php } ?>
                        </div>
                    <?php } else {?>
                        <?php echo $row->version;?>
                    <?php }?>
                    <?php if ($row->version_file_exist){?>
                        <a class="btn btn-micro tbody-icon" href='index.php?option=com_jshopping&controller=addons&task=version&id=<?php print $row->id?>'>
                            <i class="icon-info"></i>
                        </a>
                    <?php }?>
                </td>
                <?php if ($this->config->disable_admin['addons_catalog'] == 0) {?>
                <td>
                    <?php if (isset($row->last_version)) {?>
                        <div <?php if (isset($row->avialable_version_update)) {?>class="latest"<?php }?>>
                            <a target="_blank" href="<?php echo $row->catalog_url?>">
                                <?php print $row->last_version?>
                            </a>
                        </div>
                    <?php }?>
                </td>
                <?php }?>
                <td class="center">
                    <?php if ($row->usekey){?>
                    <a class="btn btn-micro" href='index.php?option=com_jshopping&controller=licensekeyaddon&alias=<?php print $row->alias?>&back=<?php print $this->back64?>'>
                        <i class="icon-key"></i>
                    </a>
                    <?php }?>
                </td>
                <td class="center">
                    <?php if ($row->config_file_exist){?>
                    <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=addons&task=edit&id=<?php print $row->id?>'>
                        <i class="icon-edit"></i>
                    </a>
                    <?php }?>                    
                </td>
                <?php if ($this->config->shop_mode > 0) {?>
                <td class="center">
                    <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=addons&task=config&id=<?php print $row->id?>'>
                        <i class="icon-wrench"></i>
                    </a>
                </td>
                <?php }?>
                <td class="center">
                    <?php if ($row->publish != -1) {?>
                    <?php echo HTMLHelper::_('jgrid.published', $row->publish, $i);?>
                    <?php }?>
                </td>
                <td class="center">
                    <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=addons&task=remove&cid[]=<?php print $row->id?>'
                        onclick="return confirm('<?php print Text::_('JSHOP_DELETE_ALL_DATA')?>')">
                        <i class="icon-delete"></i>
                    </a>
                </td>
                <td class="center">
                    <?php print $row->id;?>
                </td>
            </tr>
            <?php $i++;}?>
        </table>
        <?php }?>

        <?php if ($this->config->disable_admin['addons_catalog'] == 0) {?>
            <div class="text-center mt-3">
                <a class="btn btn-success" href="index.php?option=com_jshopping&controller=addonscatalog">
                    <span class="icon-folder" aria-hidden="true"></span>
                    <?php echo Text::_('JSHOP_ADDONS_CATALOG')?>
                </a>
            </div>
        <?php } ?>

        <input type="hidden" name="task" value="" />
        <input type="hidden" name="hidemainmenu" value="0" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php print $this->tmp_html_end?>
    </form>
</div>

<?php 
print \Joomla\CMS\HTML\HTMLHelper::_(
      'bootstrap.renderModal',
      'update_popup',
      array(
          'modal-dialog-scrollable' => true,
          'title'       => Text::_('JSHOP_UPDATE'),          
          'height'      => '400px',
          'width'       => '600px',          
          'modalWidth'  => 40,
      ),
      $this->loadTemplate('update_popup')
);?>

<script>
jQuery(function() {
    jshopAdmin.setMainMenuActive('<?php print Uri::base()?>index.php?option=com_jshopping&controller=other');
});
</script>
