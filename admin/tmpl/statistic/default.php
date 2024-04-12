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
$paid_status=$this->paid_status;
?>
<div class="row">
<div class="col-md-12">
<div id="j-main-container" class="j-main-container">
<?php JSHelperAdmin::displaySubmenuOptions();?>
<form action="index.php?option=com_jshopping" method="post" name="adminForm" id="adminForm">
<?php print $this->tmp_html_start?>
<div class="jshop_edit" style="width:100%; ">
<div>
<fieldset class="adminform" >
<legend><?php echo JText::_('JSHOP_ORDERS_STATISTICS')?></legend>

<table class="table table-striped">
<thead>
  <tr>

    <th width="140" align="left" rowspan="2">
      <?php echo JText::_('JSHOP_STATUS')?>
    </th>
    <th width="115" colspan="2">
      <?php echo JText::_('JSHOP_THIS_DAY')?>
    </th>
    <th width="115" colspan="2">
        <?php echo JText::_('JSHOP_THIS_WEEK')?>
    </th>
    <th width="115" colspan="2">
        <?php echo JText::_('JSHOP_THIS_MONTH')?>
    </th>
    <th width="115" colspan="2">
        <?php echo JText::_('JSHOP_THIS_YEAR')?>
    </th>    
  </tr>
    <tr>

    <th width="30">
      <?php echo JText::_('JSHOP_COUNT')?>
    </th>
    <th width="85">
      <?php echo JText::_('JSHOP_PRICE')?>
    </th>    
    <th width="30">
      <?php echo JText::_('JSHOP_COUNT')?>
    </th>
    <th width="85">
      <?php echo JText::_('JSHOP_PRICE')?>
    </th> 
    <th width="30">
      <?php echo JText::_('JSHOP_COUNT')?>
    </th>
    <th width="85">
      <?php echo JText::_('JSHOP_PRICE')?>
    </th> 
    <th width="30">
      <?php echo JText::_('JSHOP_COUNT')?>
    </th>
    <th width="85">
      <?php echo JText::_('JSHOP_PRICE')?>
    </th>   
  </tr>
</thead> 
<?php $total_d=$total_sum_d=$total_w=$total_sum_w=$total_m=$total_sum_m=$total_y=$total_sum_y=0;
$ptotal_d=$ptotal_sum_d=$ptotal_w=$ptotal_sum_w=$ptotal_m=$ptotal_sum_m=$ptotal_y=$ptotal_sum_y=0;
?> 
<?php foreach($rows as $row){ 
      
    ?>
  <tr >
   <td>
     <b><?php echo $row['name'];?></b>
   </td>
   <td style="text-align:right;">
     <?php $k=0;  foreach($this->today as $res)
     {
     if ($row['status_id'] == $res['order_status']) 
        {
            $damount=$res['amount']; $dsum=$res['total_sum']; $k=1;
        }
     
     }
     if ($k==0) {$damount ='0';   $dsum='0';    }
     $total_d+=$damount;  $total_sum_d+=$dsum;
     if (in_array($row['status_id'] , $paid_status)) { $ptotal_d+=$damount;  $ptotal_sum_d+=$dsum; } 
     echo   $damount;
     ?> 
      
   </td>
   <td style="text-align:right;">

     <?php  echo \JSHelper::formatprice( $dsum); ?>

   </td>   
	<td style="text-align:right;">
     <?php $k=0; foreach($this->week as $res)
     {
     if ($row['status_id'] == $res['order_status']) 
        {$damount=$res['amount']; $dsum=$res['total_sum']; $k=1;}
     
     }
     if ($k==0) {$damount ='0';   $dsum='0';    }
     $total_w+=$damount;  $total_sum_w+=$dsum;
     if (in_array($row['status_id'] , $paid_status)) { $ptotal_w+=$damount;  $ptotal_sum_w+=$dsum; }      
     echo   $damount;
     ?> 
   	</td>
    <td style="text-align:right;">
          <?php  echo \JSHelper::formatprice( $dsum) ; ?>   
    </td>
    <td style="text-align:right;">
     <?php $k=0; foreach($this->month as $res)
     {
     if ($row['status_id'] == $res['order_status']) 
        {$damount=$res['amount']; $dsum=$res['total_sum']; $k=1;}
     
     }
     if ($k==0) {$damount ='0';   $dsum='0';    }
     $total_m+=$damount;  $total_sum_m+=$dsum;
     if (in_array($row['status_id'] , $paid_status)) { $ptotal_m+=$damount;  $ptotal_sum_m+=$dsum; }      
     echo   $damount;
     ?>         
    </td>
    <td style="text-align:right;">
     <?php  echo \JSHelper::formatprice( $dsum); ?>  
    </td>
    <td style="text-align:right;">
     <?php $k=0; foreach($this->year as $res)
     {
     if ($row['status_id'] == $res['order_status']) 
        {$damount=$res['amount']; $dsum=$res['total_sum']; $k=1;}
     
     }
     if ($k==0) {$damount ='0';   $dsum='0';    }
     $total_y+=$damount;  $total_sum_y+=$dsum;
     if (in_array($row['status_id'] , $paid_status)) { $ptotal_y+=$damount;  $ptotal_sum_y+=$dsum; }      
     echo   $damount;
     ?>         
    </td>  
    <td style="text-align:right;">
    <?php  echo \JSHelper::formatprice( $dsum); ?>
    </td>  
  </tr>
  <?php
      }
?>
   <tr>

    <th colspan="9" style="b">
      
    </th>
 
  </tr>
  <tr >
   <th>
     <?php echo JText::_('JSHOP_TOTAL_PAID')?>
   </th>
   <th style="text-align:right;">
     <?php   echo   $ptotal_d; ?> 
   </th>
   <th style="text-align:right;">

     <?php  echo \JSHelper::formatprice( $ptotal_sum_d); ?>

   </th>   
    <th style="text-align:right;">
      <?php   echo   $ptotal_w; ?>  
    </th>
    <th style="text-align:right;">
      <?php  echo \JSHelper::formatprice( $ptotal_sum_w); ?>  
    </th>
    <th style="text-align:right;">
       <?php   echo   $ptotal_m; ?> 
    </th>
    <th style="text-align:right;">
       <?php  echo \JSHelper::formatprice( $ptotal_sum_m); ?>  
    </th>
    <th style="text-align:right;">
       <?php   echo   $ptotal_y; ?>  
    </th>  
    <th style="text-align:right;">
        <?php  echo \JSHelper::formatprice( $ptotal_sum_y); ?>  
    </th>  
  </tr>
  <tr >
   <th>
     <?php echo JText::_('JSHOP_TOTAL')?>
   </th>
   <th style="text-align:right;">
     <?php   echo   $total_d; ?> 
   </th>
   <th style="text-align:right;">

     <?php  echo \JSHelper::formatprice( $total_sum_d); ?>

   </th>   
    <th style="text-align:right;">
     <?php   echo   $total_w; ?>  
    </th>
    <th style="text-align:right;">
     <?php  echo \JSHelper::formatprice( $total_sum_w); ?>  
    </th>
    <th style="text-align:right;">
      <?php   echo   $total_m; ?>  
    </th>
    <th style="text-align:right;">
      <?php  echo \JSHelper::formatprice( $total_sum_m); ?> 
    </th>
    <th style="text-align:right;">
      <?php   echo   $total_y; ?>  
    </th>  
    <th style="text-align:right;">
       <?php  echo \JSHelper::formatprice( $total_sum_y); ?>   
    </th>  
  </tr>
</table>
</fieldset>
</div>
<div>
<div style="padding-left:5px;">
<fieldset class="adminform">

<div class="row">
    <div class="col-md-4">
        <table style="width:100%;">
        <tr>
            <th colspan="2" >
            <?php echo JText::_('JSHOP_CUSTOMERS')?>:
            </th>
            <!--<th colspan="2" >
            <?php echo JText::_('JSHOP_STAFF')?>:
            </th>-->
        </tr>
        <tr>
            <td style="width:100px;">
            <?php echo JText::_('JSHOP_TOTAL')?>:
            </td>
            <td style="width:100px;">
            <?php echo $this->customer;?>
            </td>
            <!--<td style="width:100px;">
            <?php if (isset($this->stuff1['usertype'])) echo $this->stuff1['usertype'];?>:
            </td>
            <td>
            <?php if (isset($this->stuff1['amount'])) echo $this->stuff1['amount'];?>
            </td>-->
        </tr>
        <tr>
            <td>
            <?php echo JText::_('JSHOP_ENABLED')?>:
            </td>
            <td>
            <?php if (isset($this->customer_enabled)) echo $this->customer_enabled;?>
            </td>
            <td>
            <!--<?php if (isset($this->stuff2['usertype'])) echo $this->stuff2['usertype'];?>:
            </td>
            <td>
            <?php if (isset($this->stuff2['amount'])) echo $this->stuff2['amount'];?>
            </td>-->
        </tr>
        <tr>
            <td>
            <?php echo JText::_('JSHOP_LOGGEDIN')?>:
            </td>
            <td>
            <?php echo $this->customer_loggedin;?>
            </td>
            <td>
            <!--<?php if (isset($this->stuff3['usertype'])) echo $this->stuff3['usertype'];?>:
            </td>
            <td>
            <?php if (isset($this->stuff3['amount'])) echo $this->stuff3['amount'];?>
            </td>-->
        </tr>
        <tr>
            <th colspan="4"> </th>
        </tr>
        <tr>
            <th colspan="4"><?php echo JText::_('JSHOP_USERGROUPS')?>:  </th>
        </tr>
        <?php foreach($this->usergroups as $res):?>
        <tr>
            <td>
            <?php echo $res['usergroup_name'];?>:
            </td>
            <td>
            <?php echo $res['amount'];?>
            </td>
            <td></td><td></td>
        </tr>
        <?php endforeach;?>
        </table>
    </div>

    <div class="col-md-4">
        <div><b><?php echo JText::_('JSHOP_CATEGORY_INVENTORY')?>:</b></div>
        <div>
            <?php echo JText::_('JSHOP_TOTAL')?>:
            <?php $active_c=$nonactive_c=0; foreach($this->category as $res)
            {
             if ($res['category_publish']=='1')   $active_c=$res['amount'];
             if ($res['category_publish']=='0')   $nonactive_c=$res['amount'];

            }
            ?>
            <?php echo $active_c+$nonactive_c;?>
        </div>
        <div>
            <?php echo JText::_('JSHOP_ACTIVE')?>:

            <?php echo $active_c;?>
        </div>
        
    </div>
    <div class="col-md-4">
        <div><b><?php echo JText::_('JSHOP_MANUFACTURE_INVENTORY')?>:</b></div>
        <div>
        <?php echo JText::_('JSHOP_TOTAL')?>:
        
         <?php $active_m=$nonactive_m=0; foreach($this->manufacture as $res)
         {
         if ($res['manufacturer_publish']=='1')   $active_m=$res['amount'];
         if ($res['manufacturer_publish']=='0')   $nonactive_m=$res['amount'];

         }
        ?>
        <?php echo $active_m+$nonactive_m;?>
        </div>
        <div>
            <?php echo JText::_('JSHOP_ACTIVE')?>:

            <?php echo $active_m;?>
        </div>
    </div>
</div>

</fieldset> 
</div>
</div> 

</div>
<?php print $this->tmp_html_end?>
<div class="clearfix"></div>
<input type="hidden" name="task">
</form>
</div>
</div>
</div>
<script>
jQuery(function(){
	jshopAdmin.setMainMenuActive('<?php print JURI::base()?>index.php?option=com_jshopping&controller=other');
});
</script>