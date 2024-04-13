<?php
/**
* @version      5.4.0 06.04.2024
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

use Joomla\Component\Jshopping\Site\Helper\SelectOptions;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die();

$rows = $this->rows;
$i = 0;
?>
<div class="row">
<div class="col-md-12">
<div id="j-main-container" class="j-main-container">
    <?php HelperAdmin::displaySubmenuOptions('addons');?>
    <form action="index.php?option=com_jshopping&controller=addons&task=listweb" method="post" name="adminForm" id="adminForm">
		
		<div class="js-filters">
			<div>
				<?php echo HTMLHelper::_('select.genericlist', SelectOptions::getItems('- '.Text::_('JSHOP_CATEGORY').' -', $this->cats), 'category_id', 'class="form-select" onchange="document.adminForm.submit();"', 'id', 'name', $this->filter['category_id'] ?? '');?>
            </div>
            <div>
				<?php echo HTMLHelper::_('select.genericlist', SelectOptions::getItems('- '.Text::_('JSHOP_TYPE').' -', $this->types), 'type', 'class="form-select" onchange="document.adminForm.submit();"', 'id', 'name', $this->filter['type'] ?? '');?>
            </div>
			<div>
                <input id="text_search" name="text_search" value="<?php echo htmlspecialchars($this->filter['text_search'] ?? '');?>" class="form-control"
                    placeholder="<?php print Text::_('JSHOP_SEARCH')?>" type="text">
            </div>
            <div>
                <button type="submit" class="btn btn-primary hasTooltip"
                    title="<?php print Text::_('JSHOP_SEARCH')?>">
                    <span class="icon-search" aria-hidden="true"></span>
                </button>
            </div>
            <div>
                <button type="button" class="btn btn-primary js-stools-btn-clear"><?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?></button>
            </div>
		</div>
		
		<table class="table table-striped">
		<thead>
			<tr>
				<th width="20">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
                <th width="93">
                    <?php echo Text::_('JSHOP_IMAGE')?>
                </th>
				<th align="left">
					<?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_TITLE'), 'name', $this->filter_order_Dir, $this->filter_order)?>
				</th>
                <th>
                    <?php echo Text::_('JSHOP_COMPATIBILITY')?>
                </th>
                <th>
                    <?php echo HTMLHelper::_( 'grid.sort', Text::_('JSHOP_LATEST')." ".Text::_('JSHOP_VERSION'), 'date', $this->filter_order_Dir, $this->filter_order);?>
                </th>                
                <th class="center">
                    <?php echo Text::_('JSHOP_DOWNLOAD')?>
                </th>
				<th width="40" class="center">
					<?php echo HTMLHelper::_( 'grid.sort', Text::_('JSHOP_ID'), 'id', $this->filter_order_Dir, $this->filter_order);?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($rows as $row){ ?>
		<tr class="row<?php echo $i % 2; ?>" data-draggable-group="1" item-id="<?php echo $row->id;?>" parents="" level="1">			
			<td>
				<?php echo HTMLHelper::_('grid.id', $i, $row->id);?>
			</td>
            <td>
                <?php if (isset($row->image_url)){?>
                    <img src="<?php print $row->image_url?>" width="120">
                <?php }?>
            </td>
			<td>
				<a target="_blank" href="<?php echo $row->url; ?>">
					<?php echo $row->name;?>
				</a>
                <div class="small"><?php echo $row->descr ?? '';?></div>
			</td>
            <td>
                <div class="small">
                    <?php if ($row->js_ver ?? '') {?>
                        <div>JoomShopping: <?php echo $row->js_ver;?></div>
                    <?php } ?>
                    <?php if ($row->j_ver ?? '') {?>
                        <div>Joomla: <?php echo $row->j_ver;?></div>
                    <?php }?>
                </div>
			</td>
            <td>
                <?php if ($row->last_file->version ?? '') {?>
                    <?php echo $row->last_file->version;?>
                    <div class="small">
                        <?php if ($row->last_file->js_ver ?? '') {?>
                            <div>JoomShopping: <?php echo $row->last_file->js_ver;?></div>
                        <?php } ?>
                        <?php if ($row->last_file->j_ver ?? '') {?>
                            <div>Joomla: <?php echo $row->last_file->j_ver;?></div>
                        <?php }?>
                        <div><?php echo Text::_('JSHOP_DATE')?>: <?php echo Helper::formatdate($row->date);?></div>                    
                    </div>
                <?php } ?>
			</td>
            <td class="center">
                <?php if (isset($row->download_url)) {?>
                    <a class="tbody-icon" href="<?php echo $row->download_url;?>" title="<?php echo Text::_('JSHOP_DOWNLOAD')?>"><i class="icon-download"></i></a>
                <?php } else {?>
                    <a class="tbody-icon" href="<?php echo $row->url;?>" target="_blank"><i class="icon-basket"></i></a>
                <?php } ?>
                <?php if (isset($row->install_url)) {?>
                    <a class="ms-2 tbody-icon" href="<?php echo $row->install_url;?>" title="<?php echo Text::_('JSHOP_INSTALL')?>"><i class="icon-flash"></i></a>
                <?php }?>
			</td>
			<td class="center">
				<?php print $row->id;?>
			</td>
		</tr>
		<?php
		$i++;
		}
		?>
		</tbody>
		</table>
		
		<div class="d-flex justify-content-between align-items-center">
            <div class="jshop_list_footer"><?php echo $this->pageNav->getListFooter(); ?></div>
            <div class="jshop_limit_box"><?php echo $this->pageNav->getLimitBox(); ?></div>
        </div>

		<input type="hidden" name="filter_order" value="<?php echo $this->filter_order?>">
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir?>">
		<input type="hidden" name="task" value="listweb">
		<input type="hidden" name="hidemainmenu" value="0">
		<input type="hidden" name="boxchecked" value="0">
    </form>
</div>
</div>
</div>
<script>
jQuery(function() {
    jshopAdmin.setMainMenuActive('<?php print Uri::base()?>index.php?option=com_jshopping&controller=other');
});
</script>