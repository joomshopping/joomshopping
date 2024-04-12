<?php
/**
* @version      5.0.0 20.05.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

class com_jshoppingInstallerScript{
	
	public function preflight($type, $parent=null){
		if ($type=='update'){
			$version = $this->getPrevVersion();
			if ($version && version_compare($version, '4.15.0', '<')) {
				$app = \JFactory::getApplication();
				$app->enqueueMessage("100", 'Error update JoomShopping < 4.15.0. Use JoomShopping / Install & Update.', 'warning');
				return false;
			}
		}
	}
	
	public function update(){
		$loader = include JPATH_LIBRARIES . '/vendor/autoload.php';
        $map = array(
            'Joomla\\Component\\Jshopping\\Site\\' => [JPATH_ROOT . "/components/com_jshopping",],
            'Joomla\\Component\\Jshopping\\Administrator\\' => [JPATH_ROOT . "/administrator/components/com_jshopping",],
        );
        foreach($map as $namespace => $path){
            $loader->setPsr4($namespace, $path);
        }
		require_once(JPATH_SITE.'/components/com_jshopping/bootstrap.php');
		$version = $this->getPrevVersion();
		
		if (version_compare($version, '4.999.0', '<')){
			require_once(JPATH_ADMINISTRATOR.'/components/com_jshopping/sql/updates/php/5.0.0.php');
			new jshoppingUpdate500();
		}
	}
	
	public function install($parent){		
	}
	
	public function postflight($type, $parent=null){
		if ($type=='install'){
			$this->installFinish();
		}
	}
	
    public function installFinish(){
        $loader = include JPATH_LIBRARIES . '/vendor/autoload.php';
        $map = array(
            'Joomla\\Component\\Jshopping\\Site\\' => [JPATH_ROOT . "/components/com_jshopping",],
            'Joomla\\Component\\Jshopping\\Administrator\\' => [JPATH_ROOT . "/administrator/components/com_jshopping",],
        );
        foreach($map as $namespace => $path){
            $loader->setPsr4($namespace, $path);
        }
		require_once(JPATH_SITE.'/components/com_jshopping/bootstrap.php');
        $db = \JFactory::getDBO();
        $adminlang = \JFactory::getLanguage();
		\JSFactory::loadAdminLanguageFile();

        $params = \JComponentHelper::getParams('com_languages');
        $frontend_lang = $params->get('site','en-GB');

        $query = 'SELECT email FROM #__users AS U LEFT JOIN #__user_usergroup_map AS UM ON UM.user_id = U.id WHERE UM.group_id = "8" ORDER BY U.id';
        $db->setQuery($query);
        $email_admin = $db->loadResult();
        
		$config = new \Joomla\Component\Jshopping\Site\Table\ConfigTable();
        $config->id = 1;
        $config->adminLanguage = $adminlang->getTag();
        $config->defaultLanguage = $frontend_lang;
        if ($email_admin){
            $config->contact_email = $email_admin;
        }
        $config->securitykey = md5($email_admin.time().JPATH_SITE);
        $config->store();

        $session = \JFactory::getSession();
        $checkedlanguage = array();
        $session->set("jshop_checked_language", $checkedlanguage);

        \JSHelper::installNewLanguages("en-GB", 0);

        @chmod(JPATH_SITE.'/components/com_jshopping/files', 0755);

        @mkdir(JPATH_SITE.'/components/com_jshopping/files/img_manufs', 0755);
        @mkdir(JPATH_SITE.'/components/com_jshopping/files/demo_products', 0755);
        @mkdir(JPATH_SITE.'/components/com_jshopping/files/img_attributes', 0755);    
        @mkdir(JPATH_SITE.'/components/com_jshopping/files/pdf_orders', 0755);    

        @chmod(JPATH_SITE.'/components/com_jshopping/files/img_manufs', 0755);
        @chmod(JPATH_SITE.'/components/com_jshopping/files/img_categories', 0755);
        @chmod(JPATH_SITE.'/components/com_jshopping/files/img_products', 0755);
        @chmod(JPATH_SITE.'/components/com_jshopping/files/img_labels', 0755);
        @chmod(JPATH_SITE.'/components/com_jshopping/files/video_products', 0755);
        @chmod(JPATH_SITE.'/components/com_jshopping/files/files_products', 0755);
        @chmod(JPATH_SITE.'/components/com_jshopping/files/importexport', 0755);
        @chmod(JPATH_SITE.'/components/com_jshopping/files/importexport/simpleexport', 0755);
        @chmod(JPATH_SITE.'/components/com_jshopping/files/importexport/simpleimport', 0755);

        print "<br>";
        $jshopConfig = \JSFactory::getConfig();
        print '<link rel="stylesheet" type="text/css" href="'.$jshopConfig->live_admin_path.'css/style.css" />';
        $view_config = array("base_path"=>JPATH_ADMINISTRATOR."/components/com_jshopping/","template_path"=>JPATH_ADMINISTRATOR."/components/com_jshopping/tmpl/panel/");
        $view = new \Joomla\Component\Jshopping\Administrator\View\Panel\HtmlView($view_config);
        $view->setLayout("info");
        $view->sidebar='';
		$view->tmp_html_start='';
		$view->tmp_html_end='';
        $view->displayInfo();
        ?>
        <br/>
        <table align="center" style="font-weight:bold">
        <tr>
        <td width="250">
        <a href="index.php?option=com_jshopping">
            <img src="components/com_jshopping/images/jshop_categories_b.png" align="left" style="margin-right:10px;" /><div style="line-height:48px;">JoomShopping</div>
        </a>
        </td>
        <td width="250">
        <a href="index.php?option=com_jshopping&controller=update&task=update&installtype=url&install_url=sm1:demo_products_5.0.0.zip&back=<?php print urldecode("index.php?option=com_jshopping")?>">
            <img src="components/com_jshopping/images/jshop_import_export_b.png" align="left" style="margin-right:8px;" /><div style="line-height:48px;">&nbsp;<?php print \JText::_('JSHOP_LOAD_SAMPLE_DATA')?></div>
        </a>
        </td>
        </tr>
        </table><br/>
    <?php
    }
    
    public function uninstall($parent){
    }
	
	private function getPrevVersion(){
		$data = \JInstaller::parseXMLInstallFile(JPATH_SITE.'/administrator/components/com_jshopping/jshopping.xml');
		return $data['version'];
	}
	
}