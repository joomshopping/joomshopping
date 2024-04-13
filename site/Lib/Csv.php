<?php
/**
* @version      5.4.0 08.04.2024
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Lib;

defined('_JEXEC') or die();

class Csv{
    
    public $delimit = ";";
    public $text_qualifier = '"';
	public $file = '';
	public $write_row_nr = 0;
	public $only_file_append = 0;
	public $file_read_resource = null;
    
    public function setDelimit($val){
        $this->delimit = $val;    
    }
    
    public function setTextQualifier($val){
        $this->text_qualifier = $val;
    }
	
	public function setFile($name) {
		$this->file = $name;
	}
	
	public function setOnlyFileAppend($val) {
		$this->only_file_append = $val;
	}

 	public function read($file){
		$rows=array();
 		$fp = fopen ($file,"r");
        while ($data = fgetcsv($fp, 262144, $this->delimit, $this->text_qualifier) ) {
			$rows[]=$data;
		}
		fclose ($fp);
		return $rows;
 	}

 	public function implodeCSV($data){
        
        $delimit = $this->delimit;
 		foreach($data as $k=>$v) {
 			$v = str_replace(array("\n", "\r", "\t"), " ", (string)$v);
            if ($this->text_qualifier!=""){ 
 			    $v = str_replace($this->text_qualifier, $this->text_qualifier.$this->text_qualifier, $v);
            }
            if ($this->text_qualifier!=""){ 
 			    if (strpos($v, $delimit)!==false || strpos($v, $this->text_qualifier)!==false){
                    $v = $this->text_qualifier.$v.$this->text_qualifier; 
                }
            }else{
                if (strpos($v, $delimit)!==false){
                    $v = str_replace($delimit, " ", $v);
                }
            }
            
            $data[$k] = $v;
 		}

	return implode($delimit, $data);
 	}

 	public function write($file, $mass2D){
 		$fp = fopen($file,"w");
 		if (!$fp) return 0;
        $countrow = count($mass2D);
 		foreach($mass2D as $k=>$v){
 			if (!is_array($v)) return 0;
            $str = $this->implodeCSV($v);
            if ($k < ($countrow-1)) $str = $str."\n";
 			fwrite($fp, $str);
 		}
		fclose($fp);
	return 1;
 	}
	
	public function readRow() {
		if (!$this->file) {
			return false;
		}
		if (!isset($this->file_read_resource)) {
			$this->file_read_resource = fopen($this->file, "r");
		}
		return fgetcsv($this->file_read_resource, 262144, $this->delimit, $this->text_qualifier);
	}
	
	public function writeRow($row) {
		if (!$this->file || !is_array($row)) {
			return 0;
		}
		$str = $this->implodeCSV($row) . "\n";
		if ($this->write_row_nr == 0 && $this->only_file_append == 0) {
			file_put_contents($this->file, $str);
		} else {
			file_put_contents($this->file, $str, FILE_APPEND);
		}
		$this->write_row_nr++;
		return 1;
	}
            
}