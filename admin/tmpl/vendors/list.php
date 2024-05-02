<?php
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$i = 0;
$rows = $this->rows;
$pageNav = $this->pageNav;
?>

<div id="j-main-container" class="j-main-container">
<?php HelperAdmin::displaySubmenuOptions();?>
<form name="adminForm" id="adminForm" method="post" action="index.php?option=com_jshopping&controller=vendors">
<?php print $this->tmp_html_start?>

<div class="js-filters">

   <?php print $this->tmp_html_filter?>

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
     <th width="20">
        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
     </th>
     <th width="150" align="left">
       <?php echo Text::_('JSHOP_USER_FIRSTNAME')?>
     </th>
     <th width="150" align="left">
       <?php echo Text::_('JSHOP_USER_LASTNAME')?>
     </th>
     <th align="left">
       <?php echo Text::_('JSHOP_STORE_NAME')?>
     </th>
     <th width="150">
       <?php echo Text::_('JSHOP_EMAIL')?>
     </th>
     <th width="60" class="center">
        <?php echo Text::_('JSHOP_DEFAULT')?>    
    </th>	 	      
     <th width="50" class="center">
        <?php echo Text::_('JSHOP_EDIT')?>
    </th>
     <th width="40" class="center">
        <?php echo Text::_('JSHOP_ID')?>
    </th>
</tr>
</thead> 
<?php 
$i=0; 
foreach($rows as $row){?>
<tr class="row<?php echo ($i%2);?>">
     <td align="center">
        <?php echo $pageNav->getRowOffset($i);?>
     </td>
     <td align="center">
        <?php echo HTMLHelper::_('grid.id', $i, $row->id);?>
     </td>
     <td>
        <?php echo $row->f_name?>
     </td>
     <td>
        <?php echo $row->l_name;?>
     </td>
     <td>
        <?php echo $row->shop_name;?>
     </td>
     <td>
        <?php echo $row->email;?>
     </td>
     <td class="center">
     <?php if ($row->main==1) {?>
        <a class="btn btn-micro btn-nopad">
            <i class="icon-default"></i>
        </a>
     <?php }?>
     </td>
     <td class="center">
        <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=vendors&task=edit&id=<?php print $row->id?>'>
            <i class="icon-edit"></i>
        </a>
     </td>
     <td class="center">
        <?php print $row->id?>
     </td>
</tr>
<?php 
$i++;
}?>
</table>
<div class="d-flex justify-content-between align-items-center">
    <div class="jshop_list_footer"><?php echo $pageNav->getListFooter(); ?></div>
    <div class="jshop_limit_box"><?php echo $pageNav->getLimitBox(); ?></div>
</div>
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<?php print $this->tmp_html_end?>
</form>
</div>
<script>
jQuery(function(){
	jshopAdmin.setMainMenuActive('<?php print Uri::base()?>index.php?option=com_jshopping&controller=other');
});
</script>