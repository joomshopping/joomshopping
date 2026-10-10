<?php
use Joomla\CMS\Language\Text;

/**
* @version      5.10.0 10.10.2026
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$config_fields = $this->config_fields;
?>
<div class="jshop" id="comjshop">

    <h1><?php print Text::_('JSHOP_MY_ACCOUNT')?></h1>

    <div class="row myaccount_layout">
        <div class="col-lg-3">
            <?php include(__DIR__."/menu.php");?>
        </div>
        <div class="col-lg-9">
            <?php echo $this->tmpl_my_account_html_start?>
            <div class="jshop_profile_data">
        
                <?php if ($this->config->show_client_id_in_my_account){?>
                    <div class="client"><span><?php print Text::_('JSHOP_CLIENT_ID')?>:</span> <?php print $this->user->user_id?></div>
                <?php }?>
    
                <?php if ($config_fields['f_name']['display'] || $config_fields['l_name']['display']){?>
                    <div class="name"><?php print $this->user->f_name." ".$this->user->l_name;?></div>
                <?php }?>
        
                <?php if ($config_fields['city']['display']){?>
                    <div class="city"><span><?php print Text::_('JSHOP_CITY')?>:</span> <?php print $this->user->city?></div>
                <?php }?>
        
                <?php if ($config_fields['state']['display']){?>
                    <div class="state"><span><?php print Text::_('JSHOP_STATE')?>:</span> <?php print $this->user->state?></div>
                <?php }?>
        
                <?php if ($config_fields['country']['display']){?>
                    <div class="country"><span><?php print Text::_('JSHOP_COUNTRY')?>:</span> <?php print $this->user->country?></div>
                <?php }?>
        
                <?php if ($config_fields['email']['display']){?>
                    <div class="email"><span><?php print Text::_('JSHOP_EMAIL')?>:</span> <?php print $this->user->email?></div>        
                <?php }?>
        
                <?php if ($this->config->display_user_group){?>
                    <div class="group">
                        <span><?php print Text::_('JSHOP_GROUP')?>:</span> 
                        <?php print $this->user->groupname?> 
                        <span class="subinfo">(<?php print Text::_('JSHOP_DISCOUNT')?>: <?php print $this->user->discountpercent?>%)</span>
                
                        <?php if ($this->config->display_user_groups_info){?>
                            <a class="jshop_user_group_info" href="<?php print $this->href_user_group_info?>"><span class="fas fa-info-circle" aria-hidden="true"></span> <?php print Text::_('JSHOP_USER_GROUPS_INFO')?></a>
                        <?php }?>
                
                    </div>
                <?php }?>
            </div>
            <?php echo $this->tmpl_my_account_html_end?>
        </div>
    </div>
</div>