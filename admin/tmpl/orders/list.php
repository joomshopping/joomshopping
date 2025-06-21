<?php
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Helper\Helper;

/**
* @version      5.8.0 19.06.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die();
$rows = $this->rows;
$lists = $this->lists;
$pageNav = $this->pageNav;
$jshopConfig = JSFactory::getConfig();
?>
<div id="j-main-container" class="j-main-container">
    <form name="adminForm" id="adminForm" method="post" action="index.php?option=com_jshopping&controller=orders">
        <?php echo HTMLHelper::_('form.token');?>
        <?php print $this->tmp_html_start?>

        <div class="js-filters">
            <?php print $this->tmp_html_filter?>
            <div>
                <?php print $lists['changestatus'];?>
            </div>
            <div>
                <?php print $lists['notfinished'];?>
            </div>
            <?php if (!$jshopConfig->without_payment){?>
            <div>
                <?php print $lists['payments'];?>
            </div>
            <?php }?>
            <?php if (!$jshopConfig->without_shipping){?>
            <div>
                <?php print $lists['shippings'];?>
            </div>
            <?php }?>
            <div>
                <?php echo HTMLHelper::_('calendar', $this->filter['date_from'], 'date_from', 'date_from', $jshopConfig->store_date_format, array('class'=>'inputbox middle2', 'size'=>'5', 'maxlength'=>'10', 'placeholder'=> Text::_('JSHOP_DATE_FROM')));?>                
            </div>
            <div>                
                <?php echo HTMLHelper::_('calendar', $this->filter['date_to'], 'date_to', 'date_to', $jshopConfig->store_date_format, array('class'=>'inputbox middle2', 'size'=>'5', 'maxlength'=>'10', 'placeholder'=> Text::_('JSHOP_DATE_TO')));?>
            </div>
            <?php print $this->tmp_html_filter_before_btn ?? '';?>
            <div>
                <input name="text_search" id="text_search" value="<?php echo htmlspecialchars($this->text_search);?>"
                    class="form-control" placeholder="<?php print Text::_('JSHOP_SEARCH')?>" type="text">
            </div>
            <div>          
                <button type="submit" class="btn btn-primary hasTooltip"
                    title="<?php print Text::_('JSHOP_SEARCH')?>">
                    <span class="icon-search" aria-hidden="true"></span>
                </button>                
            </div>
            <div>
                <button type="button"
                    class="btn btn-primary js-stools-btn-clear"><?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?></button>
            </div>
            <?php print $this->tmp_html_filter_end?>
        </div>

        <table class="table table-striped shop-list-order">
            <thead>
                <tr>
                    <th width="20">
                        #
                    </th>
                    <th width="20">
                        <input type="checkbox" name="checkall-toggle" value=""
                            title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>
                    <th width="20">
                        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_NUMBER'), 'order_number', $this->filter_order_Dir, $this->filter_order)?>
                    </th>
                    <?php print $this->_tmp_cols_1?>
                    <th>
                        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_USER'), 'name', $this->filter_order_Dir, $this->filter_order)?>
                    </th>
                    <?php print $this->_tmp_cols_after_user?>
                    <th>
                        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_EMAIL'), 'email', $this->filter_order_Dir, $this->filter_order)?>
                    </th>
                    <?php print $this->_tmp_cols_3?>
                    <?php if ($this->show_vendor){?>
                    <th>
                        <?php echo Text::_('JSHOP_VENDOR')?>
                    </th>
                    <?php }?>
                    <th class="center">
                        <?php echo Text::_('JSHOP_ORDER_PRINT_VIEW')?>
                    </th>
                    <?php print $this->_tmp_cols_4?>
                    <th>
                        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_DATE'), 'order_date', $this->filter_order_Dir, $this->filter_order)?>
                    </th>
                    <th>
                        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_ORDER_MODIFY_DATE'), 'order_m_date', $this->filter_order_Dir, $this->filter_order)?>
                    </th>
                    <?php print $this->_tmp_cols_5?>
                    <?php if (!$jshopConfig->without_payment){?>
                    <th>
                        <?php echo Text::_('JSHOP_PAYMENT')?>
                    </th>
                    <?php }?>
                    <?php if (!$jshopConfig->without_shipping){?>
                    <th>
                        <?php echo Text::_('JSHOP_SHIPPINGS')?>
                    </th>
                    <?php }?>
                    <th>
                        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_STATUS'), 'order_status', $this->filter_order_Dir, $this->filter_order)?>
                    </th>
                    <?php print $this->_tmp_cols_6?>
                    <?php print $this->_tmp_cols_7?>
                    <th>
                        <?php echo HTMLHelper::_('grid.sort', Text::_('JSHOP_ORDER_TOTAL'), 'order_total', $this->filter_order_Dir, $this->filter_order)?>
                    </th>
                    <?php print $this->_tmp_cols_8?>
                    <?php if ($jshopConfig->shop_mode==1){?>
                    <th class="center">
                        <?php echo Text::_('JSHOP_TRANSACTIONS')?>
                    </th>
                    <?php }?>
                    <th class="center">
                        <?php echo Text::_('JSHOP_EDIT')?>
                    </th>
                    <th width="50" class="center">
                        <?php echo HTMLHelper::_( 'grid.sort', 'JSHOP_ID', 'order_id', $this->filter_order_Dir, $this->filter_order); ?>
                    </th>					
                </tr>
            </thead>
            <?php 
            $i=0; 
            foreach($rows as $row){
                $display_info_order=$row->display_info_order;
            ?>
            <tr class="row<?php echo ($i  %2);?> <?php if (!$row->order_created) print 'order_not_created'?>">
                <td>
                    <?php echo $pageNav->getRowOffset($i);?>
                </td>
                <td>
                    <?php if ($row->blocked){?>
                        <i class="icon-checkedout"></i>
                    <?php }else{?>
                        <?php echo HTMLHelper::_('grid.id', $i, $row->order_id);?>
                    <?php }?>
                </td>
                <td>
                    <a class="order_detail"
                        href="index.php?option=com_jshopping&controller=orders&task=show&order_id=<?php echo $row->order_id?>"><?php echo $row->order_number;?></a>
                    <?php if (!$row->order_created) print "(".Text::_('JSHOP_NOT_FINISHED').")";?>
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
                        <?php if ($display_info_order){?>
                            <?php if ($row->pdf_file != ''){?>
                                <a class="js_window_popup" patch="<?php echo $jshopConfig->pdf_orders_live_path."/".$row->pdf_file?>" title="<?php print Text::_('JSHOP_EMAIL_BILL')?>">
                                    <i class="icon-print"></i>
                                </a>
                                <?php echo $row->_after_order_pdf ?? '';?>
                            <?php }elseif($jshopConfig->send_invoice_manually && $row->order_created){?>
                                <a href="index.php?option=com_jshopping&controller=orders&task=send&order_id=<?php echo $row->order_id?>&back=orders" title="<?php print Text::_('JSHOP_SEND_MAIL')?>">
                                    <i class="icon-envelope"></i>
                                </a>
                            <?php }?>
                        <?php }?>
                    <?php }else{?>
                        <a class="js_window_popup" patch="index.php?option=com_jshopping&controller=orders&task=printOrder&order_id=<?php echo $row->order_id?>&tmpl=component">
                            <i class="icon-print print-html"></i>
                        </a>
                    <?php }?>
                    <?php if (isset($row->_ext_order_info)) echo $row->_ext_order_info;?>
                </td>
                <?php print $row->_tmp_cols_4?>
                <td>
                    <?php echo Helper::formatdate($row->order_date, 1);?>
                </td>
                <td>
                    <?php echo Helper::formatdate($row->order_m_date, 1);?>
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
                        echo HTMLHelper::_('select.genericlist', $lists['status_orders'], 'select_status_id['.$row->order_id.']', 'class="inputbox form-control" style="width: 100px" id="status_id_'.$row->order_id.'"', 'status_id', 'name', $row->order_status);
                    }else{
                        print $this->list_order_status[$row->order_status] ?? '';
                    }
                    ?>
                    <?php print $row->_tmp_ext_info_status?>

                    <?php if ($row->order_created && $display_info_order){?>
                    <div class="d-none update_status_panel">
                        <div>
                            <input class="inputbox va-middle" type="checkbox" name="order_check_id[<?php echo $row->order_id?>]" id="order_check_id_<?php echo $row->order_id?>">
                            <label class="fs-14" for="order_check_id_<?php echo $row->order_id?>"><?php echo Text::_('JSHOP_NOTIFY_CUSTOMER')?></label>
                        </div>
                        <input class="button btn btn-primary" type="button" name=""
                            value="<?php echo Text::_('JSHOP_UPDATE_STATUS')?>"
                            onclick="jshopAdmin.verifyStatus(<?php echo $row->order_status; ?>, <?php echo $row->order_id; ?>, '<?php echo addslashes(Text::_('JSHOP_CHANGE_ORDER_STATUS'))?>', 0);" />
                    </div>
                    <?php }?>
                    <?php if ($display_info_order && !$row->order_created && !$row->blocked){?>
                    <div>
                        <a href="index.php?option=com_jshopping&controller=orders&task=finish&order_id=<?php print $row->order_id?>&js_nolang=1"><?php print Text::_('JSHOP_FINISH_ORDER')?></a>
                    </div>
                    <?php }?>
                    <?php print $row->_tmp_ext_info_update?>
                </td>
                <?php print $row->_tmp_cols_6?>
                <?php print $row->_tmp_cols_7?>
                <td>
                    <?php if ($display_info_order) echo Helper::formatprice( $row->order_total,$row->currency_code)?>
                    <?php print $row->_tmp_ext_info_order_total?>
                </td>
                <?php print $row->_tmp_cols_8?>
                <?php if ($jshopConfig->shop_mode==1){?>
                <td class="center">
                    <a class="btn btn-micro btn-nopad" href='index.php?option=com_jshopping&controller=orders&task=transactions&order_id=<?php print $row->order_id;?>'>
                        <i class="icon-tree-2"></i>
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
				<td class="center">
					<?php print $row->order_id?>
				</td>				
            </tr>
            <?php
            $i++;
            }
            ?>
            <tr>
                <?php
                $cols = 9;
                if (!$jshopConfig->without_payment) $cols++;
                if (!$jshopConfig->without_shipping) $cols++;
                if ($this->show_vendor) $cols++;
                ?>
                <?php print $this->_tmp_cols_foot_total?>
                <td colspan="<?php print $cols+(int)$this->deltaColspan0?>" class="right">
                    <b><?php print Text::_('JSHOP_TOTAL')?></b>
                </td>
                <td><b><?php print Helper::formatprice($this->total, Helper::getMainCurrencyCode())?></b></td>
                <?php if ($jshopConfig->shop_mode==1){?>
                <td></td>
                <?php }?>
                <td colspan="2"></td>
            </tr>
        </table>

        <div class="d-flex justify-content-between align-items-center">
            <div class="jshop_list_footer"><?php echo $pageNav->getListFooter(); ?></div>
            <div class="jshop_limit_box"><?php echo $pageNav->getLimitBox(); ?></div>
        </div>

        <input type="hidden" name="filter_order" value="<?php echo $this->filter_order?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $this->filter_order_Dir?>" />
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="client_id" value="<?php echo $this->client_id?>" />
        <input type="hidden" name="js_nolang" id='js_nolang' value="0">
        <?php print $this->tmp_html_end?>
    </form>
    <?php print $this->_tmp_order_list_html_end;?>
</div>