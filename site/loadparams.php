<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$app = \JFactory::getApplication();
$session = \JFactory::getSession();
$ajax = $app->input->getInt('ajax');

\JSHelper::initLoadJoomshoppingLanguageFile();
$jshopConfig = \JSFactory::getConfig();
\JSHelper::setPrevSelLang($jshopConfig->getLang());
if ($app->input->getInt('id_currency')){
	\JSHelper::reloadPriceJoomshoppingNewCurrency($app->input->getVar('back'));
}

$user = \JFactory::getUser();

$js_update_all_price = $session->get('js_update_all_price');
$js_prev_user_id = $session->get('js_prev_user_id');
if ($js_update_all_price || ($js_prev_user_id!=$user->id)){
    \JSHelper::updateAllprices();
    $session->set('js_update_all_price', 0);
}
$session->set("js_prev_user_id", $user->id);

if (!$ajax){
    \JSHelper::installNewLanguages();    
    if (\JFactory::getDocument()->getType()=="html"){
        \JSFactory::loadCssFiles();
        \JSFactory::loadJsFiles();
    }
}else{
    //header for ajax
    header('Content-Type: text/html;charset=UTF-8');
}
$app->triggerEvent('onAfterLoadShopParams', array());