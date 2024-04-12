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
<div class = "jshop pagelogin" id="comjshop">    
    <h1><?php print JText::_('JSHOP_LOGIN')?></h1>
    <?php print $this->checkout_navigator?>
    
    
    
    <?php echo $this->tmpl_login_html_1?>
    <div class="row">
        <div class="col-lg-6 login_block">
			<?php echo $this->tmpl_login_html_2?>
            <div class="small_header"><?php print JText::_('JSHOP_HAVE_ACCOUNT')?></div>
            <div class="logintext"><?php print JText::_('JSHOP_PL_LOGIN')?></div>
            
            <form method="post" action="<?php print \JSHelper::SEFLink('index.php?option=com_jshopping&controller=user&task=loginsave', 1,0, $this->config->use_ssl)?>" name="jlogin" class="form-horizontal">
                <div class="control-elms rowlogin">
                    <div class="control-label">
                        <label id="username-lbl" for="jlusername">
                            <?php print JText::_('JSHOP_USERNAME')?>:
                        </label>
                    </div>
                    <div class="controls">
                        <input type="text" id="jlusername" name="username" value="" class="inputbox form-control" required placeholder="<?php print JText::_('JSHOP_USERNAME')?>">
                    </div>
                </div>
                
                <div class="control-elms rowpasword">
                    <div class="control-label">
                        <label id="password-lbl" class="" for="jlpassword"><?php print JText::_('JSHOP_PASSWORT')?>:</label>
                    </div>
                    <div class="controls">
                        <input type="password" id="jlpassword" name="passwd" value="" class="inputbox form-control" required placeholder="<?php print JText::_('JSHOP_PASSWORT')?>">
                    </div>
                </div>
                
                <div class="control-elms checkbox rowremember">
                    <div class="controls">
                        <input type="checkbox" name="remember" id="remember_me" value="yes" />
                        <label for = "remember_me"><?php print JText::_('JSHOP_REMEMBER_ME')?></label>
                    </div>
                </div>
                
                <div class="control-elms rowbutton">
                    <div class="controls">
                        <input type="submit" class="btn btn-success button" value="<?php print JText::_('JSHOP_LOGIN')?>" />
                    </div>
                </div>
                
                <div class="control-elms rowlostpassword">
                    <div class="controls">
                        <a href = "<?php print $this->href_lost_pass ?>"><?php print JText::_('JSHOP_LOST_PASSWORD')?></a>
                    </div>
                </div>
                
                <input type = "hidden" name = "return" value = "<?php print $this->return ?>" />
                <?php echo \JHTML::_('form.token');?>
				<?php echo $this->tmpl_login_html_3?>
            </form>
			
			<?php if ($this->config->shop_user_guest && $this->show_pay_without_reg) : ?>
				<span class="text_pay_without_reg">
					<div class="small_header"><?php print JText::_('JSHOP_ORDER_WITHOUT_REGISTER')?></div>
					<a class="btn btn-primary" href="<?php print \JSHelper::SEFLink('index.php?option=com_jshopping&controller=checkout&task=step2',1,0, $this->config->use_ssl);?>"><?php print JText::_('JSHOP_GUEST_CHECKOUT')?></a>
				</span>
			<?php endif; ?>
        </div>
        <div class = "col-lg-6 register_block">
			<?php echo $this->tmpl_login_html_4?>
			<?php if ($this->allowUserRegistration){?>
				<span class="small_header"><?php print JText::_('JSHOP_HAVE_NOT_ACCOUNT')?></span>
				<div class="logintext"><?php print JText::_('JSHOP_REGISTER')?></div>
				<?php if (!$this->config->show_registerform_in_logintemplate){?>
					<div class="block_button_register">
						<input type="button" class="btn button btn-primary" value="<?php print JText::_('JSHOP_REGISTRATION')?>" onclick="location.href='<?php print $this->href_register ?>';" />
					</div>
				<?php }else{?>
					<?php $hideheaderh1 = 1; include(dirname(__FILE__)."/register.php"); ?>
				<?php }?>
			<?php }?>
			<?php echo $this->tmpl_login_html_5?>
        </div>
    </div>
	<?php echo $this->tmpl_login_html_6?>
</div>    