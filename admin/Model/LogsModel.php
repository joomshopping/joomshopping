<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model; defined('_JEXEC') or die();

class LogsModel extends BaseadminModel{

    function getList(){        
        $jshopConfig = \JSFactory::getConfig();
        $list = array();
        $dir = $jshopConfig->log_path;
        $dh = opendir($dir);
        while (($file = readdir($dh)) !== false) {
            if (preg_match("/(.*)\.log/", $file, $matches)){
                $time = filemtime($dir.$file);
                $size = filesize($dir.$file);
                $list[] = array($file, $time, $size);
            }
        }
        closedir($dh);
        return $list;
    }
    
    function read($file){
        $jshopConfig = \JSFactory::getConfig();        
        $dir = $jshopConfig->log_path;
        if (file_exists($dir.$file)) {
			return file_get_contents($dir.$file);
		} else {
			return '';
		}
    }
            
}