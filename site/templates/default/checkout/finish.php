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
<?php if (!empty($this->text)){?>
<?php echo $this->text;?>
<?php }else{?>
<p><?php print JText::_('JSHOP_THANK_YOU_ORDER')?></p>
<?php }?>