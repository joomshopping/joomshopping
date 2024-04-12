<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
?>
<?php if ($this->allow_review){?>

    <div class="review_header"><?php print JText::_('JSHOP_REVIEWS')?></div>
    
    <?php foreach($this->reviews as $curr){?>
        <div class="review_item">
            <div>
                <span class="review_user"><?php print $curr->user_name?></span>, 
                <span class='review_time'><?php print \JSHelper::formatdate($curr->time, 1);?></span>
            </div>
            <div class="review_text"><?php print nl2br($curr->review)?></div>
            <?php if ($curr->mark && !$this->config->hide_product_rating){?>
                <div class="review_mark"><?php print \JSHelper::showMarkStar($curr->mark);?></div>
            <?php } ?> 
        </div>
    <?php }?>
    
    <?php if ($this->display_pagination){?>
        <table class="jshop_pagination">
            <tr>
                <td><div class="pagination"><?php print $this->pagination?></div></td>
            </tr>
        </table>
    <?php }?>
    <?php if ($this->allow_review > 0){?>
		<?php JHtml::_('behavior.formvalidator'); ?>
        <span class="review"><?php print JText::_('JSHOP_ADD_REVIEW_PRODUCT')?></span>
        
        <form action="<?php print \JSHelper::SEFLink('index.php?option=com_jshopping&controller=product&task=reviewsave');?>" name="add_review" method="post" class="form-validate">
        
            <input type="hidden" name="product_id" value="<?php print $this->product->product_id?>" />
            <input type="hidden" name="back_link" value="<?php print \JSHelper::jsFilterUrl($_SERVER['REQUEST_URI'])?>" />
		    <?php echo \JHTML::_('form.token');?>
            
            <div id="jshop_review_write" >
                <div class = "row">
                    <div class = "col-lg-3">
                        <label for="review_user_name"><?php print JText::_('JSHOP_REVIEW_USER_NAME')?><label>
                    </div>
                    <div class = "col-lg-9">
                        <input type="text" name="user_name" id="review_user_name" class="inputbox required" value="<?php print $this->user->name?>"/>
                    </div>
                </div>
                <div class = "row">
                    <div class = "col-lg-3">
                        <label for="review_user_email"><?php print JText::_('JSHOP_REVIEW_USER_EMAIL')?></label>
                    </div>
                    <div class = "col-lg-9">
                        <input type="text" name="user_email" id="review_user_email" class="inputbox validate-email required" value="<?php print $this->user->email?>" />
                    </div>
                </div>
                <div class = "row">
                    <div class = "col-lg-3">
                        <label for="review_review"><?php print JText::_('JSHOP_REVIEW_REVIEW')?></label>
                    </div>
                    <div class = "col-lg-9">
                        <textarea name="review" id="review_review" rows="4" cols="40" class="jshop inputbox required"></textarea>
                    </div>
                </div>
                <?php if (!$this->config->hide_product_rating){?>
                <div class = "row">
                    <div class = "col-lg-3">
                        <label><?php print JText::_('JSHOP_REVIEW_MARK_PRODUCT')?></label>
                    </div>
                    <div class = "col-lg-9">
                        <?php for($i=1; $i<=$this->stars_count*$this->parts_count; $i++){?>
                            <input name="mark" type="radio" class="star {split:<?php print $this->parts_count?>}" value="<?php print $i?>" <?php if ($i==$this->stars_count*$this->parts_count){?>checked="checked"<?php }?>/>
                        <?php } ?>
                    </div>
                </div>
                <?php }?>
                <?php print $this->_tmp_product_review_before_submit;?>
                <div class = "row">
                    <div class = "col-lg-3"></div>
                    <div class = "col-lg-9">
                        <input type="submit" class="btn btn-primary button validate" value="<?php print JText::_('JSHOP_REVIEW_SUBMIT')?>" />
                    </div>
                </div>
            </div>
        </form>
    <?php }else{?>
        <div class="review_text_not_login"><?php print $this->text_review?></div>
    <?php } ?>
<?php }?>