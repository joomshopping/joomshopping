<?php
/**
* @version      5.6.2 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Jshopping\Site\Helper\SelectOptions;

defined('_JEXEC') or die();

class CategoriesController extends BaseadminController{
    
    protected $modelSaveItemFileName = 'category_image';
    protected $urlEditParamId = 'category_id';
    protected $checkToken = array('save' => 1, 'remove' => 0);
    
    public function init(){
        HelperAdmin::checkAccessController("categories");
        HelperAdmin::addSubmenu("categories");
    }
    
    function display($cachable = false, $urlparams = false){
        $app = Factory::getApplication();
        
        $_categories = JSFactory::getModel("Categories");
        
        $context = "jshopping.list.admin.category";
        $limit = $app->getUserStateFromRequest($context.'limit', 'limit', $app->getCfg('list_limit'), 'int' );
        $limitstart = $app->getUserStateFromRequest($context.'limitstart', 'limitstart', 0, 'int' );
        $filter_order = $app->getUserStateFromRequest($context.'filter_order', 'filter_order', "ordering", 'cmd');
        $filter_order_Dir = $app->getUserStateFromRequest($context.'filter_order_Dir', 'filter_order_Dir', "asc", 'cmd');
		$text_search = $app->getUserStateFromRequest($context.'text_search', 'text_search', '');
		
		$filter = array("text_search" => $text_search);
		
        $categories = $_categories->getTreeAllCategories($filter, $filter_order, $filter_order_Dir);
        $total = count($categories);
        
        $pagination = new Pagination($total, $limitstart, $limit);
        
        $countproducts = $_categories->getAllCatCountProducts();
        $categories = array_slice($categories, $pagination->limitstart, $pagination->limit);

        foreach ($categories as $category) {
            $category->tmp_html_col_after_title = "";
        }
        
        $view = $this->getView("category", 'html');
        $view->setLayout("list");
        $view->set('categories', $categories);
        $view->set('countproducts', $countproducts);
        $view->set('pagination', $pagination);
        $view->set('filter_order', $filter_order);
        $view->set('filter_order_Dir', $filter_order_Dir);
		$view->set('text_search', $text_search);

        $view->tmp_html_start = "";
        $view->tmp_html_filter = "";
        $view->tmp_html_col_after_title = "";
        $view->tmp_html_col_before_td_foot = "";
        $view->tmp_html_col_after_td_foot = "";
        $view->tmp_html_end = "";
        Factory::getApplication()->triggerEvent('onBeforeDisplayListCategoryView', array(&$view));
        $view->displayList();
    }
    
    function edit(){
        Factory::getApplication()->input->set('hidemainmenu', true);
        $cid = $this->input->getInt("category_id");
        $category = JSFactory::getTable("Category");
        $category->load($cid);
        
        $_lang = JSFactory::getModel("languages");
        $languages = $_lang->getAllLanguages(1);
        $multilang = count($languages)>1;

        OutputFilter::objectHTMLSafe($category, ENT_QUOTES);

        if ($cid){
            $parentid = $category->category_parent_id;
            $rows = JSFactory::getModel("categories")->_getAllCategoriesLevel($category->category_parent_id, $category->ordering);
        } else {
            $category->category_publish = 1;
            $parentid = $this->input->getInt("catid");
            $rows = JSFactory::getModel("categories")->_getAllCategoriesLevel($parentid);
        }
        
        $categories = SelectOptions::getCategories(Text::_('JSHOP_TOP_LEVEL'));
        $lists['templates'] = HelperAdmin::getTemplates('category', $category->category_template);
        $lists['onelevel'] = $rows;
        $lists['treecategories'] = HTMLHelper::_('select.genericlist', $categories, 'category_parent_id','class="inputbox form-select" onchange = "jshopAdmin.changeCategory()"','category_id','name', $parentid);
        $lists['parentid'] = $parentid;
        $lists['access'] = HTMLHelper::_('select.genericlist', SelectOptions::getAccessGroups(), 'access','class = "inputbox form-select"','id','title', $category->access);
        
        $sortd = SelectOptions::getSortingDirection(1);
        $lists['product_sorting_direction'] = HTMLHelper::_('select.genericlist', $sortd, 'product_sorting_direction','class="form-select"','id','value', $category->product_sorting_direction ?? -1);

        $product_sorting_options = SelectOptions::getProductSorting(1);
        $lists['product_sorting'] = HTMLHelper::_('select.genericlist', $product_sorting_options, "product_sorting", 'class="form-select"', 'id', 'name', $category->product_sorting ?? 0);

        $view = $this->getView("category", 'html');
        $view->setLayout("edit");
        $view->set('config', JSFactory::getConfig());
        $view->set('category', $category);
        $view->set('lists', $lists);
        $view->set('languages', $languages);
        $view->set('multilang', $multilang);
        $view->set('etemplatevar', '');
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeEditCategories', array(&$view));
        $view->displayEdit();
    }
    
    protected function getMessageSaveOk($post){
        return $post['category_id'] ? Text::_('JSHOP_CATEGORY_SUCC_UPDATE') : Text::_('JSHOP_CATEGORY_SUCC_ADDED');
    }
    
    function sorting_cats_html(){
        $catid = $this->input->getVar('catid');
        print JSFactory::getModel("categories")->_getAllCategoriesLevel($catid);
    die();    
    }
    
    function delete_foto(){
        $catid = $this->input->getInt("catid");
        $this->getAdminModel()->deleteFoto($catid);
        die();
    }
    
    protected function getSaveOrderWhere(){
        $category_parent_id = $this->input->getInt("category_parent_id");
        return 'category_parent_id='.(int)$category_parent_id;
    }
}