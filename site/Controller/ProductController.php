<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Controller;
use Joomla\Component\Jshopping\Site\Helper\Metadata;
use Joomla\Component\Jshopping\Site\Helper\Request;
defined('_JEXEC') or die();

class ProductController extends BaseController{
    
    public function init(){
        \JPluginHelper::importPlugin('jshoppingproducts');
        $obj = $this;
        \JFactory::getApplication()->triggerEvent('onConstructJshoppingControllerProduct', array(&$obj));
    }

    function display($cachable = false, $urlparams = false){
        $jshopConfig = \JSFactory::getConfig();
        $user = \JFactory::getUser();
		$dispatcher = \JFactory::getApplication();
		$model = \JSFactory::getModel('productShop', 'Site');
		
		$ajax = $this->input->getInt('ajax');
        $tmpl = $this->input->getVar("tmpl");
		$product_id = (int)$this->input->getInt('product_id');
        $category_id = (int)$this->input->getInt('category_id');
        $attr = $this->input->getVar("attr");
		
		\JSFactory::loadJsFilesLightBox();
		
        if ($tmpl!="component"){
			$model->storeEndPageBuy();
        }
		
        $back_value = $model->getBackValue($product_id, $attr);

        $dispatcher->triggerEvent('onBeforeLoadProduct', array(&$product_id, &$category_id, &$back_value));
        $dispatcher->triggerEvent('onBeforeLoadProductList', array());

        $product = \JSFactory::getTable('product');
        $product->load($product_id);
        $product->_tmp_var_price_ext = "";
        $product->_tmp_var_old_price_ext = "";
        $product->_tmp_var_bottom_price = "";
        $product->_tmp_var_bottom_allprices = "";
		
		$category = \JSFactory::getTable('category');
        $category->load($category_id);
        $category->name = $category->getName();
		
		$model->setProduct($product);
		
        $listcategory = $model->getCategories(1);
		
		$model->prepareView($back_value);
		$model->clearBackValue();
		
		$attributes = $model->getAttributes();
        $all_attr_values = $model->getAllAttrValues();

		if (!$product->checkView($category, $user, $category_id, $listcategory)){
            \JSError::raiseError(404, \JText::_('JSHOP_PAGE_NOT_FOUND'));
            return;
        }
        
        Metadata::product($category, $product);
        
        $product->hit();
        		
		$allow_review = $model->getAllowReview();
		$text_review = $model->getTextReview();

        $hide_buy = $model->getHideBuy();        
        $available = $model->getTextAvailable();
		$default_count_product = $model->getDefaultCountProduct($back_value);
        $displaybuttons = $model->getDisplayButtonsStyle();
        $product_images = $product->getImages();
        $product_videos = $product->getVideos();
        $product_demofiles = $product->getDemoFiles();
        if ($jshopConfig->admin_show_product_related){
            $productlist = \JSFactory::getModel('related', 'Site\\Productlist');
            $productlist->setTable($product);
            $listProductRelated = $productlist->getLoadProducts();
        }else{
            $listProductRelated = [];
        }
		$dispatcher->triggerEvent('onBeforeDisplayProductList', array(&$listProductRelated));
        
        $view = $this->getView("product");
        $view->setLayout("product_".$product->product_template);
        $view->_tmp_product_html_start = "";
        $view->_tmp_product_html_before_image = "";
        $view->_tmp_product_html_body_image = "";
        $view->_tmp_product_html_after_image = "";
        $view->_tmp_product_html_before_image_thumb = "";
        $view->_tmp_product_html_after_image_thumb = "";
        $view->_tmp_product_html_after_video = "";
        $view->_tmp_product_html_before_atributes = "";
        $view->_tmp_product_html_after_atributes = "";
        $view->_tmp_product_html_after_freeatributes = "";
        $view->_tmp_product_html_before_price = "";
        $view->_tmp_product_html_after_ef = "";
        $view->_tmp_product_html_before_buttons = "";
        $view->_tmp_qty_unit = "";
        $view->_tmp_product_html_buttons = "";
        $view->_tmp_product_html_after_buttons = "";
        $view->_tmp_product_html_before_demofiles = "";
        $view->_tmp_product_html_before_review = "";
        $view->_tmp_product_html_before_related = "";
        $view->_tmp_product_html_end = "";
        $view->_tmp_product_review_before_submit = "";
        $view->_tmp_product_ext_js = "";
        $dispatcher->triggerEvent('onBeforeDisplayProduct', array(&$product, &$view, &$product_images, &$product_videos, &$product_demofiles) );
        $view->set('config', $jshopConfig);
        $view->set('image_path', $jshopConfig->live_path.'/images');
        $view->set('noimage', $jshopConfig->noimage);
        $view->set('image_product_path', $jshopConfig->image_product_live_path);
        $view->set('video_product_path', $jshopConfig->video_product_live_path);
        $view->set('video_image_preview_path', $jshopConfig->video_product_live_path);
        $view->set('product', $product);
        $view->set('category_id', $category_id);
        $view->set('images', $product_images);
        $view->set('videos', $product_videos);
        $view->set('demofiles', $product_demofiles);
        $view->set('attributes', $attributes);
        $view->set('all_attr_values', $all_attr_values);
        $view->set('related_prod', $listProductRelated);
        $view->set('path_to_image', $jshopConfig->live_path . 'images/');
        $view->set('live_path', \JURI::root());
        $view->set('enable_wishlist', $jshopConfig->enable_wishlist);
        $view->set('action', \JSHelper::SEFLink('index.php?option=com_jshopping&controller=cart&task=add',1));
        $view->set('urlupdateprice', \JSHelper::SEFLink('index.php?option=com_jshopping&controller=product&task=ajax_attrib_select_and_price&product_id='.$product_id.'&ajax=1',1,1));
        if ($allow_review){
			\JSFactory::loadJsFilesRating();
			$modelreviewlist = \JSFactory::getModel('productReviewList', 'Site');
			$modelreviewlist->setModel($product);
			$modelreviewlist->load();
			$review_list = $modelreviewlist->getList();
			$pagination = $modelreviewlist->getPagination();
			$pagenav = $pagination->getPagesLinks();
            $view->set('reviews', $review_list);
            $view->set('pagination', $pagenav);
			$view->set('pagination_obj', $pagination);
            $view->set('display_pagination', $pagenav!="");
        }
        $view->set('allow_review', $allow_review);
        $view->set('text_review', $text_review);
        $view->set('stars_count', floor($jshopConfig->max_mark / $jshopConfig->rating_starparts));
        $view->set('parts_count', $jshopConfig->rating_starparts);
        $view->set('user', $user);
        $view->set('shippinginfo', \JSHelper::SEFLink($jshopConfig->shippinginfourl,1));
        $view->set('hide_buy', $hide_buy);
        $view->set('available', $available);
        $view->set('default_count_product', $default_count_product);
        $view->set('folder_list_products', "list_products");
        $view->set('back_value', $back_value);
		$view->set('displaybuttons', $displaybuttons);
        $view->set('prod_qty_input_type', $jshopConfig->use_decimal_qty ? 'text' : 'number');

        $dispatcher->triggerEvent('onBeforeDisplayProductView', array(&$view));        
        $view->display();
        $dispatcher->triggerEvent('onAfterDisplayProduct', array(&$product));
        if ($ajax) die();
    }
    
    function getfile(){
        $id = $this->input->getInt('id'); 
        $oid = $this->input->getInt('oid');
        $hash = $this->input->getVar('hash');
        $rl = $this->input->getInt('rl');
		
		$model = \JSFactory::getModel('productDownload', 'Site');
		$model->setId($id);
		$model->setOid($oid);
		$model->setHash($hash);
		
		if ($rl==1){
            //fix for IE
            print "<script type='text/javascript'>location.href='".$model->getUrlDownload()."';</script>";
            die();
        }
		
		if (!$model->checkHash()){
			\JSError::raiseError(500, "Error download file");
            return 0;
		}
		if (!$model->checkOrderStatusPaid()){
            \JSError::raiseWarning(500, \JText::_('JSHOP_FOR_DOWNLOAD_ORDER_MUST_BE_PAID'));
            return 0;
        }
		if (!$model->checkUser()){
            \JSHelper::checkUserLogin();
        }
		if (!$model->checkTimeDownload()){
            \JSError::raiseWarning(500, \JText::_('JSHOP_TIME_DOWNLOADS_FILE_RESTRICTED'));
            return 0; 
        }
		if (!$model->checkFileId()){
			\JSError::raiseError(500, "Error download file");
            return 0;
		}
		if (!$model->checkNumberDownload()){
			\JSError::raiseWarning(500, \JText::_('JSHOP_NUMBER_DOWNLOADS_FILE_RESTRICTED'));
            return 0;
		}

        $name = $model->getFileName();
        if ($name==""){
            \JSError::raiseWarning('', "Error download file");
            return 0;
        }
        $file_name = $model->getFile($name);

		$model->storeStatDownloads();
        
        ob_end_clean();
        @set_time_limit(0);		
		$model->downloadFile($file_name);
        \JFactory::getApplication()->triggerEvent('onAfterDownloadProductFile', array($file_name, $id, $oid, $hash, $model));
        die();
    }
    
    function reviewsave(){
        \JSession::checkToken() or die('Invalid Token');

        $post = $this->input->post->getArray();
        $backlink = $this->input->getVar('back_link');
		
		$model = \JSFactory::getModel('productReview', 'Site');
		$model->setData($post);
		if (!$model->checkAllow()){
			\JSError::raiseWarning('', $model->getError());
            $this->setRedirect($backlink);
            return 0;
		}
		if (!$model->check()){
			\JSError::raiseWarning('', $model->getError());
            $this->setRedirect($backlink);
            return 0;
		}
		$model->save();
		
		$model->mailSend();
		
		if (\JSFactory::getConfig()->display_reviews_without_confirm){
            $this->setRedirect($backlink, \JText::_('JSHOP_YOUR_REVIEW_SAVE_DISPLAY'));
        }else{
            $this->setRedirect($backlink, \JText::_('JSHOP_YOUR_REVIEW_SAVE'));
        }
    }

	function ajax_attrib_select_and_price(){
		$request = $this->input->getArray();
        $product_id = $this->input->getInt('product_id');
        $change_attr = $this->input->getInt('change_attr');
		$qty = Request::getQuantity('qty', 1);
		$attribs = Request::getAttribute('attr');
        $freeattr = Request::getFreeAttribute('freeattr');
		
		$model = \JSFactory::getModel('productAjaxRequest', 'Site');
		$model->setData($product_id, $change_attr, $qty, $attribs, $freeattr, $request);
		
		header("Content-type: application/json; charset=utf-8");
		print $model->getProductDataJson();
		die();
	}

    function showmedia(){
        $jshopConfig = \JSFactory::getConfig();
        $media_id = $this->input->getInt('media_id');
        $file = \JSFactory::getTable('productfiles');
        $file->load($media_id);
        
        $view = $this->getView("product");
        $view->setLayout("playmedia");
        $view->set('config', $jshopConfig);
        $view->set('filename', $file->demo);
        $view->set('description', $file->demo_descr);
        $view->set('scripts_load', $scripts_load);
        $view->set('file_is_video', $file->fileDemoIsVideo());
		$view->set('file_is_audio', $file->fileDemoIsAudio());
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayProductShowMediaView', array(&$view) );
        $view->display(); 
        die();
    }
}