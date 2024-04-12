<?php

/**
 * @version      5.0.0 15.09.2018
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */

namespace Joomla\Component\Jshopping\Site\Controller;

defined('_JEXEC') or die();

class BaseController extends \Joomla\CMS\MVC\Controller\BaseController {

    public function __construct($config = array(), \Joomla\CMS\MVC\Factory\MVCFactoryInterface $factory = null, $app = null, $input = null) {
        parent::__construct($config, $factory, $app, $input);
        $this->init();
    }

    public function init(){
    }

    public function getView($name = '', $type = '', $prefix = '', $config = array()) {
        $jshopConfig = \JSFactory::getConfig();
        if ($type == '') {
            $type = \JSHelper::getDocumentType();
        }
        if (empty($config)) {
            $config = array("template_path" => $jshopConfig->template_path . $jshopConfig->template . "/" . $name);
        }
        return parent::getView($name, $type, $prefix, $config);
    }

    public function getViewAddon($name = '', $type = '', $prefix = '', $viewName = 'addons') {
        $config = array("template_path" => \JSFactory::getConfig()->template_path . 'addons/' . $name);
        return $this->getView($viewName, $type, $prefix, $config);
    }
    
    public function execute($task){
        $res = parent::execute($task);
        \JSHelper::displayTextJSC();
        return $res;
    }

}
