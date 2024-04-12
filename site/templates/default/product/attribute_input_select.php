<?php
/**
* @version      5.1.0 14.09.2022
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die();
?>
<?php $param = 'class="'.$this->config->frontend_attribute_select_class_css.'" size = "'.$this->config->frontend_attribute_select_size.'" onchange="jshop.setAttrValue(\''.$this->attr_id.'\', this.value);"';?>
<?php print \JHTML::_('select.genericlist', $this->options, 'jshop_attr_id['.$this->attr_id.']', $param, 'val_id', 'value_name', $this->active);?>
<span class='prod_attr_img'><img id="prod_attr_img_<?php print $this->attr_id?>" src="<?php print $this->url_attr_img?>" alt=""></span>