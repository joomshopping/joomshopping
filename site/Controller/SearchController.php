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
use Joomla\Component\Jshopping\Site\Helper\Selects;
defined('_JEXEC') or die();

class SearchController extends BaseController{
    
    public function init(){
        \JPluginHelper::importPlugin('jshoppingproducts');
        $obj = $this;
        \JFactory::getApplication()->triggerEvent('onConstructJshoppingControllerSearch', array(&$obj));
    }
    
    function display($cachable = false, $urlparams = false){
    	$jshopConfig = \JSFactory::getConfig();    	
        $Itemid = $this->input->getInt('Itemid');
		$category_id = $this->input->getInt('category_id');
		
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeLoadSearchForm', array());
        		
		Metadata::search();

        $characteristics = $this->load_tmpl_characteristics($category_id);

        $view = $this->getView("search");
        $view->setLayout("form");
		$view->set('characteristics', $characteristics);
        $view->set('config', $jshopConfig);
        $view->set('Itemid', $Itemid);
		$view->set('action', \JSHelper::SEFLink("index.php?option=com_jshopping&controller=search&task=result"));
        $view->_tmp_ext_search_html_start = "";
        $view->_tmp_ext_search_html_end = "";
        $dispatcher->triggerEvent('onBeforeDisplaySearchFormView', array(&$view) );
		$view->display();
    }
    
    function result(){        
        $jshopConfig = \JSFactory::getConfig();		

		\JSFactory::getModel('productShop', 'Site')->storeEndPages();

		Metadata::searchResult();
		
		$productlist = \JSFactory::getModel('search', 'Site\\Productlist');
        $productlist->load();
		
		$orderby = $productlist->getOrderBy();
        $image_sort_dir = $productlist->getImageSortDir();        
        $action = $productlist->getAction();
        $products = $productlist->getProducts();
        $pagination = $productlist->getPagination();
        $pagenav = $productlist->getPagenav();
		$total = $productlist->getTotal();
		$filters = $productlist->getFilters();
        $sorting_sel = $productlist->getHtmlSelectSorting();
        $product_count_sel = $productlist->getHtmlSelectCount();                
        $allow_review = $productlist->getAllowReview();
		$search = $filters['search'];
		
		if (!$total){
            $this->noresult($search);
            return 0;
        }

        $view = $this->getView("search");
        $view->setLayout("products");
        $view->set('search', $search);
        $view->set('total', $total);
        $view->set('config', $jshopConfig);
		$view->set('template_block_list_product', $productlist->getTmplBlockListProduct());
        $view->set('template_block_form_filter', $productlist->getTmplBlockFormFilter());
        $view->set('template_block_pagination', $productlist->getTmplBlockPagination());
        $view->set('path_image_sorting_dir', $jshopConfig->live_path.'images/'.$image_sort_dir);
        $view->set('filter_show', 0);
        $view->set('filter_show_category', 0);
        $view->set('filter_show_manufacturer', 0);
        $view->set('pagination', $pagenav);
		$view->set('pagination_obj', $pagination);
        $view->set('display_pagination', $pagenav!="");
        $view->set('product_count', $product_count_sel);
        $view->set('sorting', $sorting_sel);
        $view->set('action', $action);
        $view->set('orderby', $orderby);
        $view->set('count_product_to_row', $productlist->getCountProductsToRow());
        $view->set('rows', $products);
        $view->set('allow_review', $allow_review);
        $view->set('shippinginfo', \JSHelper::SEFLink($jshopConfig->shippinginfourl,1));
        $view->set('total', $productlist->getTotal());
        $view->_tmp_list_products_html_start = "";
        $view->_tmp_list_products_html_end  = "";
        \JFactory::getApplication()->triggerEvent('onBeforeDisplayProductListView', array(&$view, &$productlist));
        $view->display();
    }
    
    function get_html_characteristics(){        
        $category_id = $this->input->getInt("category_id");
        print $this->load_tmpl_characteristics($category_id);
		die();    
    }
	
	private function noresult($search){
		$view = $this->getView('search');
		$view->setLayout("noresult");
		$view->set('search', $search);
		$view->display();
	}
	
	private function load_tmpl_characteristics($category_id){
		$jshopConfig = \JSFactory::getConfig();		
		if ($jshopConfig->admin_show_product_extra_field){
            $dispatcher = \JFactory::getApplication();
            $characteristic_fields = \JSFactory::getAllProductExtraField();			
            $characteristic_fieldvalues = \JSFactory::getAllProductExtraFieldValueDetail();
            $characteristic_displayfields = \JSFactory::getDisplayFilterExtraFieldForCategory($category_id);

            $view = $this->getView("search");
            $view->setLayout("characteristics");
            $view->set('characteristic_fields', $characteristic_fields);
            $view->set('characteristic_fieldvalues', $characteristic_fieldvalues);
            $view->set('characteristic_displayfields', $characteristic_displayfields);
            $view->tmp_ext_search_html_characteristic_start = "";
            $view->tmp_ext_search_html_characteristic_end = "";
            $dispatcher->triggerEvent('onBeforeDisplaySearchHtmlCharacteristics', array(&$view));
            $html = $view->loadTemplate();
        }else{
			$html = '';
		}
		return $html;
	}
	
}