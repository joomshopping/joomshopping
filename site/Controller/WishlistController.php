<?php
/**
* @version      5.6.0 18.12.2023
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Controller;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\Component\Jshopping\Site\Helper\Metadata;
defined('_JEXEC') or die();

class WishlistController extends BaseController{
    
    public function init(){
        PluginHelper::importPlugin('jshoppingcheckout');
        $obj = $this;
        Factory::getApplication()->triggerEvent('onConstructJshoppingControllerWishlist', array(&$obj));
    }

    function display($cachable = false, $urlparams = false){
        $this->view();
    }

    function view(){		
	    $jshopConfig = JSFactory::getConfig();        
        $ajax = $this->input->getInt('ajax');
		$dispatcher = Factory::getApplication();
		$cartpreview = JSFactory::getModel('cartPreview', 'Site');

		$cart = JSFactory::getModel('cart', 'Site')->init("wishlist", 1);
		$cart->deleteUnpublishedProducts();

		Metadata::wishlist();
		
		$cartpreview->setCart($cart);
		$cartpreview->setCheckoutStep(0);
        $shopurl = $cartpreview->getBackUrlShop();
        $products = $cartpreview->getProductsPrepare($cartpreview->getProducts());

        $view = $this->getView('cart');
        $view->setLayout("wishlist");
        $view->set('config', $jshopConfig);
		$view->set('products', $products);
		$view->set('image_product_path', $jshopConfig->image_product_live_path);
		$view->set('image_path', $jshopConfig->live_path);
		$view->set('no_image', $jshopConfig->noimage);
		$view->set('href_shop', $shopurl);
        $view->_tmp_html_before_buttons = "";
        $view->_tmp_html_after_buttons = "";
		$view->set('href_checkout', Helper::SEFLink('index.php?option=com_jshopping&controller=cart',1));
        $dispatcher->triggerEvent('onBeforeDisplayWishlistView', array(&$view));
		$view->display();
        if ($ajax) die();
    }

    function delete(){
        header("Cache-Control: no-cache, must-revalidate");
        $ajax = $this->input->getInt('ajax');
        $cart = JSFactory::getModel('cart', 'Site');
        $cart->load('wishlist');    
        $cart->delete($this->input->getInt('number_id'));
        if ($ajax){
            print Helper::getOkMessageJson($cart);
            die();
        }
        $this->setRedirect( Helper::SEFLink($cart->getUrlList(),1,1) );
    }

    function remove_to_cart(){
        header("Cache-Control: no-cache, must-revalidate");
        $ajax = $this->input->getInt('ajax');
        $number_id = $this->input->getInt('number_id');
		
        $cart = JSFactory::getModel('checkout', 'Site')->removeWishlistItemToCart($number_id);
		
        if ($ajax){
            print Helper::getOkMessageJson($cart);
            die();
        }
        if ($cart->remove_to_cart) {
            $this->setRedirect(Helper::SEFLink('index.php?option=com_jshopping&controller=cart', 1, 1));
        } else {
            $wishlist = JSFactory::getModel('cart', 'Site');
            $wishlist->load("wishlist");
            $prod = $wishlist->products[$number_id];
            $this->setRedirect(Helper::SEFLink('index.php?option=com_jshopping&controller=product&task=view&category_id='.$prod['category_id'].'&product_id='.$prod['product_id'], 1));
        }
    }
}