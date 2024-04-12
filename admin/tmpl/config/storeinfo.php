<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
$jshopConfig=\JSFactory::getConfig();
\JFilterOutput::objectHTMLSafe($jshopConfig, ENT_QUOTES);
$vendor=$this->vendor;
$lists=$this->lists;
\JHTML::_('bootstrap.tooltip');
?>
<div class="row">
<div class="col-md-12">
<div id="j-main-container" class="j-main-container">
<?php \JSHelperAdmin::displaySubmenuConfigs('storeinfo');?>
<div class="jshop_edit">
<form action="index.php?option=com_jshopping&controller=config" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<?php print $this->tmp_html_start?>
<input type="hidden" name="task" value="">
<input type="hidden" name="tab" value="5">
<input type="hidden" name="vendor_id" value="<?php print $vendor->id;?>">

<div class="col100" id="storeinfo">
<fieldset class="adminform">
    <legend><?php echo JText::_('JSHOP_STORE_INFO')?></legend>
    <table class="admintable table-striped" width="100%" >
    <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_STORE_NAME')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="shop_name" value="<?php echo $vendor->shop_name?>" />
     </td>
    </tr>
    <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_STORE_COMPANY')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="company_name" value="<?php echo $vendor->company_name?>" />
     </td>
    </tr>
    <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_STORE_URL')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="url" value="<?php echo $vendor->url?>" />
     </td>
    </tr>
    <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_LOGO')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="logo" value="<?php echo $vendor->logo?>" />
     </td>
    </tr>    
    <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_STORE_ADRESS')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="adress" value="<?php echo $vendor->adress?>" />
     </td>
    </tr>
    <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_STORE_CITY')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="city" value="<?php echo $vendor->city?>" />
     </td>
    </tr>
    <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_STORE_ZIP')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="zip"  value="<?php echo $vendor->zip?>" />
     </td>
    </tr>
    <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_STORE_STATE')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="state" value="<?php echo $vendor->state?>" />
     </td>
    </tr>
    <tr>
     <td class="key">
       <?php echo JText::_('JSHOP_STORE_COUNTRY')?>
     </td>
     <td>
       <?php echo $lists['countries'];?>
     </td>
    </tr>    
    </table>
</fieldset>
</div>
<div class="clr"></div>

<div class="col100" id="contactinfo">
<fieldset class="adminform">
    <legend><?php echo JText::_('JSHOP_CONTACT_INFO')?></legend>
    <table class="admintable table-striped" width="100%" >
    <tr>
     <td  class="key">
       <?php echo JText::_('JSHOP_CONTACT_FIRSTNAME')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="f_name" value="<?php echo $vendor->f_name?>" />
     </td>
    </tr>
    <tr>
     <td  class="key">
       <?php echo JText::_('JSHOP_CONTACT_LASTNAME')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="l_name" value="<?php echo $vendor->l_name?>" />
     </td>
    </tr>
    <tr>
     <td  class="key">
       <?php echo JText::_('JSHOP_CONTACT_MIDDLENAME')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="middlename" value="<?php echo $vendor->middlename?>" />
     </td>
    </tr>
    <tr>
     <td  class="key">
       <?php echo JText::_('JSHOP_CONTACT_PHONE')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="phone" value="<?php echo $vendor->phone?>" />
     </td>
    </tr>
    <tr>
     <td  class="key">
       <?php echo JText::_('JSHOP_CONTACT_FAX')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="fax" value="<?php echo $vendor->fax?>" />
     </td>
    </tr> 
    <tr>
     <td  class="key">
       <?php echo JText::_('JSHOP_EMAIL')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="email" value="<?php echo $vendor->email?>" />
     </td>
    </tr>
    </table>
</fieldset>
</div>
<div class="clr"></div>

<div class="col100" id="bankinfo">
<fieldset class="adminform">
    <legend><?php echo JText::_('JSHOP_BANK')?></legend>
    <table class="admintable table-striped" width="100%" >
    <tr>
     <td  class="key">
       <?php echo JText::_('JSHOP_BENEF_BANK_NAME')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="benef_bank_info" value="<?php echo $vendor->benef_bank_info?>" />
     </td>
    </tr>

    <tr>
     <td  class="key">
       <?php echo JText::_('JSHOP_BENEF_BIC')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="benef_bic" value="<?php echo $vendor->benef_bic?>" />
     </td>
    </tr>
    <tr>
     <td  class="key">
       <?php echo JText::_('JSHOP_BENEF_CONTO')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="benef_conto" value="<?php echo $vendor->benef_conto?>" />
     </td>
    </tr>
    <tr>
     <td  class="key">
       <?php echo JText::_('JSHOP_BENEF_PAYEE')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="benef_payee" value="<?php echo $vendor->benef_payee?>" />
     </td>
    </tr>
    <tr>
     <td  class="key">
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
     <td  class="key">
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

<div class="col100" id="bank2info">
<fieldset class="adminform">
    <legend><?php echo JText::_('JSHOP_INTERM_BANK')?></legend>
    <table class="admintable table-striped" width="100%" >
    <tr>
     <td  class="key">
       <?php echo JText::_('JSHOP_INTERM_NAME')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="interm_name" value="<?php echo $vendor->interm_name?>" />
     </td>
    </tr>
    <tr>
     <td  class="key">
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

<div class="col100" id="taxinfo">
<fieldset class="adminform">
    <table class="admintable table-striped" width="100%" >
    <tr>
     <td  class="key">
       <?php echo JText::_('JSHOP_IDENTIFICATION_NUMBER')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="identification_number" value="<?php echo $vendor->identification_number?>" />
     </td>
    </tr>
    <tr>
     <td  class="key">
       <?php echo JText::_('JSHOP_TAX_NUMBER')?>
     </td>
     <td>
       <input size="55" type="text" class="inputbox form-control" name="tax_number" value="<?php echo $vendor->tax_number?>" />
     </td>
    </tr>
    <tr>
     <td  class="key">
       <?php echo JText::_('JSHOP_ADDITIONAL_INFORMATION')?>
     </td>
     <td>
        <textarea rows="5" cols="55" name="additional_information" class = "form-control"><?php echo $vendor->additional_information?></textarea>
     </td>
    </tr>
    </table>
</fieldset>
</div>
<div class="clr"></div>

<div class="col100" id="pdfinfo">
<fieldset class="adminform">
    <legend><?php echo JText::_('JSHOP_PDF_CONFIG')?></legend>
    <table class="admintable table-striped" width="100%" >
    <tr>
    <td  class="key">
       <?php echo JText::_('JSHOP_PDF_HEADER')?>
       <?php echo \JSHelperAdmin::tooltip(JText::_('JSHOP_PDF_ONLYJPG'))?>
    </td>
    <td>
        <input size="55" type="file" name="header" value="" />
    </td>
    </tr>

    <tr>
    <td  class="key">
       <?php echo JText::_('JSHOP_IMAGE_WIDTH')?>
       <?php echo \JSHelperAdmin::tooltip(JText::_('JSHOP_PDF_INMM'))?>
    </td>
    <td>
        <input size="55" type="text" class="inputbox form-control" name="pdf_parameters[pdf_header_width]" value="<?php echo $jshopConfig->pdf_header_width?>" />
    </td>
    </tr>
    <tr>
    <td  class="key">
       <?php echo JText::_('JSHOP_IMAGE_HEIGHT')?>
       <?php echo \JSHelperAdmin::tooltip(JText::_('JSHOP_PDF_INMM'))?>
    </td>
    <td>
        <input size="55" type="text" class="inputbox form-control" name="pdf_parameters[pdf_header_height]" value="<?php echo $jshopConfig->pdf_header_height?>" />
    </td>
    </tr>
    <tr>
    <td> </td>
    </tr>
    <tr>
    <td  class="key">
       <?php echo JText::_('JSHOP_PDF_FOOTER')?>
       <?php echo \JSHelperAdmin::tooltip(JText::_('JSHOP_PDF_ONLYJPG'))?>
    </td>
    <td>
        <input size="55" type="file" name="footer" value="" />
    </td>
    </tr>
    <tr>
    <td  class="key">
       <?php echo JText::_('JSHOP_IMAGE_WIDTH')?>
       <?php echo \JSHelperAdmin::tooltip(JText::_('JSHOP_PDF_INMM'))?>
    </td>
    <td>
        <input size="55" type="text" class="inputbox form-control" name="pdf_parameters[pdf_footer_width]" value="<?php echo $jshopConfig->pdf_footer_width?>" />
    </td>
    </tr>
    <tr>
    <td  class="key">
       <?php echo JText::_('JSHOP_IMAGE_HEIGHT')?>
       <?php echo \JSHelperAdmin::tooltip(JText::_('JSHOP_PDF_INMM'))?>
    </td>
    <td>
        <input size="55" type="text" class="inputbox form-control" name="pdf_parameters[pdf_footer_height]" value="<?php echo $jshopConfig->pdf_footer_height?>" />
    </td>
    </tr>
    <tr>
    <td></td>
    <td >
        <?php print JText::_('JSHOP_PDF_PREVIEW_INFO1')?>
        <a class="btn btn-secondary" target="_blank" href="index.php?option=com_jshopping&controller=config&task=preview_pdf&config_id=<?php echo $jshopConfig->id?>"><?php echo JText::_('JSHOP_PDF_PREVIEW')?></a>
    </td>
    </tr>
    </table>

</fieldset>
</div>
<div class="clr"></div>

<?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
<?php print $this->tmp_html_end?>
</form>
</div>
</div>
</div>
</div>