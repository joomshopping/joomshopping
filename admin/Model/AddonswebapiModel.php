<?php
/**
* @version      5.10.0 17.10.2026
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model;

use Exception;
use Joomla\CMS\Factory;
use Joomla\Filesystem\Path;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\Component\Jshopping\Site\Lib\Cache;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Lib\HttpClientLite\Client;
defined('_JEXEC') or die();

class AddonswebapiModel extends BaseadminModel{

    private $catalog_addon_api = "https://www.webdesigner-profi.de/joomla-webdesign/shop/api";
    private $cache_dir = 'catalog_addon';
    public $use_cache = 1;
    protected $cacheTimeSec = 12 * 3600;

    public function useCache($use_cache) {
        $this->use_cache = $use_cache;
    }

    public function setCacheTime(int $sec): void
    {
        $this->cacheTimeSec = $sec;
    }

    public function products($typeshop = 'standart') {
        $json = $this->request('/products?typeshop='.urlencode($typeshop));
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

    public function getMyPaidProducts($domain, $authKey) {
        $headers = ['Authorization: Bearer '.$authKey];
        $json = $this->request('/getmypaidproducts', ['domain' => $domain], $headers);
        return json_decode($json);
    }

    public function getAddonKey($alias, $domain, $authKey) {
		$headers = ['Authorization: Bearer '.$authKey];
        $json = $this->request('/getproduckey', ['alias' => $alias, 'domain' => $domain], $headers);
        return json_decode($json);
    }

    /**
     * Downloads the addon package (free and paid - one request, the API decides who may get it).
     * @return string path to the zip in tmp
     * @throws Exception
     */
    public function downloadAddon($product_id, $domain, $authKey = '', $typeshop = 'standart') {
        $headers = ["User-Agent: JoomShopping"];
        if ($authKey) {
            $headers[] = 'Authorization: Bearer '.$authKey;
        }
        $client = new Client(['base_uri' => $this->catalog_addon_api, 'timeout' => 120]);
        $res = $client->request('POST', '/download', ['form_params' => ['product_id' => $product_id, 'domain' => $domain, 'typeshop' => $typeshop], 'headers' => $headers, 'allow_redirects' => true]);
        $info = $res->getInfo();
        if ($res->getStatusCode() != 200 || strpos($info['content_type'] ?? '', 'json') !== false || $res->getBody() === '') {
            $data = json_decode($res->getBody());
            $error = $data->error ?? 'Error download addon ('.$res->getStatusCode().')';
            Helper::saveToLog('addons.log', 'downloadAddon ('.$product_id.'): '.$error);
            throw new Exception($error, $res->getStatusCode());
        }
        $file = Path::clean(Factory::getConfig()->get('tmp_path').'/addon_'.(int)$product_id.'_'.uniqid().'.zip');
        if (file_put_contents($file, $res->getBody()) === false) {
            throw new Exception('Error save file '.$file);
        }
        return $file;
    }

    public function clearCache() {
        $cache = new Cache(JSFactory::getConfig()->cache_path . $this->cache_dir);
        $cache->clearAll();
    }

    protected function request($uri, $params = [], $headers = []) {
        $cache_key = $this->getCacheKey($uri, array_merge($params, $headers));
        $cache = new Cache(JSFactory::getConfig()->cache_path . $this->cache_dir);
        $cache->setCacheTime($this->cacheTimeSec);
        if ($this->use_cache && $cache_body = $cache->get($cache_key)) {
            return $cache_body;
        }
        $client = new Client(['base_uri' => $this->catalog_addon_api]);
        $headers = array_merge($headers, ["User-Agent: JoomShopping"]);
        if (empty($params)) {
            $res = $client->request('GET', $uri, ['headers' => $headers]);
        } else {
            $res = $client->request('POST', $uri, ['form_params' => $params, 'headers' => $headers]);
        }
        $body = $res->getBody();
        if ($this->use_cache && $res->getStatusCode() == 200) {
            $cache->set($cache_key, $body);
        }
        return $body;
    }

    protected function getCacheKey($uri, $params = []){
        $name = preg_replace('/[^a-zA-Z0-9_\-]/', '_', trim($uri, '/'))."_".md5(json_encode($params));
        return $name;
    }

}