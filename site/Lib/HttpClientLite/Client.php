<?php
/**
 * @version      5.6.0 08.04.2024
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */

namespace Joomla\Component\Jshopping\Site\Lib\HttpClientLite;

defined('_JEXEC') or die();

class Client
{
    protected $config = ['timeout' => 5, 'base_uri' => ''];
    public $debug = 0;

    public function __construct(array $config = [])
    {
        $this->setConfig($config);
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setConfig(array $config = []): void
    {
        foreach ($config as $k => $v) {
            $this->config[$k] = $v;
        }
    }

    public function request(string $method, string $uri = '', array $options = []): Response
    {
        if ($this->config['base_uri']) {
            $url = $this->config['base_uri'] . $uri;
        } else {
            $url = $uri;
        }
        if (isset($options['url_params'])) {
            $url .= '?' . http_build_query($options['url_params']);
        }
        $curl = curl_init($url);
        if (isset($options['headers'])) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $options['headers']);
            if ($this->debug) {
                print implode("\n", $options['headers'])."\n\n";
            }
        }

        $method = strtoupper($method);
        if ($method == "PUT") {
            curl_setopt($curl, CURLOPT_PUT, true);
        } elseif ($method == "POST") {
            curl_setopt($curl, CURLOPT_POST, true);
        } elseif ($method != "GET") {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        }
        if ($this->debug) {
            print $method." ".$url."\n";
        }
        if (isset($options['form_params'])) {
            $post_fields = http_build_query($options['form_params']);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields);
            if ($this->debug) {
                print $post_fields."\n";
            }
        }
        if (isset($options['json'])) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $options['json']);
            if ($this->debug) {
                print $options['json']."\n";
            }
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->config['timeout']);
        if (isset($options['http_version'])) {
            curl_setopt($curl, CURLOPT_HTTP_VERSION, $options['http_version']);
        }
        $body = curl_exec($curl);
        if ($body === false) {
            $body = '';
        }
        $info = curl_getinfo($curl);
        curl_close($curl);

        $response = new Response();
        $response->setBody($body);
        $response->setInfo($info);
        return $response;
    }
}
