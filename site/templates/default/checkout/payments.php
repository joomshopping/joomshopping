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
<div id="comjshop">
    <?php print $this->checkout_navigator?>
    <?php print $this->small_cart?>

    <div class="jshop checkout_payment_block">
        <form id="payment_form" name="payment_form" action="<?php print $this->action?>" method="post" autocomplete="off" enctype="multipart/form-data">
            <?php print $this->_tmp_ext_html_payment_start?>
            <div id="table_payments">
                <?php foreach($this->payment_methods as  $payment){?>
                    <div class="name">
                        <input type="radio" name="payment_method" id="payment_method_<?php print $payment->payment_id?>" value="<?php print $payment->payment_class?>" <?php if ($this->active_payment==$payment->payment_id){?>checked<?php } ?> >
                        <label for = "payment_method_<?php print $payment->payment_id ?>"><?php
                            if ($payment->image){
                                ?><span class="payment_image"><img src="<?php print $payment->image?>" alt="<?php print htmlspecialchars($payment->name)?>" /></span><?php
                            }
                            ?><b><?php print $payment->name;?></b> 
                            <?php if ($payment->price_add_text!=''){?>
                                <span class="payment_price">(<?php print $payment->price_add_text?>)</span>
                            <?php }?>
                        </label>
                    </div>                    
                    <div class="paymform" id="tr_payment_<?php print $payment->payment_class ?>" <?php if ($this->active_payment != $payment->payment_id){?>style="display:none"<?php } ?>>
                        <div class="jshop_payment_method">
                            <?php print $payment->payment_description?>
                            <?php print $payment->form?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        
            <?php print $this->_tmp_ext_html_payment_end?>
            <input type="submit" id="payment_submit" class="btn btn-success button" name="payment_submit" value="<?php print JText::_('JSHOP_NEXT')?>">
        </form>
    </div>
</div>