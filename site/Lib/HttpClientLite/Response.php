<?php
/**
* @version      5.6.0 06.03.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Lib\HttpClientLite;

defined('_JEXEC') or die();

class Response
{
    private $body = '';
    private $info = [];

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getJsonDecode(){
        return json_decode($this->body);
    }

    public function setInfo(array $info): void
    {
        $this->info = $info;
    }

    public function getInfo(): array
    {
        return $this->info;
    }

    public function getStatusCode(): int
    {
        return $this->info['http_code'] ?? 0;
    }
}