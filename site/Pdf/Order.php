<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Pdf;
defined('_JEXEC') or die();

include_once(JPATH_JOOMSHOPPING."/config/pdf_config.php");
include_once(JPATH_JOOMSHOPPING."/Lib/tcpdf/tcpdf.php");

class Order extends \TCPDF{
    
    public $pdfcolors = array(array(0,0,0), array(200,200,200), array(155,155,155));
    public $img_header = 'header.jpg';
    public $img_footer = 'footer.jpg';
    
    public function addNewPage(){
        $this->addPage();
        $this->addTitleHead();
    }
    
	public function addTitleHead(){
		$jshopConfig = \JSFactory::getConfig();
        $vendorinfo = $this->_vendorinfo;
		if (file_exists($jshopConfig->path.'images/'.$this->img_header)){
			$this->Image($jshopConfig->path.'images/'.$this->img_header,1,1,$jshopConfig->pdf_header_width,$jshopConfig->pdf_header_height);
		}
		if (file_exists($jshopConfig->path.'images/'.$this->img_footer)){
			$this->Image($jshopConfig->path.'images/'.$this->img_footer,1,265,$jshopConfig->pdf_footer_width,$jshopConfig->pdf_footer_height);
		}
        $this->SetFont('freesans','',8);
        $this->SetXY(115,12);
        $this->SetTextColor($this->pdfcolors[2][0], $this->pdfcolors[2][1], $this->pdfcolors[2][2]);
        $_vendor_info = array();
        $_vendor_info[] = $vendorinfo->company_name;
        $_vendor_info[] = $vendorinfo->adress;
        $_vendor_info[] = $vendorinfo->zip." ".$vendorinfo->city;
        if ($vendorinfo->phone) $_vendor_info[] = \JText::_('JSHOP_CONTACT_PHONE').": ".$vendorinfo->phone;
        if ($vendorinfo->fax) $_vendor_info[] = \JText::_('JSHOP_CONTACT_FAX') . ": ".$vendorinfo->fax;
        if ($vendorinfo->email) $_vendor_info[] = \JText::_('JSHOP_EMAIL').": ".$vendorinfo->email;
		$obj = $this;
        \JFactory::getApplication()->triggerEvent('onBeforeAddTitleHead', array(&$vendorinfo, &$pdf, &$_vendor_info, &$obj));
        $str_vendor_info = implode("\n",$_vendor_info);
        $this->MultiCell(80, 3, $str_vendor_info, 0, 'R');
        $this->SetTextColor($this->pdfcolors[0][0], $this->pdfcolors[0][1], $this->pdfcolors[0][2]);
	}
    
    static public function generatePDF($order){    
        $jshopConfig = \JSFactory::getConfig();
        $vendorinfo = $order->getVendorInfo();
        if ($order->user_id){
            $user = \JSFactory::getTable('userShop');
            $user->load($order->user_id);
        }
        
        $pdf = new self();        
        $pdf->pdfOrderHeader = \JText::_('JSHOP_EMAIL_BILL');
        
        \JPluginHelper::importPlugin('jshoppingorder');
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeCreatePdfOrder', array(&$order, &$vendorinfo, &$pdf));
        
        $pdf->_vendorinfo = $vendorinfo;
        $pdf->SetFont('freesans','',8);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0,0,0);
        $pdf->addNewPage();
        $pdf->SetAutoPageBreak(false);
        
        $y = 55;
        $pdf->SetXY(20,$y);
        $pdf->setfontsize(6);
        $pdf->SetTextColor($pdf->pdfcolors[0][0], $pdf->pdfcolors[0][1], $pdf->pdfcolors[0][2]);
        $pdf->MultiCell(80,3, $vendorinfo->company_name.", ".$vendorinfo->adress.", ".$vendorinfo->zip." ".$vendorinfo->city,0,'L');
        
        $pdf->SetXY(110,$y);
        $pdf->SetFont('freesansb','',11);
        $pdf->SetTextColor($pdf->pdfcolors[0][0], $pdf->pdfcolors[0][1], $pdf->pdfcolors[0][2]);
        $pdf->MultiCell(80,3, $pdf->pdfOrderHeader, 0, 'R');
        
        $y+=10;
        $pdf->SetFont('freesans','',11);
        $pdf->SetXY(20,$y);
            
        $address_data = array(
            $order->firma_name,
            trim($order->f_name." ".$order->l_name." ".$order->m_name),
            trim(trim($order->street." ".$order->street_nr)." ".$order->home." ".$order->apartment),
            trim($order->zip." ".$order->city),
            $order->state,
            $order->country
        );
        $pdf_address = implode("\n", array_filter($address_data));
        $dispatcher->triggerEvent('onGeneratePdfOrderBeforePdfAddress', array(&$order, &$pdf, &$pdf_address));
        $pdf->MultiCell(80,4.5, $pdf_address, 0,'L');
        
        $pdf->SetFont('freesansi','',11);
        $pdf->SetXY(110,$y);
        $pdf->MultiCell(80,4.5,\JText::_('JSHOP_ORDER_SHORT_NR')." ".$order->order_number."\n".\JText::_('JSHOP_ORDER_FROM')." ".$order->order_date,0,'R');
        if ($jshopConfig->date_invoice_in_invoice){
            $y+=12;
            $pdf->SetXY(110,$y);
            $pdf->MultiCell(80,4.5,\JText::_('JSHOP_INVOICE_DATE')." ".strftime($jshopConfig->store_date_format, strtotime($order->invoice_date)), 0, 'R');
        }
        if ($jshopConfig->user_number_in_invoice && $order->user_id && $user->number){
            $y+=11;
            $pdf->SetXY(110,$y);
            $pdf->MultiCell(80,4.5,\JText::_('JSHOP_USER_NUMBER')." ".$user->number, 0, 'R');
        }
            
        $pdf->SetDrawColor($pdf->pdfcolors[0][0], $pdf->pdfcolors[0][1], $pdf->pdfcolors[0][2]);
        $pdf->SetFont('freesans','',7);
        
        if ( $vendorinfo->identification_number){
            $pdf->SetXY(115,102);
            $pdf->MultiCell(35, 4, \JText::_('JSHOP_IDENTIFICATION_NUMBER'), 1, 'L');
            $pdf->SetXY(150,102);
            $pdf->MultiCell(40, 4, $vendorinfo->identification_number, 1, 'R');
        }
        if ($vendorinfo->tax_number){
            $pdf->SetXY(115,106);
            $pdf->MultiCell(35, 4, \JText::_('JSHOP_TAX_NUMBER'), 1, 'L');
            $pdf->SetXY(150,106);
            $pdf->MultiCell(40, 4, $vendorinfo->tax_number, 1, 'R');
        }
        
        $width_filename	= 65;
        if (!$jshopConfig->show_product_code_in_order) $width_filename = 87;
        $pdf->setfillcolor($pdf->pdfcolors[1][0], $pdf->pdfcolors[1][1], $pdf->pdfcolors[1][2]);
        $pdf->Rect(20,116,170,4,'F');
        $pdf->SetFont('freesansb','',7.5);
        $pdf->SetXY(20,116);
        $pdf->MultiCell($width_filename, 4, \JText::_('JSHOP_NAME_PRODUCT'), 1, 'L');
        
        if ($jshopConfig->show_product_code_in_order){
            $pdf->SetXY(85,116);
            $pdf->MultiCell(22, 4, \JText::_('JSHOP_EAN_PRODUCT'), 1, 'L');
        }
        
        $pdf->SetXY(107,116);
        $pdf->MultiCell(18, 4, \JText::_('JSHOP_QUANTITY'), 1, 'L');
        
        $pdf->SetXY(125,116);
        $pdf->MultiCell(25, 4, \JText::_('JSHOP_SINGLEPRICE'), 1, 'L');
        $pdf->SetXY(150,116);
        $pdf->MultiCell(40, 4,\JText::_('JSHOP_TOTAL'), 1,'R');
            
        $y = 120;
        foreach($order->products as $prod){
        
            $pdf->SetFont('freesans','',7);
            $pdf->SetXY(20, $y + 2);
            $pdf->MultiCell($width_filename, 4, $prod->product_name, 0, 'L');
            if ($prod->manufacturer!=''){
                $pdf->SetXY(20, $pdf->getY());
                $pdf->MultiCell($width_filename, 4, \JText::_('JSHOP_MANUFACTURER').": ".$prod->manufacturer, 0, 'L');
            }
            if ($jshopConfig->manufacturer_code_in_cart && $prod->manufacturer_code!=''){
                $pdf->SetXY(20, $pdf->getY());
                $pdf->MultiCell($width_filename, 4, \JText::_('JSHOP_MANUFACTURER_CODE').": ".$prod->manufacturer_code, 0, 'L');
            }
            if ($prod->product_attributes!="" || $prod->product_freeattributes!="" || $prod->delivery_time || $prod->extra_fields!=''){
                if ($prod->delivery_time){
                    $pdt = \JText::_('JSHOP_DELIVERY_TIME').": ".$prod->delivery_time;
                }else{
                    $pdt = "";
                }
                $pdf->SetXY(23, $pdf->getY());
                $pdf->SetFont('freesans','',6);
                $attribute = \JSHelper::sprintAtributeInOrder($prod->product_attributes, "pdf");
                $attribute .= \JSHelper::sprintFreeAtributeInOrder($prod->product_freeattributes, "pdf");
                $attribute .= \JSHelper::sprintExtraFiledsInOrder($prod->extra_fields,"pdf");
                if (isset($prod->_ext_attribute)) {
                    $attribute .= $prod->_ext_attribute;
                }
                $attribute .= $pdt;
                $pdf->MultiCell(62, 4, $attribute, 0, 'L');
                $pdf->SetFont('freesans','',7);
            }
            $dispatcher->triggerEvent('onGeneratePdfOrderAfterProdAttr', array(&$order, &$pdf, &$prod));
            $y2 = $pdf->getY() + 2;
            
            if ($jshopConfig->show_product_code_in_order){
                $pdf->SetXY(85, $y + 2);
                $pdf->MultiCell(22, 4, $prod->product_ean, 0, 'L');
                $y3 = $pdf->getY() + 2;
            }else{
                $y3 = $pdf->getY();
            }
            
            $pdf->SetXY(107, $y + 2);
            $pdf->MultiCell(18, 4, \JSHelper::formatqty($prod->product_quantity).$prod->_qty_unit, 0 , 'L');
            $y4 = $pdf->getY() + 2;
            
            $pdf->SetXY(125, $y + 2);
            $pdf->MultiCell(25, 4, \JSHelper::formatprice($prod->product_item_price, $order->currency_code, 0, -1), 0 , 'L');
            
            if (isset($prod->_ext_price) && $prod->_ext_price){
               $pdf->SetXY(125, $pdf->getY());
               $pdf->MultiCell(25, 4, $prod->_ext_price, 0 , 'R');
            }
            
            if ($jshopConfig->show_tax_product_in_cart && $prod->product_tax>0){            
                $pdf->SetXY(125, $pdf->getY());
                $pdf->SetFont('freesans','',6);
                $text = \JSHelper::productTaxInfo($prod->product_tax, $order->display_price);
                $pdf->MultiCell(25, 4, $text, 0 , 'L');
            }
            if ($jshopConfig->cart_basic_price_show && $prod->basicprice>0){
                $pdf->SetXY(125, $pdf->getY());
                $pdf->SetFont('freesans','',6);
                $text = \JText::_('JSHOP_BASIC_PRICE').": ".\JSHelper::sprintBasicPrice($prod);
                $pdf->MultiCell(25, 4, $text, 0 , 'L');            
            }
            $y5 = $pdf->getY() + 2;
            
            $pdf->SetFont('freesans','',7);
            $pdf->SetXY(150, $y + 2);
            $pdf->MultiCell(40, 4, \JSHelper::formatprice($prod->product_quantity * $prod->product_item_price, $order->currency_code, 0, -1), 0 , 'R');
            
            if (isset($prod->_ext_price_total) && $prod->_ext_price_total){          
               $pdf->SetXY(150, $pdf->getY());
               $pdf->MultiCell(40, 4, $prod->_ext_price_total, 0 , 'R');
            }
            
            if ($jshopConfig->show_tax_product_in_cart && $prod->product_tax>0){
                $pdf->SetXY(150, $pdf->getY());
                $pdf->SetFont('freesans','',6);
                $text = \JSHelper::productTaxInfo($prod->product_tax, $order->display_price);
                $pdf->MultiCell(40, 4, $text, 0 , 'R');
            }
            $y6 = $pdf->getY() + 2;
            
            $yn = max($y2, $y3, $y4, $y5, $y6);
            
            $dispatcher->triggerEvent('onGeneratePdfOrderAfterProd', array(&$order, &$pdf, &$prod, &$yn));
            
            $pdf->Rect(20, $y, 170, $yn - $y );
            $pdf->Rect(20, $y, 130, $yn - $y );
            
            if ($jshopConfig->show_product_code_in_order){
                $pdf->line(85, $y, 85, $yn);
            }
            $pdf->line(107, $y, 107, $yn);
            $pdf->line(125, $y, 125, $yn);
            
            $y = $yn; 
            
            
            if ($y > 260){
                $pdf->addNewPage();
                $y = 60;
            }
        }
        
        if ($y > 240){
            $pdf->addNewPage();
            $y = 60;
        }
        
        $pdf->SetFont('freesans','',10);
        
        $hide_subtotal = $order->getHideSubtotal();

        if (!$hide_subtotal){
            $pdf->SetXY(20,$y);
            $pdf->Rect(20,$y,170,5,'F');
            $pdf->MultiCell(130,5,\JText::_('JSHOP_SUBTOTAL'),'1','R');	
            $pdf->SetXY(150,$y);	
            $_pdf_ext_subtotal = isset($order->_pdf_ext_subtotal) ? $order->_pdf_ext_subtotal : '';
            $pdf->MultiCell(40,5,\JSHelper::formatprice($order->order_subtotal, $order->currency_code, 0, -1).$_pdf_ext_subtotal,'1','R');
        }else{
            $y = $y - 5;
        }
        
        $dispatcher->triggerEvent('onGeneratePdfOrderAfterSubtotal', array(&$order, &$pdf, &$y, &$hide_subtotal));
        
        if ($order->order_discount > 0){
            $y = $y + 5;     
            $pdf->SetXY(20,$y);
            $pdf->Rect(20,$y,170,5,'F');
            $_pdf_ext_discount_text = isset($order->_pdf_ext_discount_text) ? $order->_pdf_ext_discount_text : '';
            $pdf->MultiCell(130,5,_JSHOP_RABATT_VALUE.$_pdf_ext_discount_text,'1','R');
            $pdf->SetXY(150,$y);
            $_pdf_ext_discount = isset($order->_pdf_ext_discount) ? $order->_pdf_ext_discount : '';
            $pdf->MultiCell(40,5, "-".\JSHelper::formatprice($order->order_discount, $order->currency_code, 0, -1).$order->_pdf_ext_discount,'1','R');       		
        }
        
        if (!$jshopConfig->without_shipping){
            $pdf->SetXY(20,$y + 5);
            $pdf->Rect(20,$y + 5,170,5,'F');
            $pdf->MultiCell(130,5,\JText::_('JSHOP_SHIPPING_PRICE'),'1','R');
            $pdf->SetXY(150,$y + 5);
            $_pdf_ext_shipping = isset($order->_pdf_ext_shipping) ? $order->_pdf_ext_shipping : '';
            $pdf->MultiCell(40,5,\JSHelper::formatprice($order->order_shipping, $order->currency_code, 0, -1).$_pdf_ext_shipping,'1','R');
            if ($order->order_package>0 || $jshopConfig->display_null_package_price){
                $y=$y+5;
                $pdf->SetXY(20,$y + 5);
                $pdf->Rect(20,$y + 5,170,5,'F');
                $pdf->MultiCell(130,5,\JText::_('JSHOP_PACKAGE_PRICE'),'1','R');
                $pdf->SetXY(150,$y + 5);
                $_pdf_ext_shipping_package = isset($order->_pdf_ext_shipping_package) ? $order->_pdf_ext_shipping_package : '';
                $pdf->MultiCell(40,5,\JSHelper::formatprice($order->order_package, $order->currency_code, 0, -1).$_pdf_ext_shipping_package,'1','R');
            }
        }else{
            $y = $y - 5;
        }
        
        if ($order->order_payment != 0){
            $y = $y + 5;     
            $pdf->SetXY(20,$y+5);
            $pdf->Rect(20,$y+5,170,5,'F');
            $pdf->MultiCell(130,5, $order->payment_name,'1','R');
            $pdf->SetXY(150,$y+5);
            $_pdf_ext_payment = isset($order->_pdf_ext_payment) ? $order->_pdf_ext_payment : '';
            $pdf->MultiCell(40,5, \JSHelper::formatprice($order->order_payment, $order->currency_code, 0, -1).$_pdf_ext_payment, '1','R');
        }

        $show_percent_tax = $order->getShowPercentTax();

        $dispatcher->triggerEvent('onBeforeCreatePdfOrderBeforeEndTotal', array(&$order, &$pdf, &$y));
        
        if (!$jshopConfig->hide_tax){
            foreach($order->order_tax_list as $percent=>$value){
                $pdf->SetXY(20,$y + 10);
                $pdf->Rect(20,$y + 10,170,5,'F');
                $text = \JSHelper::displayTotalCartTaxName($order->display_price);
                if ($show_percent_tax) $text = $text." ".\JSHelper::formattax($percent)."%";
                $pdf->MultiCell(130,5,$text ,'1','R');        
                $pdf->SetXY(150,$y + 10);
                $_pdf_ext_tax = isset($order->_pdf_ext_tax[$percent]) ? $order->_pdf_ext_tax[$percent] : '';
                $pdf->MultiCell(40,5,\JSHelper::formatprice($value, $order->currency_code, 0, -1).$_pdf_ext_tax,'1','R');
                $y = $y + 5;
            }
        }

        $text_total = $order->getTextTotal();

        $pdf->SetFont('freesansb','',10);
        $pdf->SetXY(20,$y + 10);
        $pdf->Rect(20,$y + 10,170, 5.1,'F');
        $pdf->MultiCell(130, 5 , $text_total,'1','R');
        
        $pdf->SetXY(150,$y + 10);
        $_pdf_ext_total = isset($order->_pdf_ext_total) ? $order->_pdf_ext_total : '';
        $pdf->MultiCell(40,5,\JSHelper::formatprice($order->order_total, $order->currency_code, 0, -1).$_pdf_ext_total,'1','R');
        if ($jshopConfig->display_tax_id_in_pdf && $order->tax_number){
            $y = $y+5.2;
            $pdf->SetFont('freesans','',7);
            $pdf->SetXY(20,$y + 10);        
            $pdf->MultiCell(170, 4 , \JText::_('JSHOP_TAX_NUMBER').": ".$order->tax_number,'1','L');
        }
        $dispatcher->triggerEvent('onBeforeCreatePdfOrderAfterEndTotal', array(&$order, &$pdf, &$y));
        
        $y = $y + 10; 
        
        if ($jshopConfig->show_delivery_time_checkout && ($order->delivery_times_id || $order->delivery_time)){
            if ($y > 250){ $pdf->addNewPage(); $y = 60; }
            $deliverytimes = \JSFactory::getAllDeliveryTime();
            $delivery = $deliverytimes[$order->delivery_times_id];
            if ($delivery==""){
                $delivery = $order->delivery_time;
            }
            $y = $y+8;
            $pdf->SetFont('freesans','',7);
            $pdf->SetXY(20, $y);
            $pdf->MultiCell(170, 4, \JText::_('JSHOP_ORDER_DELIVERY_TIME').": ".$delivery, '0','L');
        }
        
        if ($jshopConfig->show_delivery_date && !\JSHelper::datenull($order->delivery_date)){
            if ($y > 250){ $pdf->addNewPage(); $y = 60; }
            $delivery_date_f = \JSHelper::formatdate($order->delivery_date); 
            $y = $y+6;
            $pdf->SetFont('freesans','',7);
            $pdf->SetXY(20, $y);
            $pdf->MultiCell(170, 4, \JText::_('JSHOP_DELIVERY_DATE').": ".$delivery_date_f, '0','L');
        }
        
        if ($order->weight==0 && $jshopConfig->hide_weight_in_cart_weight0){
            $jshopConfig->weight_in_invoice = 0;
        }
        
        if ($jshopConfig->weight_in_invoice){
            if ($y > 250){ $pdf->addNewPage(); $y = 60; }
            $y = $y+6;
            $pdf->SetFont('freesans','',7);
            $pdf->SetXY(20, $y);
            $pdf->MultiCell(170, 4 , \JText::_('JSHOP_WEIGHT_PRODUCTS').": ".\JSHelper::formatweight($order->weight), '0','L');
        }
        
        if (!$jshopConfig->without_payment && $jshopConfig->payment_in_invoice){
            if ($y > 240){ $pdf->addNewPage(); $y = 60; }
            $y = $y+6;
            $pdf->SetFont('freesansb','',7);
            $pdf->SetXY(20, $y);
            $pdf->MultiCell(170, 4, \JText::_('JSHOP_PAYMENT_INFORMATION'), '0','L');
            
            $y = $y+4;
            $pdf->SetFont('freesans','',7);
            $pdf->SetXY(20, $y);
            $pdf->MultiCell(170, 4, $order->payment_name, '0','L');
            $payment_descr = trim(trim($order->payment_information)."\n".$order->payment_description);
            if ($payment_descr!=''){
                $y = $y+4;
                $pdf->SetXY(20, $y);
                $pdf->MultiCell(170, 4,  strip_tags($payment_descr), '0','L');
                $y = $pdf->getY()-4;
            }        
        }
        
        if (!$jshopConfig->without_shipping && $jshopConfig->shipping_in_invoice){
            if ($y > 250){ $pdf->addNewPage(); $y = 60; }
            $y = $y+6;
            $pdf->SetFont('freesansb','',7);
            $pdf->SetXY(20, $y);
            $pdf->MultiCell(170, 4, \JText::_('JSHOP_SHIPPING_INFORMATION'), '0','L');
            
            $y = $y+4;
            $pdf->SetFont('freesans','',7);
            $pdf->SetXY(20, $y);
            $pdf->MultiCell(170, 4, $order->shipping_information, '0','L');
        }
        
        $y = $y + 20;
        if ($y > 240){
            $pdf->addNewPage();
            $y = 60;
        }
        
        $pdf->SetFont('freesans','',7);
        
        $show_bank_in_order = 1;
        $order_description = '';
        if ($order->payment_method_id){
            $pm_method = $order->getPayment();
            $show_bank_in_order = $pm_method->show_bank_in_order;
            $order_description = $pm_method->order_description;
        }
        
        if ($order_description){
            $pdf->SetXY(20, $y);
            $pdf->MultiCell(170,4, $order_description,'0','L');
            $y = $pdf->getY();
        }
        
        $y2 = 0;
        if ($show_bank_in_order){
            if ($vendorinfo->benef_bank_info || $vendorinfo->benef_bic || $vendorinfo->benef_conto || $vendorinfo->benef_payee || $vendorinfo->benef_iban || $vendorinfo->benef_swift){
                $pdf->SetXY(115, $y);
                $pdf->Rect(115, $y, 75,4,'F');
                $pdf->MultiCell(75,4,\JText::_('JSHOP_BANK'),'1','L');
            }
            
            if ($vendorinfo->benef_bank_info){
                $y2 += 4;
                $pdf->SetXY(115, $y2 + $y);
                $pdf->MultiCell(75,4,\JText::_('JSHOP_BENEF_BANK_NAME'),'1','L');
            }
            
            if ($vendorinfo->benef_bic){
                $y2 += 4;
                $pdf->SetXY(115, $y2 + $y);
                $pdf->MultiCell(75,4,\JText::_('JSHOP_BENEF_BIC'),'1','L');
            }
            
            if ($vendorinfo->benef_conto){
                $y2 += 4;
                $pdf->SetXY(115, $y2 + $y);
                $pdf->MultiCell(75,4,\JText::_('JSHOP_BENEF_CONTO'),'1','L');
            }
            
            if ($vendorinfo->benef_payee){
                $y2 += 4;
                $pdf->SetXY(115, $y2 + $y);
                $pdf->MultiCell(75,4,\JText::_('JSHOP_BENEF_PAYEE'),'1','L');
            }
            
            if ($vendorinfo->benef_iban){
                $y2 += 4;
                $pdf->SetXY(115, $y2 + $y);
                $pdf->MultiCell(75,4,\JText::_('JSHOP_BENEF_IBAN'),'1','L');
            }
            
            if ($vendorinfo->benef_bic_bic){
                $y2 += 4;
                $pdf->SetXY(115, $y2 + $y);
                $pdf->MultiCell(75,4,\JText::_('JSHOP_BIC_BIC'),'1','L');
            }
            
            if ($vendorinfo->benef_swift){
                $y2 += 4;
                $pdf->SetXY(115, $y2 + $y);
                $pdf->MultiCell(75,4,\JText::_('JSHOP_BENEF_SWIFT'),'1','L');
            }
            
            if ($vendorinfo->interm_name || $vendorinfo->interm_swift){
                $y2 += 4;
                $pdf->Rect(115,$y2 + $y,75,4,'F');
                $pdf->SetXY(115, $y2 + $y);
                $pdf->MultiCell(75,4,\JText::_('JSHOP_INTERM_BANK'),'1','L');
            }
            
            if ($vendorinfo->interm_name){
                $y2 += 4;
                $pdf->SetXY(115, $y2 + $y);
                $pdf->MultiCell(75,4,\JText::_('JSHOP_INTERM_NAME'),'1','L');
            }
            
            if ($vendorinfo->interm_swift){
                $y2 += 4;
                $pdf->SetXY(115, $y2 + $y);
                $pdf->MultiCell(75,4,\JText::_('JSHOP_INTERM_SWIFT'),'1','L');
            }
            
            
            $y2 = 0;
            if ($vendorinfo->benef_bank_info){
                $y2 += 4;
                $pdf->SetXY(115, $y2 + $y);
                $pdf->MultiCell(75,4,$vendorinfo->benef_bank_info,'0','R');
            }
            
            if ($vendorinfo->benef_bic){
                $y2 += 4;
                $pdf->SetXY(115, $y2 + $y);
                $pdf->MultiCell(75,4,$vendorinfo->benef_bic,'0','R');
            }
            
            if ($vendorinfo->benef_conto){
                $y2 += 4;
                $pdf->SetXY(115, $y2 + $y);
                $pdf->MultiCell(75,4,$vendorinfo->benef_conto,'0','R');
            }
            
            if ($vendorinfo->benef_payee){
                $y2 += 4;
                $pdf->SetXY(115, $y2 + $y);
                $pdf->MultiCell(75,4,$vendorinfo->benef_payee,'0','R');
            }
            
            if ($vendorinfo->benef_iban){
                $y2 += 4;
                $pdf->SetXY(115, $y2 + $y);
                $pdf->MultiCell(75,4,$vendorinfo->benef_iban,'0','R');
            }
            
            if ($vendorinfo->benef_bic_bic){
                $y2 += 4;
                $pdf->SetXY(115, $y2 + $y);
                $pdf->MultiCell(75,4,$vendorinfo->benef_bic_bic,'0','R');
            }
            
            if ($vendorinfo->benef_swift){
                $y2 += 4;
                $pdf->SetXY(115, $y2 + $y);
                $pdf->MultiCell(75,4,$vendorinfo->benef_swift,'0','R');
            }
            
            $y2 += 4;
            if ($vendorinfo->interm_name){
                $y2 += 4;
                $pdf->SetXY(115, $y2 + $y);
                $pdf->MultiCell(75,4,$vendorinfo->interm_name,'0','R');
            }

            if ($vendorinfo->interm_swift){
                $y2 += 4;
                $pdf->SetXY(115, $y2 + $y);
                $pdf->MultiCell(75,4,$vendorinfo->interm_swift,'0','R');
            }
        }

        if ($vendorinfo->additional_information){
            if ($y2 + $y > 240){
                $pdf->addNewPage();
                $y = 50;
                $y2 = 0;
            }
            $y2 += 6;
            $pdf->SetXY(20, $y2 + $y);
            $pdf->MultiCell(170,4,$vendorinfo->additional_information,'0','L');
        }
        
        if ($jshopConfig->show_return_policy_text_in_pdf){
            $pdf->SetAutoPageBreak(1);
            $y = $pdf->getY();
            if ($y>240){
                $pdf->addNewPage();
                $y = 50;
            }
            $list = $order->getReturnPolicy();
            $listtext = array();
            foreach($list as $v){
                $listtext[] = $v->text;
            }
            $rptext = implode("\n\n", $listtext);
            $rptext = strip_tags($rptext);
            $dispatcher->triggerEvent('onBeforeCreatePdfOrderRPText', array(&$order, &$pdf, &$rptext));
            $y += 6;
            $pdf->SetXY(20, $y);
            $pdf->MultiCell(170,4,$rptext,'0','L');
            $pdf->SetAutoPageBreak(false);
        }
        
        if ($order->pdf_file!=''){
            $name_pdf = $order->pdf_file;
        }else{
            $name_pdf = $order->order_id."_".md5(uniqid(rand(0,100))).".pdf";
        }
        $dispatcher->triggerEvent('onBeforeCreatePdfOrderEnd', array(&$order, &$pdf, &$name_pdf));
        $pdf->Output($jshopConfig->pdf_orders_path."/".$name_pdf, 'F');
        return $name_pdf;
    }
}