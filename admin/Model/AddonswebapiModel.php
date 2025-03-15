<?php
/**
* @version      5.4.0 06.04.2024
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model;

use Joomla\Component\Jshopping\Site\Lib\Cache;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Lib\HttpClientLite\Client;
defined('_JEXEC') or die();

class AddonswebapiModel extends BaseadminModel{

    private $catalog_addon_api = "https://www.webdesigner-profi.de/joomla-webdesign/shop/api";
    private $cache_dir = 'catalog_addon';
    public $use_cache = 1;

    public function useCache($use_cache) {
        $this->use_cache = $use_cache;
    }

    public function products() {
        $json = $this->request('/products');
        if ($json) {
            return json_decode($json);
        } else {
            return [];
        }
    }

    public function categorys() {
        $json = $this->request('/categorys');
        if ($json) {
            return json_decode($json);
        } else {
            return [];
        }
    }

    public function config() {
        $json = $this->request('/config');
        if ($json) {
            return json_decode($json);
        } else {
            return [];
        }
    }

    public function files($alias) {
        $json = $this->request('/files', ['alias' => $alias]);
        if ($json) {
            return json_decode($json);
        } else {
            return [];
        }
    }

    public function clearCache() {
        $cache = new Cache(JSFactory::getConfig()->cache_path . $this->cache_dir);
        $cache->clearAll();
    }

    protected function request($uri, $params = []) {
        $cache_key = $this->getCacheKey($uri, $params);
        $cache = new Cache(JSFactory::getConfig()->cache_path . $this->cache_dir);
        if ($this->use_cache && $cache_body = $cache->get($cache_key)) {
            return $cache_body;
        }
        $client = new Client(['base_uri' => $this->catalog_addon_api]);
        if (empty($params)) {
            $res = $client->request('GET', $uri, ['headers' => ["User-Agent: JoomShopping"]]);
        } else {
            $res = $client->request('POST', $uri, ['form_params' => $params, 'headers' => ["User-Agent: JoomShopping"]]);
        }
        $body = $res->getBody();
        if ($this->use_cache) {
            $cache->set($cache_key, $body);
        }
        return $body;
    }

    protected function getCacheKey($uri, $params = []){
        $name = trim($uri, '/')."_".md5(json_encode($params));
        return $name;
    }

}