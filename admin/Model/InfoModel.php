<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2012 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Model;


defined('_JEXEC') or die();

class InfoModel{

	function _remote_file_exists($url){
		return (bool)preg_match('~HTTP/1\.\d\s+200\s+OK~', @current(get_headers($url)));
	}

    function getUpdateObj($version, $jshopConfig) {
		$result = new \stdClass();
		$xml = null;
		$str = \JSHelper::file_get_content_curl($jshopConfig->xml_update_path);        
        if ($str){
            $xml = simplexml_load_string($str);
        }elseif (self::_remote_file_exists($jshopConfig->xml_update_path)){
            $xml = simplexml_load_file($jshopConfig->xml_update_path);
        }
        if ($xml){
            if (count($xml->update)) {
                foreach($xml->update as $v){
                    if (((string)$v['version'] == $version) && ((string)$v['newversion'])) {
                        $result->text = sprintf(\JText::_('JSHOP_UPDATE_ARE_AVAILABLE'), (string)$v['newversion']);
                        $result->file = (string)$v['file'];
                        $result->link = $jshopConfig->updates_site_path;
                        $result->text2 = sprintf(\JText::_('JSHOP_UPDATE_TO'), (string)$v['newversion']);
                        $result->link2 = 'index.php?option=com_jshopping&controller=update&task=update&installtype=url&install_url=sm0:'.$result->file.'&back='.urlencode('index.php?option=com_jshopping&controller=info');
                    }
                }
            }
        }
		return $result;
	}
}