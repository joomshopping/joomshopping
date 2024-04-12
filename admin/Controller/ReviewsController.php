<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
use Joomla\Component\Jshopping\Site\Helper\SelectOptions;
defined('_JEXEC') or die;

class ReviewsController extends BaseadminController{

    function init(){
        \JSHelperAdmin::checkAccessController("reviews");
        \JSHelperAdmin::addSubmenu("other");
    }

    public function getUrlEditItem($id = 0){
        return "index.php?option=com_jshopping&controller=".$this->getNameController()."&task=edit&cid[]=".$id;
    }
    
    function display($cachable = false, $urlparams = false){
        $jshopConfig = \JSFactory::getConfig();
        $app = \JFactory::getApplication();
        $id_vendor_cuser = \JSHelperAdmin::getIdVendorForCUser();
        $reviews_model = \JSFactory::getModel("reviews");
        $context = "jshoping.list.admin.reviews";
        $limit = $app->getUserStateFromRequest( $context.'limit', 'limit', $app->getCfg('list_limit'), 'int' );
        $limitstart = $app->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0, 'int' );
        $category_id = $app->getUserStateFromRequest( $context.'category_id', 'category_id', 0, 'int' );
        $text_search = $app->getUserStateFromRequest( $context.'text_search', 'text_search', '');
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "pr_rew.review_id", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "desc", 'cmd');

        if ($category_id){
            $product_id = $app->getUserStateFromRequest( $context.'product_id', 'product_id', 0, 'int' );
        } else {
            $product_id = null;
        }

        $products_select = "";

        if ($category_id){
            $prod_filter = array("category_id"=>$category_id);
            if ($id_vendor_cuser){
                $prod_filter['vendor_id'] = $id_vendor_cuser;
            }
            $products = SelectOptions::getProducts(1, 0, array('filter'=>$prod_filter, 'limitstart'=>0, 'limit'=>100));
            $products_select = \JHTML::_('select.genericlist', $products, 'product_id', 'class="form-select" onchange="document.adminForm.submit();" ', 'product_id', 'name', $product_id);
        }

        $total = $reviews_model->getAllReviews($category_id, $product_id, NULL, NULL, $text_search, "count", $id_vendor_cuser, $filter_order, $filter_order_Dir);

        jimport('joomla.html.pagination');
        $pagination = new \JPagination($total, $limitstart, $limit);

        $reviews = $reviews_model->getAllReviews($category_id, $product_id, $pagination->limitstart, $pagination->limit, $text_search, "list", $id_vendor_cuser, $filter_order, $filter_order_Dir);

        $categories = \JHTML::_('select.genericlist', SelectOptions::getCategories(\JText::_('JSHOP_SELECT_CATEGORY')), 'category_id', 'class="form-select" onchange="document.adminForm.submit();"', 'category_id', 'name', $category_id);

        foreach ($reviews as $review) {
            $review->_tmp_cols_14 = "";
        }

        $view = $this->getView("comments", 'html');
        $view->setLayout("list");
        $view->set('categories', $categories);
        $view->set('reviews', $reviews);
        $view->set('limit', $limit);
        $view->set('limitstart', $limitstart);
        $view->set('text_search', $text_search);
        $view->set('pagination', $pagination);
        $view->set('products_select', $products_select);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
        $view->set('config', $jshopConfig);
        $view->tmp_html_start = "";
        $view->tmp_html_filter = "";
        $view->tmp_html_filter_end = "";
        $view->_tmp_cols_14 = "";
        $view->tmp_html_end = "";
        $view->sidebar = \JHTMLSidebar::render();
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayReviews', array(&$view));
        $view->displayList();
     }

    function edit(){
        \JFactory::getApplication()->input->set('hidemainmenu', true);
        $jshopConfig = \JSFactory::getConfig();
        $reviews_model = \JSFactory::getModel("reviews");
        $cid = $this->input->getVar('cid');
        $review = $reviews_model->getReview($cid[0]);
        $mark = \JHTML::_('select.genericlist', SelectOptions::getReviewMarks(), 'mark', 'class = "inputbox form-control"', 'value', 'text', $review->mark);
        \JFilterOutput::objectHTMLSafe($review, ENT_QUOTES);

        $view = $this->getView("comments", 'html');
        $view->setLayout("edit");
        if ($this->getTask()=='edit'){
            $view->set('edit', 1);
        }
        $view->set('review', $review);
        $view->set('mark', $mark);
        $view->set('config', $jshopConfig);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditReviews', array(&$view));
        $view->displayEdit();
    }

}