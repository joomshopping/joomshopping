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

$config_fields=$this->config_fields;
JHtml::_('behavior.formvalidator');
?>
<div class="jshop max-500" id="comjshop_register">
    <?php if (!isset($hideheaderh1)) : ?>
    <h1><?php print JText::_('JSHOP_REGISTRATION')?></h1>
    <?php endif; ?>
    
    <form action="<?php print \JSHelper::SEFLink('index.php?option=com_jshopping&controller=user&task=registersave',1,0, $this->config->use_ssl)?>" class="form-validate form-horizontal" method="post" name="registration-form" autocomplete="off" enctype="multipart/form-data">
    <?php echo $this->_tmpl_register_html_1?>
    <div class="jshop_register">
        <?php if ($config_fields['title']['display']) : ?>
        <div class="control-group">
            <div class="control-label name">
                <label for="title">
                <?php print JText::_('JSHOP_REG_TITLE')?> <?php if ($config_fields['title']['require']) : ?><span>*</span><?php endif; ?>
                </label>
            </div>
            <div class="controls">
                <?php
				if ($config_fields['title']['require']){
					$attribs='class="inputbox form-control required"';
				}else{
					$attribs='class="inputbox form-control"';
				}
				?>
				<?php print Selects::getTitle($this->user->title, $attribs)?>
            </div>
        </div>
        <?php endif; ?>
		
        <?php if ($config_fields['f_name']['display']) : ?>
        <div class="control-group">
          <div class="control-label name">
            <label for="f_name">
                <?php print JText::_('JSHOP_F_NAME')?> <?php if ($config_fields['f_name']['require']) : ?><span>*</span><?php endif; ?>
            </label> 
          </div>
          <div class="controls">
            <input type="text" name="f_name" id="f_name" value="<?php print $this->user->f_name?>" class="input form-control <?php if ($config_fields['f_name']['require']):?>required<?php endif?>">
          </div>
        </div>
        <?php endif; ?>
		
        <?php if ($config_fields['l_name']['display']) : ?>
        <div class="control-group">
          <div class="control-label name">
            <label for="l_name">
            <?php print JText::_('JSHOP_L_NAME')?> <?php if ($config_fields['l_name']['require']) : ?><span>*</span><?php endif; ?>
            </label>
          </div>
          <div class="controls">
            <input type="text" name="l_name" id="l_name" value="<?php print $this->user->l_name?>" class="input form-control <?php if ($config_fields['l_name']['require']):?>required<?php endif?>">
          </div>
        </div>
        <?php endif; ?>
		
        <?php if ($config_fields['m_name']['display']) : ?>
        <div class="control-group">
          <div class="control-label name">
            <label for="m_name">
            <?php print JText::_('JSHOP_M_NAME')?> <?php if ($config_fields['m_name']['require']) : ?><span>*</span><?php endif; ?>
            </label>
          </div>
          <div class="controls">
            <input type="text" name="m_name" id="m_name" value="<?php print $this->user->m_name?>" class="input form-control <?php if ($config_fields['m_name']['require']):?>required<?php endif?>">
          </div>
        </div>
        <?php endif; ?>
		
        <?php if ($config_fields['firma_name']['display']) : ?>
        <div class="control-group">
          <div class="control-label name">
            <label for="firma_name">
            <?php print JText::_('JSHOP_FIRMA_NAME')?> <?php if ($config_fields['firma_name']['require']) : ?><span>*</span><?php endif; ?>
            </label>
          </div>
          <div class="controls">
            <input type="text" name="firma_name" id="firma_name" value="<?php print $this->user->firma_name?>" class="input form-control <?php if ($config_fields['firma_name']['require']):?>required<?php endif?>">
          </div>
        </div>
        <?php endif; ?>
		
        <?php if ($config_fields['client_type']['display']) : ?>
        <div class="control-group">
          <div class="control-label name">
            <label for="client_type">
            <?php print JText::_('JSHOP_CLIENT_TYPE')?> <?php if ($config_fields['client_type']['require']) : ?><span>*</span><?php endif; ?>
            </label>
          </div>
          <div class="controls">
			<?php
			if ($config_fields['client_type']['require']){
				$attribs='class="inputbox form-control required"';
			}else{
				$attribs='class="inputbox form-control"';
			}
			?>
			<?php print Selects::getClientType($this->user->client_type, $attribs)?>
          </div>
        </div>
        <?php endif; ?>
		
        <?php if ($config_fields['firma_code']['display']) : ?>
        <div class="control-group" id='tr_field_firma_code' <?php if ($config_fields['client_type']['display']) : ?>style="display:none;"<?php endif; ?>>
          <div class="control-label name">
            <label for="firma_code">
            <?php print JText::_('JSHOP_FIRMA_CODE')?> <?php if ($config_fields['firma_code']['require']) : ?><span>*</span><?php endif; ?>
            </label>
          </div>
          <div class="controls">
			<?php
			if ($config_fields['tax_number']['require']){
				if ($config_fields['client_type']['display']){
					$class="required-company";
				}else{
					$class="required";
				}
			}else{
				$class='';
			}
			?>
            <input type="text" name="firma_code" id="firma_code" value="<?php print $this->user->firma_code?>" class="input form-control <?php print $class;?>">
          </div>
        </div>
        <?php endif; ?>
		
        <?php if ($config_fields['tax_number']['display']) : ?>
        <div class="control-group" id='tr_field_tax_number' <?php if ($config_fields['client_type']['display']) : ?>style="display:none;"<?php endif; ?>>
          <div class="control-label name">
            <label for="tax_number">
            <?php print JText::_('JSHOP_VAT_NUMBER')?> <?php if ($config_fields['tax_number']['require']) : ?><span>*</span><?php endif; ?>
            </label>
          </div>
          <div class="controls">
			<?php
			if ($config_fields['tax_number']['require']){
				if ($config_fields['client_type']['display']){
					$class="required-company";
				}else{
					$class="required";
				}
			}else{
				$class='';
			}
			?>
            <input type="text" name="tax_number" id="tax_number" value="<?php print $this->user->tax_number?>" class="input form-control <?php print $class?>">
          </div>
        </div>
        <?php endif; ?>
		
        <?php if ($config_fields['email']['display']) : ?>
        <div class="control-group">
          <div class="control-label name">
            <label for="email">
            <?php print JText::_('JSHOP_EMAIL')?> <?php if ($config_fields['email']['require']) : ?><span>*</span><?php endif; ?>
            </label>
          </div>
          <div class="controls">
            <input type="text" name="email" id="email" value="<?php print $this->user->email?>" class="input form-control validate-email <?php if ($config_fields['email']['require']):?>required<?php endif?>">
          </div>
        </div>
        <?php endif; ?>
		
        <?php if ($config_fields['email2']['display']) : ?>
        <div class="control-group">
          <div class="control-label name">
            <label for="email2">
            <?php print JText::_('JSHOP_EMAIL2')?> <?php if ($config_fields['email2']['require']) : ?><span>*</span><?php endif; ?>
            </label>
          </div>
          <div class="controls">
            <input type="text" name="email2" id="email2" value="<?php print $this->user->email2?>" class="input form-control validate-email <?php if ($config_fields['email2']['require']):?>required<?php endif?>">
          </div>
        </div>
        <?php endif; ?>
		
        <?php if ($config_fields['birthday']['display']) : ?>
        <div class="control-group">
          <div class="control-label name">
            <label for="birthday">
            <?php print JText::_('JSHOP_BIRTHDAY')?> <?php if ($config_fields['birthday']['require']) : ?><span>*</span><?php endif; ?>
            </label>
          </div>
          <div class="controls">
			<?php 
			$params=array('class'=>'input', 'size'=>'25', 'maxlength'=>'19');
			if ($config_fields['birthday']['require']){
				$params['class']='input required';
			}
			?>
            <?php echo \JHTML::_('calendar', $this->user->birthday, 'birthday', 'birthday', $this->config->field_birthday_format, $params);?>
          </div>
        </div>
        <?php endif; ?>

        <?php echo $this->_tmpl_register_html_2?>

        <?php if ($config_fields['home']['display']) : ?>
        <div class="control-group">
          <div class="control-label name">
            <label for="home">
            <?php print JText::_('JSHOP_HOME')?> <?php if ($config_fields['home']['require']) : ?><span>*</span><?php endif; ?>
            </label>
          </div>
          <div class="controls">
            <input type="text" name="home" id="home" value="<?php print $this->user->home?>" class="input form-control <?php if ($config_fields['home']['require']):?>required<?php endif?>">
          </div>
        </div>
        <?php endif; ?>

        <?php if ($config_fields['apartment']['display']) : ?>
        <div class="control-group">
          <div class="control-label name">
            <label for="apartment">
            <?php print JText::_('JSHOP_APARTMENT')?> <?php if ($config_fields['apartment']['require']) : ?><span>*</span><?php endif; ?>
            </label>
          </div>
          <div class="controls">
            <input type="text" name="apartment" id="apartment" value="<?php print $this->user->apartment?>" class="input form-control <?php if ($config_fields['apartment']['require']):?>required<?php endif?>">
          </div>
        </div>
        <?php endif; ?>

        <?php if ($config_fields['street']['display']) : ?>
        <div class="control-group">
          <div class="control-label name">
            <label for="street">
            <?php print JText::_('JSHOP_STREET_NR')?> <?php if ($config_fields['street']['require']) : ?><span>*</span><?php endif; ?>
            </label>
          </div>
          <div class="controls">
            <input type="text" name="street" id="street" value="<?php print $this->user->street?>" class="input form-control <?php if ($config_fields['street']['require']):?>required<?php endif?>">
            <?php if ($config_fields['street_nr']['display']){?>
            <input type="text" name="street_nr" id="street_nr" value="<?php print $this->user->street_nr?>" class="input form-control <?php if ($config_fields['street_nr']['require']):?>required<?php endif?>"            >
            <?php }?>
          </div>
        </div>
        <?php endif; ?>

        <?php if ($config_fields['zip']['display']) : ?>
        <div class="control-group">
          <div class="control-label name">
            <label for="zip">
            <?php print JText::_('JSHOP_ZIP')?> <?php if ($config_fields['zip']['require']) : ?><span>*</span><?php endif; ?>
            </label>
          </div>
          <div class="controls">
            <input type="text" name="zip" id="zip" value="<?php print $this->user->zip?>" class="input form-control <?php if ($config_fields['zip']['require']):?>required<?php endif?>">
          </div>
        </div>
        <?php endif; ?>

        <?php if ($config_fields['city']['display']) : ?>
        <div class="control-group">
          <div class="control-label name">
            <label for="city">
            <?php print JText::_('JSHOP_CITY')?> <?php if ($config_fields['city']['require']) : ?><span>*</span><?php endif; ?>
          </div>
          <div class="controls">
            <input type="text" name="city" id="city" value="<?php print $this->user->city?>" class="input form-control <?php if ($config_fields['city']['require']):?>required<?php endif?>">
          </div>
        </div>
        <?php endif; ?>
		
        <?php if ($config_fields['state']['display']) : ?>
        <div class="control-group">
          <div class="control-label name">
            <label for="state">
            <?php print JText::_('JSHOP_STATE')?> <?php if ($config_fields['state']['require']) : ?><span>*</span><?php endif; ?>
            </label>
          </div>
          <div class="controls">
            <input type="text" name="state" id="state" value="<?php print $this->user->state?>" class="input form-control <?php if ($config_fields['state']['require']):?>required<?php endif?>">
          </div>
        </div>
        <?php endif; ?>

        <?php if ($config_fields['country']['display']) : ?>
        <div class="control-group">
          <div class="control-label name">
            <label for="country">
            <?php print JText::_('JSHOP_COUNTRY')?> <?php if ($config_fields['country']['require']) : ?><span>*</span><?php endif; ?>
            </label>
          </div>
          <div class="controls">
			<?php
			if ($config_fields['country']['require']){
				$attribs='class="inputbox form-control required"';
			}else{
				$attribs='class="inputbox form-control"';
			}
			?>
			<?php print Selects::getCountry($this->user->country, $attribs)?>
          </div>
        </div>
        <?php endif; ?>
    
        <?php echo $this->_tmpl_register_html_3?>
    
        <?php if ($config_fields['phone']['display']) : ?>
        <div class="control-group">
          <div class="control-label name">
            <label for="phone">
            <?php print JText::_('JSHOP_TELEFON')?> <?php if ($config_fields['phone']['require']) : ?><span>*</span><?php endif; ?>
            </label>
          </div>
          <div class="controls">
            <input type="text" name="phone" id="phone" value="<?php print $this->user->phone?>" class="input form-control <?php if ($config_fields['phone']['require']):?>required<?php endif?>">
          </div>
        </div>
        <?php endif; ?>
        
        <?php if ($config_fields['mobil_phone']['display']) : ?>
        <div class="control-group">
          <div class="control-label name">
            <label for="mobil_phone">
            <?php print JText::_('JSHOP_MOBIL_PHONE')?> <?php if ($config_fields['mobil_phone']['require']) : ?><span>*</span><?php endif; ?>
            </label>
          </div>
          <div class="controls">
            <input type="text" name="mobil_phone" id="mobil_phone" value="<?php print $this->user->mobil_phone?>" class="input form-control <?php if ($config_fields['mobil_phone']['require']):?>required<?php endif?>">
          </div>
        </div>
        <?php endif; ?>

        <?php if ($config_fields['fax']['display']) : ?>
        <div class="control-group">
          <div class="control-label name">
            <label for="fax">
            <?php print JText::_('JSHOP_FAX')?> <?php if ($config_fields['fax']['require']) : ?><span>*</span><?php endif; ?>
            </label>
          </div>
          <div class="controls">
            <input type="text" name="fax" id="fax" value="<?php print $this->user->fax?>" class="input form-control <?php if ($config_fields['fax']['require']):?>required<?php endif?>">
          </div>
        </div>
        <?php endif; ?>
        
        <?php if ($config_fields['ext_field_1']['display']) : ?>
        <div class="control-group">
          <div class="control-label name">
            <label for="ext_field_1">
            <?php print JText::_('JSHOP_EXT_FIELD_1')?> <?php if ($config_fields['ext_field_1']['require']) : ?><span>*</span><?php endif; ?>
            </label>
          </div>
          <div class="controls">
            <input type="text" name="ext_field_1" id="ext_field_1" value="<?php print $this->user->ext_field_1?>" class="input form-control <?php if ($config_fields['ext_field_1']['require']):?>required<?php endif?>">
          </div>
        </div>
        <?php endif; ?>

        <?php if ($config_fields['ext_field_2']['display']) : ?>
        <div class="control-group">
          <div class="control-label name">
            <label for="ext_field_2">
            <?php print JText::_('JSHOP_EXT_FIELD_2')?> <?php if ($config_fields['ext_field_2']['require']) : ?><span>*</span><?php endif; ?>
            </label>            
          </div>
          <div class="controls">
            <input type="text" name="ext_field_2" id="ext_field_2" value="<?php print $this->user->ext_field_2?>" class="input form-control <?php if ($config_fields['ext_field_2']['require']):?>required<?php endif?>">
          </div>
        </div>
        <?php endif; ?>

        <?php if ($config_fields['ext_field_3']['display']) : ?>
        <div class="control-group">
          <div class="control-label name">
            <label for="ext_field_3">
            <?php print JText::_('JSHOP_EXT_FIELD_3')?> <?php if ($config_fields['ext_field_3']['require']) : ?><span>*</span><?php endif; ?>
            </label>
          </div>
          <div class="controls">
            <input type="text" name="ext_field_3" id="ext_field_3" value="<?php print $this->user->ext_field_3?>" class="input form-control <?php if ($config_fields['ext_field_3']['require']):?>required<?php endif?>">
          </div>
        </div>
        <?php endif; ?>
    
        <?php echo $this->_tmpl_register_html_4?>
    
        <?php if ($config_fields['u_name']['display']) : ?>
        <div class="control-group">
          <div class="control-label name">
            <label for="u_name">
            <?php print JText::_('JSHOP_USERNAME')?> <?php if ($config_fields['u_name']['require']) : ?><span>*</span><?php endif; ?>
            </label>
          </div>
          <div class="controls">
            <input type="text" name="u_name" id="u_name" value="<?php print $this->user->u_name?>" class="input form-control validate-username <?php if ($config_fields['u_name']['require']):?>required<?php endif?>">
          </div>
        </div>
        <?php endif; ?>

        <?php if ($config_fields['password']['display']) : ?>
        <div class="control-group">
          <div class="control-label name">
            <label for="password">
            <?php print JText::_('JSHOP_PASSWORD')?> <?php if ($config_fields['password']['require']) : ?><span>*</span><?php endif; ?>
            </label>
          </div>
          <div class="controls">
            <input type="password" name="password" id="password" value="" class="input form-control registrationTestPassword validate-password <?php if ($config_fields['password']['require']):?>required<?php endif?>">
            <span id="reg_test_password"></span>
          </div>
        </div>
        <?php endif; ?>

        <?php if ($config_fields['password_2']['display']) : ?>
        <div class="control-group">
          <div class="control-label name">
            <label for="password_2">
            <?php print JText::_('JSHOP_PASSWORD_2')?> <?php if ($config_fields['password_2']['require']) : ?><span>*</span><?php endif; ?>
            </label>
          </div>
          <div class="controls">
            <input type="password" name="password_2" id="password_2" value="" class="input form-control <?php if ($config_fields['password_2']['require']):?>required<?php endif?>">
          </div>
        </div>
        <?php endif; ?>

        <?php if ($config_fields['privacy_statement']['display']) : ?>
        <div class="control-group">
          <div class="control-label name">
            <label for="privacy_statement">
            <a class="privacy_statement" href="#" onclick="window.open('<?php print \JSHelper::SEFLink('index.php?option=com_jshopping&controller=content&task=view&page=privacy_statement&tmpl=component', 1);?>','window','width=800, height=600, scrollbars=yes, status=no, toolbar=no, menubar=no, resizable=yes, location=no');return false;">
            <?php print JText::_('JSHOP_PRIVACY_STATEMENT')?> <?php if ($config_fields['privacy_statement']['require']) : ?><span>*</span><?php endif; ?>
            </a>            
            </label>
          </div>
          <div class="controls">
            <input type="checkbox" name="privacy_statement" id="privacy_statement" value="1" <?php if ($config_fields['privacy_statement']['require']):?>required<?php endif?> >
          </div>
        </div>
        <?php endif; ?>

        <?php echo $this->_tmpl_register_html_5?>

    </div>

    <?php echo $this->_tmpl_register_html_51?>

    <div class="control-group box_button">
        <div class="controls">
            <div class="requiredtext">* <?php print JText::_('JSHOP_REQUIRED')?></div>
            <?php echo $this->_tmpl_register_html_6?>
            <input type="submit" value="<?php print JText::_('JSHOP_SEND_REGISTRATION')?>" class="btn btn-primary button" />
        </div>
    </div>
    <?php echo \JHTML::_('form.token');?>
    </form>
</div>

<script type="text/javascript">
	var jshopParams = jshopParams || {};
    jshopParams.urlcheckpassword = '<?php print $this->urlcheckpassword?>';
</script>