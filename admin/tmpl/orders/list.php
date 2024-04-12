<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die();
$rows=$this->rows;
$lists=$this->lists;
$pageNav=$this->pageNav;
$jshopConfig = \JSFactory::getConfig();
$input = \JFactory::getApplication()->input;
$limitstart = $input->getVar('limitstart' ,'');
$limit = $input->getVar('limit', 10);
$status_id = $input->getVar('status_id', '');
?>
<div class="row">
<div class="col-md-12">
<div id="j-main-container" class="j-main-container">
<form name="adminForm" id="adminForm" method="post" action="index.php?option=com_jshopping&controller=orders">
<?php echo \JHTML::_('form.token');?>
<?php print $this->tmp_html_start?>

<div class="js-stools clearfix jshop_block_filter">    
    <div class="js-stools-container-bar">
        <div class="btn-toolbar" role="toolbar">
            <?php print $this->tmp_html_filter?>

            <div class="btn-group">
                <div class="input-group">
                    <div class="js-stools-field-filter">
                        <div class="control"><?php print $lists['changestatus'];?></div>
                    </div>
                    <div class="js-stools-field-filter">                        
                        <div class="control"><?php print $lists['notfinished'];?></div>
                    </div>
                    <div class="js-stools-field-filter">
                        <div class="control"><?php echo JHTML::_('calendar', $this->filter['date_from'], 'date_from', 'date_from', $jshopConfig->field_birthday_format, array('class'=>'inputbox middle2', 'size'=>'5', 'maxlength'=>'10', 'placeholder'=> JText::_('JSHOP_DATE_FROM')));?></div>
                    </div>
                    <div class="js-stools-field-filter">
                        <div class="control"><?php echo JHTML::_('calendar', $this->filter['date_to'], 'date_to', 'date_to', $jshopConfig->field_birthday_format, array('class'=>'inputbox middle2', 'size'=>'5', 'maxlength'=>'10', 'placeholder'=> JText::_('JSHOP_DATE_TO')));?></div>
                    </div>
                </div>
            </div>

            <div class="btn-group">
                <div class="input-group">
                    <div class="js-stools-field-filter">
                        <input name="text_search" id="text_search" value="<?php echo htmlspecialchars($this->text_search);?>" class="form-control" placeholder="<?php print JText::_('JSHOP_SEARCH')?>" type="text">
                    </div>
                    <div class="js-stools-field-filter">
                        <span class="input-group-append">
                            <button type="submit" class="btn btn-primary hasTooltip" title="<?php print JText::_('JSHOP_SEARCH')?>">
                                <span class="icon-search" aria-hidden="true"></span>
                            </button>                        
                        </span>
                    </div>
                </div>
            </div>

            <div class="js-stools-field-filter">
                <button type="button" class="btn btn-primary js-stools-btn-clear"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
            </div>

            <?php print $this->tmp_html_filter_end?>
        </div>
    </div>
</div>
 
 <table class="table table-striped" width="100%">
 <thead>
   <tr>
     <th width="20">
       #
     </th>
     <th width="20">
       <input type="checkbox" name="checkall-toggle" value="" title="<?php echo \JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
     </th>
     <th width="20">
       <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_NUMBER'), 'order_number', $this->filter_order_Dir, $this->filter_order)?>
     </th>
	 <?php print $this->_tmp_cols_1?>
     <th>
       <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_USER'), 'name', $this->filter_order_Dir, $this->filter_order)?>
     </th>
     <?php print $this->_tmp_cols_after_user?>
     <th>
       <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_EMAIL'), 'email', $this->filter_order_Dir, $this->filter_order)?>
     </th>
	 <?php print $this->_tmp_cols_3?>
     <?php if ($this->show_vendor){?>
     <th>
       <?php echo JText::_('JSHOP_VENDOR')?>
     </th>
     <?php }?>
     <th class="center">
       <?php echo JText::_('JSHOP_ORDER_PRINT_VIEW')?>
     </th>
	 <?php print $this->_tmp_cols_4?>
     <th>
       <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_DATE'), 'order_date', $this->filter_order_Dir, $this->filter_order)?>
     </th>
     <th>
       <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_ORDER_MODIFY_DATE'), 'order_m_date', $this->filter_order_Dir, $this->filter_order)?>
     </th>
	 <?php print $this->_tmp_cols_5?>
     <?php if (!$jshopConfig->without_payment){?>
     <th>
       <?php echo JText::_('JSHOP_PAYMENT')?>
     </th>
     <?php }?>
     <?php if (!$jshopConfig->without_shipping){?>
     <th>
       <?php echo JText::_('JSHOP_SHIPPINGS')?>
     </th>
     <?php }?>
     <th>
       <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_STATUS'), 'order_status', $this->filter_order_Dir, $this->filter_order)?>
     </th>
	 <?php print $this->_tmp_cols_6?>
     <th>
       <?php echo JText::_('JSHOP_ORDER_UPDATE')?>
     </th>
	 <?php print $this->_tmp_cols_7?>
     <th>
       <?php echo \JHTML::_('grid.sort', JText::_('JSHOP_ORDER_TOTAL'), 'order_total', $this->filter_order_Dir, $this->filter_order)?>
     </th>
	 <?php print $this->_tmp_cols_8?>
     <?php if ($jshopConfig->shop_mode==1){?>
     <th class="center">
       <?php echo JText::_('JSHOP_TRANSACTIONS')?>
     </th>
     <?php }?>
     <th class="center">
       <?php echo JText::_('JSHOP_EDIT')?>
     </th>  
   </tr>
   </thead>
   <?php 
   $i=0; 
   foreach($rows as $row){
       $display_info_order=$row->display_info_order;
   ?>
   <tr class="row<?php echo ($i  %2);?>" <?php if (!$row->order_created) print "style='font-style:italic; color: #b00;'"?>>
     <td>
       <?php echo $pageNav->getRowOffset($i);?>
     </td>
     <td>
        <?php if ($row->blocked){?>
            <img src="components/com_jshopping/images/checked_out.png" />
        <?php }else{?>
            <?php echo \JHTML::_('grid.id', $i, $row->order_id);?>
        <?php }?>
     </td>
     <td>
       <a class="order_detail" href = "index.php?option=com_jshopping&controller=orders&task=show&order_id=<?php echo $row->order_id?>"><?php echo $row->order_number;?></a>
       <?php if (!$row->order_created) print "(".JText::_('JSHOP_NOT_FINISHED').")";?>
	   <?php print $row->_tmp_ext_info_order_number?>
     </td>
	 <?php print $row->_tmp_cols_1?>
     <td>
        <?php if ($row->user_id > 0){?>
         <a href="index.php?option=com_jshopping&controller=users&task=edit&user_id=<?php print $row->user_id?>">
        <?php }?>
        <?php echo $row->name?>
        <?php if ($row->user_id > 0){?>
         </a>
        <?php }?>
     </td>
     <?php print $row->_tmp_cols_after_user?>
     <td><?php echo $row->email?></td>
	 <?php print $row->_tmp_cols_3?>
     <?php if ($this->show_vendor){?>
     <td>
        <?php print $row->vendor_name;?>
     </td>
     <?php }?>
     <td class="center">
		<?php if ($jshopConfig->generate_pdf){?>
            <?php if ($display_info_order && $row->order_created){?>
                <?php if ($row->pdf_file!=''){?>
                    <a href="javascript:void window.open('<?php echo $jshopConfig->pdf_orders_live_path."/".$row->pdf_file?>', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=800,height=600,directories=no,location=no');">
                        <img border="0" src="components/com_jshopping/images/jshop_print.png" alt="print" />
                    </a>
                <?php }elseif($jshopConfig->send_invoice_manually){?>
                    <a href="index.php?option=com_jshopping&controller=orders&task=send&order_id=<?php echo $row->order_id?>&back=orders" title="<?php print JText::_('JSHOP_SEND_MAIL')?>">
                        <i class="icon-envelope"></i>
                    </a>
                <?php }?>
            <?php }?>
        <?php }else{?>
            <a href = "javascript:void window.open('index.php?option=com_jshopping&controller=orders&task=printOrder&order_id=<?php echo $row->order_id?>&tmpl=component', 'win2', 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=yes,resizable=yes,width=800,height=600,directories=no,location=no');">
                <img border="0" src="components/com_jshopping/images/jshop_print.png" alt="printhtml" />
            </a>
        <?php }?>
        <?php if (isset($row->_ext_order_info)) echo $row->_ext_order_info;?>
     </td>
	 <?php print $row->_tmp_cols_4?>
     <td>
       <?php echo JSHelper::formatdate($row->order_date, 1);?>
     </td>
     <td>
       <?php echo JSHelper::formatdate($row->order_m_date, 1);?>
     </td>
	 <?php print $row->_tmp_cols_5?>
     <?php if (!$jshopConfig->without_payment){?>
     <td>
       <?php echo $row->payment_name?>
     </td>
     <?php }?>
     <?php if (!$jshopConfig->without_shipping){?>
     <td>
       <?php echo $row->shipping_name?>
     </td>
     <?php }?>
     <td>
        <?php if ($display_info_order && $row->order_created){
            echo \JHTML::_('select.genericlist', $lists['status_orders'], 'select_status_id['.$row->order_id.']', 'class="inputbox form-control" style = "width: 100px" id="status_id_'.$row->order_id.'"', 'status_id', 'name', $row->order_status );
        }else{
            print $this->list_order_status[$row->order_status];
        }
        ?>
		<?php print $row->_tmp_ext_info_status?>
     </td>
	 <?php print $row->_tmp_cols_6?>
     <td>
     <?php if ($row->order_created && $display_info_order){?>
        <input class="inputbox va-middle" type="checkbox" name="order_check_id[<?php echo $row->order_id?>]" id="order_check_id_<?php echo $row->order_id?>" />
        <label class="fs-14" for="order_check_id_<?php echo $row->order_id?>"><?php echo JText::_('JSHOP_NOTIFY_CUSTOMER')?></label>
        <input class="button btn btn-primary" type="button" name="" value="<?php echo JText::_('JSHOP_UPDATE_STATUS')?>" onclick="jshopAdmin.verifyStatus(<?php echo $row->order_status; ?>, <?php echo $row->order_id; ?>, '<?php echo addslashes(JText::_('JSHOP_CHANGE_ORDER_STATUS'))?>', 0);" />
     <?php }?>
     <?php if ($display_info_order && !$row->order_created && !$row->blocked){?>
        <a href="index.php?option=com_jshopping&controller=orders&task=finish&order_id=<?php print $row->order_id?>&js_nolang=1"><?php print JText::_('JSHOP_FINISH_ORDER')?></a>
     <?php }?>
	 <?php print $row->_tmp_ext_info_update?>
     </td>
	 <?php print $row->_tmp_cols_7?>
     <td>
       <?php if ($display_info_order) echo \JSHelper::formatprice( $row->order_total,$row->currency_code)?>
	   <?php print $row->_tmp_ext_info_order_total?>
     </td>
	 <?php print $row->_tmp_cols_8?>
     <?php if ($jshopConfig->shop_mode==1){?>
     <td class="center">
       <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=orders&task=transactions&order_id=<?php print $row->order_id;?>'>
        <img src='components/com_jshopping/images/jshop_options_s.png'>
       </a>
     </td>
     <?php }?>
     <td class="center">
     <?php if ($display_info_order){?>
        <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=orders&task=edit&order_id=<?php print $row->order_id;?>&client_id=<?php print $this->client_id;?>'>
            <i class="icon-edit"></i>
        </a>
     <?php }?>
   </td>
   </tr>
   <?php
   $i++;
   }
   ?>
<tr>
	<?php
    $cols = 10;
    if (!$jshopConfig->without_payment) $cols++;
    if (!$jshopConfig->without_shipping) $cols++;
    if ($this->show_vendor) $cols++;
    ?>
    <?php print $this->_tmp_cols_foot_total?>
    <td colspan="<?php print $cols+(int)$this->deltaColspan0?>" class="right"><b><?php print JText::_('JSHOP_TOTAL')?></b></td>
    <td><b><?php print \JSHelper::formatprice($this->total, \JSHelper::getMainCurrencyCode())?></b></td>
    <?php if ($jshopConfig->shop_mode==1){?>
     <td></td>
    <?php }?>
    <td></td>
</tr>
<tfoot>
<tr>
	<?php 
    $cols = 20;
    if (!$jshopConfig->without_payment) $cols++;
    if (!$jshopConfig->without_shipping) $cols++;
    if ($this->show_vendor) $cols++;
    ?>
<?php print $this->tmp_html_col_before_td_foot?>
<td colspan="<?php print $cols+(int)$this->deltaColspan?>">
	<div class="jshop_list_footer"><?php echo $pageNav->getListFooter(); ?></div>
    <div class="jshop_limit_box"><?php echo $pageNav->getLimitBox(); ?></div>
</td>
<?php print $this->tmp_html_col_after_td_foot?>
</tr>
</tfoot>  
</table>

<input type="hidden" name="filter_order" value="<?php echo $this->filter_order?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value = "0" />
<input type="hidden" name="client_id" value ="<?php echo $this->client_id?>" />
<input type="hidden" name="js_nolang" id='js_nolang' value="0">
<?php print $this->tmp_html_end?>
</form>
<?php print $this->_tmp_order_list_html_end;?>
</div>
</div>
</div>