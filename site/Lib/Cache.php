<?php
/**
 * @version      5.6.2 28.04.2025
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */

namespace Joomla\Component\Jshopping\Site\Lib;

defined('_JEXEC') or die();

class Cache
{
    protected $dir = '';
    protected $cacheTimeSec = 12 * 3600;

    public function __construct(string $dir = '')
    {
        $this->setDir($dir);
    }

    public function setDir($dir): void
    {
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        $this->dir = $dir;
    }

    public function set($key, $data): void
    {
        file_put_contents($this->dir . '/' . $key, $data);
    }

    public function get($key): ?string
    {        
        $dir = $this->dir;
        if (file_exists($dir . '/' . $key) && (time() - filemtime($dir . '/' . $key) < $this->cacheTimeSec)) {
            return file_get_contents($dir . '/' . $key);
        } else {
            return null;
        }
    }

    public function setCacheTime(int $sec): void
    {
        $this->cacheTimeSec = $sec;
    }

    public function clearAll(): void
    {
        $files = glob($this->dir . '/' . '*');
        foreach($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

}