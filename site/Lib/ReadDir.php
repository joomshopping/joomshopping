<?php
/**
* @version      5.8.0 19.06.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Lib;

defined('_JEXEC') or die();

class ReadDir{

    private $path;
    private $skipFiles = [];

    public function __construct($path){
        $this->path = $path;
    }

    public function setSkipFiles($files) {
        $this->skipFiles = $files;
    }

    public function getFiles($filter = '', $with_prefix = ''){
        $files = [];
        $iterator = new \DirectoryIterator($this->path);
        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile()) {
                $filename = $fileinfo->getFilename();
                if ($filter && !str_contains($filename, $filter)) continue;
                if (in_array($filename, $this->skipFiles)) continue;
                if ($with_prefix && !file_exists($this->path . '/' . $with_prefix . $filename)) continue;
                $files[] = $filename;
            }
        }
        return $files;
    }
}