<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
* @version      5.6.0 01.03.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$rows = $this->rows;
$pageNav = $this->pageNav;
$select_user = $this->select_user;
?>

<div id="j-main-container" class="j-main-container">  
<form name="adminForm" id="adminForm" method="post" action="index.php?option=com_jshopping&controller=users<?php if($select_user) {?>&tmpl=component&select_user=1<?php }?>">
<?php echo HTMLHelper::_('form.token');?>
<?php print $this->tmp_html_start?>

<div class="js-filters">
    <?php print $this->tmp_html_filter?>
    <div>
        <?php print $this->select_group?>
    </div>
    <div>
        <?php print $this->select_enable?>
    </div>

    <div>
        <input name="text_search" id="text_search" value="<?php echo htmlspecialchars($this->text_search);?>" class="form-control" placeholder="<?php print Text::_('JSHOP_SEARCH')?>" type="text">
    </div>
    <div>
        <span class="input-group-append">
            <button type="submit" class="btn btn-primary hasTooltip" title="<?php print Text::_('JSHOP_SEARCH')?>">
                <span class="icon-search" aria-hidden="true"></span>
            </button>                        
        </span>
    </div>
    <div>
        <button type="button" class="btn btn-primary js-stools-btn-clear"><?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?></button>
    </div>
    <?php print $this->tmp_html_filter_end?>
</div>


<table class="table table-striped" width="100%">
<thead>
<tr>
    <th width="20">
    #
    </th>
    <?php if (!$select_user) {?> 
    <th width="20">
        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
    </th>
    <?php }?>
    <th align="left">
        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_NUMBER'), 'number', $this->filter_order_Dir, $this->filter_order)?>
    </th>
    <th align="left">
        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_USERNAME'), 'u_name', $this->filter_order_Dir, $this->filter_order)?>
    </th>
    <th width="150" align="left">
        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_USER_FIRSTNAME'), 'f_name', $this->filter_order_Dir, $this->filter_order)?>
    </th>
    <th width="150" align="left">
        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_USER_LASTNAME'), 'l_name', $this->filter_order_Dir, $this->filter_order)?>
    </th>
    <th align="left">
        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_FIRMA'), 'firma_name', $this->filter_order_Dir, $this->filter_order)?>
    </th>
    <th>
        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_EMAIL'), 'U.email', $this->filter_order_Dir, $this->filter_order)?>
    </th>
    <?php print $this->tmp_html_col_after_email?>
    <th>
        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_USERGROUP_NAME'), 'usergroup_name', $this->filter_order_Dir, $this->filter_order)?>
    </th>
    <?php if(!$select_user) {?> 
    <th class="center">
        <?php print Text::_('JSHOP_ORDERS')?>
    </th>
    <th class="center">
        <?php print Text::_('JSHOP_ENABLED')?>
    </th>
    <th width="50" class="center">
        <?php echo Text::_('JSHOP_EDIT')?>
    </th>
    <?php }?>
    <th width="40" class="center">
        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_ID'), 'user_id', $this->filter_order_Dir, $this->filter_order)?>
    </th>
    <?php print $this->tmp_html_col_after_id?>
</tr>
</thead> 
<?php $i=0; foreach($rows as $row){?>
<tr class="row<?php echo ($i  %2);?>">
    <td>
        <?php echo $pageNav->getRowOffset($i);?>
    </td>
    <?php if (!$select_user) {?> 
    <td>
        <?php echo HTMLHelper::_('grid.id', $i, $row->user_id);?>
    </td>
    <?php }?>
    <td>
        <?php if ($select_user) {?>
            <a onclick="window.parent.selectUserBehaviour('<?php echo $row->user_id?>','<?php echo $this->e_name?>')" href="#"> 
        <?php }?>
        <?php echo $row->number;?>
        <?php if ($select_user) {?>
            </a>
        <?php }?>
        <?php print $row->tmp_html_after_number ?? '';?>
    </td>
    <td>
        <?php if(!$select_user) {?>    
        <a href="index.php?option=com_jshopping&controller=users&task=edit&user_id=<?php echo $row->user_id?>">
            <?php echo $row->u_name?>
        </a>
        <?php }else{?>
        <a onclick="window.parent.selectUserBehaviour('<?php echo $row->user_id?>','<?php echo $this->e_name?>')" href="#">  
            <?php echo $row->u_name?>
        </a>
        <?php }?>
        <?php print $row->tmp_html_after_u_name ?? '';?>
    </td>
    <td>
        <?php echo $row->f_name;?>
    </td>
    <td>
        <?php echo $row->l_name;?>
    </td>
    <td>
        <?php echo $row->firma_name;?>
    </td>
    <td>
        <?php echo $row->email;?>
    </td>
    <?php print $row->tmp_html_col_after_email?>
    <td>
        <?php echo $row->usergroup_name;?>
    </td>
    <?php if (!$select_user) {?> 
    <td class="center">
        <a class="no_blank" href='index.php?option=com_jshopping&controller=orders&client_id=<?php print $row->user_id?>' target='_blank'>
            <i class="icon-list"></i>
        </a>
    </td>
    <td class="center">
        <?php echo HTMLHelper::_('jgrid.published', !$row->block, $i);?>
    </td>
    <td class="center">
        <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=users&task=edit&user_id=<?php print $row->user_id?>'>
            <i class="icon-edit"></i>
        </a>
    </td>
    <?php }?>
    <td class="center">
        <?php print $row->user_id?>
    </td>
    <?php print $row->tmp_html_col_after_id?>
</tr>

<?php 
$i++;
}?>  
</table>
<div class="d-flex justify-content-between align-items-center">
    <div class="jshop_list_footer"><?php echo $pageNav->getListFooter(); ?></div>
    <div class="jshop_limit_box"><?php echo $pageNav->getLimitBox(); ?></div>
</div>
<input type="hidden" name="filter_order" value="<?php echo $this->filter_order?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<?php print $this->tmp_html_end?>
</form>
</div>