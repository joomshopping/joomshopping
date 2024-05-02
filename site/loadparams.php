<?php
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;

/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$app = Factory::getApplication();
$session = Factory::getSession();
$ajax = $app->input->getInt('ajax');

Helper::initLoadJoomshoppingLanguageFile();
$jshopConfig = JSFactory::getConfig();
Helper::setPrevSelLang($jshopConfig->getLang());
if ($app->input->getInt('id_currency')){
	Helper::reloadPriceJoomshoppingNewCurrency($app->input->getVar('back'));
}

$user = Factory::getUser();

$js_update_all_price = $session->get('js_update_all_price');
$js_prev_user_id = $session->get('js_prev_user_id');
if ($js_update_all_price || ($js_prev_user_id!=$user->id)){
    Helper::updateAllprices();
    $session->set('js_update_all_price', 0);
}
$session->set("js_prev_user_id", $user->id);

if (!$ajax){
    Helper::installNewLanguages();    
    if (Factory::getDocument()->getType()=="html"){
        JSFactory::loadCssFiles();
        JSFactory::loadJsFiles();
    }
}else{
    //header for ajax
    header('Content-Type: text/html;charset=UTF-8');
}
$app->triggerEvent('onAfterLoadShopParams', array());