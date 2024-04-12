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
<div class="jshop vendordetailinfo" id="comjshop">
    <?php if ($this->header) : ?>
        <h1><?php print $this->header ?></h1>
    <?php endif; ?>
    
    <div class = "row">
        <div class = "col-lg-6">
            <table class="vendor_info">
            <tr>
                <td class="name">
                    <?php print JText::_('JSHOP_F_NAME')?>: 
                </td>
                <td>
                    <?php print $this->vendor->f_name ?>
                </td>
            </tr>

            <tr>
                <td class="name">
                    <?php print JText::_('JSHOP_L_NAME')?>:
                </td>
                <td>
                    <?php print $this->vendor->l_name ?>
                </td>
            </tr>        
            <tr>
                <td class="name">
                    <?php print JText::_('JSHOP_FIRMA_NAME')?>:
                </td>
                <td>
                    <?php print $this->vendor->company_name ?>
                </td>
            </tr>

            <tr>
                <td class="name">
                    <?php print JText::_('JSHOP_EMAIL')?>:
                </td>
                <td>
                    <?php print $this->vendor->email ?>
                </td>
            </tr>        
            <tr>
                <td  class="name">
                    <?php print JText::_('JSHOP_STREET_NR')?>:
                </td>
                <td>
                    <?php print $this->vendor->adress ?>
                </td>
            </tr>

            <tr>
                <td class="name">
                    <?php print JText::_('JSHOP_ZIP')?>:
                </td>
                <td>
                    <?php print $this->vendor->zip ?>
                </td>
            </tr>        
            <tr>
                <td class="name">
                    <?php print JText::_('JSHOP_CITY')?>:
                </td>
                <td>
                    <?php print $this->vendor->city ?>
                </td>
            </tr>        
            <tr>
                <td class="name">
                    <?php print JText::_('JSHOP_STATE')?>:
                </td>
                <td>
                    <?php print $this->vendor->state ?>
                </td>
            </tr>

            <tr>
                <td class="name">
                    <?php print JText::_('JSHOP_COUNTRY')?>:
                </td>
                <td>
                    <?php print $this->vendor->country ?>
                </td>
            </tr>

            <tr>
                <td class="name">
                    <?php print JText::_('JSHOP_TELEFON')?>:
                </td>
                <td>
                    <?php print $this->vendor->phone ?>
                </td>
            </tr>
            
            <tr>
                <td class="name">
                    <?php print JText::_('JSHOP_FAX')?>:
                </td>
                <td>
                    <?php print $this->vendor->fax ?>
                </td>
            </tr>
            </table>
        </div>
        <div class = "col-lg-6 vendor_logo">
            <?php if ($this->vendor->logo!="") : ?>
                <img src="<?php print $this->vendor->logo?>" alt="<?php print htmlspecialchars($this->vendor->shop_name);?>" />
            <?php endif; ?>
        </div>
    </div>
</div>    