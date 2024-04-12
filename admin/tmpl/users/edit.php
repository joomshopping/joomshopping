<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$user=$this->user;
$lists=$this->lists;
$config_fields=$this->config_fields;
?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=users" method="post" name="adminForm" id="adminForm" autocomplete="off">
<?php echo \JHTML::_('form.token');?>
<input type="password" name="fkpswd" style="display:none">
<?php print $this->tmp_html_start?>
<ul class="joomla-tabs nav nav-tabs">    
    <li class="nav-item"><a href="#firstpage1" class="nav-link active" data-toggle="tab"><?php echo JText::_('JSHOP_GENERAL')?></a></li>
    <li class="nav-item"><a href="#secondpage2" class="nav-link" data-toggle="tab"><?php echo JText::_('JSHOP_BILL_TO')?></a></li>
    <?php if ($this->count_filed_delivery > 0){?>
        <li class="nav-item"><a href="#thirdpage3" class="nav-link" data-toggle="tab"><?php echo JText::_('JSHOP_SHIP_TO')?></a></li>
    <?php }?>
</ul>

<div id="editdata-document" class="tab-content">
<div id="firstpage1" class="tab-pane active">
    <div class="col100">
    <fieldset class="adminform">
    <table class="admintable">
        <tr>
            <td class="key">
                <?php echo JText::_('JSHOP_USERNAME')?>*
            </td>
            <td>
                <input type="text" class="inputbox form-control" name="u_name" value="<?php echo $user->u_name ?>" />
            </td>
        </tr>
        <tr>
          <td class="key">
            <?php echo JText::_('JSHOP_EMAIL')?>*
          </td>
          <td>
            <input type="text" class="inputbox form-control" name="email" value="<?php echo $user->email ?>" />
          </td>
        </tr>
		<tr>
		  <td class="key">
			<?php echo JText::_('JSHOP_NUMBER')?>
		  </td>
		  <td>
			<input type="text" class="inputbox form-control" name="number" value="<?php echo $user->number?>" />
		  </td>
		</tr>
        <tr>
            <td class="key">
                <?php echo JText::_('JSHOP_NEW_PASSWORD')?>
            </td>
            <td>
                <input class="inputbox form-control" type="password" name="password"/>
            </td>
        </tr>
        <tr>
            <td class="key">
                <?php echo JText::_('JSHOP_PASSWORD_2')?>
            </td>
            <td>
                <input class="inputbox form-control" type="password" name="password2"/>
            </td>
        </tr>
        <?php if (\JFactory::getUser()->authorise('core.admin', 'com_jshopping')){?>
        <tr>
            <td class="key">
                <?php echo JText::_('JSHOP_BLOCK_USER')?>
            </td>
            <td>
                <?php echo $this->lists['block']; ?>
            </td>
        </tr>
        <?php } ?>
        <tr>
          <td class="key">
            <?php echo JText::_('JSHOP_USERGROUP_NAME')?>*
          </td>
          <td>
            <?php echo $lists['usergroups'];?>
          </td>
        </tr>
    </table>
    </fieldset>
    </div>
    <div class="clr"></div>
<?php $pkey = "etemplatevar0";if ($this->$pkey){print $this->$pkey;}?>
</div>

<div id="secondpage2" class="tab-pane">
    <div class="col100">
    <fieldset class="adminform">
    <table class="admintable">
    <?php if ($config_fields['title']['display']){?>
    <tr>
        <td class="key">
            <?php echo JText::_('JSHOP_USER_TITLE')?>
        </td>
        <td>
            <?php echo $lists['select_titles'];?>
        </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['f_name']['display']){?>
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_USER_FIRSTNAME')?>
      </td>
      <td>
        <input type="text" class="inputbox form-control" name="f_name" value="<?php echo $user->f_name ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['l_name']['display']){?>
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_USER_LASTNAME')?>
      </td>
      <td>
        <input type="text" class="inputbox form-control" name="l_name" value="<?php echo $user->l_name ?>" />
      </td>
    </tr>
    <?php } ?>
	<?php if ($config_fields['m_name']['display']){?>
	<tr>
	  <td class="key">
		<?php print JText::_('JSHOP_M_NAME')?>
	  </td>
	  <td>
		<input type = "text" name = "m_name" id = "m_name" value = "<?php print $user->m_name ?>" class = "inputbox form-control" />
	  </td>
	</tr>
	<?php } ?>
    <?php if ($config_fields['firma_name']['display']){?>
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_FIRMA_NAME')?>
      </td>
      <td>
        <input type="text" class="inputbox form-control" name="firma_name" value="<?php echo $user->firma_name ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['client_type']['display']){?>
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_CLIENT_TYPE')?>
      </td>
      <td>
        <?php print $lists['select_client_types'];?>
      </td>
    </tr>
    <?php } ?>

    <?php if ($config_fields['firma_code']['display']){?>
    <tr>
      <td class="key">
        <?php print JText::_('JSHOP_FIRMA_CODE')?> 
      </td>
      <td>
        <input type="text" name="firma_code" id="firma_code" value="<?php print $user->firma_code ?>" class="inputbox form-control" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['tax_number']['display']){?>
    <tr>
      <td class="key">
        <?php print JText::_('JSHOP_VAT_NUMBER')?>
      </td>
      <td>
        <input type="text" name="tax_number" id="tax_number" value="<?php print $user->tax_number ?>" class="inputbox form-control" />
      </td>
    </tr>
    <?php } ?>
	<?php if ($config_fields['birthday']['display']){?>
	<tr>
	  <td class="key">
		<?php print JText::_('JSHOP_BIRTHDAY')?>
	  </td>
	  <td>
		<?php echo \JHTML::_('calendar', $user->birthday, 'birthday', 'birthday', $this->config->field_birthday_format, array('class'=>'inputbox', 'size'=>'25', 'maxlength'=>'19'));?>
	  </td>
	</tr>
<?php } ?>
    <?php if ($config_fields['home']['display']){?>
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_FIELD_HOME')?>
      </td>
      <td>
        <input type="text" class="inputbox form-control" name="home" value="<?php echo $user->home ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['apartment']['display']){?>
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_FIELD_APARTMENT')?>
      </td>
      <td>
        <input type="text" class="inputbox form-control" name="apartment" value="<?php echo $user->apartment ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['street']['display']){?>
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_STREET_NR')?>
      </td>
      <td>
        <input type="text" class="inputbox form-control" name="street" value="<?php echo $user->street ?>" />
        <?php if ($config_fields['street_nr']['display']){?>
        <input type = "text" class = "inputbox form-control" name = "street_nr" value = "<?php echo $user->street_nr ?>" />
        <?php }?>
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['city']['display']){?>
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_CITY')?>
      </td>
      <td>
        <input type="text" class="inputbox form-control" name="city" value="<?php echo $user->city ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['zip']['display']){?>
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_ZIP')?>
      </td>
      <td>
        <input type="text" class="inputbox form-control" name="zip" value="<?php echo $user->zip ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['state']['display']){?>
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_STATE')?>
      </td>
      <td>
        <input type="text" class="inputbox form-control" name="state" value="<?php echo $user->state ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['country']['display']){?>
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_COUNTRY')?>
      </td>
      <td>
        <?php echo $lists['country'];?>
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['phone']['display']){?>
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_TELEFON')?>
      </td>
      <td>
        <input type="text" class="inputbox form-control" name="phone" value="<?php echo $user->phone ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['mobil_phone']['display']){?>
    <tr>
      <td class="key">
        <?php print JText::_('JSHOP_MOBIL_PHONE')?>
      </td>
      <td>
        <input type="text" name="mobil_phone" class = "form-control" id="mobil_phone" value="<?php print $user->mobil_phone ?>" class="inputbox" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['fax']['display']){?>
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_FAX')?>
      </td>
      <td>
        <input type="text" class="inputbox form-control" name="fax" value="<?php echo $user->fax ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['ext_field_1']['display']){?>
    <tr>
      <td class="key">
        <?php print JText::_('JSHOP_EXT_FIELD_1')?>
      </td>
      <td>
        <input type="text" class="form-control" name="ext_field_1" id="ext_field_1" value="<?php print $user->ext_field_1 ?>" class="inputbox" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['ext_field_2']['display']){?>
    <tr>
      <td class="key">
        <?php print JText::_('JSHOP_EXT_FIELD_2')?>
      </td>
      <td>
        <input type="text" class="form-control" name="ext_field_2" id="ext_field_2" value="<?php print $user->ext_field_2 ?>" class="inputbox" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['ext_field_3']['display']){?>
    <tr>
      <td class="key">
        <?php print JText::_('JSHOP_EXT_FIELD_3')?>
      </td>
      <td>
        <input type="text" class="form-control" name="ext_field_3" id="ext_field_3" value="<?php print $user->ext_field_3 ?>" class="inputbox" />
      </td>
    </tr>
    <?php } ?>

    </table>
    </fieldset>
    </div>
    <div class="clr"></div>
<?php $pkey = "etemplatevar1";if ($this->$pkey){print $this->$pkey;}?>
</div>
<?php if ($this->count_filed_delivery > 0){?>
<div id="thirdpage3" class="tab-pane">
    <div class="col100">
    <fieldset class="adminform">
    <table class="admintable">
    <tr>
        <td class="key">
            <?php echo JText::_('JSHOP_DELIVERY_ADRESS')?>
        </td>
        <td>
            <input type="radio" name="delivery_adress" <?php if ($user->delivery_adress==0) {?> checked="checked" <?php } ?> value="0" onchange="jshopAdmin.userEditenableFields(this.value)"> <?php echo JText::_('JSHOP_NO')?>
            &nbsp;
            <input type="radio" name="delivery_adress" <?php if ($user->delivery_adress==1) {?> checked="checked" <?php } ?> value="1" onchange="jshopAdmin.userEditenableFields(this.value)"> <?php echo JText::_('JSHOP_YES')?>
        </td>
    </tr>
    <?php if ($config_fields['d_title']['display']){?>
    <tr>
        <td class="key">
            <?php echo JText::_('JSHOP_USER_TITLE')?>
        </td>
        <td>
            <?php echo $lists['select_d_titles'];?>
        </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['d_f_name']['display']){?>
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_USER_FIRSTNAME')?>
      </td>
      <td>
        <input type="text" class="form-control inputbox endes" name="d_f_name" value="<?php echo $user->d_f_name ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['d_l_name']['display']){?>
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_USER_LASTNAME')?>
      </td>
      <td>
        <input type="text" class="form-control inputbox endes" name="d_l_name" value="<?php echo $user->d_l_name ?>" />
      </td>
    </tr>
    <?php } ?>
	<?php if ($config_fields['d_m_name']['display']){?>
	<tr>
	  <td class="key">
		<?php print JText::_('JSHOP_M_NAME')?>
	  </td>
	  <td>
		<input type = "text" class="form-control endes" name = "d_m_name" id = "d_m_name" value = "<?php print $user->d_m_name ?>" class = "inputbox endes" />
	  </td>
	</tr>
	<?php } ?>
    <?php if ($config_fields['d_firma_name']['display']){?>
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_FIRMA_NAME')?>
      </td>
      <td>
        <input type="text" class="form-control inputbox endes" name="d_firma_name" value="<?php echo $user->d_firma_name ?>" />
      </td>
    </tr>
    <?php } ?>
	<?php if ($config_fields['d_email']['display']){ ?>
    <tr>
      <td class="key">
        <?php echo _JSHOP_EMAIL;?>
      </td>
      <td>
        <input type="text" class="inputbox endes" name="d_email" value="<?php echo $user->d_email ?>" />
      </td>
    </tr>
    <?php } ?>
	<?php if ($config_fields['d_birthday']['display']){?>
	<tr>
	  <td class="key">
		<?php print JText::_('JSHOP_BIRTHDAY')?>
	  </td>
	  <td>
		<?php echo \JHTML::_('calendar', $user->d_birthday, 'd_birthday', 'd_birthday', $this->config->field_birthday_format, array('class'=>'inputbox endes', 'size'=>'25', 'maxlength'=>'19'));?>
	  </td>
	</tr>
	<?php } ?>
    <?php if ($config_fields['d_home']['display']){?>
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_FIELD_HOME')?>
      </td>
      <td>
        <input type="text"  class="inputbox form-control endes" name="d_home" value="<?php echo $user->d_home ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['d_apartment']['display']){?>
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_FIELD_APARTMENT')?>
      </td>
      <td>
        <input type="text" class="inputbox form-control endes" name="d_apartment" value="<?php echo $user->d_apartment ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['d_street']['display']){?>
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_STREET_NR')?>
      </td>
      <td>
        <input type="text" class="inputbox endes form-control" name="d_street" value="<?php echo $user->d_street ?>" />
        <?php if ($config_fields['d_street_nr']['display']){?>
        <input type = "text" class = "inputbox endes form-control" name = "d_street_nr" value = "<?php echo $user->d_street_nr ?>" />
        <?php }?>
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['d_city']['display']){?>
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_CITY')?>
      </td>
      <td>
        <input type="text" class="inputbox endes form-control" name="d_city" value="<?php echo $user->d_city ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['d_zip']['display']){?>
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_ZIP')?>
      </td>
      <td>
        <input type="text" class="inputbox endes form-control" name="d_zip" value="<?php echo $user->d_zip ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['d_state']['display']){?>
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_STATE')?>
      </td>
      <td>
        <input type="text" class="inputbox endes form-control" name="d_state" value="<?php echo $user->d_state ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['d_country']['display']){?>
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_COUNTRY')?>
      </td>
      <td>
        <?php echo $lists['d_country'];?>
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['d_phone']['display']){?>
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_TELEFON')?>
      </td>
      <td>
        <input type="text" class="inputbox endes form-control" name="d_phone" value="<?php echo $user->d_phone ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['d_mobil_phone']['display']){?>
    <tr>
      <td class="key">
        <?php print JText::_('JSHOP_MOBIL_PHONE')?>
      </td>
      <td>
        <input type="text" class="form-control" name="d_mobil_phone" id="d_mobil_phone" value="<?php print $user->d_mobil_phone ?>" class="inputbox endes" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['d_fax']['display']){?>
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_FAX')?>
      </td>
      <td>
        <input type="text" class="inputbox endes form-control" name="d_fax" value="<?php echo $user->d_fax ?>" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['d_ext_field_1']['display']){?>
    <tr>
      <td class="key">
        <?php print JText::_('JSHOP_EXT_FIELD_1')?>
      </td>
      <td>
        <input type="text" class="inputbox endes form-control" name="d_ext_field_1" id="d_ext_field_1" value="<?php print $user->d_ext_field_1 ?>" class="inputbox endes" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['d_ext_field_2']['display']){?>
    <tr>
      <td class="key">
        <?php print JText::_('JSHOP_EXT_FIELD_2')?>
      </td>
      <td>
        <input type="text" class="inputbox endes form-control" name="d_ext_field_2" id="d_ext_field_2" value="<?php print $user->d_ext_field_2 ?>" class="inputbox endes" />
      </td>
    </tr>
    <?php } ?>
    <?php if ($config_fields['d_ext_field_3']['display']){?>
    <tr>
      <td class="key">
        <?php print JText::_('JSHOP_EXT_FIELD_3')?>
      </td>
      <td>
        <input type="text" class="inputbox endes form-control" name="d_ext_field_3" id="d_ext_field_3" value="<?php print $user->d_ext_field_3 ?>" class="inputbox endes" />
      </td>
    </tr>
    <?php } ?>

    <?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
    </table>
    </fieldset>
    </div>
    <div class="clr"></div>

    <script type="text/javascript">
        jshopAdmin.userEditenableFields(<?php echo $user->delivery_adress?>);
    </script>
</div>
<?php }?>
</div>
<?php $pkey = "etemplatevarend";if ($this->$pkey){print $this->$pkey;}?>
<input type="hidden" name="task" value="">
<input type="hidden" name="user_id" value="<?php print (int)$user->user_id?>">
<?php print $this->tmp_html_end?>
</form>
</div>