<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
use Joomla\Component\Jshopping\Site\Helper\Selects;
defined('_JEXEC') or die();

?>
<div class = "jshop max-500" id="comjshop">
    <h1><?php print JText::_('JSHOP_SEARCH')?></h1>
    
    <form action="<?php print $this->action?>" name="form_ad_search" method="<?php print $this->config->search_form_method?>" class = "form-horizontal">
        <?php if ($this->config->search_form_method=='get'){?>
            <input type="hidden" name="option" value="com_jshopping">
            <input type="hidden" name="controller" value="search">
            <input type="hidden" name="task" value="result">
        <?php }?>
        <input type="hidden" name="setsearchdata" value="1">
        <div class = "jshop">
            <?php print $this->_tmp_ext_search_html_start;?>
            <div class = "control-group">
                <div class = "control-label">
                    <?php print JText::_('JSHOP_SEARCH_TEXT')?>
                </div>
                <div class = "controls">
                    <input type = "text" name = "search" class="input form-control" />
                </div>
            </div>
            <div class = "control-group">
                <div class = "control-label">
                  <?php print JText::_('JSHOP_SEARCH_FOR')?>
                </div>
                <div class = "controls">
                    <input type="radio" name="search_type" value="any" id="search_type_any" checked="checked" /> <label for="search_type_any"><?php print JText::_('JSHOP_ANY_WORDS')?></label>
                    <input type="radio" name="search_type" value="all" id="search_type_all" /> <label for="search_type_all"><?php print JText::_('JSHOP_ALL_WORDS')?></label>
                    <input type="radio" name="search_type" value="exact" id="search_type_exact" /> <label for="search_type_exact"><?php print JText::_('JSHOP_EXACT_WORDS')?></label>
                </div>
            </div>
            <div class = "control-group">
                <div class = "control-label">
                    <?php print JText::_('JSHOP_SEARCH_CATEGORIES')?>
                </div>
                <div class = "controls">
                    <div><?php print Selects::getSearchCategory(null, 'class="inputbox form-control"');?></div>
                    <div>
                        <input type = "checkbox" name = "include_subcat" id = "include_subcat" value = "1" />
                        <label for = "include_subcat"><?php print JText::_('JSHOP_SEARCH_INCLUDE_SUBCAT')?></label>
                    </div>
                </div>
            </div>
            <div class = "control-group">
                <div class = "control-label">
                    <?php print JText::_('JSHOP_SEARCH_MANUFACTURERS')?>    
                </div>
                <div class = "controls">
                    <div><?php print Selects::getManufacturer(null, 'class="inputbox form-control"');?></div>
                </div>
            </div>
            <?php if (\JSHelper::getDisplayPriceShop()){?>
            <div class = "control-group">
                <div class = "control-label">
                    <?php print JText::_('JSHOP_SEARCH_PRICE_FROM')?>
                    (<?php print $this->config->currency_code?>)
                </div>
                <div class = "controls">
                    <input type = "text" class="input form-control" name = "price_from" id = "price_from" />
                </div>
            </div>
            <div class = "control-group">
                <div class = "control-label">
                    <?php print JText::_('JSHOP_SEARCH_PRICE_TO')?>
                    (<?php print $this->config->currency_code?>)
                </div>
                <div class = "controls">
                    <input type = "text" class="input form-control" name = "price_to" id = "price_to" />
                </div>
            </div>
            <?php }?>
            <div class = "control-group">
                <div class = "control-label">
                    <?php print JText::_('JSHOP_SEARCH_DATE_FROM')?>      
                </div>
                <div class = "controls">
                    <?php echo \JHTML::_('calendar','', 'date_from', 'date_from', '%Y-%m-%d', array('class'=>'inputbox form-control', 'size'=>'25', 'maxlength'=>'19')); ?>
                </div>
            </div>
            <div class = "control-group">
                <div class = "control-label">
                    <?php print JText::_('JSHOP_SEARCH_DATE_TO')?>      
                </div>
                <div class = "controls">
                    <?php echo \JHTML::_('calendar','', 'date_to', 'date_to', '%Y-%m-%d', array('class'=>'inputbox form-control', 'size'=>'25', 'maxlength'=>'19')); ?>
                </div>
            </div>
            
            <div id="list_characteristics"><?php print $this->characteristics?></div>
            
            <?php print $this->_tmp_ext_search_html_end;?>
        </div>
        <div class = "control-group">
            <div class = "controls">
                <input type = "submit" class = "btn btn-primary button" value = "<?php print JText::_('JSHOP_SEARCH')?>" />  
            </div>
        </div>
    </form>
</div>