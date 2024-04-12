<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$vendor=$this->vendor;
$lists=$this->lists;
?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=vendors" method="post" name="adminForm" id="adminForm" >
<?php print $this->tmp_html_start?>
<div class="col100">
<fieldset class="adminform">
<table class="admintable">
	<tr>
     <td class="key">
       <?php echo JText::_('JSHOP_PUBLISH')?>
     </td>
     <td>
       <input type="checkbox" name="publish" value="1" <?php if ($vendor->publish) echo 'checked="checked"'?> />
     </td>
   </tr>
	<tr>
	  <td class="key">
	    <?php echo JText::_('JSHOP_USER_FIRSTNAME')?>*
	  </td>
	  <td>
	    <input type="text" class="inputbox form-control" size="40" name="f_name" value="<?php echo $vendor->f_name ?>" />
	  </td>
	</tr>

	<tr>
	  <td class="key">
	    <?php echo JText::_('JSHOP_USER_LASTNAME')?>*
	  </td>
	  <td>
	    <input type="text" class="inputbox form-control" size="40" name="l_name" value="<?php echo $vendor->l_name ?>" />
	  </td>
	</tr>
		
	<tr>
	  <td class="key">
	    <?php echo JText::_('JSHOP_STORE_NAME')?>*
	  </td>
	  <td>
	    <input type="text" class="inputbox form-control" size="40" name="shop_name" value="<?php echo $vendor->shop_name ?>" />
	  </td>
	</tr>

	<tr>
	  <td class="key">
	    <?php echo JText::_('JSHOP_STORE_COMPANY')?>*
	  </td>
	  <td>
	    <input type="text" class="inputbox form-control" size="40" name="company_name" value="<?php echo $vendor->company_name ?>" />
	  </td>
	</tr>
    
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_LOGO')." (".JText::_('JSHOP_URL').")";?>
      </td>
      <td>
        <input type="text" class="inputbox form-control" size="80" name="logo" value="<?php echo $vendor->logo ?>" />
      </td>
    </tr>    

	<tr>
	  <td class="key">
	    <?php echo JText::_('JSHOP_URL');?>
	  </td>
	  <td>
	    <input type="text" class="inputbox form-control" size="80" name="url" value="<?php echo $vendor->url ?>" />
	  </td>
	</tr>

	<tr>
	  <td class="key">
	    <?php echo JText::_('JSHOP_ADRESS')?>
	  </td>
	  <td>
	    <input type="text" class="inputbox form-control" size="40" name="adress" value="<?php echo $vendor->adress ?>" />
	  </td>
	</tr>

	<tr>
	  <td class="key">
	    <?php echo JText::_('JSHOP_CITY')?>
	  </td>
	  <td>
	    <input type="text" class="inputbox form-control" size="40" name="city" value="<?php echo $vendor->city ?>" />
	  </td>
	</tr>

	<tr>
	  <td class="key">
	    <?php echo JText::_('JSHOP_ZIP')?>
	  </td>
	  <td>
	    <input type="text" class="inputbox form-control" size="40" name="zip" value="<?php echo $vendor->zip ?>" />
	  </td>
	</tr>

	<tr>
	  <td class="key">
	    <?php echo JText::_('JSHOP_STATE')?>
	  </td>
	  <td>
	    <input type="text" class="inputbox form-control" size="40" name="state" value="<?php echo $vendor->state ?>" />
	  </td>
	</tr>

	<tr>
	  <td class="key">
	    <?php echo JText::_('JSHOP_COUNTRY')?>*
	  </td>
	  <td>
	    <?php echo $lists['country'];?>
	  </td>
	</tr>

	<tr>
	  <td class="key">
	    <?php echo JText::_('JSHOP_TELEFON')?>
	  </td>
	  <td>
	    <input type="text" class="inputbox form-control" size="40" name="phone" value="<?php echo $vendor->phone ?>" />
	  </td>
	</tr>

	<tr>
	  <td class="key">
	    <?php echo JText::_('JSHOP_FAX')?>
	  </td>
	  <td>
	    <input type="text" class="inputbox form-control" size="40" name="fax" value="<?php echo $vendor->fax ?>" />
	  </td>
	</tr>

    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_EMAIL')?>*
      </td>
      <td>
        <input type="text" class="inputbox form-control" size="40" name="email" value="<?php echo $vendor->email ?>" />
      </td>
    </tr>  
			    
    <tr>
      <td class="key">
        <?php echo JText::_('JSHOP_USER_ID')." (".JText::_('JSHOP_MANAGER').")"?>
      </td>
      <td>
        <input type="text" class="inputbox form-control" name="user_id" value="<?php echo $vendor->user_id ?>" />
      </td>
    </tr>   

</table>
</fieldset>
</div>
<div class="clr"></div>

<div class="col100">
<fieldset class="adminform">
    <legend><?php echo JText::_('JSHOP_BANK')?></legend>
    <table class="admintable" width="100%" >
    <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_BENEF_BANK_NAME')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="benef_bank_info" value="<?php echo $vendor->benef_bank_info?>" />
     </td>
    </tr>

    <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_BENEF_BIC')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="benef_bic" value="<?php echo $vendor->benef_bic?>" />
     </td>
    </tr>
    <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_BENEF_CONTO')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="benef_conto" value="<?php echo $vendor->benef_conto?>" />
     </td>
    </tr>
    <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_BENEF_PAYEE')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="benef_payee" value="<?php echo $vendor->benef_payee?>" />
     </td>
    </tr>
    <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_BENEF_IBAN')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="benef_iban" value="<?php echo $vendor->benef_iban?>" />
     </td>
    </tr>
	<tr>
     <td class="key">
       <?php echo JText::_('JSHOP_BIC_BIC')?>
     </td>
     <td>
       <input size="55" type = "text" class = "inputbox form-control" name = "benef_bic_bic" value = "<?php echo $vendor->benef_bic_bic?>" />
     </td>
    </tr>
    <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_BENEF_SWIFT')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="benef_swift" value="<?php echo $vendor->benef_swift?>" />
     </td>
    </tr>
    </table>
</fieldset>
</div>
<div class="clr"></div>

<div class="col100">
<fieldset class="adminform">
    <legend><?php echo JText::_('JSHOP_INTERM_BANK')?></legend>
    <table class="admintable" width="100%" >
    <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_INTERM_NAME')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="interm_name" value="<?php echo $vendor->interm_name?>" />
     </td>
    </tr>
    <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_INTERM_SWIFT')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="interm_swift" value="<?php echo $vendor->interm_swift?>" />
     </td>
    </tr>
    </table>
</fieldset>
</div>
<div class="clr"></div>

<div class="col100">
<fieldset class="adminform">
    <table class="admintable" width="100%" >
    <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_IDENTIFICATION_NUMBER')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="identification_number" value="<?php echo $vendor->identification_number?>" />
     </td>
    </tr>
    <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_TAX_NUMBER')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="tax_number" value="<?php echo $vendor->tax_number?>" />
     </td>
    </tr>
    <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_ADDITIONAL_INFORMATION')?>
     </td>
     <td>
        <textarea rows="5" cols="55" name="additional_information" class = "form-control"><?php echo $vendor->additional_information?></textarea>
     </td>
    </tr>
    <?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
    </table>
</fieldset>
</div>
<div class="clr"></div>

<input type="hidden" name="task" value=""/>
<input type="hidden" name="f-id" value="<?php print (int)$vendor->id?>"/>
<?php print $this->tmp_html_end?>
</form>
</div>