<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Helper\Helper;

/**
* @version      5.3.5 09.03.2024
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
use Joomla\Component\Jshopping\Site\Helper\Selects;

defined('_JEXEC') or die();

$config_fields = $this->config_fields;
$cssreq = $this->cssreq_fields;
HTMLHelper::_('behavior.formvalidator');
?>
<div class="jshop editaccount_block max-500" id="comjshop">

    <h1><?php print Text::_('JSHOP_EDIT_DATA') ?></h1>

    <form action="<?php print $this->action ?>" method="post" name="loginForm" class="form-validate form-horizontal"
        enctype="multipart/form-data">
        <?php echo $this->_tmpl_editaccount_html_1 ?>
        <div class="jshop_register">

            <?php if ($config_fields['title']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="title">
                        <?php print Text::_('JSHOP_REG_TITLE') ?>
                        <?php if ($config_fields['title']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <?php                        
                    $attribs = 'class="inputbox form-control form-select '.$cssreq['title'].'"';
                    ?>
                    <?php print Selects::getTitle($this->user->title, $attribs) ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['f_name']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="f_name">
                        <?php print Text::_('JSHOP_F_NAME') ?>
                        <?php if ($config_fields['f_name']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="f_name" id="f_name" value="<?php print $this->user->f_name ?>"
                        class="input form-control <?php echo $cssreq['f_name']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['l_name']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="l_name">
                        <?php print Text::_('JSHOP_L_NAME') ?>
                        <?php if ($config_fields['l_name']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="l_name" id="l_name" value="<?php print $this->user->l_name ?>"
                        class="input form-control <?php echo $cssreq['l_name']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['m_name']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="m_name">
                        <?php print Text::_('JSHOP_M_NAME') ?>
                        <?php if ($config_fields['m_name']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="m_name" id="m_name" value="<?php print $this->user->m_name ?>"
                        class="input form-control <?php echo $cssreq['m_name']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['firma_name']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="firma_name">
                        <?php print Text::_('JSHOP_FIRMA_NAME') ?>
                        <?php if ($config_fields['firma_name']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="firma_name" id="firma_name" value="<?php print $this->user->firma_name ?>"
                        class="input form-control <?php echo $cssreq['firma_name']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['client_type']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="client_type">
                        <?php print Text::_('JSHOP_CLIENT_TYPE') ?>
                        <?php if ($config_fields['client_type']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <?php
                    $attribs='class="inputbox form-control form-select '.$cssreq['client_type'].'"';
                    ?>
                    <?php print Selects::getClientType($this->user->client_type, $attribs) ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['firma_code']['display']) : ?>
            <div class="control-group" id='tr_field_firma_code'
                <?php if ($config_fields['client_type']['display']) : ?>style="display:none;" <?php endif; ?>>
                <div class="control-label name">
                    <label for="firma_code">
                        <?php print Text::_('JSHOP_FIRMA_CODE') ?>
                        <?php if ($config_fields['firma_code']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="firma_code" id="firma_code" value="<?php print $this->user->firma_code ?>"
                        class="input form-control <?php echo $cssreq['firma_code'];?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['tax_number']['display']) : ?>
            <div class="control-group" id='tr_field_tax_number'
                <?php if ($config_fields['client_type']['display']) : ?>style="display:none;" <?php endif; ?>>
                <div class="control-label name">
                    <label for="tax_number">
                        <?php print Text::_('JSHOP_VAT_NUMBER') ?>
                        <?php if ($config_fields['tax_number']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="tax_number" id="tax_number" value="<?php print $this->user->tax_number ?>"
                        class="input form-control <?php echo $cssreq['tax_number'];?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['email']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="email">
                        <?php print Text::_('JSHOP_EMAIL') ?>
                        <?php if ($config_fields['email']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="email" id="email" value="<?php print $this->user->email ?>"
                        class="input form-control validate-email <?php echo $cssreq['email']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['birthday']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="birthday">
                        <?php print Text::_('JSHOP_BIRTHDAY') ?>
                        <?php if ($config_fields['birthday']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <?php
                    $params = array('class' => 'input '.$cssreq['birthday'], 'size' => '25', 'maxlength' => '19');
                    ?>
                    <?php echo HTMLHelper::_('calendar', $this->user->birthday, 'birthday', 'birthday', $this->config->field_birthday_format, $params); ?>
                </div>
            </div>
            <?php endif; ?>

            <?php echo $this->_tmpl_editaccount_html_2 ?>

            <?php if ($config_fields['home']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="home">
                        <?php print Text::_('JSHOP_HOME') ?>
                        <?php if ($config_fields['home']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="home" id="home" value="<?php print $this->user->home ?>"
                        class="input form-control <?php echo $cssreq['home']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['apartment']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="apartment">
                        <?php print Text::_('JSHOP_APARTMENT') ?>
                        <?php if ($config_fields['apartment']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="apartment" id="apartment" value="<?php print $this->user->apartment ?>"
                        class="input form-control <?php echo $cssreq['apartment']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['street']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="street">
                        <?php print Text::_('JSHOP_STREET_NR') ?>
                        <?php if ($config_fields['street']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="street" id="street" value="<?php print $this->user->street ?>"
                        class="input form-control <?php echo $cssreq['street']; ?>">
                    <?php if ($config_fields['street_nr']['display']) { ?>
                    <input type="text" name="street_nr" id="street_nr" value="<?php print $this->user->street_nr ?>"
                        class="input form-control <?php echo $cssreq['street_nr']; ?>">
                    <?php } ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['zip']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="zip">
                        <?php print Text::_('JSHOP_ZIP') ?>
                        <?php if ($config_fields['zip']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="zip" id="zip" value="<?php print $this->user->zip ?>"
                        class="input form-control <?php echo $cssreq['zip']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['city']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="city">
                        <?php print Text::_('JSHOP_CITY') ?>
                        <?php if ($config_fields['city']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="city" id="city" value="<?php print $this->user->city ?>"
                        class="input form-control <?php echo $cssreq['city']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['state']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="state">
                        <?php print Text::_('JSHOP_STATE') ?>
                        <?php if ($config_fields['state']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="state" id="state" value="<?php print $this->user->state ?>"
                        class="input form-control <?php echo $cssreq['state']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['country']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="country">
                        <?php print Text::_('JSHOP_COUNTRY') ?>
                        <?php if ($config_fields['country']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <?php
                    $attribs='class="inputbox form-control form-select '.$cssreq['country'].'"';
                    ?>
                    <?php print Selects::getCountry($this->user->country, $attribs) ?>
                </div>
            </div>
            <?php endif; ?>

            <?php echo $this->_tmpl_editaccount_html_3 ?>

            <?php if ($config_fields['phone']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="phone">
                        <?php print Text::_('JSHOP_TELEFON') ?>
                        <?php if ($config_fields['phone']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="phone" id="phone" value="<?php print $this->user->phone ?>"
                        class="input form-control <?php echo $cssreq['phone']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['mobil_phone']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="mobil_phone">
                        <?php print Text::_('JSHOP_MOBIL_PHONE') ?>
                        <?php if ($config_fields['mobil_phone']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="mobil_phone" id="mobil_phone"
                        value="<?php print $this->user->mobil_phone ?>"
                        class="input form-control <?php echo $cssreq['mobil_phone']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['fax']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="fax">
                        <?php print Text::_('JSHOP_FAX') ?>
                        <?php if ($config_fields['fax']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="fax" id="fax" value="<?php print $this->user->fax ?>"
                        class="input form-control <?php echo $cssreq['fax']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['ext_field_1']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="ext_field_1">
                        <?php print Text::_('JSHOP_EXT_FIELD_1') ?>
                        <?php if ($config_fields['ext_field_1']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="ext_field_1" id="ext_field_1"
                        value="<?php print $this->user->ext_field_1 ?>"
                        class="input form-control <?php echo $cssreq['ext_field_1']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['ext_field_2']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="ext_field_2">
                        <?php print Text::_('JSHOP_EXT_FIELD_2') ?>
                        <?php if ($config_fields['ext_field_2']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="ext_field_2" id="ext_field_2"
                        value="<?php print $this->user->ext_field_2 ?>"
                        class="input form-control <?php echo $cssreq['ext_field_2']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['ext_field_3']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="ext_field_3">
                        <?php print Text::_('JSHOP_EXT_FIELD_3') ?>
                        <?php if ($config_fields['ext_field_3']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="ext_field_3" id="ext_field_3"
                        value="<?php print $this->user->ext_field_3 ?>"
                        class="input form-control <?php echo $cssreq['ext_field_3']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php echo $this->_tmpl_editaccount_html_4 ?>

            <?php if ($config_fields['password']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="password">
                        <?php print Text::_('JSHOP_PASSWORD') ?>
                        <?php if ($config_fields['password']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="password" name="password" id="password" value=""
                        class="input form-control validate-password <?php echo $cssreq['password']; ?>">
                    <span id="reg_test_password"></span>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['password_2']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="password_2">
                        <?php print Text::_('JSHOP_PASSWORD_2') ?>
                        <?php if ($config_fields['password_2']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="password" name="password_2" id="password_2" value=""
                        class="input form-control <?php echo $cssreq['password_2']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php echo $this->_tmpl_editaccount_html_4_1 ?>
            <?php echo $this->tmp_fields ?>
        </div>

        <?php if ($this->count_filed_delivery > 0) { ?>
        <div class="control-group other_delivery_adress">
            <div class="control-label name">
                <?php print Text::_('JSHOP_DELIVERY_ADRESS') ?>
            </div>
            <div class="controls">
                <input type="radio" name="delivery_adress" id="delivery_adress_1" value="0"
                    <?php if (!$this->delivery_adress) { ?> checked="checked" <?php } ?>>
                <label for="delivery_adress_1"><?php print Text::_('JSHOP_NO') ?></label>
                <input type="radio" name="delivery_adress" id="delivery_adress_2" value="1"
                    <?php if ($this->delivery_adress) { ?> checked="checked" <?php } ?>>
                <label for="delivery_adress_2"><?php print Text::_('JSHOP_YES') ?></label>
            </div>
        </div>
        <?php } ?>

        <div id="div_delivery" class="jshop_register"
            style="<?php if (!$this->delivery_adress) { ?>display:none;<?php } ?>">

            <?php if ($config_fields['d_title']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="d_title">
                        <?php print Text::_('JSHOP_REG_TITLE') ?>
                        <?php if ($config_fields['d_title']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <?php
                    $attribs = 'class="inputbox form-control form-select '.$cssreq['d_title'].'"';
                    ?>
                    <?php print Selects::getTitle($this->user->d_title, $attribs, 'd_title') ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['d_f_name']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="d_f_name">
                        <?php print Text::_('JSHOP_F_NAME') ?>
                        <?php if ($config_fields['d_f_name']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="d_f_name" id="d_f_name" value="<?php print $this->user->d_f_name ?>"
                        class="input form-control <?php echo $cssreq['d_f_name']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['d_l_name']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="d_l_name">
                        <?php print Text::_('JSHOP_L_NAME') ?>
                        <?php if ($config_fields['d_l_name']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="d_l_name" id="d_l_name" value="<?php print $this->user->d_l_name ?>"
                        class="input form-control <?php echo $cssreq['d_l_name']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['d_m_name']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="d_m_name">
                        <?php print Text::_('JSHOP_M_NAME') ?>
                        <?php if ($config_fields['d_m_name']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="d_m_name" id="d_m_name" value="<?php print $this->user->d_m_name ?>"
                        class="input form-control <?php echo $cssreq['d_m_name']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['d_firma_name']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="d_firma_name">
                        <?php print Text::_('JSHOP_FIRMA_NAME') ?>
                        <?php if ($config_fields['d_firma_name']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="d_firma_name" id="d_firma_name"
                        value="<?php print $this->user->d_firma_name ?>"
                        class="input form-control <?php echo $cssreq['d_firma_name']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['d_email']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="d_email">
                        <?php print Text::_('JSHOP_EMAIL') ?>
                        <?php if ($config_fields['d_email']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="d_email" id="d_email" value="<?php print $this->user->d_email ?>"
                        class="input form-control validate-email <?php echo $cssreq['d_email']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['d_birthday']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="d_birthday">
                        <?php print Text::_('JSHOP_BIRTHDAY') ?>
                        <?php if ($config_fields['d_birthday']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <?php
                    $params = array('class' => 'input '.$cssreq['d_birthday'], 'size' => '25', 'maxlength' => '19');
                    ?>
                    <?php echo HTMLHelper::_('calendar', $this->user->d_birthday, 'd_birthday', 'd_birthday', $this->config->field_birthday_format, $params); ?>
                </div>
            </div>
            <?php endif; ?>

            <?php echo $this->_tmpl_editaccount_html_5 ?>

            <?php if ($config_fields['d_home']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="d_home">
                        <?php print Text::_('JSHOP_HOME') ?>
                        <?php if ($config_fields['d_home']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="d_home" id="d_home" value="<?php print $this->user->d_home ?>"
                        class="input form-control <?php echo $cssreq['d_home']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['d_apartment']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="d_apartment">
                        <?php print Text::_('JSHOP_APARTMENT') ?>
                        <?php if ($config_fields['d_apartment']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="d_apartment" id="d_apartment"
                        value="<?php print $this->user->d_apartment ?>"
                        class="input form-control <?php echo $cssreq['d_apartment']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['d_street']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="d_street">
                        <?php print Text::_('JSHOP_STREET_NR') ?>
                        <?php if ($config_fields['d_street']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="d_street" id="d_street" value="<?php print $this->user->d_street ?>"
                        class="input form-control <?php echo $cssreq['d_street']; ?>">
                    <?php if ($config_fields['d_street_nr']['display']) { ?>
                    <input type="text" name="d_street_nr" id="d_street_nr"
                        value="<?php print $this->user->d_street_nr ?>"
                        class="input form-control <?php echo $cssreq['d_street_nr']; ?>">
                    <?php } ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['d_zip']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="d_zip">
                        <?php print Text::_('JSHOP_ZIP') ?>
                        <?php if ($config_fields['d_zip']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="d_zip" id="d_zip" value="<?php print $this->user->d_zip ?>"
                        class="input form-control <?php echo $cssreq['d_zip']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['d_city']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="d_city">
                        <?php print Text::_('JSHOP_CITY') ?>
                        <?php if ($config_fields['d_city']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="d_city" id="d_city" value="<?php print $this->user->d_city ?>"
                        class="input form-control <?php echo $cssreq['d_city']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['d_state']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="d_state">
                        <?php print Text::_('JSHOP_STATE') ?>
                        <?php if ($config_fields['d_state']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="d_state" id="d_state" value="<?php print $this->user->d_state ?>"
                        class="input form-control <?php echo $cssreq['d_state']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['d_country']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="d_country">
                        <?php print Text::_('JSHOP_COUNTRY') ?>
                        <?php if ($config_fields['d_country']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <?php
                    $attribs = 'class="inputbox form-control form-select '.$cssreq['d_country'].'"';
                    ?>
                    <?php print Selects::getCountry($this->user->d_country, $attribs, 'd_country') ?>
                </div>
            </div>
            <?php endif; ?>

            <?php echo $this->_tmpl_editaccount_html_6 ?>

            <?php if ($config_fields['d_phone']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="d_phone">
                        <?php print Text::_('JSHOP_TELEFON') ?>
                        <?php if ($config_fields['d_phone']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="d_phone" id="d_phone" value="<?php print $this->user->d_phone ?>"
                        class="input form-control <?php echo $cssreq['d_phone']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['d_mobil_phone']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="d_mobil_phone">
                        <?php print Text::_('JSHOP_MOBIL_PHONE') ?>
                        <?php if ($config_fields['d_mobil_phone']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="d_mobil_phone" id="d_mobil_phone"
                        value="<?php print $this->user->d_mobil_phone ?>"
                        class="input form-control <?php echo $cssreq['d_mobil_phone']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['d_fax']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="d_fax">
                        <?php print Text::_('JSHOP_FAX') ?>
                        <?php if ($config_fields['d_fax']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="d_fax" id="d_fax" value="<?php print $this->user->d_fax ?>"
                        class="input form-control <?php echo $cssreq['d_fax']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['d_ext_field_1']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="d_ext_field_1">
                        <?php print Text::_('JSHOP_EXT_FIELD_1') ?>
                        <?php if ($config_fields['d_ext_field_1']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="d_ext_field_1" id="d_ext_field_1"
                        value="<?php print $this->user->d_ext_field_1 ?>"
                        class="input form-control <?php echo $cssreq['d_ext_field_1']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['d_ext_field_2']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="d_ext_field_2">
                        <?php print Text::_('JSHOP_EXT_FIELD_2') ?>
                        <?php if ($config_fields['d_ext_field_2']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="d_ext_field_2" id="d_ext_field_2"
                        value="<?php print $this->user->d_ext_field_2 ?>"
                        class="input form-control <?php echo $cssreq['d_ext_field_2']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php if ($config_fields['d_ext_field_3']['display']) : ?>
            <div class="control-group">
                <div class="control-label name">
                    <label for="d_ext_field_3">
                        <?php print Text::_('JSHOP_EXT_FIELD_3') ?>
                        <?php if ($config_fields['d_ext_field_3']['require']) : ?><span>*</span><?php endif; ?>
                    </label>
                </div>
                <div class="controls">
                    <input type="text" name="d_ext_field_3" id="d_ext_field_3"
                        value="<?php print $this->user->d_ext_field_3 ?>"
                        class="input form-control <?php echo $cssreq['d_ext_field_3']; ?>">
                </div>
            </div>
            <?php endif; ?>

            <?php echo $this->tmp_d_fields ?>
        </div>

        <?php if ($config_fields['privacy_statement']['display']) : ?>
        <div class="jshop_block_privacy_statement">
            <div class="control-group">
                <div class="control-label name">
                    <label for="privacy_statement">
                        <a class="privacy_statement" href="#"
                            onclick="window.open('<?php print Helper::SEFLink('index.php?option=com_jshopping&controller=content&task=view&page=privacy_statement&tmpl=component', 1); ?>','window','width=800, height=600, scrollbars=yes, status=no, toolbar=no, menubar=no, resizable=yes, location=no');return false;">
                            <?php print Text::_('JSHOP_PRIVACY_STATEMENT') ?>
                            <?php if ($config_fields['privacy_statement']['require']) : ?><span>*</span><?php endif; ?>
                        </a>
                    </label>
                </div>
                <div class="controls">
                    <input type="checkbox" name="privacy_statement" id="privacy_statement" value="1"
                        <?php echo $cssreq['privacy_statement'];?>>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="control-group box_button">
            <div class="controls">
                <?php echo $this->_tmpl_editaccount_html_7 ?>
                <div class="requiredtext">* <?php print Text::_('JSHOP_REQUIRED') ?></div>
                <?php echo $this->_tmpl_editaccount_html_8 ?>
                <input type="submit" name="next" value="<?php print Text::_('JSHOP_SAVE') ?>"
                    class="btn btn-primary button" />
            </div>
        </div>
    </form>
</div>