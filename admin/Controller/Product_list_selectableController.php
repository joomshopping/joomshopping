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
defined('_JEXEC') or die();
jimport('joomla.html.pagination');

class Product_List_SelectableController extends BaseadminController{
    
	function display($cachable = false, $urlparams = false){
        \JSHelperAdmin::checkAccessController("product_list_selectable");		
		$app = \JFactory::getApplication();
		$jshopConfig = \JSFactory::getConfig();
		$prodMdl = \JSFactory::getModel('Products');

		$context = "jshoping.list.admin.product";
		$limit = $app->getUserStateFromRequest($context.'limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int');

		if (isset($_GET['category_id']) && $_GET['category_id'] === "0"){
			$app->setUserState($context.'category_id', 0);
			$app->setUserState($context.'manufacturer_id', 0);
			$app->setUserState($context.'label_id', 0);
			$app->setUserState($context.'publish', 0);
			$app->setUserState($context.'text_search', '');
		}

		$category_id = $app->getUserStateFromRequest($context.'category_id', 'category_id', 0, 'int');
		$manufacturer_id = $app->getUserStateFromRequest($context.'manufacturer_id', 'manufacturer_id', 0, 'int');
		$label_id = $app->getUserStateFromRequest($context.'label_id', 'label_id', 0, 'int');
		$publish = $app->getUserStateFromRequest($context.'publish', 'publish', 0, 'int');
		$text_search = $app->getUserStateFromRequest($context.'text_search', 'text_search', '');
        $eName = $this->input->getVar('e_name');
		$jsfname = $this->input->getVar('jsfname');
        $eName = preg_replace('#[^A-Z0-9\-\_\[\]]#i', '', $eName);        
        if (!$jsfname) $jsfname = 'selectProductBehaviour';
		
		$filter = array("category_id" => $category_id,"manufacturer_id" => $manufacturer_id, "label_id" => $label_id,"publish" => $publish,"text_search" => $text_search);
		$total = $prodMdl->getCountAllProducts($filter);
		$pagination = new \JPagination($total, $limitstart, $limit);
		$rows = $prodMdl->getAllProducts($filter, $pagination->limitstart, $pagination->limit);

		$lists['treecategories'] = \JHTML::_('select.genericlist', SelectOptions::getCategories(), 'category_id', 'class="form-select" style="width: 150px;" onchange="document.adminForm.submit();"', 'category_id', 'name', $category_id);
		$lists['manufacturers'] = \JHTML::_('select.genericlist', SelectOptions::getManufacturers(), 'manufacturer_id', 'class="form-select" style="width: 220px;" onchange="document.adminForm.submit();"', 'manufacturer_id', 'name', $manufacturer_id);
		if ($jshopConfig->admin_show_product_labels){
			$lists['labels'] = \JHTML::_('select.genericlist', SelectOptions::getLabels(), 'label_id', 'class="form-select" style="width: 120px;" onchange="document.adminForm.submit();"','id','name', $label_id);
		}
		$lists['publish'] = \JHTML::_('select.genericlist', SelectOptions::getPublish(), 'publish', 'class="form-select" style="width: 120px;" onchange="document.adminForm.submit();"', 'id', 'name', $publish);

		foreach ($rows as $row) {
			$row->tmp_html_col_after_title = "";
		}
		
		$view = $this->getView('product_list', 'html');
        $view->setLayout("selectable");
		$view->set('rows', $rows);
		$view->set('lists', $lists);
		$view->set('category_id', $category_id);
		$view->set('manufacturer_id', $manufacturer_id);
		$view->set('pagination', $pagination);
		$view->set('text_search', $text_search);
        $view->set('config', $jshopConfig);        
		$view->set('eName', $eName);		
		$view->set('jsfname', $jsfname);
		$view->tmp_html_start = "";
		$view->tmp_html_filter = "";
		$view->show_vendor = "";
		$view->tmp_html_col_after_title = "";
		$view->tmp_html_col_before_td_foot = "";
		$view->tmp_html_col_after_td_foot = "";
		$view->tmp_html_end = "";
        $dispatcher = \JFactory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayProductListSelectable', array(&$view));
		$view->displaySelectable();
	}
}