<?php
/**
* @version      5.5.5 01.02.2015
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
defined('_JEXEC') or die();

class LogsModel extends BaseadminModel{

	public function getListItems(array $filters = [], array $orderBy = [], array $limit = [], array $params = []){
		$list = $this->getList();
        usort($list, function($a, $b) use ($orderBy) {
            if ($orderBy['order'] == 'date') {
                $i = 1;
            } else if ($orderBy['order'] == 'size') {
                $i = 2;
            } else {
                $i = 0; // name
            }
            if ($orderBy['dir'] == 'desc') {
                $ret = $b[$i] <=> $a[$i]; 
            } else { // asc
                $ret = $a[$i] <=> $b[$i]; 
            }
            return $ret;
        });

        return $list;
	}

    public function getList(){        
        $jshopConfig = JSFactory::getConfig();
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
    
    public function read($file){
        $jshopConfig = JSFactory::getConfig();        
        $dir = $jshopConfig->log_path;
        if (file_exists($dir.$file)) {
			return file_get_contents($dir.$file);
		} else {
			return '';
		}
    }

    public function download($file){
        $jshopConfig = JSFactory::getConfig();
        $dir = $jshopConfig->log_path;
        $file_name = $dir.$file;
		if (!file_exists($file_name)){
            throw new \Exception('Error. File not exist');
        }
		
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-Type: application/octet-stream");
        header("Content-Length: " . (string)(filesize($file_name)));
        header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
        header("Content-Transfer-Encoding: binary");

        if ($jshopConfig->productDownloadFilePart8kb) {
            $fp = fopen($file_name, "rb");
            while( (!feof($fp)) && (connection_status()==0) ){
                print(fread($fp, 1024*8));
                flush();
            }
            fclose($fp);
        } else {
            print readfile($file_name);
        }
	}

    public function deleteList(array $cid, $msg = 1){
        $app = Factory::getApplication();
        $res = array();
		foreach($cid as $id){
            $this->delete($id);
            if ($msg){
                $app->enqueueMessage(Text::_('JSHOP_ITEM_DELETED'), 'message');
            }
            $res[$id] = true;
		}
        return $res;
    }

    public function delete($file) {
        $jshopConfig = JSFactory::getConfig();
        $filename = str_replace(array('..', '/', ':'), '', $file);
        $dir = $jshopConfig->log_path;
        $file_name = $dir.$filename;
        @unlink($file_name);
        return 1;
    }
            
}