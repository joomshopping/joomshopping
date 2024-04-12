<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die;
?>
<?php print JText::_('JSHOP_HI')?> <?php print $this->order->f_name;?> <?php print $this->order->l_name;?>,
<?php printf(JText::_('JSHOP_YOUR_ORDER_STATUS_CHANGE'), $this->order->order_number);?>

<?php print JText::_('JSHOP_NEW_STATUS_IS')?>: <?php print $this->order_status?> 
<?php if ($this->order_detail){?>
<?php print JText::_('JSHOP_ORDER_DETAILS')?>: <?php print $this->order_detail?>
<?php }?>

<?php if ($this->comment!=""){?>
<?php print JText::_('JSHOP_COMMENT_YOUR_ORDER')?>: <?php print $this->comment;?>

<?php }?>

<?php print $this->vendorinfo->company_name?> 
<?php print $this->vendorinfo->adress?> 
<?php print $this->vendorinfo->zip?> <?php print $this->vendorinfo->city?> 
<?php print $this->vendorinfo->country?> 
<?php print JText::_('JSHOP_CONTACT_PHONE')?>: <?php print $this->vendorinfo->phone?> 
<?php print JText::_('JSHOP_CONTACT_FAX')?>: <?php print $this->vendorinfo->fax?>