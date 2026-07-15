<?php
/**
* @version      5.6.3 10.05.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\Controller;

use Exception;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Response\JsonResponse;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;
use Joomla\Component\Jshopping\Site\Helper\Helper;
use Joomla\Component\Jshopping\Site\Helper\SelectOptions;

defined('_JEXEC') or die();

class AddonsController extends BaseadminController{
    
    public function init(){
        HelperAdmin::checkAccessController("addons");
        HelperAdmin::addSubmenu("other");
    }

    public function display($cachable = false, $urlparams = false){
        $app = Factory::getApplication();
        $context = "jshoping.list.admin.addons";
        $ifilter = $app->getUserStateFromRequest($context.'filter', 'filter', [], 'array');
        $filter = [];
        if ($ifilter['text_search'] ?? '') {
            $filter['text_search'] = $ifilter['text_search'];
        }
        if ($ifilter['publish'] ?? 0) {
            $filter['publish'] = $ifilter['publish'] % 2;
        }

        $addons = JSFactory::getModel("addons");
        $back = "index.php?option=com_jshopping&controller=addons";
        $domain = Helper::getClearHost();
        $rows = $addons->getList(1, $filter, $domain, $back);
        $back64 = base64_encode($back);
        $filterinput = [];
        $filterinput['publish'] = HTMLHelper::_('select.genericlist', SelectOptions::getPublish(), 'filter[publish]', 'class="form-select" onchange="document.adminForm.submit();"', 'id', 'name', $ifilter['publish'] ?? 0);

        $view = $this->getView("addons", 'html');
        $view->setLayout("list");
        $view->rows = $rows;
        $view->back64 = $back64;
        $view->config = JSFactory::getConfig();
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $view->ifilter = $ifilter;
        $view->filterinput = $filterinput;

        $dispatcher = Factory::getApplication();
        $dispatcher->triggerEvent('onBeforeDisplayAddons', array(&$view));
        $view->displayList();
    }
    
    public function edit(){
        $this->input->set('hidemainmenu', true);
        $id = $this->input->getVar("id");
        $dispatcher = Factory::getApplication();
        $row = JSFactory::getTable('addon');
        $row->load($id);
        $config_file_patch = JPATH_COMPONENT_SITE."/addons/".$row->alias."/config.tmpl.php";
        $config_file_exist = file_exists($config_file_patch);

        $view = $this->getView("addons", 'html');
        $view->setLayout("edit");
        $view->set('row', $row);
        $view->set('params', $row->getParams());
        $view->set('config_file_patch', $config_file_patch);
        $view->set('config_file_exist', $config_file_exist);
        $view->tmp_html_start = "";
        $view->tmp_html_end = "";
        $dispatcher->triggerEvent('onBeforeEditAddons', array(&$view));
        $view->displayEdit();
    }
    
    public function save(){
		$post = $this->input->post->getArray();
        $params = $this->input->post->get('params', null, 'RAW');
        if (isset($params)) {
            $post['params'] = $params;
        }
	 	if (isset($post['f-id'])){
	    	$post['id'] = $post['f-id'];
        	unset($post['f-id']);
    	}
        JSFactory::getModel("addons")->save($post);
        if ($this->getTask()=='apply') {
            $this->setRedirect("index.php?option=com_jshopping&controller=addons&task=edit&id=".$post['id']);
        } else {
            $this->setRedirect("index.php?option=com_jshopping&controller=addons");
        }
    }
    
    public function info(){
        $id = $this->input->getVar("id");
        
        $dispatcher = Factory::getApplication();
        $row = JSFactory::getTable('addon');
        $row->load($id);
        $file_patch = JPATH_COMPONENT_SITE."/addons/".$row->alias."/info.tmpl.php";
        $file_exist = file_exists($file_patch);

        $view = $this->getView("addons", 'html');
        $view->setLayout("info");
        $view->set('row', $row);
        $view->set('params', $row->getParams());
        $view->set('file_patch', $file_patch);
        $view->set('file_exist', $file_exist);
        $dispatcher->triggerEvent('onBeforeInfoAddons', array(&$view));
        $view->displayInfo();
    }
    
    public function version(){
        $id = $this->input->getVar("id");
        
        $dispatcher = Factory::getApplication();
        $row = JSFactory::getTable('addon');
        $row->load($id);
        $file_patch = JPATH_COMPONENT_SITE."/addons/".$row->alias."/version.tmpl.php";
        $file_exist = file_exists($file_patch);

        $view = $this->getView("addons", 'html');
        $view->setLayout("info");
        $view->set('row', $row);
        $view->set('params', $row->getParams());
        $view->set('file_patch', $file_patch);
        $view->set('file_exist', $file_exist);
        $dispatcher->triggerEvent('onBeforeVersionAddons', array(&$view));
        $view->displayVersion();
    }

    public function help(){
        $view = $this->getView("addons", 'html');
        $view->setLayout("help");
        $view->displayHelp();
    }

    public function config(){
        $jshopConfig = JSFactory::getConfig();
        $app = Factory::getApplication();
        $app->input->set('hidemainmenu', true);
		$id = $this->input->getInt("id");
		$row = JSFactory::getTable('addon');
		$row->load($id);
        $config = $row->getConfig();
        $def_folder_view = 'components/com_jshopping/templates/addons/'.$row->alias;
        $def_folder_js   = 'components/com_jshopping/js/addons';
        $def_folder_css  = 'components/com_jshopping/css/addons';

        $def_overrides_view = 'templates/{YOUR_JOOMLA_TEMPLATE}/html/com_jshopping/addons/'.$row->alias;
        $def_overrides_js   = 'templates/{YOUR_JOOMLA_TEMPLATE}/js/addons';
        $def_overrides_css  = 'templates/{YOUR_JOOMLA_TEMPLATE}/css/addons';

        $debug_options = [0 => Text::_('JNo'), 1 => Text::_('JYES')." L1", 2 => Text::_('JYES')." L2", 3 => Text::_('JYES')." L3"];
        $debug_select = HTMLHelper::_('select.genericlist', $debug_options, 'config[debug]','class="inputbox form-select"','id','name', $config['debug'] ?? 0);

        $tmp_vars = $config['tmp_vars'] ?? [];

        $jsPath = JPATH_SITE . '/components/com_jshopping/';
        $has_folder_view = is_dir($jsPath . 'templates/addons/' . $row->alias);
        $has_file_js     = is_file($jsPath . 'js/addons/' . $row->alias . '.js');
        $has_file_css    = is_file($jsPath . 'css/addons/' . $row->alias . '.css');
        $has_overrides   = $has_folder_view || $has_file_js || $has_file_css;

        $wa = JSFactory::getWebAssetManager();
        $wap = $jshopConfig->getWebAssetParams('script', 'com.jshopping.admin.addonoverride');
        $wa->registerAndUseScript('com.jshopping.admin.addonoverride', $jshopConfig->live_admin_path.'js/addonoverride.js', $wap['options'], $wap['attributes'], $wap['dependencies']);

		$view = $this->getView("addons", 'html');
        $view->setLayout("config");
        $view->row = $row;
        $view->config = $config;
        $view->def_folder_view = $def_folder_view;
        $view->def_folder_js = $def_folder_js;
        $view->def_folder_css = $def_folder_css;
        $view->def_overrides_view = $def_overrides_view;
        $view->def_overrides_js = $def_overrides_js;
        $view->def_overrides_css = $def_overrides_css;
        $view->debug_select = $debug_select;
        $view->tmp_vars = $tmp_vars;
        $view->has_folder_view = $has_folder_view;
        $view->has_file_js = $has_file_js;
        $view->has_file_css = $has_file_css;
        $view->has_overrides = $has_overrides;
        $app->triggerEvent('onBeforeConfigAddons', array(&$view));
		$view->displayConfig();
	}

    public function saveconfig() {
        $post = $this->input->post->getArray(array(), null, 'RAW');
	 	if (isset($post['f-id'])){
	    	$post['id'] = $post['f-id'];
        	unset($post['f-id']);
    	}
        JSFactory::getModel("addons")->saveconfig($post);
        $this->setRedirect("index.php?option=com_jshopping&controller=addons");
    }

    public function back(){
        $this->setRedirect("index.php?option=com_jshopping&controller=addons");
    }

    public function override() {
        $app = Factory::getApplication();
        $input = $app->input;
        $id           = $input->getInt('id');
        $templateName = $input->getString('template_name', '');
        $templateId   = $input->getInt('template_id', 0);

        $row = JSFactory::getTable('addon');
        $row->load($id);
        $config = $row->getConfig();

        $model     = JSFactory::getModel('addonsoverride');
        $templates = $model->getTemplates();

        $selectedClientId = 0;
        if (!$templateName) {
            $default          = $model->getDefaultTemplate();
            $templateName     = $default ? $default->template : '';
            $templateId       = $default ? $default->id : 0;
            $selectedClientId = $default ? (int) $default->client_id : 0;
        } else {
            foreach ($templates as $t) {
                if ($t->id === $templateId) {
                    $selectedClientId = (int) $t->client_id;
                    break;
                }
            }
        }
        try {
            $data = [
                'alias'     => $row->alias,
                'sources'   => $model->getSourceFiles($row->alias),
                'templates' => $templates,
                'overrides' => $model->getOverrideFiles($row->alias, $templateId, $templateName, $config, $selectedClientId),
                'paths'     => $model->getPaths($row->alias, $templateName, $config, $selectedClientId),
                'config'    => [
                    'folder_overrides_view' => $config['folder_overrides_view'] ?? '',
                    'folder_overrides_js'   => $config['folder_overrides_js'] ?? '',
                    'folder_overrides_css'  => $config['folder_overrides_css'] ?? '',
                ],
                'selected_template_id'   => $templateId,
                'selected_template_name' => $templateName,
            ];
            echo new JsonResponse($data);
        } catch (Exception $e) {
            echo new JsonResponse(null, $e->getMessage(), true);
        }
        $app->close();
    }

    public function overridesave() {
        $this->checkToken();
        $app = Factory::getApplication();
        $input = $app->input;
        $alias        = $input->getString('alias');
        $customFolder = $input->getString('folder');
        $fileType     = $input->getString('type');
        $templateName = $input->getString('template_name');
        $fileName     = $input->getString('file_name', '');
        $model    = JSFactory::getModel('addonsoverride');
        $clientId = 0;
        foreach ($model->getTemplates() as $t) {
            if ($t->template === $templateName) {
                $clientId = (int) $t->client_id;
                break;
            }
        }
        try {
            switch ($fileType) {
                case 'view':
                    $result = $model->overrideView($alias, $customFolder, $templateName, $fileName, $clientId);
                    break;
                case 'js':
                case 'css':
                    $result = $model->overrideJsOrCss($alias, $customFolder, $fileType, $templateName, $fileName, $clientId);
                    break;
                default:
                    throw new Exception("Invalid file type: $fileType");
            }
            echo new JsonResponse($result, "Override $fileType completed successfully.");
        } catch (Exception $e) {
            echo new JsonResponse(null, $e->getMessage(), true);
        }
        $app->close();
    }    

}