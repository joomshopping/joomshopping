<?php
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

/**
* @version      5.5.5 01.02.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/ 
defined('_JEXEC') or die();
$rows = $this->rows;
?>

<div id="j-main-container" class="j-main-container">
    <?php HelperAdmin::displaySubmenuOptions();?>
    <form action="index.php?option=com_jshopping&controller=logs" method="post" name="adminForm" id="adminForm">
        <?php print $this->tmp_html_start?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="title" width="10"> # </th>
                    <th width="20">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>
                    <th align="left"><?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_TITLE'), 'name', $this->filter_order_Dir, $this->filter_order); ?></th>
                    <th align="left"><?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_DATE'), 'date', $this->filter_order_Dir, $this->filter_order); ?></th>
                    <th align="left"><?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_SIZE'), 'size', $this->filter_order_Dir, $this->filter_order); ?></th>
                    <th class="center"><?php echo Text::_('JSHOP_DOWNLOAD')?></th>
                    <th class="center"><?php echo Text::_('JSHOP_DELETE')?></th>
                </tr>
            </thead>
            <?php $i = 0; ?>
            <?php foreach($rows as $file){?>
            <tr>
                <td>
                    <?php echo $i + 1;?>
                </td>
                <td>
                    <?php echo HTMLHelper::_('grid.id', $i, $file[0]);?>
                </td>                
                <td>
                    <a href="index.php?option=com_jshopping&controller=logs&task=edit&id=<?php echo $file[0];?>">
                        <?php echo $file[0];?>
                    </a>
                </td>
                <td><?php print date('Y-m-d H:i:s', $file[1])?></td>
                <td><?php print $file[2]?></td>
                <td class="center">
                    <a href="index.php?option=com_jshopping&controller=logs&task=download&id=<?php echo $file[0];?>">
                        <i class="icon-download"></i>
                    </a>
                </td>
                <td class="center">
                    <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=logs&task=remove&cid=<?php print $file[0];?>' onclick="return confirm('<?php print Text::_('JSHOP_DELETE')?>');">
                        <i class="icon-delete"></i>
                    </a>
                </td>
            </tr>
            <?php
            $i++;
            }
            ?>
        </table>
        <input type="hidden" name="filter_order" value="<?php echo $this->filter_order?>">
        <input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir?>">        
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="hidemainmenu" value="0" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php print $this->tmp_html_end?>
    </form>
</div>
<script>
jQuery(function() {
    jshopAdmin.setMainMenuActive('<?php print Uri::base()?>index.php?option=com_jshopping&controller=other');
});
</script>