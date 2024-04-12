<?php
// File name   : tcpdf.php
// Begin       : 2002-08-03
// Last Update : 2008-03-07
// Author      : Nicola Asuni
// Version     : 2.5.000_PHP4
// License     : GNU LGPL (http://www.gnu.org/copyleft/lesser.html)
// NOTE:
// This class was originally derived in 2002 from the Public
// Domain FPDF class by Olivier Plathey (http://www.fpdf.org).
/**
 * Tools to encode your unicode fonts are on fonts/ttf2ufm directory.</p>
 * @name TCPDF
 * @package com.tecnick.tcpdf
 * @abstract Class for generating PDF files on-the-fly without requiring external extensions.
 * @author Nicola Asuni
 * @copyright 2004-2008 Nicola Asuni - Tecnick.com S.r.l (www.tecnick.com) Via Della Pace, 11 - 09044 - Quartucciu (CA) - ITALY - www.tecnick.com - info@tecnick.com
 * @link http://www.tcpdf.org
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @version 2.5.000_PHP4
*/
defined('_JEXEC') or die();
if(!class_exists('TCPDF')) {
define('PDF_PRODUCER','TCPDF 2.5.000_PHP4 (http://www.tcpdf.org)');
class TCPDF {

	var $page;

	var $n;

	var $offsets;

	var $buffer;

	var $pages;
	var $state;
	var $compress;
	var $DefOrientation;
	var $CurOrientation;
	var $OrientationChanges;
	var $k;
	var $fwPt;
	var $fhPt;
	var $fw;
	var $fh;
	var $wPt;
	var $hPt;
	var $w;
	var $h;
	var $lMargin;
	var $tMargin;
	var $rMargin;
	var $bMargin;
	var $cMargin;
	var $x;
	var $y;
	var $lasth;
	var $LineWidth;
	var $CoreFonts;
	var $fonts;
	var $FontFiles;
	var $diffs;
	var $images;
	var $PageLinks;
	var $links;
	var $FontFamily;
	var $FontStyle;
	var $underline;
	var $CurrentFont;
	var $FontSizePt;
	var $FontSize;
	var $DrawColor;
	var $FillColor;
	var $TextColor;
	var $ColorFlag;
	var $ws;
	var $AutoPageBreak;
	var $PageBreakTrigger;
	var $InFooter;
	var $ZoomMode;
	var $LayoutMode;
	var $title;
	var $subject;
	var $author;
	var $keywords;
	var $creator;
	var $AliasNbPages;
	var $img_rb_x;
	var $img_rb_y;
	var $imgscale = 1;
	var $isunicode = false;
	var $PDFVersion = "1.5";
	var $header_margin;
	var $footer_margin;
	var $original_lMargin;
	var $original_rMargin;
	var $header_font;
	var $footer_font;
	var $l;
	var $barcode = false;
	var $print_header = true;
	var $print_footer = true;
	var $header_width = 0;
	var $header_logo = "";
	var $header_logo_width = 30;
	var $header_title = "";
	var $header_string = "";
	var $default_table_columns = 4;
	var $HREF;
	var $fontList;
	var $issetfont;
	var $issetcolor;
	var $listordered = false;
	var $listcount = 0;
	var $tableborder = 0;
	var $tdbegin = false;
	var $tdwidth = 0;
	var $tdheight = 0;
	var $tdalign = "L";
	var $tdbgcolor = false;
	var $tempfontsize = 10;
	var $b;
	var $u;
	var $i;
	var $lispacer = "";
	var $encoding = "UTF-8";
	var $internal_encoding;
	var $prevFillColor = array(255,255,255);
	var $prevTextColor = array(0,0,0);
	var $prevFontFamily;
	var $prevFontStyle;
	var $rtl = false;
	var $tmprtl = false;
	var $encrypted;

	var $Uvalue;
	var $Ovalue;
	var $Pvalue;
	var $enc_obj_id;
	var $last_rc4_key;
	var $last_rc4_key_c;
	var $outlines = array();
	var $OutlineRoot;
	var $javascript = "";
    var $n_js;

	function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding="UTF-8") {

		/* Set internal character encoding to ASCII */
		if (function_exists("mb_internal_encoding") AND mb_internal_encoding()) {
			$this->internal_encoding = mb_internal_encoding();
			mb_internal_encoding("ASCII");
		}

        ini_set("magic_quotes_runtime", 0);

		// set language direction
		$this->rtl = (isset($this->l['a_meta_dir']) && $this->l['a_meta_dir']=='rtl') ? true : false;
		$this->tmprtl = false;

		//Some checks
		$this->_dochecks();

		//Initialization of properties
		$this->isunicode=$unicode;
		$this->page=0;
		$this->n=2;
		$this->buffer='';
		$this->pages=array();
		$this->OrientationChanges=array();
		$this->state=0;
		$this->fonts=array();
		$this->FontFiles=array();
		$this->diffs=array();
		$this->images=array();
		$this->links=array();
		$this->InFooter=false;
		$this->lasth=0;
		$this->FontFamily='';
		$this->FontStyle='';
		$this->FontSizePt=12;
		$this->underline=false;
		$this->DrawColor='0 G';
		$this->FillColor='0 g';
		$this->TextColor='0 g';
		$this->ColorFlag=false;
		$this->ws=0;
		// encryption values
		$this->encrypted=false;
		$this->last_rc4_key='';
		$this->padding="\x28\xBF\x4E\x5E\x4E\x75\x8A\x41\x64\x00\x4E\x56\xFF\xFA\x01\x08\x2E\x2E\x00\xB6\xD0\x68\x3E\x80\x2F\x0C\xA9\xFE\x64\x53\x69\x7A";

		//Standard Unicode fonts
		$this->CoreFonts=array(
		'courier'=>'Courier',
		'courierB'=>'Courier-Bold',
		'courierI'=>'Courier-Oblique',
		'courierBI'=>'Courier-BoldOblique',
		'helvetica'=>'Helvetica',
		'helveticaB'=>'Helvetica-Bold',
		'helveticaI'=>'Helvetica-Oblique',
		'helveticaBI'=>'Helvetica-BoldOblique',
		'times'=>'Times-Roman',
		'timesB'=>'Times-Bold',
		'timesI'=>'Times-Italic',
		'timesBI'=>'Times-BoldItalic',
		'symbol'=>'Symbol',
		'zapfdingbats'=>'ZapfDingbats'
		);

		//Scale factor
		switch (strtolower($unit)){
			case 'pt': {$this->k=1; break;}
			case 'mm': {$this->k=72/25.4; break;}
			case 'cm': {$this->k=72/2.54; break;}
			case 'in': {$this->k=72; break;}
			default : {$this->Error('Incorrect unit: '.$unit); break;}
		}

		//Page format
		if(is_string($format)) {
			// Page formats (45 standard ISO paper formats and 4 american common formats).
			// Paper cordinates are calculated in this way: (inches * 72) where (1 inch = 2.54 cm)
			switch (strtoupper($format)){
				case '4A0': {$format = array(4767.87,6740.79); break;}
				case '2A0': {$format = array(3370.39,4767.87); break;}
				case 'A0': {$format = array(2383.94,3370.39); break;}
				case 'A1': {$format = array(1683.78,2383.94); break;}
				case 'A2': {$format = array(1190.55,1683.78); break;}
				case 'A3': {$format = array(841.89,1190.55); break;}
				case 'A4': default: {$format = array(595.28,841.89); break;}
				case 'A5': {$format = array(419.53,595.28); break;}
				case 'A6': {$format = array(297.64,419.53); break;}
				case 'A7': {$format = array(209.76,297.64); break;}
				case 'A8': {$format = array(147.40,209.76); break;}
				case 'A9': {$format = array(104.88,147.40); break;}
				case 'A10': {$format = array(73.70,104.88); break;}
				case 'B0': {$format = array(2834.65,4008.19); break;}
				case 'B1': {$format = array(2004.09,2834.65); break;}
				case 'B2': {$format = array(1417.32,2004.09); break;}
				case 'B3': {$format = array(1000.63,1417.32); break;}
				case 'B4': {$format = array(708.66,1000.63); break;}
				case 'B5': {$format = array(498.90,708.66); break;}
				case 'B6': {$format = array(354.33,498.90); break;}
				case 'B7': {$format = array(249.45,354.33); break;}
				case 'B8': {$format = array(175.75,249.45); break;}
				case 'B9': {$format = array(124.72,175.75); break;}
				case 'B10': {$format = array(87.87,124.72); break;}
				case 'C0': {$format = array(2599.37,3676.54); break;}
				case 'C1': {$format = array(1836.85,2599.37); break;}
				case 'C2': {$format = array(1298.27,1836.85); break;}
				case 'C3': {$format = array(918.43,1298.27); break;}
				case 'C4': {$format = array(649.13,918.43); break;}
				case 'C5': {$format = array(459.21,649.13); break;}
				case 'C6': {$format = array(323.15,459.21); break;}
				case 'C7': {$format = array(229.61,323.15); break;}
				case 'C8': {$format = array(161.57,229.61); break;}
				case 'C9': {$format = array(113.39,161.57); break;}
				case 'C10': {$format = array(79.37,113.39); break;}
				case 'RA0': {$format = array(2437.80,3458.27); break;}
				case 'RA1': {$format = array(1729.13,2437.80); break;}
				case 'RA2': {$format = array(1218.90,1729.13); break;}
				case 'RA3': {$format = array(864.57,1218.90); break;}
				case 'RA4': {$format = array(609.45,864.57); break;}
				case 'SRA0': {$format = array(2551.18,3628.35); break;}
				case 'SRA1': {$format = array(1814.17,2551.18); break;}
				case 'SRA2': {$format = array(1275.59,1814.17); break;}
				case 'SRA3': {$format = array(907.09,1275.59); break;}
				case 'SRA4': {$format = array(637.80,907.09); break;}
				case 'LETTER': {$format = array(612.00,792.00); break;}
				case 'LEGAL': {$format = array(612.00,1008.00); break;}
				case 'EXECUTIVE': {$format = array(521.86,756.00); break;}
				case 'FOLIO': {$format = array(612.00,936.00); break;}
				// default: {$this->Error('Unknown page format: '.$format); break;}
			}
			$this->fwPt=$format[0];
			$this->fhPt=$format[1];
		}
		else {
			$this->fwPt=$format[0]*$this->k;
			$this->fhPt=$format[1]*$this->k;
		}

		$this->fw=$this->fwPt/$this->k;
		$this->fh=$this->fhPt/$this->k;

		//Page orientation
		$orientation=strtolower($orientation);
		if($orientation=='p' or $orientation=='portrait') {
			$this->DefOrientation='P';
			$this->wPt=$this->fwPt;
			$this->hPt=$this->fhPt;
		}
		elseif($orientation=='l' or $orientation=='landscape') {
			$this->DefOrientation='L';
			$this->wPt=$this->fhPt;
			$this->hPt=$this->fwPt;
		}
		else {
			$this->Error('Incorrect orientation: '.$orientation);
		}

		$this->CurOrientation=$this->DefOrientation;
		$this->w=$this->wPt/$this->k;
		$this->h=$this->hPt/$this->k;
		//Page margins (1 cm)
		$margin=28.35/$this->k;
		$this->SetMargins($margin,$margin);
		//Interior cell margin (1 mm)
		$this->cMargin=$margin/10;
		//Line width (0.2 mm)
		$this->LineWidth=.567/$this->k;
		//Automatic page break
		$this->SetAutoPageBreak(true,2*$margin);
		//Full width display mode
		$this->SetDisplayMode('fullwidth');
		//Compression
		$this->SetCompression(true);
		//Set default PDF version number
		$this->PDFVersion = "1.5";

		$this->encoding = $encoding;
		$this->b = 0;
		$this->i = 0;
		$this->u = 0;
		$this->HREF = '';
		$this->fontlist = array("arial", "times", "courier", "helvetica", "symbol");
		$this->issetfont = false;
		$this->issetcolor = false;
		$this->tableborder = 0;
		$this->tdbegin = false;
		$this->tdwidth=  0;
		$this->tdheight = 0;
		if($this->rtl) {
			$this->tdalign = "R";
		} else {
			$this->tdalign = "L";
		}
		$this->tdbgcolor = false;

		$this->SetFillColor(200, 200, 200, true);
		$this->SetTextColor(0, 0, 0, true);
	}

	function setRTL($enable) {
		$this->rtl = $enable ? true : false;
		$this->tmprtl = false;
	}

	function setTempRTL($mode) {
		switch ($mode) {
			case false:
			case 'L':
			case 'R': {
				$this->tmprtl = $mode;
			}
		}
	}

	function setLastH($h) {
		$this->lasth=$h;
	}

	function setImageScale($scale) {
		$this->imgscale=$scale;
	}
	function getImageScale() {
		return $this->imgscale;
	}

	function getPageWidth() {
		return $this->w;
	}

	function getPageHeight() {
		return $this->h;
	}

	function getBreakMargin() {
		return $this->bMargin;
	}

	function getScaleFactor() {
		return $this->k;
	}

	function SetMargins($left, $top, $right=-1) {
		//Set left, top and right margins
		$this->lMargin=$left;
		$this->tMargin=$top;
		if($right==-1) {
			$right=$left;
		}
		$this->rMargin=$right;
	}

	function SetLeftMargin($margin) {
		//Set left margin
		$this->lMargin=$margin;
		if(($this->page > 0) AND ($this->x < $margin)) {
			$this->x = $margin;
		}
	}

	function SetTopMargin($margin) {
		//Set top margin
		$this->tMargin=$margin;
		if(($this->page > 0) AND ($this->y < $margin)) {
			$this->y = $margin;
		}
	}

	function SetRightMargin($margin) {
		$this->rMargin=$margin;
		if(($this->page > 0) AND ($this->x > ($this->w - $margin))) {
			$this->x = $this->w - $margin;
		}
	}

	function SetCellPadding($pad) {
		$this->cMargin=$pad;
	}

	function SetAutoPageBreak($auto, $margin=0) {
		//Set auto page break mode and triggering margin
		$this->AutoPageBreak = $auto;
		$this->bMargin = $margin;
		$this->PageBreakTrigger = $this->h - $margin;
	}

	function SetDisplayMode($zoom, $layout='continuous') {
		//Set display mode in viewer
		if($zoom=='fullpage' or $zoom=='fullwidth' or $zoom=='real' or $zoom=='default' or !is_string($zoom)) {
			$this->ZoomMode=$zoom;
		}
		else {
			$this->Error('Incorrect zoom display mode: '.$zoom);
		}
		if($layout=='single' or $layout=='continuous' or $layout=='two' or $layout=='default') {
			$this->LayoutMode=$layout;
		}
		else {
			$this->Error('Incorrect layout display mode: '.$layout);
		}
	}

	function SetCompression($compress) {
		//Set page compression
		if(function_exists('gzcompress')) {
			$this->compress=$compress;
		}
		else {
			$this->compress=false;
		}
	}

	function SetTitle($title) {
		//Title of document
		$this->title=$title;
	}

	function SetSubject($subject) {
		//Subject of document
		$this->subject=$subject;
	}

	function SetAuthor($author) {
		//Author of document
		$this->author=$author;
	}

	function SetKeywords($keywords) {
		//Keywords of document
		$this->keywords=$keywords;
	}

	function SetCreator($creator) {
		//Creator of document
		$this->creator=$creator;
	}

	function AliasNbPages($alias='{nb}') {
		//Define an alias for total number of pages
		$this->AliasNbPages = $this->_escapetext($alias);
	}

	function Error($msg) {
		//Fatal error
		die('<strong>TCPDF error: </strong>'.$msg);
	}

	function Open() {
		//Begin document
		$this->state=1;
	}

	function Close() {
		//Terminate document
		if($this->state==3) {
			return;
		}
		if($this->page==0) {
			$this->AddPage();
		}
		//Page footer
		$this->InFooter=true;
		$this->Footer();
		$this->InFooter=false;
		//Close page
		$this->_endpage();
		//Close document
		$this->_enddoc();
	}

	function lastPage() {
		$this->page = count($this->pages);
	}

	function setPage($pnum) {
		if(($pnum > 0) AND ($pnum <= count($this->pages))) {
			$this->page = $pnum;
		}
	}

	function getPage() {
		return $this->page;
	}

	function getNumPages() {
		return count($this->pages);
	}

	function AddPage($orientation='') {
		if (count($this->pages) > $this->page) {
			// this page has been already added
			$this->page++;
			$this->y = $this->tMargin;
			return;
		}
		//Start a new page
		if($this->state==0) {
			$this->Open();
		}
		$family=$this->FontFamily;
		$style=$this->FontStyle.($this->underline ? 'U' : '');
		$size=$this->FontSizePt;
		$lw=$this->LineWidth;
		$dc=$this->DrawColor;
		$fc=$this->FillColor;
		$tc=$this->TextColor;
		$cf=$this->ColorFlag;
		if($this->page>0) {
			//Page footer
			$this->InFooter=true;
			$this->Footer();
			$this->InFooter=false;
			//Close page
			$this->_endpage();
		}
		//Start new page
		$this->_beginpage($orientation);
		//Set line cap style to square
		$this->_out('2 J');
		//Set line width
		$this->LineWidth=$lw;
		$this->_out(sprintf('%.2f w',$lw*$this->k));
		//Set font
		if($family) {
			$this->SetFont($family,$style,$size);
		}
		//Set colors
		$this->DrawColor=$dc;
		if($dc!='0 G') {
			$this->_out($dc);
		}
		$this->FillColor=$fc;
		if($fc!='0 g') {
			$this->_out($fc);
		}
		$this->TextColor=$tc;
		$this->ColorFlag=$cf;
		//Page header
		$this->Header();
		//Restore line width
		if($this->LineWidth!=$lw) {
			$this->LineWidth=$lw;
			$this->_out(sprintf('%.2f w',$lw*$this->k));
		}
		//Restore font
		if($family) {
			$this->SetFont($family,$style,$size);
		}
		//Restore colors
		if($this->DrawColor!=$dc) {
			$this->DrawColor=$dc;
			$this->_out($dc);
		}
		if($this->FillColor!=$fc) {
			$this->FillColor=$fc;
			$this->_out($fc);
		}
		$this->TextColor=$tc;
		$this->ColorFlag=$cf;
	}


	function setHeaderData($ln="", $lw=0, $ht="", $hs="") {
		$this->header_logo = $ln;
		$this->header_logo_width = $lw;
		$this->header_title = $ht;
		$this->header_string = $hs;
	}

	function setHeaderMargin($hm=10) {
		$this->header_margin = $hm;
	}

	function setFooterMargin($fm=10) {
		$this->footer_margin = $fm;
	}

	function setPrintHeader($val=true) {
		$this->print_header = $val;
	}

	function setPrintFooter($val=true) {
		$this->print_footer = $val;
	}

	function Header() {
		if ($this->print_header) {

			if (!isset($this->original_lMargin)) {
				$this->original_lMargin = $this->lMargin;
			}
			if (!isset($this->original_rMargin)) {
				$this->original_rMargin = $this->rMargin;
			}

			// reset original header margins
			$this->rMargin = $this->original_rMargin;
			$this->lMargin = $this->original_lMargin;

			// save current font values
			$font_family =  $this->FontFamily;
			$font_style = $this->FontStyle;
			$font_size = $this->FontSizePt;

			//set current position
			if ($this->rtl) {
				$this->SetXY($this->original_rMargin, $this->header_margin);
			} else {
				$this->SetXY($this->original_lMargin, $this->header_margin);
			}

			if (($this->header_logo) AND ($this->header_logo != K_BLANK_IMAGE)) {
				$this->Image(K_PATH_IMAGES.$this->header_logo, $this->GetX(), $this->header_margin, $this->header_logo_width);
			} else {
				$this->img_rb_x = $this->GetX();
				$this->img_rb_y = $this->GetY();
			}

			$cell_height = round((K_CELL_HEIGHT_RATIO * $this->header_font[2]) / $this->k, 2);
			// set starting margin for text data cell
			if ($this->rtl) {
				$header_x = $this->original_rMargin + ($this->header_logo_width * 1.1);
			} else {
				$header_x = $this->original_lMargin + ($this->header_logo_width * 1.1);
			}

			// header title
			$this->SetFont($this->header_font[0], 'B', $this->header_font[2] + 1);
			$this->SetX($header_x);
			$this->Cell($this->header_width, $cell_height, $this->header_title, 0, 1, '');

			// header string
			$this->SetFont($this->header_font[0], $this->header_font[1], $this->header_font[2]);
			$this->SetX($header_x);
			$this->MultiCell($this->header_width, $cell_height, $this->header_string, 0, '', 0, 1, 0, 0, true, 0);

			// print an ending header line
			//set style for cell border
			$prevlinewidth = $this->GetLineWidth();
			$line_width = 0.3;
			$this->SetLineWidth($line_width);
			$this->SetDrawColor(0, 0, 0);
			$this->SetY(1 + max($this->img_rb_y, $this->GetY()));
			if ($this->rtl) {
				$this->SetX($this->original_rMargin);
			} else {
				$this->SetX($this->original_lMargin);
			}
			$this->Cell(0, 0, '', 'T', 0, 'C');
			$this->SetLineWidth($prevlinewidth);

			//restore position
			if ($this->rtl) {
				$this->SetXY($this->original_rMargin, $this->tMargin);
			} else {
				$this->SetXY($this->original_lMargin, $this->tMargin);
			}

			// restore font values
			$this->SetFont($font_family, $font_style, $font_size);
		}
	}

	function Footer() {
		if ($this->print_footer) {

			if (!isset($this->original_lMargin)) {
				$this->original_lMargin = $this->lMargin;
			}
			if (!isset($this->original_rMargin)) {
				$this->original_rMargin = $this->rMargin;
			}

			// reset original header margins
			$this->rMargin = $this->original_rMargin;
			$this->lMargin = $this->original_lMargin;

			// save current font values
			$font_family =  $this->FontFamily;
			$font_style = $this->FontStyle;
			$font_size = $this->FontSizePt;

			//set font
			$this->SetFont($this->footer_font[0], $this->footer_font[1] , $this->footer_font[2]);
			//set style for cell border
			$prevlinewidth = $this->GetLineWidth();
			$line_width = 0.3;
			$this->SetLineWidth($line_width);
			$this->SetDrawColor(0, 0, 0);

			$footer_height = round((K_CELL_HEIGHT_RATIO * $this->footer_font[2]) / $this->k, 2); //footer height
			//get footer y position
			$footer_y = $this->h - $this->footer_margin - $footer_height;
			//set current position
			if ($this->rtl) {
				$this->SetXY($this->original_rMargin, $footer_y);
			} else {
				$this->SetXY($this->original_lMargin, $footer_y);
			}

			//print document barcode
			if ($this->barcode) {
				$this->Ln();
				$barcode_width = round(($this->w - $this->original_lMargin - $this->original_rMargin)/3); //max width
				$this->writeBarcode($this->GetX(), $footer_y + $line_width, $barcode_width, $footer_height - $line_width, "C128B", false, false, 2, $this->barcode);
			}

			$pagenumtxt = $this->l['w_page']." ".$this->PageNo().' / {nb}';

			$this->SetY($footer_y);

			//Print page number
			if ($this->rtl) {
				$this->SetX($this->original_rMargin);
				$this->Cell(0, $footer_height, $pagenumtxt, 'T', 0, 'L');
			} else {
				$this->SetX($this->original_lMargin);
				$this->Cell(0, $footer_height, $pagenumtxt, 'T', 0, 'R');
			}
			// restore line width
			$this->SetLineWidth($prevlinewidth);

			// restore font values
			$this->SetFont($font_family, $font_style, $font_size);
		}
	}

	function PageNo() {
		//Get current page number
		return $this->page;
	}

	function SetDrawColor($r, $g=-1, $b=-1) {
		//Set color for all stroking operations
		if(($r==0 and $g==0 and $b==0) or $g==-1) {
			$this->DrawColor=sprintf('%.3f G',$r/255);
		}
		else {
			$this->DrawColor=sprintf('%.3f %.3f %.3f RG',$r/255,$g/255,$b/255);
		}
		if($this->page>0) {
			$this->_out($this->DrawColor);
		}
	}

	function SetFillColor($r, $g=-1, $b=-1, $storeprev=false) {
		//Set color for all filling operations
		if(($r==0 and $g==0 and $b==0) or $g==-1) {
			$this->FillColor=sprintf('%.3f g',$r/255);
		}
		else {
			$this->FillColor=sprintf('%.3f %.3f %.3f rg',$r/255,$g/255,$b/255);
		}
		$this->ColorFlag=($this->FillColor!=$this->TextColor);
		if($this->page>0) {
			$this->_out($this->FillColor);
		}
		if ($storeprev) {
			// store color as previous value
			$this->prevFillColor = array($r, $g, $b);
		}
	}

	function SetTextColor($r, $g=-1, $b=-1, $storeprev=false) {
		//Set color for text
		if(($r==0 and $g==0 and $b==0) or $g==-1) {
			$this->TextColor=sprintf('%.3f g',$r/255);
		}
		else {
			$this->TextColor=sprintf('%.3f %.3f %.3f rg',$r/255,$g/255,$b/255);
		}
		$this->ColorFlag=($this->FillColor!=$this->TextColor);
		if ($storeprev) {
			// store color as previous value
			$this->prevTextColor = array($r, $g, $b);
		}
	}

	function GetStringWidth($s) {
		return $this->GetArrStringWidth($this->utf8Bidi($this->UTF8StringToArray($s), $this->tmprtl));
	}

	function GetArrStringWidth($sa) {
		$w = 0;
		foreach($sa as $char) {
			$w += $this->GetCharWidth($char);
		}
		return $w;
	}

	function GetCharWidth($char) {
		$cw = &$this->CurrentFont['cw'];
		if (isset($cw[$char])) {
			$w = $cw[$char];
		} elseif(isset($cw[ord($char)])) {
			$w = $cw[ord($char)];
		} elseif(isset($cw[chr($char)])) {
			$w = $cw[chr($char)];
		} elseif(isset($this->CurrentFont['desc']['MissingWidth'])) {
			$w = $this->CurrentFont['desc']['MissingWidth']; // set default size
		} else {
			$w = 500;
		}
		return ($w * $this->FontSize / 1000);
	}

	function GetNumChars($s) {
		if($this->isunicode) {
			return count($this->UTF8StringToArray($s));
		}
		return strlen($s);
	}

	function AddFont($family, $style='', $file='') {
		if(empty($family)) {
			return;
		}

		//Add a TrueType or Type1 font
		$family = strtolower($family);
		if((!$this->isunicode) AND ($family == 'arial')) {
			$family = 'helvetica';
		}

		$style=strtoupper($style);
		$style=str_replace('U','',$style);
		if($style == 'IB') {
			$style = 'BI';
		}

		$fontkey = $family.$style;
		// check if the font has been already added
		if(isset($this->fonts[$fontkey])) {
			return;
		}

		if($file=='') {
			$file = str_replace(' ', '', $family).strtolower($style).'.php';
		}
		if(!file_exists($this->_getfontpath().$file)) {
			// try to load the basic file without styles
			$file = str_replace(' ', '', $family).'.php';
		}

		include($this->_getfontpath().$file);

		if(!isset($name) AND !isset($fpdf_charwidths)) {
			$this->Error('Could not include font definition file');
		}

		$i = count($this->fonts)+1;

		if($this->isunicode) {
			$this->fonts[$fontkey] = array('i'=>$i, 'type'=>$type, 'name'=>$name, 'desc'=>$desc, 'up'=>$up, 'ut'=>$ut, 'cw'=>$cw, 'enc'=>$enc, 'file'=>$file, 'ctg'=>$ctg);
			$fpdf_charwidths[$fontkey] = $cw;
		} else {
			$this->fonts[$fontkey]=array('i'=>$i, 'type'=>'core', 'name'=>$this->CoreFonts[$fontkey], 'up'=>-100, 'ut'=>50, 'cw'=>$fpdf_charwidths[$fontkey]);
		}

		if(isset($diff) AND (!empty($diff))) {
			//Search existing encodings
			$d=0;
			$nb=count($this->diffs);
			for($i=1;$i<=$nb;$i++) {
				if($this->diffs[$i]==$diff) {
					$d=$i;
					break;
				}
			}
			if($d==0) {
				$d=$nb+1;
				$this->diffs[$d]=$diff;
			}
			$this->fonts[$fontkey]['diff']=$d;
		}
		if(!empty($file)) {
			if((strcasecmp($type,"TrueType") == 0) OR (strcasecmp($type,"TrueTypeUnicode") == 0)) {
				$this->FontFiles[$file]=array('length1'=>$originalsize);
			}
			else {
				$this->FontFiles[$file]=array('length1'=>$size1,'length2'=>$size2);
			}
		}
	}

	function SetFont($family, $style='', $size=0) {
		// save previous values
		$this->prevFontFamily = $this->FontFamily;
		$this->prevFontStyle = $this->FontStyle;

		//Select a font; size given in points
		global $fpdf_charwidths;

		$family=strtolower($family);
		if($family=='') {
			$family=$this->FontFamily;
		}
		if((!$this->isunicode) AND ($family == 'arial')) {
			$family = 'helvetica';
		}
		elseif(($family=="symbol") OR ($family=="zapfdingbats")) {
			$style='';
		}
		$style=strtoupper($style);

		if(strpos($style,'U')!==false) {
			$this->underline=true;
			$style=str_replace('U','',$style);
		}
		else {
			$this->underline=false;
		}
		if($style=='IB') {
			$style='BI';
		}
		if($size==0) {
			$size=$this->FontSizePt;
		}

		// try to add font (if not already added)
		if($this->isunicode) {
			$this->AddFont($family, $style);
		}

		//Test if font is already selected
		if(($this->FontFamily == $family) AND ($this->FontStyle == $style) AND ($this->FontSizePt == $size)) {
			return;
		}

		$fontkey = $family.$style;
		//if(!isset($this->fonts[$fontkey]) AND isset($this->fonts[$family])) {
		//	$style='';
		//}

		//Test if used for the first time
		if(!isset($this->fonts[$fontkey])) {
			//Check if one of the standard fonts
			if(isset($this->CoreFonts[$fontkey])) {
				if(!isset($fpdf_charwidths[$fontkey])) {
					//Load metric file
					$file = $family;
					if(($family!='symbol') AND ($family!='zapfdingbats')) {
						$file .= strtolower($style);
					}
					if(!file_exists($this->_getfontpath().$file.'.php')) {
						// try to load the basic file without styles
						$file = $family;
						$fontkey = $family;
					}
					include($this->_getfontpath().$file.'.php');
					if (($this->isunicode AND !isset($ctg)) OR ((!$this->isunicode) AND (!isset($fpdf_charwidths[$fontkey]))) ) {
						$this->Error("Could not include font metric file [".$fontkey."]: ".$this->_getfontpath().$file.".php");
					}
				}
				$i = count($this->fonts) + 1;

				if($this->isunicode) {
					$this->fonts[$fontkey] = array('i'=>$i, 'type'=>$type, 'name'=>$name, 'desc'=>$desc, 'up'=>$up, 'ut'=>$ut, 'cw'=>$cw, 'enc'=>$enc, 'file'=>$file, 'ctg'=>$ctg);
					$fpdf_charwidths[$fontkey] = $cw;
				} else {
					$this->fonts[$fontkey]=array('i'=>$i, 'type'=>'core', 'name'=>$this->CoreFonts[$fontkey], 'up'=>-100, 'ut'=>50, 'cw'=>$fpdf_charwidths[$fontkey]);
				}
			}
			else {
				$this->Error('Undefined font: '.$family.' '.$style);
			}
		}
		//Select it
		$this->FontFamily = $family;
		$this->FontStyle = $style;
		$this->FontSizePt = $size;
		$this->FontSize = $size / $this->k;
		$this->CurrentFont = &$this->fonts[$fontkey];
		if($this->page>0) {
			$this->_out(sprintf('BT /F%d %.2f Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
		}
	}

	function SetFontSize($size) {
		//Set font size in points
		if($this->FontSizePt==$size) {
			return;
		}
		$this->FontSizePt = $size;
		$this->FontSize = $size / $this->k;
		if($this->page > 0) {
			$this->_out(sprintf('BT /F%d %.2f Tf ET', $this->CurrentFont['i'], $this->FontSizePt));
		}
	}

	function AddLink() {
		//Create a new internal link
		$n=count($this->links)+1;
		$this->links[$n]=array(0,0);
		return $n;
	}

	function SetLink($link, $y=0, $page=-1) {
		//Set destination of internal link
		if($y==-1) {
			$y=$this->y;
		}
		if($page==-1) {
			$page=$this->page;
		}
		$this->links[$link]=array($page,$y);
	}

	function Link($x, $y, $w, $h, $link) {
		$this->PageLinks[$this->page][] = array($x * $this->k, $this->hPt - $y * $this->k, $w * $this->k, $h*$this->k, $link);
	}

	function Text($x, $y, $txt) {
		//Output a string
		if($this->rtl) {
			// bidirectional algorithm (some chars may be changed affecting the line length)
			$s = $this->utf8Bidi($this->UTF8StringToArray($txt), $this->tmprtl);
			$l = $this->GetArrStringWidth($s);
			$xr = $this->w - $x - $this->GetArrStringWidth($s);
		} else {
			$xr = $x;
		}
		$s = sprintf('BT %.2f %.2f Td (%s) Tj ET', $xr * $this->k, ($this->h-$y) * $this->k, $this->_escapetext($txt));
		if($this->underline AND ($txt!='')) {
			$s .= ' '.$this->_dounderline($xr, $y, $txt);
		}
		if($this->ColorFlag) {
			$s='q '.$this->TextColor.' '.$s.' Q';
		}
		$this->_out($s);
	}


	function AcceptPageBreak() {
		//Accept automatic page break or not
		return $this->AutoPageBreak;
	}

	function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0) {

		$k = $this->k;

		if((($this->y + $h) > $this->PageBreakTrigger) AND empty($this->InFooter) AND $this->AcceptPageBreak()) {
			//Automatic page break
			$x = $this->x;
			$ws = $this->ws;
			if($ws > 0) {
				$this->ws = 0;
				$this->_out('0 Tw');
			}
			$this->AddPage($this->CurOrientation);
			if($ws > 0) {
				$this->ws = $ws;
				$this->_out(sprintf('%.3f Tw',$ws * $k));
			}
			$this->x = $x;
		}
		if($w == 0) {
			if ($this->rtl) {
				$w = $this->x - $this->lMargin;
			} else {
				$w = $this->w - $this->rMargin - $this->x;
			}
		}
		$s = '';
		if(($fill == 1) OR ($border == 1)) {
			if($fill == 1) {
				$op = ($border == 1) ? 'B' : 'f';
			} else {
				$op = 'S';
			}
			if ($this->rtl) {
				$xk = ($this->x - $w) * $k;
			} else {
				$xk = $this->x * $k;
			}
			$s .= sprintf('%.2f %.2f %.2f %.2f re %s ', $xk, ($this->h - $this->y) * $k, $w * $k, -$h * $k, $op);
		}
		if(is_string($border)) {
			$x=$this->x;
			$y=$this->y;
			if(strpos($border,'L')!==false) {
				if ($this->rtl) {
					$xk = ($x - $w) * $k;
				} else {
					$xk = $x * $k;
				}
				$s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$xk,($this->h-$y)*$k,$xk,($this->h-($y+$h))*$k);
			}
			if(strpos($border,'T')!==false) {
				if ($this->rtl) {
					$xk = ($x - $w) * $k;
					$xwk = $x * $k;
				} else {
					$xk = $x * $k;
					$xwk = ($x + $w) * $k;
				}
				$s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$xk,($this->h-$y)*$k,$xwk,($this->h-$y)*$k);
			}
			if(strpos($border,'R')!==false) {
				if ($this->rtl) {
					$xk = $x * $k;
				} else {
					$xk = ($x + $w) * $k;
				}
				$s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$xk,($this->h-$y)*$k,$xk,($this->h-($y+$h))*$k);
			}
			if(strpos($border,'B')!==false) {
				if ($this->rtl) {
					$xk = ($x - $w) * $k;
					$xwk = $x * $k;
				} else {
					$xk = $x * $k;
					$xwk = ($x + $w) * $k;
				}
				$s.=sprintf('%.2f %.2f m %.2f %.2f l S ',$xk,($this->h-($y+$h))*$k,$xwk,($this->h-($y+$h))*$k);
			}
		}
		if($txt != '') {
			// text lenght
			$width = $this->GetStringWidth($txt);
			// ratio between cell lenght and text lenght
			$ratio = ($w - (2 * $this->cMargin)) / $width;

			// stretch text if required
			if (($stretch > 0) AND (($ratio < 1) OR (($ratio > 1) AND (($stretch % 2) == 0)))) {
				if ($stretch > 2) {
					// spacing
					//Calculate character spacing in points
					$char_space = ($w - $width - (2 * $this->cMargin)) / max($this->GetNumChars($s)-1,1) * $this->k;
					//Set character spacing
					$this->_out(sprintf('BT %.2f Tc ET', $char_space));
				} else {
					// scaling
					//Calculate horizontal scaling
					$horiz_scale = $ratio*100.0;
					//Set horizontal scaling
					$this->_out(sprintf('BT %.2f Tz ET', $horiz_scale));
				}
				$align = '';
				$width = $w - (2 * $this->cMargin);
			} else {
				$stretch == 0;
			}

			if($align == 'L') {
				if ($this->rtl) {
					$dx = $w - $width - $this->cMargin;
				} else {
					$dx = $this->cMargin;
				}
			} elseif($align == 'R') {
				if ($this->rtl) {
					$dx = $this->cMargin;
				} else {
					$dx = $w - $width - $this->cMargin;
				}
			} elseif($align=='C') {
				$dx = ($w - $width)/2;
			} elseif($align=='J') {
				if ($this->rtl) {
					$dx = $w - $width - $this->cMargin;
				} else {
					$dx = $this->cMargin;
				}
			} else {
				$dx = $this->cMargin;
			}
			if($this->ColorFlag) {
				$s .= 'q '.$this->TextColor.' ';
			}
			$txt2 = $this->_escapetext($txt);
			if ($this->rtl) {
				$xdk = ($this->x - $dx - $width) * $k;
			} else {
				$xdk = ($this->x + $dx) * $k;
			}
			// 2008-02-16 Jacek Czekaj - multibyte justification
			if ($align == 'J') {
				// count number of spaces
				$ns = substr_count($txt, ' ');
				// get string width without spaces
				$width = $this->GetStringWidth(str_replace(' ', '', $txt));
				// set word position to be used with TJ operator
				$txt2 = str_replace(chr(0).' ', ') '. -2830*($w-$width-(2*$this->cMargin))/($ns?$ns:1)/$this->FontSize/$this->k . ' (', $txt2);
			}

			$s.=sprintf('BT %.2f %.2f Td [(%s)] TJ ET', $xdk, ($this->h - ($this->y + 0.5 * $h + 0.3 * $this->FontSize)) * $k, $txt2);

			if($this->underline) {
				if ($this->rtl) {
					$xdx = $this->x - $dx - $width;
				} else {
					$xdx = $this->x + $dx;
				}
				$s.=' '.$this->_dounderline($xdx, $this->y + 0.5 * $h + 0.3 * $this->FontSize, $txt);
			}
			if($this->ColorFlag) {
				$s.=' Q';
			}
			if($link) {
				if ($this->rtl) {
					$xdx = $this->x - $dx - $width;
				} else {
					$xdx = $this->x + $dx;
				}
				$this->Link($xdx, $this->y + 0.5 * $h - 0.5 * $this->FontSize, $width, $this->FontSize, $link);
			}
		}

		// output cell
		if($s) {
			// output cell
			$this->_out($s);
			// reset text stretching
			if($stretch > 2) {
				//Reset character horizontal spacing
				$this->_out('BT 0 Tc ET');
			} elseif($stretch > 0) {
				//Reset character horizontal scaling
				$this->_out('BT 100 Tz ET');
			}
		}

		$this->lasth = $h;

		if($ln>0) {
			//Go to the beginning of the next line
			$this->y += $h;
			if($ln == 1) {
				if ($this->rtl) {
					$this->x = $this->w - $this->rMargin;
				} else {
					$this->x = $this->lMargin;
				}
			}
		} else {
			// go left or right by case
			if ($this->rtl) {
				$this->x -= $w;
			} else {
				$this->x += $w;
			}
		}
	}

	function MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0) {
		if ((empty($this->lasth))OR ($reseth)) {
			//set row height
			$this->lasth = $this->FontSize * K_CELL_HEIGHT_RATIO;
		}

		// get current page number
		$startpage = $this->page;

		if (!empty($y)) {
			$this->SetY($y);
		} else {
			$y = $this->GetY();
		}
		if (!empty($x)) {
			$this->SetX($x);
		} else {
			$x = $this->GetX();
		}

		if(empty($w)) {
			if ($this->rtl) {
				$w = $this->x - $this->lMargin;
			} else {
				$w = $this->w - $this->rMargin - $this->x;
			}
		}

		// store original margin values
		$lMargin = $this->lMargin;
		$rMargin = $this->rMargin;

		// set new margin values
		if ($this->rtl) {
			$this->SetLeftMargin($this->x - $w);
			$this->SetRightMargin($this->w - $this->x);
		} else {
			$this->SetLeftMargin($this->x);
			$this->SetRightMargin($this->w - $this->x - $w);
		}

		// calculate remaining vertical space on first page ($startpage)
		$restspace = $this->getPageHeight() - $this->GetY() - $this->getBreakMargin();

		// Write text
		$nl = $this->Write($this->lasth, $txt, '', $fill, $align, true, $stretch);

		// Get end-of-text Y position
		$currentY = $this->GetY();
		// get latest page number
		$endpage = $this->page;

		if (!empty($border)) {
			// check if a new page has been created
			if ($endpage > $startpage) {
				// design borders around HTML cells.
				for ($page=$startpage; $page<=$endpage; $page++) {
					$this->page = $page;
					if ($page==$startpage) {
						$this->SetY($this->getPageHeight() - $restspace - $this->getBreakMargin());
						$h = $restspace - 1;
					} elseif ($page==$endpage) {
						$this->SetY($this->tMargin); // put cursor at the beginning of text
						$h = $currentY - $this->tMargin;
					} else {
						$this->SetY($this->tMargin); // put cursor at the beginning of text
						$h = $this->getPageHeight() - $this->tMargin - $this->getBreakMargin();
					}
					$this->SetX($x);
					$this->Cell($w, $h, "", $border, 1, '', 0);
				}
			} else {
				$h = max($h, ($currentY - $y));
				$this->SetY($y); // put cursor at the beginning of text
				$this->SetX($x);
				// design a cell around the text
				$this->Cell($w, $h, "", $border, 1, '', 0);
			}
		}

		// restore original margin values
		$this->SetLeftMargin($lMargin);
		$this->SetRightMargin($rMargin);

		if($ln>0) {
			//Go to the beginning of the next line
			$this->SetY($currentY);
			if($ln == 2) {
				$this->SetX($x + $w);
			}
		} else {
			// go left or right by case
			$this->page = $startpage;
			$this->y = $y;
			$this->SetX($x + $w);
		}

		return $nl;
	}

	function Write($h, $txt, $link='', $fill=0, $align='', $ln=false, $stretch=0) {

		// store current position
		$prevx = $this->x;
		$prevy = $this->y;

		// Adjust internal padding
		if ($this->cMargin < ($this->LineWidth/2)) {
			$this->cMargin = ($this->LineWidth/2);
		}

		// Add top space if needed
		if (($h - $this->FontSize) < $this->LineWidth) {
			$this->y += $this->LineWidth/2;
		}

		//if ($h < ($this->LineWidth)) {
		//	$h = ($this->LineWidth);
		//}

		// calculating remaining line width ($w)
		if ($this->rtl) {
			$w = $this->x - $this->lMargin;
		} else {
			$w = $this->w - $this->rMargin - $this->x;
		}

		// remove carriage returns
		$s = str_replace("\r", '', $txt);

		// get array of chars
		$chars = $this->UTF8StringToArray($s);

		// get the number of characters
		$nb = count($chars);

		// handle single space character
		if(($nb==1) AND preg_match("/[\s]/u", $s)) {
			if ($this->rtl) {
				$this->x -= $this->GetStringWidth($s);
			} else {
				$this->x += $this->GetStringWidth($s);
			}
			return;
		}

		// max column width
		$wmax = $w - (2 * $this->cMargin);

		$i = 0; // character position
		$j = 0; // current srting starting position
		$sep = -1; // position of the last blank space
		$l = 0; // current string lenght
		$nl = 0; //number of lines

		// for each character
		while($i < $nb) {
			//Get the current character
			$c = $chars[$i];
			if ($c == 10) {
				// 10 = "\n" = new line
				//Explicit line break
				if ($align == "J") {
					if ($this->rtl) {
						$talign = "R";
					} else {
						$talign = "L";
					}
				} else {
					$talign = $align;
				}
				$this->Cell($w, $h, $this->UTF8ArrSubString($chars, $j, $i), 0, 2, $talign, $fill, $link, $stretch);
				$nl++;
				$j = $i + 1;
				$l = 0;
				$sep = -1;
				if($nl == 1) {
					// set the next line width and position
					if ($this->rtl) {
						$this->x = $this->w - $this->rMargin;
						$w = $this->x - $this->lMargin;
					}
					else {
						$this->x = $this->lMargin;
						$w = $this->w - $this->rMargin - $this->x;
					}
					$wmax = $w - (2 * $this->cMargin);
				}
			} else {
				if(preg_match("/[\s]/u", $this->unichr($c))) {
					// update last blank space position
					$sep = $i;
				}

				// update string length
				if($this->isunicode) {
					// with bidirectional algorithm some chars may be changed affecting the line length
					// *** very slow
					$l = $this->GetArrStringWidth($this->utf8Bidi(array_slice($chars, $j, $i-$j+1), $this->tmprtl));
				} else {
					$l += $this->GetCharWidth($c);
				}

				if($l > $wmax) {
					// we have reached the end of column
					if($sep == -1) {
						// truncate the word because do not fit on column
						$this->Cell($w, $h, $this->UTF8ArrSubString($chars, $j, $i), 0, 2, $align, $fill, $link, $stretch);
						$nl++;
						if($nl == 1) {
							// set the next line width and position
							if ($this->rtl) {
								$this->x = $this->w - $this->rMargin;
								$w = $this->x - $this->lMargin;
							}
							else {
								$this->x = $this->lMargin;
								$w = $this->w - $this->rMargin - $this->x;
							}
							$wmax = $w - (2 * $this->cMargin);
						}
					} else {
						// word wrapping
						$this->Cell($w, $h, $this->UTF8ArrSubString($chars, $j, $sep), 0, 2, $align, $fill, $link, $stretch);
						$nl++;
						$i = $sep + 1;
						if($nl == 1) {
							// set the next line width and position
							if ($this->rtl) {
								$this->x = $this->w - $this->rMargin;
								$w = $this->x - $this->lMargin;
							}
							else {
								$this->x = $this->lMargin;
								$w = $this->w - $this->rMargin - $this->x;
							}
							$wmax = $w - (2 * $this->cMargin);
						}
					}
					$sep = -1;
					$j = $i;
					$l = 0;
				}
			}
			$i++;
		} // end while i < nb
		// print last row
		if($i != $j) {
			$this->Cell($w, $h, $this->UTF8ArrSubString($chars, $j, $nb), 0, $ln, $align, $fill, $link, $stretch);
			$nl++;
		}

		$w = $this->GetStringWidth($this->UTF8ArrSubString($chars, $j, $nb)) + (2 * $this->cMargin);
		if ($this->rtl) {
			$this->x = $prevx - $w;
		} else {
			$this->x = $prevx + $w;
		}

		// Add bottom space if needed
		if (($ln > 0) AND (($h - $this->FontSize) < $this->LineWidth)) {
			$this->y += $this->LineWidth/2;
		}

		return $nl;
	}

	function UTF8ArrSubString($strarr, $start='', $end='') {
		if (strlen($start) == 0) {
			$start = 0;
		}
		if (strlen($end) == 0) {
			$end = count($strarr);
		}
		$string = "";
		for ($i=$start; $i < $end; $i++) {
			$string .= $this->unichr($strarr[$i]);
		}
		return $string;
	}

	function unichr($c) {
		if (!$this->isunicode) {
			return chr($c);
		} elseif ($c <= 0x7F) {
			// one byte
			return chr($c);
		} else if ($c <= 0x7FF) {
			// two bytes
			return chr(0xC0 | $c >> 6).chr(0x80 | $c & 0x3F);
		} else if ($c <= 0xFFFF) {
			// three bytes
			return chr(0xE0 | $c >> 12).chr(0x80 | $c >> 6 & 0x3F).chr(0x80 | $c & 0x3F);
		} else if ($c <= 0x10FFFF) {
			// four bytes
			return chr(0xF0 | $c >> 18).chr(0x80 | $c >> 12 & 0x3F).chr(0x80 | $c >> 6 & 0x3F).chr(0x80 | $c & 0x3F);
		} else {
			return "";
		}
	}

	function Image($file, $x, $y, $w=0, $h=0, $type='', $link='', $align='') {
		//Put an image on the page
		if(!isset($this->images[$file])) {
			//First use of image, get info
			if($type == '') {
				$pos = strrpos($file,'.');
				if(empty($pos)) {
					$this->Error('Image file has no extension and no type was specified: '.$file);
				}
				$type = substr($file, $pos+1);
			}
			$type = strtolower($type);

			if($type == 'jpg' or $type == 'jpeg') {
				$info=$this->_parsejpg($file);
			} elseif($type == 'gif') {
				$info=$this->_parsegif($file);
			} elseif($type == 'png') {
				$info=$this->_parsepng($file);
			}else {
				//Allow for additional formats
				$mtd='_parse'.$type;
				if(!method_exists($this,$mtd)) {
					$this->Error('Unsupported image type: '.$type);
				}
				$info=$this->$mtd($file);
			}
			if($info === false) {
				//If false, we cannot process image
				return;
			}

			$info['i']=count($this->images)+1;
			$this->images[$file]=$info;
		}
		else {
			$info=$this->images[$file];
		}
		//Automatic width and height calculation if needed
		if(($w == 0) and ($h == 0)) {
			//Put image at 72 dpi
			// 2004-06-14 :: Nicola Asuni, scale factor where added
			$w = $info['w'] / ($this->imgscale * $this->k);
			$h = $info['h'] / ($this->imgscale * $this->k);
		}
		if($w == 0) {
			$w = $h * $info['w'] / $info['h'];
		}
		if($h == 0) {
			$h = $w * $info['h'] / $info['w'];
		}

		// 2007-10-19 Warren Sherliker
		// Check whether we need a new page first as this does not fit
		// Copied from Cell()
		if((($this->y + $h) > $this->PageBreakTrigger) AND empty($this->InFooter) AND $this->AcceptPageBreak()) {
			// Automatic page break
			$this->AddPage($this->CurOrientation);
			// Reset coordinates to top fo next page
			$x = $this->GetX();
			$y = $this->GetY();
		}
		// 2007-10-19 Warren Sherliker: End Edit

		// set bottomcoordinates
		$this->img_rb_y = $y + $h;
		if ($this->rtl) {
			$ximg = ($this->w - $x -$w);
			// set left side coordinate
			$this->img_rb_x = $ximg;
		} else {
			$ximg = $x;
			// set right side coordinate
			$this->img_rb_x = $ximg + $w;
		}
		$xkimg = $ximg * $this->k;
		$this->_out(sprintf('q %.2f 0 0 %.2f %.2f %.2f cm /I%d Do Q', $w*$this->k, $h*$this->k, $xkimg, ($this->h-($y+$h))*$this->k, $info['i']));

		if($link) {
			$this->Link($ximg, $y, $w, $h, $link);
		}

		// set pointer to align the successive text/objects
		switch($align) {
			case 'T':{
				$this->y = $y;
				$this->x = $this->img_rb_x;
				break;
			}
			case 'M':{
				$this->y = $y + round($h/2);
				$this->x = $this->img_rb_x;
				break;
			}
			case 'B':{
				$this->y = $this->img_rb_y;
				$this->x = $this->img_rb_x;
				break;
			}
			case 'N':{
				$this->SetY($this->img_rb_y);
				break;
			}
			default:{
				break;
			}
		}
	}


	function Ln($h='') {
		//Line feed; default value is last cell height
		if ($this->rtl) {
			$this->x = $this->w - $this->rMargin;
		} else {
			$this->x = $this->lMargin;
		}
		if(is_string($h)) {
			$this->y += $this->lasth;
		} else {
			$this->y += $h;
		}
	}

	function GetX() {
		//Get x position
		if ($this->rtl) {
			return ($this->w - $this->x);
		} else {
			return $this->x;
		}
	}

	function GetAbsX() {
		return $this->x;
	}

	function GetY() {
		//Get y position
		return $this->y;
	}

	function SetX($x) {
		//Set x position
		if ($this->rtl) {
			if($x >= 0) {
				$this->x = $this->w - $x;
			} else {
				$this->x = abs($x);
			}
		} else {
			if($x >= 0) {
				$this->x = $x;
			} else {
				$this->x = $this->w + $x;
			}
		}
	}

	function SetY($y) {
		//Set y position and reset x
		if ($this->rtl) {
			$this->x = $this->w - $this->rMargin;
		} else {
			$this->x = $this->lMargin;
		}
		if($y >= 0) {
			$this->y = $y;
		} else {
			$this->y = $this->h + $y;
		}
	}

	function SetXY($x, $y) {
		//Set x and y positions
		$this->SetY($y);
		$this->SetX($x);
	}

	function Output($name='',$dest='') {
		//Output PDF to some destination
		//Finish document if necessary
		if($this->state < 3) {
			$this->Close();
		}
		//Normalize parameters
		if(is_bool($dest)) {
			$dest=$dest ? 'D' : 'F';
		}
		$dest=strtoupper($dest);
		if($dest=='') {
			if($name=='') {
				$name='doc.pdf';
				$dest='I';
			} else {
				$dest='F';
			}
		}
		switch($dest) {
			case 'I': {
				//Send to standard output
				if(ob_get_contents()) {
					$this->Error('Some data has already been output, can\'t send PDF file');
				}
				if(php_sapi_name()!='cli') {
					//We send to a browser
					header('Content-Type: application/pdf');
					if(headers_sent()) {
						$this->Error('Some data has already been output to browser, can\'t send PDF file');
					}
					header('Content-Length: '.strlen($this->buffer));
					header('Content-disposition: inline; filename="'.$name.'"');
				}
				echo $this->buffer;
				break;
			}
			case 'D': {
				//Download file
				if(ob_get_contents()) {
					$this->Error('Some data has already been output, can\'t send PDF file');
				}
				if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],'MSIE')) {
					header('Content-Type: application/force-download');
				} else {
					header('Content-Type: application/octet-stream');
				}
				if(headers_sent()) {
					$this->Error('Some data has already been output to browser, can\'t send PDF file');
				}
				header('Content-Length: '.strlen($this->buffer));
				header('Content-disposition: attachment; filename="'.$name.'"');
				echo $this->buffer;
				break;
			}
			case 'F': {
				//Save to local file
				$f=fopen($name,'wb');
				if(!$f) {
					$this->Error('Unable to create output file: '.$name);
				}
				fwrite($f,$this->buffer,strlen($this->buffer));
				fclose($f);
				break;
			}
			case 'S': {
				//Return as a string
				return $this->buffer;
			}
			default: {
				$this->Error('Incorrect output destination: '.$dest);
			}
		}
		return '';
	}

	function _dochecks() {
		//Check for locale-related bug
		if(1.1==1) {
			$this->Error('Don\'t alter the locale before including class file');
		}
		//Check for decimal separator
		if(sprintf('%.1f',1.0)!='1.0') {
			setlocale(LC_NUMERIC,'C');
		}
	}

	function _getfontpath() {
		if(!defined('K_PATH_FONTS') AND is_dir(dirname(__FILE__).'/font')) {
			define('K_PATH_FONTS', dirname(__FILE__).'/font/');
		}
		return defined('K_PATH_FONTS') ? K_PATH_FONTS : '';
	}

	function _begindoc() {
		//Start document
		$this->state=1;
		$this->_out('%PDF-'.$this->PDFVersion);
	}

	function _putpages() {
		$nb = $this->page;
		if(!empty($this->AliasNbPages)) {
			$nbstr = $this->UTF8ToUTF16BE($nb, false);
			//Replace number of pages
			for($n=1;$n<=$nb;$n++) {
				$this->pages[$n]=str_replace($this->AliasNbPages, $nbstr, $this->pages[$n]);
			}
		}
		if($this->DefOrientation=='P') {
			$wPt=$this->fwPt;
			$hPt=$this->fhPt;
		}
		else {
			$wPt=$this->fhPt;
			$hPt=$this->fwPt;
		}
		$filter=($this->compress) ? '/Filter /FlateDecode ' : '';
		for($n=1;$n<=$nb;$n++) {
			//Page
			$this->_newobj();
			$this->_out('<</Type /Page');
			$this->_out('/Parent 1 0 R');
			if(isset($this->OrientationChanges[$n])) {
				$this->_out(sprintf('/MediaBox [0 0 %.2f %.2f]',$hPt,$wPt));
			}
			$this->_out('/Resources 2 0 R');
			if(isset($this->PageLinks[$n])) {
				//Links
				$annots='/Annots [';
				foreach($this->PageLinks[$n] as $pl) {
					$rect=sprintf('%.2f %.2f %.2f %.2f',$pl[0],$pl[1],$pl[0]+$pl[2],$pl[1]-$pl[3]);
					$annots.='<</Type /Annot /Subtype /Link /Rect ['.$rect.'] /Border [0 0 0] ';
					if(is_string($pl[4])) {
						$annots.='/A <</S /URI /URI '.$this->_uristring($pl[4]).'>>>>';
					}
					else {
						$l=$this->links[$pl[4]];
						$h=isset($this->OrientationChanges[$l[0]]) ? $wPt : $hPt;
						$annots.=sprintf('/Dest [%d 0 R /XYZ 0 %.2f null]>>',1+2*$l[0],$h-$l[1]*$this->k);
					}
				}
				$this->_out($annots.']');
			}
			$this->_out('/Contents '.($this->n+1).' 0 R>>');
			$this->_out('endobj');
			//Page content
			$p=($this->compress) ? gzcompress($this->pages[$n]) : $this->pages[$n];
			$this->_newobj();
			$this->_out('<<'.$filter.'/Length '.strlen($p).'>>');
			$this->_putstream($p);
			$this->_out('endobj');
		}
		//Pages root
		$this->offsets[1]=strlen($this->buffer);
		$this->_out('1 0 obj');
		$this->_out('<</Type /Pages');
		$kids='/Kids [';
		for($i=0;$i<$nb;$i++) {
			$kids.=(3+2*$i).' 0 R ';
		}
		$this->_out($kids.']');
		$this->_out('/Count '.$nb);
		$this->_out(sprintf('/MediaBox [0 0 %.2f %.2f]',$wPt,$hPt));
		$this->_out('>>');
		$this->_out('endobj');
	}

	function _putfonts() {
		$nf=$this->n;
		foreach($this->diffs as $diff) {
			//Encodings
			$this->_newobj();
			$this->_out('<</Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences ['.$diff.']>>');
			$this->_out('endobj');
		}
		foreach($this->FontFiles as $file=>$info) {
			//Font file embedding
			$this->_newobj();
			$this->FontFiles[$file]['n']=$this->n;
			$font='';
			$f=fopen($this->_getfontpath().strtolower($file),'rb',1);
			if(!$f) {
				$this->Error('Font file not found: '.$file);
			}
			while(!feof($f)) {
				$font .= fread($f, 8192);
			}
			fclose($f);
			$compressed=(substr($file,-2)=='.z');
			if(!$compressed && isset($info['length2'])) {
				$header=(ord($font[0])==128);
				if($header) {
					//Strip first binary header
					$font=substr($font,6);
				}
				if($header && ord($font[$info['length1']])==128) {
					//Strip second binary header
					$font=substr($font,0,$info['length1']).substr($font,$info['length1']+6);
				}
			}
			$this->_out('<</Length '.strlen($font));
			if($compressed) {
				$this->_out('/Filter /FlateDecode');
			}
			$this->_out('/Length1 '.$info['length1']);
			if(isset($info['length2'])) {
				$this->_out('/Length2 '.$info['length2'].' /Length3 0');
			}
			$this->_out('>>');
			$this->_putstream($font);
			$this->_out('endobj');
		}

		foreach($this->fonts as $k=>$font) {
			//Font objects
			$this->fonts[$k]['n']=$this->n+1;
			$type=$font['type'];
			$name=$font['name'];
			if($type=='core') {
				//Standard font
				$this->_newobj();
				$this->_out('<</Type /Font');
				$this->_out('/BaseFont /'.$name);
				$this->_out('/Subtype /Type1');
				if($name!='Symbol' && $name!='ZapfDingbats') {
					$this->_out('/Encoding /WinAnsiEncoding');
				}
				$this->_out('>>');
				$this->_out('endobj');
			} elseif($type=='Type1' OR $type=='TrueType') {
				//Additional Type1 or TrueType font
				$this->_newobj();
				$this->_out('<</Type /Font');
				$this->_out('/BaseFont /'.$name);
				$this->_out('/Subtype /'.$type);
				$this->_out('/FirstChar 32 /LastChar 255');
				$this->_out('/Widths '.($this->n+1).' 0 R');
				$this->_out('/FontDescriptor '.($this->n+2).' 0 R');
				if($font['enc']) {
					if(isset($font['diff'])) {
						$this->_out('/Encoding '.($nf+$font['diff']).' 0 R');
					} else {
						$this->_out('/Encoding /WinAnsiEncoding');
					}
				}
				$this->_out('>>');
				$this->_out('endobj');
				//Widths
				$this->_newobj();
				$cw=&$font['cw'];
				$s='[';
				for($i=32;$i<=255;$i++) {
					$s.=$cw[chr($i)].' ';
				}
				$this->_out($s.']');
				$this->_out('endobj');
				//Descriptor
				$this->_newobj();
				$s='<</Type /FontDescriptor /FontName /'.$name;
				foreach($font['desc'] as $k=>$v) {
					$s.=' /'.$k.' '.$v;
				}
				$file = $font['file'];
				if($file) {
					$s.=' /FontFile'.($type=='Type1' ? '' : '2').' '.$this->FontFiles[$file]['n'].' 0 R';
				}
				$this->_out($s.'>>');
				$this->_out('endobj');
			} else {
				//Allow for additional types
				$mtd='_put'.strtolower($type);
				if(!method_exists($this, $mtd)) {
					$this->Error('Unsupported font type: '.$type);
				}
				$this->$mtd($font);
			}
		}
	}

	function _putimages() {
		$filter=($this->compress) ? '/Filter /FlateDecode ' : '';
		reset($this->images);
		//while(list($file,$info)=each($this->images)) {
        foreach($this->images as $file=>$info){
			$this->_newobj();
			$this->images[$file]['n']=$this->n;
			$this->_out('<</Type /XObject');
			$this->_out('/Subtype /Image');
			$this->_out('/Width '.$info['w']);
			$this->_out('/Height '.$info['h']);

			if (isset($info["masked"])) {
				$this->_out('/SMask '.($this->n-1).' 0 R');
			}

			if($info['cs']=='Indexed') {
				$this->_out('/ColorSpace [/Indexed /DeviceRGB '.(strlen($info['pal'])/3-1).' '.($this->n+1).' 0 R]');
			}
			else {
				$this->_out('/ColorSpace /'.$info['cs']);
				if($info['cs']=='DeviceCMYK') {
					$this->_out('/Decode [1 0 1 0 1 0 1 0]');
				}
			}
			$this->_out('/BitsPerComponent '.$info['bpc']);
			if(isset($info['f'])) {
				$this->_out('/Filter /'.$info['f']);
			}
			if(isset($info['parms'])) {
				$this->_out($info['parms']);
			}
			if(isset($info['trns']) and is_array($info['trns'])) {
				$trns='';
				for($i=0;$i<count($info['trns']);$i++) {
					$trns.=$info['trns'][$i].' '.$info['trns'][$i].' ';
				}
				$this->_out('/Mask ['.$trns.']');
			}
			$this->_out('/Length '.strlen($info['data']).'>>');
			$this->_putstream($info['data']);
			unset($this->images[$file]['data']);
			$this->_out('endobj');
			//Palette
			if($info['cs']=='Indexed') {
				$this->_newobj();
				$pal=($this->compress) ? gzcompress($info['pal']) : $info['pal'];
				$this->_out('<<'.$filter.'/Length '.strlen($pal).'>>');
				$this->_putstream($pal);
				$this->_out('endobj');
			}
		}
	}

	function _putxobjectdict() {
		foreach($this->images as $image) {
			$this->_out('/I'.$image['i'].' '.$image['n'].' 0 R');
		}
	}

	function _putresourcedict(){
		$this->_out('/ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
		$this->_out('/Font <<');
		foreach($this->fonts as $font) {
			$this->_out('/F'.$font['i'].' '.$font['n'].' 0 R');
		}
		$this->_out('>>');
		$this->_out('/XObject <<');
		$this->_putxobjectdict();
		$this->_out('>>');
	}

	function _putresources() {
		$this->_putfonts();
		$this->_putimages();
		//Resource dictionary
		$this->offsets[2]=strlen($this->buffer);
		$this->_out('2 0 obj');
		$this->_out('<<');
		$this->_putresourcedict();
		$this->_out('>>');
		$this->_out('endobj');
		$this->_putjavascript();
		$this->_putbookmarks();
		// encryption
		if ($this->encrypted) {
			$this->_newobj();
			$this->enc_obj_id = $this->n;
			$this->_out('<<');
			$this->_putencryption();
			$this->_out('>>');
			$this->_out('endobj');
		}
	}

	function _putinfo() {
		$this->_out('/CreationDate ('.$this->_escape('D:'.date('YmdHis')).')');
		$this->_out('/ModDate ('.$this->_escape('D:'.date('YmdHis')).')');
		$this->_out('/Producer '.$this->_textstring(PDF_PRODUCER));
		if(!empty($this->title)) {
			$this->_out('/Title '.$this->_textstring($this->title));
		}
		if(!empty($this->subject)) {
			$this->_out('/Subject '.$this->_textstring($this->subject));
		}
		if(!empty($this->author)) {
			$this->_out('/Author '.$this->_textstring($this->author));
		}
		if(!empty($this->keywords)) {
			$this->_out('/Keywords '.$this->_textstring($this->keywords));
		}
		if(!empty($this->creator)) {
			$this->_out('/Creator '.$this->_textstring($this->creator));
		}
	}

	function _putcatalog() {
		$this->_out('/Type /Catalog');
		$this->_out('/Pages 1 0 R');
		if($this->ZoomMode=='fullpage') {
			$this->_out('/OpenAction [3 0 R /Fit]');
		}
		elseif($this->ZoomMode=='fullwidth') {
			$this->_out('/OpenAction [3 0 R /FitH null]');
		}
		elseif($this->ZoomMode=='real') {
			$this->_out('/OpenAction [3 0 R /XYZ null null 1]');
		}
		elseif(!is_string($this->ZoomMode)) {
			$this->_out('/OpenAction [3 0 R /XYZ null null '.($this->ZoomMode/100).']');
		}
		if($this->LayoutMode=='single') {
			$this->_out('/PageLayout /SinglePage');
		}
		elseif($this->LayoutMode=='continuous') {
			$this->_out('/PageLayout /OneColumn');
		}
		elseif($this->LayoutMode=='two') {
			$this->_out('/PageLayout /TwoColumnLeft');
		}
		if (!empty($this->javascript)) {
			$this->_out('/Names <</JavaScript '.($this->n_js).' 0 R>>');
		}
		if(count($this->outlines)>0) {
			$this->_out('/Outlines '.$this->OutlineRoot.' 0 R');
			$this->_out('/PageMode /UseOutlines');
		}
		if($this->rtl) {
			$this->_out('/ViewerPreferences << /Direction /R2L >>');
		}
	}

	function _puttrailer() {
		$this->_out('/Size '.($this->n+1));
		$this->_out('/Root '.$this->n.' 0 R');
		$this->_out('/Info '.($this->n-1).' 0 R');
		if ($this->encrypted) {
			$this->_out('/Encrypt '.$this->enc_obj_id.' 0 R');
			$this->_out('/ID [()()]');
		}
	}

	function _putheader() {
		$this->_out('%PDF-'.$this->PDFVersion);
	}

	function _enddoc() {
		$this->_putheader();
		$this->_putpages();
		$this->_putresources();
		//Info
		$this->_newobj();
		$this->_out('<<');
		$this->_putinfo();
		$this->_out('>>');
		$this->_out('endobj');
		//Catalog
		$this->_newobj();
		$this->_out('<<');
		$this->_putcatalog();
		$this->_out('>>');
		$this->_out('endobj');
		//Cross-ref
		$o=strlen($this->buffer);
		$this->_out('xref');
		$this->_out('0 '.($this->n+1));
		$this->_out('0000000000 65535 f ');
		for($i=1;$i<=$this->n;$i++) {
			$this->_out(sprintf('%010d 00000 n ',$this->offsets[$i]));
		}
		//Trailer
		$this->_out('trailer');
		$this->_out('<<');
		$this->_puttrailer();
		$this->_out('>>');
		$this->_out('startxref');
		$this->_out($o);
		$this->_out('%%EOF');
		$this->state=3;
	}

	function _beginpage($orientation) {
		$this->page++;
		$this->pages[$this->page]='';
		$this->state=2;
		if ($this->rtl) {
			$this->x = $this->w - $this->rMargin;
		} else {
			$this->x = $this->lMargin;
		}
		$this->y = $this->tMargin;
		$this->FontFamily='';
		//Page orientation
		if(empty($orientation)) {
			$orientation=$this->DefOrientation;
		}
		else {
			$orientation=strtoupper($orientation[0]);
			if($orientation!=$this->DefOrientation) {
				$this->OrientationChanges[$this->page]=true;
			}
		}
		if($orientation!=$this->CurOrientation) {
			//Change orientation
			if($orientation=='P') {
				$this->wPt=$this->fwPt;
				$this->hPt=$this->fhPt;
				$this->w=$this->fw;
				$this->h=$this->fh;
			}
			else {
				$this->wPt=$this->fhPt;
				$this->hPt=$this->fwPt;
				$this->w=$this->fh;
				$this->h=$this->fw;
			}
			$this->PageBreakTrigger=$this->h-$this->bMargin;
			$this->CurOrientation=$orientation;
		}
	}

	function _endpage() {
		$this->state=1;
	}
	function _newobj() {
		$this->n++;
		$this->offsets[$this->n]=strlen($this->buffer);
		$this->_out($this->n.' 0 obj');
	}

	function _dounderline($x, $y, $txt) {
		$up = $this->CurrentFont['up'];
		$ut = $this->CurrentFont['ut'];
		$w = $this->GetStringWidth($txt) + $this->ws * substr_count($txt,' ');
		return sprintf('%.2f %.2f %.2f %.2f re f', $x * $this->k, ($this->h - ($y - $up / 1000 * $this->FontSize)) * $this->k, $w * $this->k, -$ut / 1000 * $this->FontSizePt);
	}

	function _parsejpg($file) {
		if(!function_exists('imagecreatefromjpeg')) {
			// GD is not installed, try legacy method
			return $this->_legacyparsejpg($file);
		}
		$a=getimagesize($file);
		if(empty($a)) {
			$this->Error('Missing or incorrect image file: '.$file);
		}
		if($a[2]!=2) {
			$this->Error('Not a JPEG file: '.$file);
		}
		$jpeg = imagecreatefromjpeg($file);
		return $this->outputjpg($file, $jpeg);
	}

	function _parsegif($file) {
		if(!function_exists('imagecreatefromgif')) {
			// PDF doesn't support native GIF and GD is not installed
			return false;
		}
		$a=getimagesize($file);
		if(empty($a)) {
			$this->Error('Missing or incorrect image file: '.$file);
		}
		if($a[2]!=1) {
			$this->Error('Not a GIF file: '.$file);
		}
		// Temporary convert file to jpg and then delete this temp data file
		$gif = imagecreatefromgif($file);
		return $this->toJPEG($file, $gif);
	}

	function _parsepng($file) {
		if(!function_exists('imagecreatefrompng')) {
			// GD is not installed, try legacy method
			return $this->_legacyparsepng($file);
		}
		$f=fopen($file,'rb');
		if(empty($f)) {
			$this->Error('Can\'t open image file: '.$file);
		}
		//Check signature
		if(fread($f,8)!=chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10)) {
			$this->Error('Not a PNG file: '.$file);
		}
		//Read header chunk
		fread($f,4);
		if(fread($f,4)!='IHDR') {
			$this->Error('Incorrect PNG file: '.$file);
		}
		// Temporary convert file to jpg and then delete this temp data file
		$a=getimagesize($file);
		$png = imagecreatefrompng($file);
		return $this->toJPEG($file, $png);
	}

	function _legacyparsejpg($file) {
		$a=GetImageSize($file);
		if(empty($a)) {
			$this->Error('Missing or incorrect image file: '.$file);
		}
		if($a[2]!=2) {
			$this->Error('Not a JPEG file: '.$file);
		}
		if(!isset($a['channels']) or $a['channels']==3) {
			$colspace='DeviceRGB';
		}
		elseif($a['channels']==4) {
			$colspace='DeviceCMYK';
		}
		else {
			$colspace='DeviceGray';
		}
		$bpc=isset($a['bits']) ? $a['bits'] : 8;
		//Read whole file
		$f=fopen($file,'rb');
		$data='';
		while(!feof($f)) {
			$data.=fread($f,4096);
		}
		fclose($f);
		return array('w'=>$a[0],'h'=>$a[1],'cs'=>$colspace,'bpc'=>$bpc,'f'=>'DCTDecode','data'=>$data);
	}

	function _legacyparsepng($file) {
		$f=fopen($file,'rb');
		if(empty($f)) {
			$this->Error('Can\'t open image file: '.$file);
		}
		//Check signature
		if(fread($f,8)!=chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10)) {
			$this->Error('Not a PNG file: '.$file);
		}
		//Read header chunk
		fread($f,4);
		if(fread($f,4)!='IHDR') {
			$this->Error('Incorrect PNG file: '.$file);
		}
		$w=$this->_freadint($f);
		$h=$this->_freadint($f);
		$bpc=ord(fread($f,1));
		if($bpc>8) {
			$this->Error('16-bit depth not supported: '.$file);
		}
		$ct=ord(fread($f,1));
		if($ct==0) {
			$colspace='DeviceGray';
		}
		elseif($ct==2) {
			$colspace='DeviceRGB';
		}
		elseif($ct==3) {
			$colspace='Indexed';
		}
		else {
			$this->Error('Alpha channel not supported: '.$file);
		}
		if(ord(fread($f,1))!=0) {
			$this->Error('Unknown compression method: '.$file);
		}
		if(ord(fread($f,1))!=0) {
			$this->Error('Unknown filter method: '.$file);
		}
		if(ord(fread($f,1))!=0) {
			$this->Error('Interlacing not supported: '.$file);
		}
		fread($f,4);
		$parms='/DecodeParms <</Predictor 15 /Colors '.($ct==2 ? 3 : 1).' /BitsPerComponent '.$bpc.' /Columns '.$w.'>>';
		//Scan chunks looking for palette, transparency and image data
		$pal='';
		$trns='';
		$data='';
		do {
			$n=$this->_freadint($f);
			$type=fread($f,4);
			if($type=='PLTE') {
				//Read palette
				$pal=fread($f,$n);
				fread($f,4);
			}
			elseif($type=='tRNS') {
				//Read transparency info
				$t=fread($f,$n);
				if($ct==0) {
					$trns=array(ord(substr($t,1,1)));
				}
				elseif($ct==2) {
					$trns=array(ord(substr($t,1,1)),ord(substr($t,3,1)),ord(substr($t,5,1)));
				}
				else {
					$pos=strpos($t,chr(0));
					if($pos!==false) {
						$trns=array($pos);
					}
				}
				fread($f,4);
			}
			elseif($type=='IDAT') {
				//Read image data block
				$data.=fread($f,$n);
				fread($f,4);
			}
			elseif($type=='IEND') {
				break;
			}
			else {
				fread($f,$n+4);
			}
		}
		while($n);
		if($colspace=='Indexed' and empty($pal)) {
			$this->Error('Missing palette in '.$file);
		}
		fclose($f);
		return array('w'=>$w, 'h'=>$h, 'cs'=>$colspace, 'bpc'=>$bpc, 'f'=>'FlateDecode', 'parms'=>$parms, 'pal'=>$pal, 'trns'=>$trns, 'data'=>$data);
	}

	function toJPEG($file, $image) {
		if ($image) {
			// output
			$tempname = tempnam(K_PATH_CACHE,'jpg');
			imagejpeg($image, $tempname, 100);
			imagedestroy($image);
			$retvars = $this->outputjpg($tempname);
			// tidy up by removing temporary image
			unlink($tempname);
			return $retvars;
		} else {
			$this->Error('Can\'t open image file: '.$file);
		}
	}

	function outputjpg($filename) {
		$a=getimagesize($filename);

		if(!isset($a['channels']) or $a['channels']==3) {
			$colspace='DeviceRGB';
		}
		elseif($a['channels']==4) {
			$colspace='DeviceCMYK';
		}
		else {
			$colspace='DeviceGray';
		}
		$bpc=isset($a['bits']) ? $a['bits'] : 8;
		//Read whole file

		$f=fopen($filename,'rb');
		$data='';
		while(!feof($f)) {
			$data.=fread($f,4096);
		}
		fclose($f);

		return array('w'=>$a[0],'h'=>$a[1],'cs'=>$colspace,'bpc'=>$bpc,'f'=>'DCTDecode','data'=>$data);
	}

	function _freadint($f) {
		$a=unpack('Ni',fread($f,4));
		return $a['i'];
	}

	function _textstring($s) {
		if($this->isunicode) {
			//Convert string to UTF-16BE
			$s = $this->UTF8ToUTF16BE($s, true);
		}
		if ($this->encrypted) {
			$s = $this->_RC4($this->_objectkey($this->n), $s);
		}
		return '('. $this->_escape($s).')';
	}

	function _uristring($s) {
		if ($this->encrypted) {
			$s = $this->_RC4($this->_objectkey($this->n), $s);
		}
		return '('.$this->_escape($s).')';
	}

	function _escapetext($s) {
		if($this->isunicode) {
			//Convert string to UTF-16BE and reverse RTL language
			$s = $this->utf8StrRev($s, false, $this->tmprtl);
		}
		return $this->_escape($s);
	}

	function _escape($s) {
		// the chr(13) substitution fixes the Bugs item #1421290.
		return strtr($s, array(')' => '\\)', '(' => '\\(', '\\' => '\\\\', chr(13) => '\r'));
	}

	function _putstream($s) {
		if ($this->encrypted) {
			$s = $this->_RC4($this->_objectkey($this->n), $s);
		}
		$this->_out('stream');
		$this->_out($s);
		$this->_out('endstream');
	}

	function _out($s) {
		if($this->state==2) {
			$this->pages[$this->page] .= $s."\n";
		}
		else {
			$this->buffer .= $s."\n";
		}
	}

	function _puttruetypeunicode($font) {
		// Type0 Font
		// A composite font composed of other fonts, organized hierarchically
		$this->_newobj();
		$this->_out('<</Type /Font');
		$this->_out('/Subtype /Type0');
		$this->_out('/BaseFont /'.$font['name'].'');
		$this->_out('/Encoding /Identity-H'); //The horizontal identity mapping for 2-byte CIDs; may be used with CIDFonts using any Registry, Ordering, and Supplement values.
		$this->_out('/DescendantFonts ['.($this->n + 1).' 0 R]');
		$this->_out('/ToUnicode '.($this->n + 2).' 0 R');
		$this->_out('>>');
		$this->_out('endobj');

		// CIDFontType2
		// A CIDFont whose glyph descriptions are based on TrueType font technology
		$this->_newobj();
		$this->_out('<</Type /Font');
		$this->_out('/Subtype /CIDFontType2');
		$this->_out('/BaseFont /'.$font['name'].'');
		$this->_out('/CIDSystemInfo '.($this->n + 2).' 0 R');
		$this->_out('/FontDescriptor '.($this->n + 3).' 0 R');
		if (isset($font['desc']['MissingWidth'])){
			$this->_out('/DW '.$font['desc']['MissingWidth'].''); // The default width for glyphs in the CIDFont MissingWidth
		}
		$w = "";
		foreach ($font['cw'] as $cid => $width) {
			$w .= ''.$cid.' ['.$width.'] '; // define a specific width for each individual CID
		}
		$this->_out('/W ['.$w.']'); // A description of the widths for the glyphs in the CIDFont
		$this->_out('/CIDToGIDMap '.($this->n + 4).' 0 R');
		$this->_out('>>');
		$this->_out('endobj');

		// ToUnicode
		// is a stream object that contains the definition of the CMap
		// (PDF Reference 1.3 chap. 5.9)
		$this->_newobj();
		$this->_out('<</Length 345>>');
		$this->_out('stream');
		$this->_out('/CIDInit /ProcSet findresource begin');
		$this->_out('12 dict begin');
		$this->_out('begincmap');
		$this->_out('/CIDSystemInfo');
		$this->_out('<</Registry (Adobe)');
		$this->_out('/Ordering (UCS)');
		$this->_out('/Supplement 0');
		$this->_out('>> def');
		$this->_out('/CMapName /Adobe-Identity-UCS def');
		$this->_out('/CMapType 2 def');
		$this->_out('1 begincodespacerange');
		$this->_out('<0000> <FFFF>');
		$this->_out('endcodespacerange');
		$this->_out('1 beginbfrange');
		$this->_out('<0000> <FFFF> <0000>');
		$this->_out('endbfrange');
		$this->_out('endcmap');
		$this->_out('CMapName currentdict /CMap defineresource pop');
		$this->_out('end');
		$this->_out('end');
		$this->_out('endstream');
		$this->_out('endobj');

		// CIDSystemInfo dictionary
		// A dictionary containing entries that define the character collection of the CIDFont.
		$this->_newobj();
		$this->_out('<</Registry (Adobe)'); // A string identifying an issuer of character collections
		$this->_out('/Ordering (UCS)'); // A string that uniquely names a character collection issued by a specific registry
		$this->_out('/Supplement 0'); // The supplement number of the character collection.
		$this->_out('>>');
		$this->_out('endobj');

		// Font descriptor
		// A font descriptor describing the CIDFont default metrics other than its glyph widths
		$this->_newobj();
		$this->_out('<</Type /FontDescriptor');
		$this->_out('/FontName /'.$font['name']);
		foreach ($font['desc'] as $key => $value) {
			$this->_out('/'.$key.' '.$value);
		}
		if ($font['file']) {
			// A stream containing a TrueType font program
			$this->_out('/FontFile2 '.$this->FontFiles[$font['file']]['n'].' 0 R');
		}
		$this->_out('>>');
		$this->_out('endobj');

		// Embed CIDToGIDMap
		// A specification of the mapping from CIDs to glyph indices
		$this->_newobj();
		$ctgfile = $this->_getfontpath().strtolower($font['ctg']);
		if(!file_exists($ctgfile)) {
			$this->Error('Font file not found: '.$ctgfile);
		}
		$size = filesize($ctgfile);
		$this->_out('<</Length '.$size.'');
		if(substr($ctgfile, -2) == '.z') { // check file extension
			/* Decompresses data encoded using the public-domain
			zlib/deflate compression method, reproducing the
			original text or binary data */
			$this->_out('/Filter /FlateDecode');
		}
		$this->_out('>>');
		$this->_putstream(file_get_contents($ctgfile));
		$this->_out('endobj');
	}

	function UTF8StringToArray($str) {
		if(!$this->isunicode) {
			// split string into array of chars
			$strarr = str_split($str);
			// convert chars to equivalent code
			while(list($pos,$char)=each($strarr)) {
				$strarr[$pos] = ord($char);
			}
			return $strarr;
		}
		$unicode = array(); // array containing unicode values
		$bytes  = array(); // array containing single character byte sequences
		$numbytes  = 1; // number of octetc needed to represent the UTF-8 character

		$str .= ""; // force $str to be a string
		$length = strlen($str);

		for($i = 0; $i < $length; $i++) {
			$char = ord($str[$i]); // get one string character at time
			if(count($bytes) == 0) { // get starting octect
				if ($char <= 0x7F) {
					$unicode[] = $char; // use the character "as is" because is ASCII
					$numbytes = 1;
				} elseif (($char >> 0x05) == 0x06) { // 2 bytes character (0x06 = 110 BIN)
					$bytes[] = ($char - 0xC0) << 0x06;
					$numbytes = 2;
				} elseif (($char >> 0x04) == 0x0E) { // 3 bytes character (0x0E = 1110 BIN)
					$bytes[] = ($char - 0xE0) << 0x0C;
					$numbytes = 3;
				} elseif (($char >> 0x03) == 0x1E) { // 4 bytes character (0x1E = 11110 BIN)
					$bytes[] = ($char - 0xF0) << 0x12;
					$numbytes = 4;
				} else {
					// use replacement character for other invalid sequences
					$unicode[] = 0xFFFD;
					$bytes = array();
					$numbytes = 1;
				}
			} elseif (($char >> 0x06) == 0x02) { // bytes 2, 3 and 4 must start with 0x02 = 10 BIN
				$bytes[] = $char - 0x80;
				if (count($bytes) == $numbytes) {
					// compose UTF-8 bytes to a single unicode value
					$char = $bytes[0];
					for($j = 1; $j < $numbytes; $j++) {
						$char += ($bytes[$j] << (($numbytes - $j - 1) * 0x06));
					}
					if ((($char >= 0xD800) AND ($char <= 0xDFFF)) OR ($char >= 0x10FFFF)) {
						/* The definition of UTF-8 prohibits encoding character numbers between
						U+D800 and U+DFFF, which are reserved for use with the UTF-16
						encoding form (as surrogate pairs) and do not directly represent
						characters. */
						$unicode[] = 0xFFFD; // use replacement character
					}
					else {
						$unicode[] = $char; // add char to array
					}
					// reset data for next char
					$bytes = array();
					$numbytes = 1;
				}
			} else {
				// use replacement character for other invalid sequences
				$unicode[] = 0xFFFD;
				$bytes = array();
				$numbytes = 1;
			}
		}
		return $unicode;
	}

	function UTF8ToUTF16BE($str, $setbom=true) {
		if(!$this->isunicode) {
			return $str; // string is not in unicode
		}
		$unicode = $this->UTF8StringToArray($str); // array containing UTF-8 unicode values
		return $this->arrUTF8ToUTF16BE($unicode, $setbom);
	}

	function arrUTF8ToUTF16BE($unicode, $setbom=true) {
		$outstr = ""; // string to be returned
		if ($setbom) {
			$outstr .= "\xFE\xFF"; // Byte Order Mark (BOM)
		}
		foreach($unicode as $char) {
			if($char == 0xFFFD) {
				$outstr .= "\xFF\xFD"; // replacement character
			} elseif ($char < 0x10000) {
				$outstr .= chr($char >> 0x08);
				$outstr .= chr($char & 0xFF);
			} else {
				$char -= 0x10000;
				$w1 = 0xD800 | ($char >> 0x10);
				$w2 = 0xDC00 | ($char & 0x3FF);
				$outstr .= chr($w1 >> 0x08);
				$outstr .= chr($w1 & 0xFF);
				$outstr .= chr($w2 >> 0x08);
				$outstr .= chr($w2 & 0xFF);
			}
		}
		return $outstr;
	}


	function setHeaderFont($font) {
		$this->header_font = $font;
	}

	function setFooterFont($font) {
		$this->footer_font = $font;
	}

	function setLanguageArray($language) {
		$this->l = $language;
		$this->rtl = $this->l['a_meta_dir']=='rtl' ? true : false;
	}

	function setBarcode($bc="") {
		$this->barcode = $bc;
	}

	function writeBarcode($x, $y, $w, $h, $type, $style, $font, $xres, $code) {
		require_once(dirname(__FILE__)."/barcode/barcode.php");
		require_once(dirname(__FILE__)."/barcode/i25object.php");
		require_once(dirname(__FILE__)."/barcode/c39object.php");
		require_once(dirname(__FILE__)."/barcode/c128aobject.php");
		require_once(dirname(__FILE__)."/barcode/c128bobject.php");
		require_once(dirname(__FILE__)."/barcode/c128cobject.php");

		if (empty($code)) {
			return;
		}

		if (empty($style)) {
			$style  = BCS_ALIGN_LEFT;
			$style |= BCS_IMAGE_PNG;
			$style |= BCS_TRANSPARENT;
			//$style |= BCS_BORDER;
			//$style |= BCS_DRAW_TEXT;
			//$style |= BCS_STRETCH_TEXT;
			//$style |= BCS_REVERSE_COLOR;
		}
		if (empty($font)) {$font = BCD_DEFAULT_FONT;}
		if (empty($xres)) {$xres = BCD_DEFAULT_XRES;}

		$scale_factor = 1.5 * $xres * $this->k;
		$bc_w = round($w * $scale_factor); //width in points
		$bc_h = round($h * $scale_factor); //height in points

		switch (strtoupper($type)) {
			case "I25": {
				$obj = new I25Object($bc_w, $bc_h, $style, $code);
				break;
			}
			case "C128A": {
				$obj = new C128AObject($bc_w, $bc_h, $style, $code);
				break;
			}
			default:
			case "C128B": {
				$obj = new C128BObject($bc_w, $bc_h, $style, $code);
				break;
			}
			case "C128C": {
				$obj = new C128CObject($bc_w, $bc_h, $style, $code);
				break;
			}
			case "C39": {
				$obj = new C39Object($bc_w, $bc_h, $style, $code);
				break;
			}
		}

		$obj->SetFont($font);
		$obj->DrawObject($xres);

		//use a temporary file....
		$tmpName = tempnam(K_PATH_CACHE,'img');
		imagepng($obj->getImage(), $tmpName);
		$this->Image($tmpName, $x, $y, $w, $h, 'png');
		$obj->DestroyObject();
		unset($obj);
		unlink($tmpName);
	}

	function getPDFData() {
		if($this->state < 3) {
			$this->Close();
		}
		return $this->buffer;
	}

	function writeHTML($html, $ln=true, $fill=0, $reseth=false, $cell=false) {

		// store some variables
		$html=strip_tags($html,"<h1><h2><h3><h4><h5><h6><b><u><i><a><img><p><br><br/><strong><em><font><blockquote><li><ul><ol><hr><td><th><tr><table><sup><sub><small><span><div>"); //remove all unsupported tags
		//replace carriage returns, newlines and tabs
		$repTable = array("\t" => " ", "\n" => " ", "\r" => " ", "\0" => " ", "\x0B" => " ");
		$html = strtr($html, $repTable);
		$pattern = '/(<[^>]+>)/Uu';
		$a = preg_split($pattern, $html, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY); //explodes the string

		if ((empty($this->lasth))OR ($reseth)) {
			//set row height
			$this->lasth = $this->FontSize * K_CELL_HEIGHT_RATIO;
		}

		foreach($a as $key=>$element) {
			if (!preg_match($pattern, $element)) {
				//Text
				if($this->HREF) {
					$this->addHtmlLink($this->HREF, $element, $fill);
				} elseif($this->tdbegin) {
					if((strlen(trim($element)) > 0) AND ($element != "&nbsp;")) {
						$this->Cell($this->tdwidth, $this->tdheight, $this->unhtmlentities($element), $this->tableborder, '', $this->tdalign, $this->tdbgcolor);
					} elseif($element == "&nbsp;") {
						$this->Cell($this->tdwidth, $this->tdheight, '', $this->tableborder, '', $this->tdalign, $this->tdbgcolor);
					}
				} else {

					$ctmpmargin = $this->cMargin;
					if(!$cell) {
						$this->cMargin = 0;
					}

					$this->Write($this->lasth, stripslashes($this->unhtmlentities($element)), '', $fill, '', false, 0);

					$this->cMargin = $ctmpmargin;
				}
			} else {
				$element = substr($element, 1, -1);
				//Tag
				if($element[0]=='/') {
					$this->closedHTMLTagHandler(strtolower(substr($element, 1)));
				}
				else {
					//Extract attributes
					// get tag name
					preg_match('/([a-zA-Z0-9]*)/', $element, $tag);
					$tag = strtolower($tag[0]);
					// get attributes
					preg_match_all('/([^=\s]*)=["\']?([^"\']*)["\']?/', $element, $attr_array, PREG_PATTERN_ORDER);
					$attr = array(); // reset attribute array
					while(list($id,$name)=each($attr_array[1])) {
						$attr[strtolower($name)] = $attr_array[2][$id];
					}
					$this->openHTMLTagHandler($tag, $attr, $fill);
				}
			}
		}
		if ($ln) {
			$this->Ln($this->lasth);
		}
	}

	function writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true) {

		if ((empty($this->lasth))OR ($reseth)) {
			//set row height
			$this->lasth = $this->FontSize * K_CELL_HEIGHT_RATIO;
		}

		// get current page number
		$startpage = $this->page;

		if (!empty($y)) {
			$this->SetY($y);
		} else {
			$y = $this->GetY();
		}
		if (!empty($x)) {
			$this->SetX($x);
		} else {
			$x = $this->GetX();
		}

		if(empty($w)) {
			if ($this->rtl) {
				$w = $this->x - $this->lMargin;
			} else {
				$w = $this->w - $this->rMargin - $this->x;
			}
		}

		// store original margin values
		$lMargin = $this->lMargin;
		$rMargin = $this->rMargin;

		// set new margin values
		if ($this->rtl) {
			$this->SetLeftMargin($this->x - $w);
			$this->SetRightMargin($this->w - $this->x);
		} else {
			$this->SetLeftMargin($this->x);
			$this->SetRightMargin($this->w - $this->x - $w);
		}

		// calculate remaining vertical space on first page ($startpage)
		$restspace = $this->getPageHeight() - $this->GetY() - $this->getBreakMargin();

		// Write HTML text
		$this->writeHTML($html, true, $fill, $reseth, true);

		// Get end-of-text Y position
		$currentY = $this->GetY();
		// get latest page number
		$endpage = $this->page;

		if (!empty($border)) {
			// check if a new page has been created
			if ($endpage > $startpage) {
				// design borders around HTML cells.
				for ($page=$startpage; $page<=$endpage; $page++) {
					$this->page = $page;
					if ($page==$startpage) {
						$this->SetY($this->getPageHeight() - $restspace - $this->getBreakMargin());
						$h = $restspace - 1;
					} elseif ($page==$endpage) {
						$this->SetY($this->tMargin); // put cursor at the beginning of text
						$h = $currentY - $this->tMargin;
					} else {
						$this->SetY($this->tMargin); // put cursor at the beginning of text
						$h = $this->getPageHeight() - $this->tMargin - $this->getBreakMargin();
					}
					$this->SetX($x);
					$this->Cell($w, $h, "", $border, 1, '', 0);
				}
			} else {
				$h = max($h, ($currentY - $y));
				$this->SetY($y); // put cursor at the beginning of text
				$this->SetX($x);
				// design a cell around the text
				$this->Cell($w, $h, "", $border, 1, '', 0);
			}
		}

		// restore original margin values
		$this->SetLeftMargin($lMargin);
		$this->SetRightMargin($rMargin);

		if($ln>0) {
			//Go to the beginning of the next line
			$this->SetY($currentY);
			if($ln == 2) {
				$this->SetX($x + $w);
			}
		} else {
			// go left or right by case
			$this->page = $startpage;
			$this->y = $y;
			$this->SetX($x + $w);
		}
	}

	function openHTMLTagHandler($tag, $attr, $fill=0) {
		// check for text direction attribute
		if (isset($attr['dir'])) {
				$this->tmprtl = $attr['dir']=='rtl' ? 'R' : 'L';
		} else {
			$this->tmprtl = false;
		}
		//Opening tag
		switch($tag) {
			case 'table': {
				if ((isset($attr['border'])) AND ($attr['border'] != '')) {
					$this->tableborder = $attr['border'];
				}
				else {
					$this->tableborder = 0;
				}
				break;
			}
			case 'tr': {
				break;
			}
			case 'td':
			case 'th': {
				if ((isset($attr['width'])) AND ($attr['width'] != '')) {
					$this->tdwidth = ($attr['width']/4);
				}
				else {
					$this->tdwidth = (($this->w - $this->lMargin - $this->rMargin) / $this->default_table_columns);
				}
				if ((isset($attr['height'])) AND ($attr['height'] != '')) {
					$this->tdheight=($attr['height'] / $this->k);
				}
				else {
					$this->tdheight = $this->lasth;
				}
				if ((isset($attr['align'])) AND ($attr['align'] != '')) {
					switch ($attr['align']) {
						case 'center': {
							$this->tdalign = "C";
							break;
						}
						case 'right': {
							$this->tdalign = "R";
							break;
						}
						default:
						case 'left': {
							$this->tdalign = "L";
							break;
						}
					}
				} else {
					if($this->rtl) {
						$this->tdalign = "R";
					} else {
						$this->tdalign = "L";
					}
				}
				if ((isset($attr['bgcolor'])) AND ($attr['bgcolor'] != '')) {
					$coul = $this->convertColorHexToDec($attr['bgcolor']);
					$this->SetFillColor($coul['R'], $coul['G'], $coul['B']);
					$this->tdbgcolor=true;
				}
				$this->tdbegin=true;
				break;
			}
			case 'hr': {
				$this->Ln();
				if ((isset($attr['width'])) AND ($attr['width'] != '')) {
					$hrWidth = $attr['width'];
				}
				else {
					$hrWidth = $this->w - $this->lMargin - $this->rMargin;
				}
				$x = $this->GetX();
				$y = $this->GetY();
				$this->GetLineWidth();
				$prevlinewidth = $this->SetLineWidth(0.2);
				$this->Line($x, $y, $x + $hrWidth, $y);
				$this->SetLineWidth($prevlinewidth);
				$this->Ln();
				break;
			}
			case 'strong': {
				$this->setStyle('b', true);
				break;
			}
			case 'em': {
				$this->setStyle('i', true);
				break;
			}
			case 'b':
			case 'i':
			case 'u': {
				$this->setStyle($tag, true);
				break;
			}
			case 'a': {
				$this->HREF = $attr['href'];
				break;
			}
			case 'img': {
				if(isset($attr['src'])) {
					// replace relative path with real server path
					if ($attr['src'][0] == '/') {
						$attr['src'] = $_SERVER['DOCUMENT_ROOT'].$attr['src'];
					}
					$attr['src'] = str_replace(K_PATH_URL, K_PATH_MAIN, $attr['src']);
					if(!isset($attr['width'])) {
						$attr['width'] = 0;
					}
					if(!isset($attr['height'])) {
						$attr['height'] = 0;
					}
					if(!isset($attr['align'])) {
						$align = 'N';
					} else {
						switch($attr['align']) {
							case 'top':{
								$align = 'T';
								break;
							}
							case 'middle':{
								$align = 'M';
								break;
							}
							case 'bottom':{
								$align = 'B';
								break;
							}
							default:{
								$align = 'N';
								break;
							}
						}
					}
					$this->Image($attr['src'], $this->GetX(),$this->GetY(), $this->pixelsToMillimeters($attr['width']), $this->pixelsToMillimeters($attr['height']), '', '', $align);

				}
				break;
			}
			case 'ul': {
				$this->listordered = false;
				$this->listcount = 0;
				break;
			}
			case 'ol': {
				$this->listordered = true;
				$this->listcount = 0;
				break;
			}
			case 'li': {
				$this->Ln();
				if ($this->listordered) {
					if (isset($attr['value'])) {
						$this->listcount = intval($attr['value']);
					}
					$this->lispacer = "    ".(++$this->listcount).". ";
				} else {
					//unordered list simbol
					$this->lispacer = "    -  ";
				}
				$rtldir = $this->tmprtl;
				$this->tmprtl = false;
				$this->Write($this->lasth, $this->lispacer, '', $fill, '', false, 0);
				$this->tmprtl = $rtldir;
				break;
			}
			case 'blockquote':
			case 'br': {
				$this->Ln();
				if(strlen($this->lispacer) > 0) {
					if ($this->rtl) {
						$this->x -= $this->GetStringWidth($this->lispacer);
					} else {
						$this->x += $this->GetStringWidth($this->lispacer);
					}
				}
				break;
			}
			case 'p': {
				$this->Ln();
				$this->Ln();
				break;
			}
			case 'sup': {
				$currentFontSize = $this->FontSize;
				$this->tempfontsize = $this->FontSizePt;
				$this->SetFontSize($this->FontSizePt * K_SMALL_RATIO);
				$this->SetXY($this->GetX(), $this->GetY() - (($currentFontSize - $this->FontSize)*(K_SMALL_RATIO)));
				break;
			}
			case 'sub': {
				$currentFontSize = $this->FontSize;
				$this->tempfontsize = $this->FontSizePt;
				$this->SetFontSize($this->FontSizePt * K_SMALL_RATIO);
				$this->SetXY($this->GetX(), $this->GetY() + (($currentFontSize - $this->FontSize)*(K_SMALL_RATIO)));
				break;
			}
			case 'small': {
				$currentFontSize = $this->FontSize;
				$this->tempfontsize = $this->FontSizePt;
				$this->SetFontSize($this->FontSizePt * K_SMALL_RATIO);
				$this->SetXY($this->GetX(), $this->GetY() + (($currentFontSize - $this->FontSize)/3));
				break;
			}
			case 'font': {
				if (isset($attr['color']) AND $attr['color']!='') {
					$coul = $this->convertColorHexToDec($attr['color']);
					$this->SetTextColor($coul['R'],$coul['G'],$coul['B']);
					$this->issetcolor=true;
				}
				if (isset($attr['face']) and in_array(strtolower($attr['face']), $this->fontlist)) {
					$this->SetFont(strtolower($attr['face']));
					$this->issetfont=true;
				}
				if (isset($attr['size'])) {
					$headsize = intval($attr['size']);
				} else {
					$headsize = 0;
				}
				$currentFontSize = $this->FontSize;
				$this->tempfontsize = $this->FontSizePt;
				$this->SetFontSize($this->FontSizePt + $headsize);
				$this->lasth = $this->FontSize * K_CELL_HEIGHT_RATIO;
				break;
			}
			case 'h1':
			case 'h2':
			case 'h3':
			case 'h4':
			case 'h5':
			case 'h6': {
				$headsize = (4 - substr($tag, 1)) * 2;
				$currentFontSize = $this->FontSize;
				$this->tempfontsize = $this->FontSizePt;
				$this->SetFontSize($this->FontSizePt + $headsize);
				$this->setStyle('b', true);
				$this->lasth = $this->FontSize * K_CELL_HEIGHT_RATIO;
				break;
			}
		}
	}

	function closedHTMLTagHandler($tag) {
		//Closing tag
		switch($tag) {
			case 'td':
			case 'th': {
				$this->tdbegin = false;
				$this->tdwidth = 0;
				$this->tdheight = 0;
				if($this->rtl) {
					$this->tdalign = "R";
				} else {
					$this->tdalign = "L";
				}
				$this->tdbgcolor = false;
				$this->SetFillColor($this->prevFillColor[0], $this->prevFillColor[1], $this->prevFillColor[2]);
				break;
			}
			case 'tr': {
				$this->Ln();
				break;
			}
			case 'table': {
				$this->tableborder=0;
				break;
			}
			case 'strong': {
				$this->setStyle('b', false);
				break;
			}
			case 'em': {
				$this->setStyle('i', false);
				break;
			}
			case 'b':
			case 'i':
			case 'u': {
				$this->setStyle($tag, false);
				break;
			}
			case 'a': {
				$this->HREF = '';
				break;
			}
			case 'sup': {
				$currentFontSize = $this->FontSize;
				$this->SetFontSize($this->tempfontsize);
				$this->tempfontsize = $this->FontSizePt;
				$this->SetXY($this->GetX(), $this->GetY() - (($currentFontSize - $this->FontSize)*(K_SMALL_RATIO)));
				break;
			}
			case 'sub': {
				$currentFontSize = $this->FontSize;
				$this->SetFontSize($this->tempfontsize);
				$this->tempfontsize = $this->FontSizePt;
				$this->SetXY($this->GetX(), $this->GetY() + (($currentFontSize - $this->FontSize)*(K_SMALL_RATIO)));
				break;
			}
			case 'small': {
				$currentFontSize = $this->FontSize;
				$this->SetFontSize($this->tempfontsize);
				$this->tempfontsize = $this->FontSizePt;
				$this->SetXY($this->GetX(), $this->GetY() - (($this->FontSize - $currentFontSize)/3));
				break;
			}
			case 'font': {
				if ($this->issetcolor == true) {
					$this->SetTextColor($this->prevTextColor[0], $this->prevTextColor[1], $this->prevTextColor[2]);
				}
				if ($this->issetfont) {
					$this->FontFamily = $this->prevFontFamily;
					$this->FontStyle = $this->prevFontStyle;
					$this->SetFont($this->FontFamily);
					$this->issetfont = false;
				}
				$currentFontSize = $this->FontSize;
				$this->SetFontSize($this->tempfontsize);
				$this->tempfontsize = $this->FontSizePt;
				//$this->TextColor = $this->prevTextColor;
				$this->lasth = $this->FontSize * K_CELL_HEIGHT_RATIO;
				break;
			}
			case 'p': {
				$this->Ln();
				$this->Ln();
				break;
			}
			case 'ul':
			case 'ol': {
				$this->Ln();
				$this->Ln();
				break;
			}
			case 'li': {
				$this->lispacer = "";
				break;
			}
			case 'h1':
			case 'h2':
			case 'h3':
			case 'h4':
			case 'h5':
			case 'h6': {
				$currentFontSize = $this->FontSize;
				$this->SetFontSize($this->tempfontsize);
				$this->tempfontsize = $this->FontSizePt;
				$this->setStyle('b', false);
				$this->Ln();
				$this->lasth = $this->FontSize * K_CELL_HEIGHT_RATIO;
				break;
			}
			default : {
				break;
			}
		}
		$this->tmprtl = false;
	}

	function setStyle($tag, $enable) {
		//Modify style and select corresponding font
		$this->$tag += ($enable ? 1 : -1);
		$style='';
		foreach(array('b', 'i', 'u') as $s) {
			if($this->$s > 0) {
				$style .= $s;
			}
		}
		$this->SetFont('', $style);
	}

	function addHtmlLink($url, $name, $fill=0) {
		//Put a hyperlink
		$this->SetTextColor(0, 0, 255);
		$this->setStyle('u', true);
		$this->Write($this->lasth, $name, $url, $fill, '', false, 0);
		$this->setStyle('u', false);
		$this->SetTextColor(0);
	}

	function convertColorHexToDec($color = "#000000"){
		$tbl_color = array();
		$tbl_color['R'] = hexdec(substr($color, 1, 2));
		$tbl_color['G'] = hexdec(substr($color, 3, 2));
		$tbl_color['B'] = hexdec(substr($color, 5, 2));
		return $tbl_color;
	}

	function pixelsToMillimeters($px){
		return $px * 25.4 / 72;
	}
	function unhtmlentities($text_to_convert) {
		if (!$this->isunicode) {
			return html_entity_decode($text_to_convert);
		}
		require_once(dirname(__FILE__).'/html_entity_decode_php4.php');
		return html_entity_decode_php4($text_to_convert);
	}

	function _objectkey($n) {
		return substr($this->_md5_16($this->encryption_key.pack('VXxx',$n)),0,10);
	}
	function _putencryption() {
		$this->_out('/Filter /Standard');
		$this->_out('/V 1');
		$this->_out('/R 2');
		$this->_out('/O ('.$this->_escape($this->Ovalue).')');
		$this->_out('/U ('.$this->_escape($this->Uvalue).')');
		$this->_out('/P '.$this->Pvalue);
	}

	function _RC4($key, $text) {
		if ($this->last_rc4_key != $key) {
			$k = str_repeat($key, 256/strlen($key)+1);
			$rc4 = range(0,255);
			$j = 0;
			for ($i=0; $i<256; $i++) {
				$t = $rc4[$i];
				$j = ($j + $t + ord($k[$i])) % 256;
				$rc4[$i] = $rc4[$j];
				$rc4[$j] = $t;
			}
			$this->last_rc4_key = $key;
			$this->last_rc4_key_c = $rc4;
		} else {
			$rc4 = $this->last_rc4_key_c;
		}
		$len = strlen($text);
		$a = 0;
		$b = 0;
		$out = '';
		for ($i=0; $i<$len; $i++) {
			$a = ($a+1)%256;
			$t= $rc4[$a];
			$b = ($b+$t)%256;
			$rc4[$a] = $rc4[$b];
			$rc4[$b] = $t;
			$k = $rc4[($rc4[$a]+$rc4[$b])%256];
			$out.=chr(ord($text[$i]) ^ $k);
		}
		return $out;
	}

	function _md5_16($str) {
		return pack('H*',md5($str));
	}

	function _Ovalue($user_pass, $owner_pass) {
		$tmp = $this->_md5_16($owner_pass);
		$owner_RC4_key = substr($tmp,0,5);
		return $this->_RC4($owner_RC4_key, $user_pass);
	}

	function _Uvalue() {
		return $this->_RC4($this->encryption_key, $this->padding);
	}

	function _generateencryptionkey($user_pass, $owner_pass, $protection) {
		// Pad passwords
		$user_pass = substr($user_pass.$this->padding,0,32);
		$owner_pass = substr($owner_pass.$this->padding,0,32);
		// Compute O value
		$this->Ovalue = $this->_Ovalue($user_pass,$owner_pass);
		// Compute encyption key
		$tmp = $this->_md5_16($user_pass.$this->Ovalue.chr($protection)."\xFF\xFF\xFF");
		$this->encryption_key = substr($tmp,0,5);
		// Compute U value
		$this->Uvalue = $this->_Uvalue();
		// Compute P value
		$this->Pvalue = -(($protection^255)+1);
	}

	function SetProtection($permissions=array(),$user_pass='',$owner_pass=null) {
		$options = array('print' => 4, 'modify' => 8, 'copy' => 16, 'annot-forms' => 32);
		$protection = 192;
		foreach($permissions as $permission) {
			if (!isset($options[$permission])) {
				$this->Error('Incorrect permission: '.$permission);
			}
			$protection += $options[$permission];
		}
		if ($owner_pass === null) {
			$owner_pass = uniqid(rand());
		}
		$this->encrypted = true;
		$this->_generateencryptionkey($user_pass, $owner_pass, $protection);
	}

	function StartTransform() {
		$this->_out('q');
	}

	function StopTransform() {
		$this->_out('Q');
	}
	function ScaleX($s_x, $x='', $y=''){
		$this->Scale($s_x, 100, $x, $y);
	}

	function ScaleY($s_y, $x='', $y=''){
		$this->Scale(100, $s_y, $x, $y);
	}

	function ScaleXY($s, $x='', $y=''){
		$this->Scale($s, $s, $x, $y);
	}

	function Scale($s_x, $s_y, $x='', $y=''){
		if($x === '') {
			$x=$this->x;
		}
		if($y === '') {
			$y=$this->y;
		}
		if($this->rtl) {
			$x = $this->w - $x;
		}
		if($s_x == 0 OR $s_y == 0)
			$this->Error('Please use values unequal to zero for Scaling');
		$y=($this->h-$y)*$this->k;
		$x*=$this->k;
		//calculate elements of transformation matrix
		$s_x/=100;
		$s_y/=100;
		$tm[0]=$s_x;
		$tm[1]=0;
		$tm[2]=0;
		$tm[3]=$s_y;
		$tm[4]=$x*(1-$s_x);
		$tm[5]=$y*(1-$s_y);
		//scale the coordinate system
		$this->Transform($tm);
	}

	function MirrorH($x=''){
		$this->Scale(-100, 100, $x);
	}

	function MirrorV($y=''){
		$this->Scale(100, -100, '', $y);
	}

	function MirrorP($x='',$y=''){
		$this->Scale(-100, -100, $x, $y);
	}

	function MirrorL($angle=0, $x='',$y=''){
		$this->Scale(-100, 100, $x, $y);
		$this->Rotate(-2*($angle-90),$x,$y);
	}

	function TranslateX($t_x){
		$this->Translate($t_x, 0);
	}

	function TranslateY($t_y){
		$this->Translate(0, $t_y, $x, $y);
	}

	function Translate($t_x, $t_y){
		if($this->rtl) {
			$t_x = -$t_x;
		}
		//calculate elements of transformation matrix
		$tm[0]=1;
		$tm[1]=0;
		$tm[2]=0;
		$tm[3]=1;
		$tm[4]=$t_x*$this->k;
		$tm[5]=-$t_y*$this->k;
		//translate the coordinate system
		$this->Transform($tm);
	}

	function Rotate($angle, $x='', $y=''){
		if($x === '') {
			$x=$this->x;
		}
		if($y === '') {
			$y=$this->y;
		}
		if($this->rtl) {
			$x = $this->w - $x;
			$angle = -$angle;
		}
		$y=($this->h-$y)*$this->k;
		$x*=$this->k;
		//calculate elements of transformation matrix
		$tm[0]=cos(deg2rad($angle));
		$tm[1]=sin(deg2rad($angle));
		$tm[2]=-$tm[1];
		$tm[3]=$tm[0];
		$tm[4]=$x+$tm[1]*$y-$tm[0]*$x;
		$tm[5]=$y-$tm[0]*$y-$tm[1]*$x;
		//rotate the coordinate system around ($x,$y)
		$this->Transform($tm);
	}

	function SkewX($angle_x, $x='', $y=''){
		$this->Skew($angle_x, 0, $x, $y);
	}

	function SkewY($angle_y, $x='', $y=''){
		$this->Skew(0, $angle_y, $x, $y);
	}

	function Skew($angle_x, $angle_y, $x='', $y=''){
		if($x === '') {
			$x=$this->x;
		}
		if($y === '') {
			$y=$this->y;
		}
		if($this->rtl) {
			$x = $this->w - $x;
			$angle_x = -$angle_x;
		}
		if($angle_x <= -90 OR $angle_x >= 90 OR $angle_y <= -90 OR $angle_y >= 90)
			$this->Error('Please use values between -90� and 90� for skewing');
		$x*=$this->k;
		$y=($this->h-$y)*$this->k;
		//calculate elements of transformation matrix
		$tm[0]=1;
		$tm[1]=tan(deg2rad($angle_y));
		$tm[2]=tan(deg2rad($angle_x));
		$tm[3]=1;
		$tm[4]=-$tm[2]*$y;
		$tm[5]=-$tm[1]*$x;
		//skew the coordinate system
		$this->Transform($tm);
	}

	function Transform($tm){
		$this->_out(sprintf('%.3f %.3f %.3f %.3f %.3f %.3f cm', $tm[0],$tm[1],$tm[2],$tm[3],$tm[4],$tm[5]));
	}
	function SetLineWidth($width) {
		//Set line width
		$this->LineWidth=$width;
		if($this->page>0) {
			$this->_out(sprintf('%.2f w',$width*$this->k));
		}
	}

	function GetLineWidth() {
		return $this->LineWidth;
	}

	function SetLineStyle($style) {
		extract($style);
		if (isset($width)) {
			$width_prev = $this->LineWidth;
			$this->SetLineWidth($width);
			$this->LineWidth = $width_prev;
		}
		if (isset($cap)) {
			$ca = array("butt" => 0, "round"=> 1, "square" => 2);
			if (isset($ca[$cap])) {
				$this->_out($ca[$cap] . " J");
			}
		}
		if (isset($join)) {
			$ja = array("miter" => 0, "round" => 1, "bevel" => 2);
			if (isset($ja[$join])) {
				$this->_out($ja[$join] . " j");
			}
		}
		if (isset($dash)) {
			$dash_string = "";
			if ($dash) {
				if (ereg("^.+,", $dash)) {
					$tab = explode(",", $dash);
				} else {
					$tab = array($dash);
				}
				$dash_string = "";
				foreach ($tab as $i => $v) {
					if ($i) {
						$dash_string .= " ";
					}
					$dash_string .= sprintf("%.2f", $v);
				}
			}
			if (!isset($phase) OR !$dash) {
				$phase = 0;
			}
			$this->_out(sprintf("[%s] %.2f d", $dash_string, $phase));
		}
		if (isset($color)) {
			list($r, $g, $b) = $color;
			$this->SetDrawColor($r, $g, $b);
		}
	}

	function _outPoint($x, $y) {
		if($this->rtl) {
			$x = $this->w - $x;
		}
		$this->_out(sprintf("%.2f %.2f m", $x * $this->k, ($this->h - $y) * $this->k));
	}

	function _outLine($x, $y) {
		if($this->rtl) {
			$x = $this->w - $x;
		}
		$this->_out(sprintf("%.2f %.2f l", $x * $this->k, ($this->h - $y) * $this->k));
	}

	function _outRect($x, $y, $w, $h, $op) {
		if($this->rtl) {
			$x = $this->w - $x - $w;
		}
		$this->_out(sprintf('%.2f %.2f %.2f %.2f re %s',$x*$this->k,($this->h-$y)*$this->k,$w*$this->k,-$h*$this->k,$op));
	}

	function _outCurve($x1, $y1, $x2, $y2, $x3, $y3) {
		if($this->rtl) {
			$x1 = $this->w - $x1;
			$x2 = $this->w - $x2;
			$x3 = $this->w - $x3;
		}
		$this->_out(sprintf("%.2f %.2f %.2f %.2f %.2f %.2f c", $x1 * $this->k, ($this->h - $y1) * $this->k, $x2 * $this->k, ($this->h - $y2) * $this->k, $x3 * $this->k, ($this->h - $y3) * $this->k));
	}

	function Line($x1, $y1, $x2, $y2, $style = array()) {
		if ($style) {
			$this->SetLineStyle($style);
		}
		$this->_outPoint($x1, $y1);
		$this->_outLine($x2, $y2);
		$this->_out(" S");
	}

	function Rect($x, $y, $w, $h, $style='', $border_style = array(), $fill_color = array()) {
		if (!(false === strpos($style, "F")) AND $fill_color) {
			list($r, $g, $b) = $fill_color;
			$this->SetFillColor($r, $g, $b);
		}
		switch ($style) {
			case "F": {
				$op='f';
				$border_style = array();
				$this->_outRect($x, $y, $w, $h, $op);
				break;
			}
			case "DF":
			case "FD": {
				if (!$border_style OR isset($border_style["all"])) {
					$op='B';
					if (isset($border_style["all"])) {
						$this->SetLineStyle($border_style["all"]);
						$border_style = array();
					}
				} else {
					$op='f';
				}
				$this->_outRect($x, $y, $w, $h, $op);
				break;
			}
			default: {
				$op='S';
				if (!$border_style OR isset($border_style["all"])) {
					if (isset($border_style["all"]) && $border_style["all"]) {
						$this->SetLineStyle($border_style["all"]);
						$border_style = array();
					}
					$this->_outRect($x, $y, $w, $h, $op);
				}
				break;
			}
		}
		if ($border_style) {
			$border_style2 = array();
			foreach ($border_style as $line => $value) {
				$lenght = strlen($line);
				for ($i = 0; $i < $lenght; $i++) {
					$border_style2[$line[$i]] = $value;
				}
			}
			$border_style = $border_style2;
			if (isset($border_style["L"]) && $border_style["L"]) {
				$this->Line($x, $y, $x, $y + $h, $border_style["L"]);
			}
			if (isset($border_style["T"]) && $border_style["T"]) {
				$this->Line($x, $y, $x + $w, $y, $border_style["T"]);
			}
			if (isset($border_style["R"]) && $border_style["R"]) {
				$this->Line($x + $w, $y, $x + $w, $y + $h, $border_style["R"]);
			}
			if (isset($border_style["B"]) && $border_style["B"]) {
				$this->Line($x, $y + $h, $x + $w, $y + $h, $border_style["B"]);
			}
		}
	}

	function Curve($x0, $y0, $x1, $y1, $x2, $y2, $x3, $y3, $style = "", $line_style = array(), $fill_color = array()) {
		if (!(false === strpos($style, "F")) AND $fill_color) {
			list($r, $g, $b) = $fill_color;
			$this->SetFillColor($r, $g, $b);
		}
		switch ($style) {
			case "F": {
				$op = "f";
				$line_style = array();
				break;
			}
			case "FD":
			case "DF": {
				$op = "B";
				break;
			}
			default: {
				$op = "S";
				break;
			}
		}
		if ($line_style) {
			$this->SetLineStyle($line_style);
		}
		$this->_outPoint($x0, $y0);
		$this->_outCurve($x1, $y1, $x2, $y2, $x3, $y3);
		$this->_out($op);
	}

	function Ellipse($x0, $y0, $rx, $ry = 0, $angle = 0, $astart = 0, $afinish = 360, $style = "", $line_style = array(), $fill_color = array(), $nc = 8) {
		if ($angle) {
			$this->StartTransform();
			$this->Rotate($angle, $x0, $y0);
			$this->Ellipse($x0, $y0, $rx, $ry, 0, $astart, $afinish, $style, $line_style, $fill_color, $nc);
			$this->StopTransform();
			return;
		}
		if ($rx) {
			if (!(false === strpos($style, "F")) AND $fill_color) {
				list($r, $g, $b) = $fill_color;
				$this->SetFillColor($r, $g, $b);
			}
			switch ($style) {
				case "F": {
					$op = "f";
					$line_style = array();
					break;
				}
				case "FD":
				case "DF": {
					$op = "B";
					break;
				}
				case "C": {
					$op = "s"; // Small "s" signifies closing the path as well
					break;
				}
				default: {
					$op = "S";
					break;
				}
			}
			if ($line_style) {
				$this->SetLineStyle($line_style);
			}
			if (!$ry) {
				$ry = $rx;
			}
			$rx *= $this->k;
			$ry *= $this->k;
			if ($nc < 2){
				$nc = 2;
			}
			$astart = deg2rad((float) $astart);
			$afinish = deg2rad((float) $afinish);
			$total_angle = $afinish - $astart;
			$dt = $total_angle / $nc;
			$dtm = $dt/3;
			$x0 *= $this->k;
			$y0 = ($this->h - $y0) * $this->k;
			$t1 = $astart;
			$a0 = $x0 + ($rx * cos($t1));
			$b0 = $y0 + ($ry * sin($t1));
			$c0 = -$rx * sin($t1);
			$d0 = $ry * cos($t1);
			$this->_outPoint($a0 / $this->k, $this->h - ($b0 / $this->k));
			for ($i = 1; $i <= $nc; $i++) {
				// Draw this bit of the total curve
				$t1 = ($i * $dt) + $astart;
				$a1 = $x0 + ($rx * cos($t1));
				$b1 = $y0 + ($ry * sin($t1));
				$c1 = -$rx * sin($t1);
				$d1 = $ry * cos($t1);
				$this->_outCurve(($a0 + ($c0 * $dtm)) / $this->k, $this->h - (($b0 + ($d0 * $dtm)) / $this->k), ($a1 - ($c1 * $dtm)) / $this->k, $this->h - (($b1 - ($d1 * $dtm)) / $this->k), $a1 / $this->k, $this->h - ($b1 / $this->k));
				$a0 = $a1;
				$b0 = $b1;
				$c0 = $c1;
				$d0 = $d1;
			}
			$this->_out($op);
		}
	}

	function Circle($x0, $y0, $r, $astart = 0, $afinish = 360, $style = "", $line_style = array(), $fill_color = array(), $nc = 8) {
		$this->Ellipse($x0, $y0, $r, 0, 0, $astart, $afinish, $style, $line_style, $fill_color, $nc);
	}

	function Polygon($p, $style = "", $line_style = array(), $fill_color = array()) {
		$np = count($p) / 2;
		if (!(false === strpos($style, "F")) AND $fill_color) {
			list($r, $g, $b) = $fill_color;
			$this->SetFillColor($r, $g, $b);
		}
		switch ($style) {
			case "F": {
				$line_style = array();
				$op = "f";
				break;
			}
			case "FD":
			case "DF": {
				$op = "B";
				break;
			}
			default: {
				$op = "S";
				break;
			}
		}
		$draw = true;
		if ($line_style) {
			if (isset($line_style["all"])) {
				$this->SetLineStyle($line_style["all"]);
			}
			else { // 0 .. (np - 1), op = {B, S}
				$draw = false;
				if ("B" == $op) {
					$op = "f";
					$this->_outPoint($p[0], $p[1]);
					for ($i = 2; $i < ($np * 2); $i = $i + 2) {
						$this->_outLine($p[$i], $p[$i + 1]);
					}
					$this->_outLine($p[0], $p[1]);
					$this->_out($op);
				}
				$p[$np * 2] = $p[0];
				$p[($np * 2) + 1] = $p[1];
				for ($i = 0; $i < $np; $i++) {
					if (isset($line_style[$i])) {
						$this->Line($p[$i * 2], $p[($i * 2) + 1], $p[($i * 2) + 2], $p[($i * 2) + 3], $line_style[$i]);
					}
				}
			}
		}
		if ($draw) {
			$this->_outPoint($p[0], $p[1]);
			for ($i = 2; $i < ($np * 2); $i = $i + 2) {
				$this->_outLine($p[$i], $p[$i + 1]);
			}
			$this->_outLine($p[0], $p[1]);
			$this->_out($op);
		}
	}

	function RegularPolygon($x0, $y0, $r, $ns, $angle = 0, $draw_circle = false, $style = "", $line_style = array(), $fill_color = array(), $circle_style = "", $circle_outLine_style = array(), $circle_fill_color = array()) {
		if (3 > $ns) {
			$ns = 3;
		}
		if ($draw_circle) {
			$this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_outLine_style, $circle_fill_color);
		}
		$p = array();
		for ($i = 0; $i < $ns; $i++) {
			$a = $angle + ($i * 360 / $ns);
			$a_rad = deg2rad((float) $a);
			$p[] = $x0 + ($r * sin($a_rad));
			$p[] = $y0 + ($r * cos($a_rad));
		}
		$this->Polygon($p, $style, $line_style, $fill_color);
	}

	function StarPolygon($x0, $y0, $r, $nv, $ng, $angle = 0, $draw_circle = false, $style = "", $line_style = array(), $fill_color = array(), $circle_style = "", $circle_outLine_style = array(), $circle_fill_color = array()) {
		if (2 > $nv) {
			$nv = 2;
		}
		if ($draw_circle) {
			$this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_outLine_style, $circle_fill_color);
		}
		$p2 = array();
		$visited = array();
		for ($i = 0; $i < $nv; $i++) {
			$a = $angle + ($i * 360 / $nv);
			$a_rad = deg2rad((float) $a);
			$p2[] = $x0 + ($r * sin($a_rad));
			$p2[] = $y0 + ($r * cos($a_rad));
			$visited[] = false;
		}
		$p = array();
		$i = 0;
		do {
			$p[] = $p2[$i * 2];
			$p[] = $p2[($i * 2) + 1];
			$visited[$i] = true;
			$i += $ng;
			$i %= $nv;
		} while (!$visited[$i]);
		$this->Polygon($p, $style, $line_style, $fill_color);
	}

	function RoundedRect($x, $y, $w, $h, $r, $round_corner = "1111", $style = "", $border_style = array(), $fill_color = array()) {
		if ("0000" == $round_corner) { // Not rounded
			$this->Rect($x, $y, $w, $h, $style, $border_style, $fill_color);
		} else { // Rounded
			if (!(false === strpos($style, "F")) AND $fill_color) {
				list($red, $g, $b) = $fill_color;
				$this->SetFillColor($red, $g, $b);
			}
			switch ($style) {
				case "F": {
					$border_style = array();
					$op = "f";
					break;
				}
				case "FD":
				case "DF": {
					$op = "B";
					break;
				}
				default: {
					$op = "S";
					break;
				}
			}
			if ($border_style) {
				$this->SetLineStyle($border_style);
			}
			$MyArc = 4 / 3 * (sqrt(2) - 1);
			$this->_outPoint($x + $r, $y);
			$xc = $x + $w - $r;
			$yc = $y + $r;
			$this->_outLine($xc, $y);
			if ($round_corner[0]) {
				$this->_outCurve($xc + ($r * $MyArc), $yc - $r, $xc + $r, $yc - ($r * $MyArc), $xc + $r, $yc);
			} else {
				$this->_outLine($x + $w, $y);
			}
			$xc = $x + $w - $r;
			$yc = $y + $h - $r;
			$this->_outLine($x + $w, $yc);
			if ($round_corner[1]) {
				$this->_outCurve($xc + $r, $yc + ($r * $MyArc), $xc + ($r * $MyArc), $yc + $r, $xc, $yc + $r);
			} else {
				$this->_outLine($x + $w, $y + $h);
			}
			$xc = $x + $r;
			$yc = $y + $h - $r;
			$this->_outLine($xc, $y + $h);
			if ($round_corner[2]) {
				$this->_outCurve($xc - ($r * $MyArc), $yc + $r, $xc - $r, $yc + ($r * $MyArc), $xc - $r, $yc);
			} else {
				$this->_outLine($x, $y + $h);
			}
			$xc = $x + $r;
			$yc = $y + $r;
			$this->_outLine($x, $yc);
			if ($round_corner[3]) {
				$this->_outCurve($xc - $r, $yc - ($r * $MyArc), $xc - ($r * $MyArc), $yc - $r, $xc, $yc - $r);
			} else {
				$this->_outLine($x, $y);
				$this->_outLine($x + $r, $y);
			}
			$this->_out($op);
		}
	}

	function utf8StrRev($str, $setbom=false, $forcertl=false) {
		return $this->arrUTF8ToUTF16BE($this->utf8Bidi($this->UTF8StringToArray($str), $forcertl=false), $setbom);
	}

	function utf8Bidi($ta, $forcertl=false) {
		global $unicode,$unicode_mirror, $unicode_arlet;
		require_once(dirname(__FILE__).'/unicode_data.php');

		// paragraph embedding level
		$pel = 0;
		// max level
		$maxlevel = 0;

		// create string from array
		$str = $this->UTF8ArrSubString($ta);

		// check if string contains arabic text
		if (preg_match(K_RE_PATTERN_ARABIC, $str)) {
			$arabic = true;
		} else {
			$arabic = false;
		}

		// check if string contains RTL text
		if (!($forcertl OR $arabic OR preg_match(K_RE_PATTERN_RTL, $str))) {
			return $ta;
		}

		// get number of chars
		$numchars = count($ta);

		if ($forcertl == 'R') {
				$pel = 1;
		} elseif ($forcertl == 'L') {
				$pel = 0;
		} else {
			// P2. In each paragraph, find the first character of type L, AL, or R.
			// P3. If a character is found in P2 and it is of type AL or R, then set the paragraph embedding level to one; otherwise, set it to zero.
			for ($i=0; $i < $numchars; $i++) {
				$type = $unicode[$ta[$i]];
				if ($type == 'L') {
					$pel = 0;
					break;
				} elseif (($type == 'AL') OR ($type == 'R')) {
					$pel = 1;
					break;
				}
			}
		}

		// Current Embedding Level
		$cel = $pel;
		// directional override status
		$dos = 'N';
		$remember = array();
		// start-of-level-run
		$sor = $pel % 2 ? 'R' : 'L';
		$eor = $sor;

		//$levels = array(array('level' => $cel, 'sor' => $sor, 'eor' => '', 'chars' => array()));
		//$current_level = &$levels[count( $levels )-1];

		// Array of characters data
		$chardata = Array();

		// X1. Begin by setting the current embedding level to the paragraph embedding level. Set the directional override status to neutral. Process each character iteratively, applying rules X2 through X9. Only embedding levels from 0 to 61 are valid in this phase.
		// 	In the resolution of levels in rules I1 and I2, the maximum embedding level of 62 can be reached.
		for ($i=0; $i < $numchars; $i++) {
			if ($ta[$i] == K_RLE) {
				// X2. With each RLE, compute the least greater odd embedding level.
				//	a. If this new level would be valid, then this embedding code is valid. Remember (push) the current embedding level and override status. Reset the current level to this new level, and reset the override status to neutral.
				//	b. If the new level would not be valid, then this code is invalid. Do not change the current level or override status.
				$next_level = $cel + ($cel % 2) + 1;
				if ($next_level < 62) {
					$remember[] = array('num' => K_RLE, 'cel' => $cel, 'dos' => $dos);
					$cel = $next_level;
					$dos = 'N';
					$sor = $eor;
					$eor = $cel % 2 ? 'R' : 'L';
				}
			} elseif ($ta[$i] == K_LRE) {
				// X3. With each LRE, compute the least greater even embedding level.
				//	a. If this new level would be valid, then this embedding code is valid. Remember (push) the current embedding level and override status. Reset the current level to this new level, and reset the override status to neutral.
				//	b. If the new level would not be valid, then this code is invalid. Do not change the current level or override status.
				$next_level = $cel + 2 - ($cel % 2);
				if ( $next_level < 62 ) {
					$remember[] = array('num' => K_LRE, 'cel' => $cel, 'dos' => $dos);
					$cel = $next_level;
					$dos = 'N';
					$sor = $eor;
					$eor = $cel % 2 ? 'R' : 'L';
				}
			} elseif ($ta[$i] == K_RLO) {
				// X4. With each RLO, compute the least greater odd embedding level.
				//	a. If this new level would be valid, then this embedding code is valid. Remember (push) the current embedding level and override status. Reset the current level to this new level, and reset the override status to right-to-left.
				//	b. If the new level would not be valid, then this code is invalid. Do not change the current level or override status.
				$next_level = $cel + ($cel % 2) + 1;
				if ($next_level < 62) {
					$remember[] = array('num' => K_RLO, 'cel' => $cel, 'dos' => $dos);
					$cel = $next_level;
					$dos = 'R';
					$sor = $eor;
					$eor = $cel % 2 ? 'R' : 'L';
				}
			} elseif ($ta[$i] == K_LRO) {
				// X5. With each LRO, compute the least greater even embedding level.
				//	a. If this new level would be valid, then this embedding code is valid. Remember (push) the current embedding level and override status. Reset the current level to this new level, and reset the override status to left-to-right.
				//	b. If the new level would not be valid, then this code is invalid. Do not change the current level or override status.
				$next_level = $cel + 2 - ($cel % 2);
				if ( $next_level < 62 ) {
					$remember[] = array('num' => K_LRO, 'cel' => $cel, 'dos' => $dos);
					$cel = $next_level;
					$dos = 'L';
					$sor = $eor;
					$eor = $cel % 2 ? 'R' : 'L';
				}
			} elseif ($ta[$i] == K_PDF) {
				// X7. With each PDF, determine the matching embedding or override code. If there was a valid matching code, restore (pop) the last remembered (pushed) embedding level and directional override.
				if (count($remember)) {
					$last = count($remember ) - 1;
					if (($remember[$last]['num'] == K_RLE) OR
						  ($remember[$last]['num'] == K_LRE) OR
						  ($remember[$last]['num'] == K_RLO) OR
						  ($remember[$last]['num'] == K_LRO)) {
						$match = array_pop($remember);
						$cel = $match['cel'];
						$dos = $match['dos'];
						$sor = $eor;
						$eor = ($cel > $match['cel'] ? $cel : $match['cel']) % 2 ? 'R' : 'L';
					}
				}
			} elseif (($ta[$i] != K_RLE) AND
							 ($ta[$i] != K_LRE) AND
							 ($ta[$i] != K_RLO) AND
							 ($ta[$i] != K_LRO) AND
							 ($ta[$i] != K_PDF)) {
				// X6. For all types besides RLE, LRE, RLO, LRO, and PDF:
				//	a. Set the level of the current character to the current embedding level.
				//	b. Whenever the directional override status is not neutral, reset the current character type to the directional override status.
				if ($dos != 'N') {
					$chardir = $dos;
				} else {
					$chardir = $unicode[$ta[$i]];
				}
				// stores string characters and other information
				$chardata[] = array('char' => $ta[$i], 'level' => $cel, 'type' => $chardir, 'sor' => $sor, 'eor' => $eor);
			}
		} // end for each char

		// X8. All explicit directional embeddings and overrides are completely terminated at the end of each paragraph. Paragraph separators are not included in the embedding.
		// X9. Remove all RLE, LRE, RLO, LRO, PDF, and BN codes.
		// X10. The remaining rules are applied to each run of characters at the same level. For each run, determine the start-of-level-run (sor) and end-of-level-run (eor) type, either L or R. This depends on the higher of the two levels on either side of the boundary (at the start or end of the paragraph, the level of the �other� run is the base embedding level). If the higher level is odd, the type is R; otherwise, it is L.

		// 3.3.3 Resolving Weak Types
		// Weak types are now resolved one level run at a time. At level run boundaries where the type of the character on the other side of the boundary is required, the type assigned to sor or eor is used.
		// Nonspacing marks are now resolved based on the previous characters.
		$numchars = count($chardata);

		// W1. Examine each nonspacing mark (NSM) in the level run, and change the type of the NSM to the type of the previous character. If the NSM is at the start of the level run, it will get the type of sor.
		$prevlevel = -1; // track level changes
		$levcount = 0; // counts consecutive chars at the same level
		for ($i=0; $i < $numchars; $i++) {
			if ($chardata[$i]['type'] == 'NSM') {
				if ($levcount) {
					$chardata[$i]['type'] = $chardata[$i]['sor'];
				} elseif ($i > 0) {
					$chardata[$i]['type'] = $chardata[($i-1)]['type'];
				}
			}
			if ($chardata[$i]['level'] != $prevlevel) {
				$levcount = 0;
			} else {
				$levcount++;
			}
			$prevlevel = $chardata[$i]['level'];
		}

		// W2. Search backward from each instance of a European number until the first strong type (R, L, AL, or sor) is found. If an AL is found, change the type of the European number to Arabic number.
		$prevlevel = -1;
		$levcount = 0;
		for ($i=0; $i < $numchars; $i++) {
			if ($chardata[$i]['char'] == 'EN') {
				for ($j=$levcount; $j >= 0; $j--) {
					if ($chardata[$j]['type'] == 'AL') {
						$chardata[$i]['type'] = 'AN';
					} elseif (($chardata[$j]['type'] == 'L') OR ($chardata[$j]['type'] == 'R')) {
						break;
					}
				}
			}
			if ($chardata[$i]['level'] != $prevlevel) {
				$levcount = 0;
			} else {
				$levcount++;
			}
			$prevlevel = $chardata[$i]['level'];
		}

		// W3. Change all ALs to R.
		for ($i=0; $i < $numchars; $i++) {
			if ($chardata[$i]['type'] == 'AL') {
				$chardata[$i]['type'] = 'R';
			}
		}

		// W4. A single European separator between two European numbers changes to a European number. A single common separator between two numbers of the same type changes to that type.
		$prevlevel = -1;
		$levcount = 0;
		for ($i=0; $i < $numchars; $i++) {
			if (($levcount > 0) AND (($i+1) < $numchars) AND ($chardata[($i+1)]['level'] == $prevlevel)) {
				if (($chardata[$i]['type'] == 'ES') AND ($chardata[($i-1)]['type'] == 'EN') AND ($chardata[($i+1)]['type'] == 'EN')) {
					$chardata[$i]['type'] = 'EN';
				} elseif (($chardata[$i]['type'] == 'CS') AND ($chardata[($i-1)]['type'] == 'EN') AND ($chardata[($i+1)]['type'] == 'EN')) {
					$chardata[$i]['type'] = 'EN';
				} elseif (($chardata[$i]['type'] == 'CS') AND ($chardata[($i-1)]['type'] == 'AN') AND ($chardata[($i+1)]['type'] == 'AN')) {
					$chardata[$i]['type'] = 'AN';
				}
			}
			if ($chardata[$i]['level'] != $prevlevel) {
				$levcount = 0;
			} else {
				$levcount++;
			}
			$prevlevel = $chardata[$i]['level'];
		}

		// W5. A sequence of European terminators adjacent to European numbers changes to all European numbers.
		$prevlevel = -1;
		$levcount = 0;
		for ($i=0; $i < $numchars; $i++) {
			if($chardata[$i]['type'] == 'ET') {
				if (($levcount > 0) AND ($chardata[($i-1)]['type'] == 'EN')) {
					$chardata[$i]['type'] = 'EN';
				} else {
					$j = $i+1;
					while (($j < $numchars) AND ($chardata[$j]['level'] == $prevlevel)) {
						if ($chardata[$j]['type'] == 'EN') {
							$chardata[$i]['type'] = 'EN';
							break;
						} elseif ($chardata[$j]['type'] != 'ET') {
							break;
						}
						$j++;
					}
				}
			}
			if ($chardata[$i]['level'] != $prevlevel) {
				$levcount = 0;
			} else {
				$levcount++;
			}
			$prevlevel = $chardata[$i]['level'];
		}

		// W6. Otherwise, separators and terminators change to Other Neutral.
		$prevlevel = -1;
		$levcount = 0;
		for ($i=0; $i < $numchars; $i++) {
			if (($chardata[$i]['type'] == 'ET') OR ($chardata[$i]['type'] == 'ES') OR ($chardata[$i]['type'] == 'CS')) {
				$chardata[$i]['type'] = 'ON';
			}
			if ($chardata[$i]['level'] != $prevlevel) {
				$levcount = 0;
			} else {
				$levcount++;
			}
			$prevlevel = $chardata[$i]['level'];
		}

		//W7. Search backward from each instance of a European number until the first strong type (R, L, or sor) is found. If an L is found, then change the type of the European number to L.
		$prevlevel = -1;
		$levcount = 0;
		for ($i=0; $i < $numchars; $i++) {
			if ($chardata[$i]['char'] == 'EN') {
				for ($j=$levcount; $j >= 0; $j--) {
					if ($chardata[$j]['type'] == 'L') {
						$chardata[$i]['type'] = 'L';
					} elseif ($chardata[$j]['type'] == 'R') {
						break;
					}
				}
			}
			if ($chardata[$i]['level'] != $prevlevel) {
				$levcount = 0;
			} else {
				$levcount++;
			}
			$prevlevel = $chardata[$i]['level'];
		}

		// N1. A sequence of neutrals takes the direction of the surrounding strong text if the text on both sides has the same direction. European and Arabic numbers act as if they were R in terms of their influence on neutrals. Start-of-level-run (sor) and end-of-level-run (eor) are used at level run boundaries.
		$prevlevel = -1;
		$levcount = 0;
		for ($i=0; $i < $numchars; $i++) {
			if (($levcount > 0) AND (($i+1) < $numchars) AND ($chardata[($i+1)]['level'] == $prevlevel)) {
				if (($chardata[$i]['type'] == 'N') AND ($chardata[($i-1)]['type'] == 'L') AND ($chardata[($i+1)]['type'] == 'L')) {
					$chardata[$i]['type'] = 'L';
				} elseif (($chardata[$i]['type'] == 'N') AND
				 (($chardata[($i-1)]['type'] == 'R') OR ($chardata[($i-1)]['type'] == 'EN') OR ($chardata[($i-1)]['type'] == 'AN')) AND
				 (($chardata[($i+1)]['type'] == 'R') OR ($chardata[($i+1)]['type'] == 'EN') OR ($chardata[($i+1)]['type'] == 'AN'))) {
					$chardata[$i]['type'] = 'R';
				} elseif ($chardata[$i]['type'] == 'N') {
					// N2. Any remaining neutrals take the embedding direction
					$chardata[$i]['type'] = $chardata[$i]['sor'];
				}
			} elseif (($levcount == 0) AND (($i+1) < $numchars) AND ($chardata[($i+1)]['level'] == $prevlevel)) {
				// first char
				if (($chardata[$i]['type'] == 'N') AND ($chardata[$i]['sor'] == 'L') AND ($chardata[($i+1)]['type'] == 'L')) {
					$chardata[$i]['type'] = 'L';
				} elseif (($chardata[$i]['type'] == 'N') AND
				 (($chardata[$i]['sor'] == 'R') OR ($chardata[$i]['sor'] == 'EN') OR ($chardata[$i]['sor'] == 'AN')) AND
				 (($chardata[($i+1)]['type'] == 'R') OR ($chardata[($i+1)]['type'] == 'EN') OR ($chardata[($i+1)]['type'] == 'AN'))) {
					$chardata[$i]['type'] = 'R';
				} elseif ($chardata[$i]['type'] == 'N') {
					// N2. Any remaining neutrals take the embedding direction
					$chardata[$i]['type'] = $chardata[$i]['sor'];
				}
			} elseif (($levcount > 0) AND ((($i+1) == $numchars) OR (($i+1) < $numchars) AND ($chardata[($i+1)]['level'] != $prevlevel))) {
				//last char
				if (($chardata[$i]['type'] == 'N') AND ($chardata[($i-1)]['type'] == 'L') AND ($chardata[$i]['eor'] == 'L')) {
					$chardata[$i]['type'] = 'L';
				} elseif (($chardata[$i]['type'] == 'N') AND
				 (($chardata[($i-1)]['type'] == 'R') OR ($chardata[($i-1)]['type'] == 'EN') OR ($chardata[($i-1)]['type'] == 'AN')) AND
				 (($chardata[$i]['eor'] == 'R') OR ($chardata[$i]['eor'] == 'EN') OR ($chardata[$i]['eor'] == 'AN'))) {
					$chardata[$i]['type'] = 'R';
				} elseif ($chardata[$i]['type'] == 'N') {
					// N2. Any remaining neutrals take the embedding direction
					$chardata[$i]['type'] = $chardata[$i]['sor'];
				}
			} elseif ($chardata[$i]['type'] == 'N') {
				// N2. Any remaining neutrals take the embedding direction
				$chardata[$i]['type'] = $chardata[$i]['sor'];
			}
			if ($chardata[$i]['level'] != $prevlevel) {
				$levcount = 0;
			} else {
				$levcount++;
			}
			$prevlevel = $chardata[$i]['level'];
		}

		// I1. For all characters with an even (left-to-right) embedding direction, those of type R go up one level and those of type AN or EN go up two levels.
		// I2. For all characters with an odd (right-to-left) embedding direction, those of type L, EN or AN go up one level.
		for ($i=0; $i < $numchars; $i++) {
			$odd = $chardata[$i]['level'] % 2;
			if ($odd) {
				if (($chardata[$i]['type'] == 'L') OR ($chardata[$i]['type'] == 'AN') OR ($chardata[$i]['type'] == 'EN')){
					$chardata[$i]['level'] += 1;
				}
			} else {
				if ($chardata[$i]['type'] == 'R') {
					$chardata[$i]['level'] += 1;
				} elseif (($chardata[$i]['type'] == 'AN') OR ($chardata[$i]['type'] == 'EN')){
					$chardata[$i]['level'] += 2;
				}
			}
			$maxlevel = max($chardata[$i]['level'],$maxlevel);
		}

		// L1. On each line, reset the embedding level of the following characters to the paragraph embedding level:
		//	1. Segment separators,
		//	2. Paragraph separators,
		//	3. Any sequence of whitespace characters preceding a segment separator or paragraph separator, and
		//	4. Any sequence of white space characters at the end of the line.
		for ($i=0; $i < $numchars; $i++) {
			if (($chardata[$i]['type'] == 'B') OR ($chardata[$i]['type'] == 'S')) {
				$chardata[$i]['level'] = $pel;
			} elseif ($chardata[$i]['type'] == 'WS') {
				$j = $i+1;
				while ($j < $numchars) {
					if ((($chardata[$j]['type'] == 'B') OR ($chardata[$j]['type'] == 'S')) OR
						(($j == ($numchars-1)) AND ($chardata[$j]['type'] == 'WS'))) {
						$chardata[$i]['level'] = $pel;;
						break;
					} elseif ($chardata[$j]['type'] != 'WS') {
						break;
					}
					$j++;
				}
			}
		}

		// Arabic Shaping
		// Cursively connected scripts, such as Arabic or Syriac, require the selection of positional character shapes that depend on adjacent characters. Shaping is logically applied after the Bidirectional Algorithm is used and is limited to characters within the same directional run.
		if ($arabic) {
			for ($i=0; $i < $numchars; $i++) {
				if ($unicode[$chardata[$i]['char']] == 'AL') {
					if (($i > 0) AND (($i+1) < $numchars) AND
							($unicode[$chardata[($i-1)]['char']] == 'AL') AND
							($unicode[$chardata[($i+1)]['char']] == 'AL') AND
							($chardata[($i-1)]['type'] == $chardata[$i]['type']) AND
							($chardata[($i+1)]['type'] == $chardata[$i]['type'])) {
						// medial
						if (isset($unicode_arlet[$chardata[$i]['char']][3])) {
							$chardata[$i]['char'] = $unicode_arlet[$chardata[$i]['char']][3];
						}
					} elseif ((($i+1) < $numchars) AND
							($unicode[$chardata[($i+1)]['char']] == 'AL') AND
							($chardata[($i+1)]['type'] == $chardata[$i]['type'])) {
						// initial
						if (isset($unicode_arlet[$chardata[$i]['char']][2])) {
							$chardata[$i]['char'] = $unicode_arlet[$chardata[$i]['char']][2];
						}
					} elseif (($i > 0) AND
							($unicode[$chardata[($i-1)]['char']] == 'AL') AND
							($chardata[($i-1)]['type'] == $chardata[$i]['type'])) {
						// final
						if (isset($unicode_arlet[$chardata[$i]['char']][1])) {
							$chardata[$i]['char'] = $unicode_arlet[$chardata[$i]['char']][1];
						}
					} elseif (isset($unicode_arlet[$chardata[$i]['char']][0])) {
						// isolated
						$chardata[$i]['char'] = $unicode_arlet[$chardata[$i]['char']][0];
					}
				}
			}
		}

		// L2. From the highest level found in the text to the lowest odd level on each line, including intermediate levels not actually present in the text, reverse any contiguous sequence of characters that are at that level or higher.
		for ($j=$maxlevel; $j > 0; $j--) {
			$ordarray = Array();
			$revarr = Array();
			$onlevel = false;
			for ($i=0; $i < $numchars; $i++) {
				if ($chardata[$i]['level'] >= $j) {
					$onlevel = true;
					if (isset($unicode_mirror[$chardata[$i]['char']])) {
						// L4. A character is depicted by a mirrored glyph if and only if (a) the resolved directionality of that character is R, and (b) the Bidi_Mirrored property value of that character is true.
						$chardata[$i]['char'] = $unicode_mirror[$chardata[$i]['char']];
					}
					$revarr[] = $chardata[$i];
				} else {
					if($onlevel) {
						$revarr = array_reverse($revarr);
						$ordarray = array_merge($ordarray, $revarr);
						$revarr = Array();
						$onlevel = false;
					}
					$ordarray[] = $chardata[$i];
				}
			}
			if($onlevel) {
				$revarr = array_reverse($revarr);
				$ordarray = array_merge($ordarray, $revarr);
			}
			$chardata = $ordarray;
		}

		$ordarray = array();
		for ($i=0; $i < $numchars; $i++) {
			$ordarray[] = $chardata[$i]['char'];
		}

		return $ordarray;
	}

	function Bookmark($txt, $level=0, $y=-1) {
    if($y == -1) {
			$y = $this->GetY();
		}
		$this->outlines[]=array('t'=>$txt,'l'=>$level,'y'=>$y,'p'=>$this->PageNo());
	}

	function _putbookmarks() {
		$nb = count($this->outlines);
		if($nb == 0) {
			return;
		}
		$lru = array();
		$level = 0;
		foreach($this->outlines as $i=>$o) {
			if($o['l'] > 0) {
				$parent = $lru[$o['l'] - 1];
				//Set parent and last pointers
				$this->outlines[$i]['parent'] = $parent;
				$this->outlines[$parent]['last'] = $i;
				if($o['l'] > $level) {
					//Level increasing: set first pointer
					$this->outlines[$parent]['first'] = $i;
				}
			} else {
				$this->outlines[$i]['parent']=$nb;
			}
			if($o['l']<=$level and $i>0) {
				//Set prev and next pointers
				$prev = $lru[$o['l']];
				$this->outlines[$prev]['next'] = $i;
				$this->outlines[$i]['prev'] = $prev;
			}
			$lru[$o['l']] = $i;
			$level = $o['l'];
		}
		//Outline items
		$n = $this->n+1;
		foreach($this->outlines as $i=>$o) {
			$this->_newobj();
			$this->_out('<</Title '.$this->_textstring($o['t']));
			$this->_out('/Parent '.($n+$o['parent']).' 0 R');
			if(isset($o['prev']))
			$this->_out('/Prev '.($n+$o['prev']).' 0 R');
			if(isset($o['next']))
			$this->_out('/Next '.($n+$o['next']).' 0 R');
			if(isset($o['first']))
			$this->_out('/First '.($n+$o['first']).' 0 R');
			if(isset($o['last']))
			$this->_out('/Last '.($n+$o['last']).' 0 R');
			$this->_out(sprintf('/Dest [%d 0 R /XYZ 0 %.2f null]',1+2*$o['p'],($this->h-$o['y'])*$this->k));
			$this->_out('/Count 0>>');
			$this->_out('endobj');
		}
		//Outline root
		$this->_newobj();
		$this->OutlineRoot=$this->n;
		$this->_out('<</Type /Outlines /First '.$n.' 0 R');
		$this->_out('/Last '.($n+$lru[0]).' 0 R>>');
		$this->_out('endobj');
	}


	function IncludeJS($script) {
		$this->javascript .= $script;
	}

	function _putjavascript() {
		if (empty($this->javascript)) {
			return;
		}
		$this->_newobj();
		$this->n_js = $this->n;
		$this->_out('<<');
		$this->_out('/Names [(EmbeddedJS) '.($this->n+1).' 0 R ]');
		$this->_out('>>');
		$this->_out('endobj');
		$this->_newobj();
		$this->_out('<<');
		$this->_out('/S /JavaScript');
		$this->_out('/JS '.$this->_textstring($this->javascript));
		$this->_out('>>');
		$this->_out('endobj');
	}

	function _JScolor($color) {
		static $aColors = array('transparent','black','white','red','green','blue','cyan','magenta','yellow','dkGray','gray','ltGray');
		if(substr($color,0,1) == '#') {
			return sprintf("['RGB',%.3f,%.3f,%.3f]", hexdec(substr($color,1,2))/255, hexdec(substr($color,3,2))/255, hexdec(substr($color,5,2))/255);
		}
		if(!in_array($color,$aColors)) {
			$this->Error('Invalid color: '.$color);
		}
		return 'color.'.$color;
	}

	function _addfield($type, $name, $x, $y, $w, $h, $prop) {
		$k = $this->k;
		$this->javascript .= sprintf("f=addField('%s','%s',%d,[%.2f,%.2f,%.2f,%.2f]);",$name,$type,$this->PageNo()-1,$x*$k,($this->h-$y)*$k+1,($x+$w)*$k,($this->h-$y-$h)*$k+1);
		$this->javascript .= 'f.textSize='.$this->FontSizePt.';';
		while(list($key, $val) = each($prop)) {
			if (strcmp(substr($key,-5),"Color") == 0) {
				$val = $this->_JScolor($val);
			} else {
				$val = "'".$val."'";
			}
			$this->javascript .= "f.".$key."=".$val.";";
		}
		$this->x+=$w;
	}

	function TextField($name, $w, $h, $prop=array()) {
		$this->_addfield('text',$name,$this->x,$this->y,$w,$h,$prop);
	}

	function RadioButton($name, $w, $prop=array()) {
		if(!isset($prop['strokeColor'])) {
			$prop['strokeColor']='black';
		}
		$this->_addfield('radiobutton',$name,$this->x,$this->y,$w,$w,$prop);
	}

	function ListBox($name, $w, $h, $values, $prop=array()) {
		if(!isset($prop['strokeColor'])) {
			$prop['strokeColor']='ltGray';
		}
		$this->_addfield('listbox',$name,$this->x,$this->y,$w,$h,$prop);
		$s = '';
		foreach($values as $value) {
			$s .= "'".addslashes($value)."',";
		}
		$this->javascript .= 'f.setItems(['.substr($s,0,-1).']);';
	}

	function ComboBox($name, $w, $h, $values, $prop=array()) {
		$this->_addfield('combobox',$name,$this->x,$this->y,$w,$h,$prop);
		$s = '';
		foreach($values as $value) {
			$s .= "'".addslashes($value)."',";
		}
		$this->javascript .= 'f.setItems(['.substr($s,0,-1).']);';
	}

	function CheckBox($name, $w, $checked=false, $prop=array()) {
		$prop['value'] = ($checked ? 'Yes' : 'Off');
		if(!isset($prop['strokeColor'])) {
			$prop['strokeColor']='black';
		}
		$this->_addfield('checkbox',$name,$this->x,$this->y,$w,$w,$prop);
	}

	function Button($name, $w, $h, $caption, $action, $prop=array()) {
		if(!isset($prop['strokeColor'])) {
			$prop['strokeColor']='black';
		}
		if(!isset($prop['borderStyle'])) {
			$prop['borderStyle']='beveled';
		}
		$this->_addfield('button',$name,$this->x,$this->y,$w,$h,$prop);
		$this->javascript .= "f.buttonSetCaption('".addslashes($caption)."');";
		$this->javascript .= "f.setAction('MouseUp','".addslashes($action)."');";
		$this->javascript .= "f.highlight='push';";
		$this->javascript .= 'f.print=false;';
	}
}
    //Handle special IE contype request
    if(isset($_SERVER['HTTP_USER_AGENT']) AND ($_SERVER['HTTP_USER_AGENT']=='contype')) {
	    header('Content-Type: application/pdf');
	    exit;
    }
}
?>