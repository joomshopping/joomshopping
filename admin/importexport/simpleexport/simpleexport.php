<?php
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Session\Session;

/**
* @version      5.10.0 27.10.2026
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
use Joomla\Component\Jshopping\Site\Lib\Csv;
defined('_JEXEC') or die();

class IeSimpleExport extends IeController{
    
    function view(){	
        $app = Factory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $ie_id = $this->ie_id;
        $_importexport = JSFactory::getTable('ImportExport'); 
        $_importexport->load($ie_id);
        $name = $_importexport->get('name');
        $ie_params = $this->getIeParams($_importexport);
                
        $files = Folder::files($jshopConfig->importexport_path.$_importexport->get('alias'), '.csv');
        $count = count($files);
            
        ToolbarHelper::title(Text::_('JSHOP_EXPORT'). ' "'.$name.'"', 'generic.png' ); 
        ToolbarHelper::custom("backtolistie", "arrow-left", 'arrow-left', Text::_('JSHOP_BACK_TO').' "'.Text::_('JSHOP_PANEL_IMPORT_EXPORT').'"', false );
        ToolbarHelper::spacer();
        ToolbarHelper::save("save", Text::_('JSHOP_EXPORT'));                
        
        include(dirname(__FILE__)."/list_csv.php");  
    }

    function save(){
        $app = Factory::getApplication();
        if ($app->isClient('administrator')){
            Session::checkToken() or die('Invalid Token');
        }

        $ie_id = $this->get('ie_id');
        $_importexport = JSFactory::getTable('ImportExport');
        $_importexport->load($ie_id);
        $alias = $_importexport->get('alias');
        $_importexport->set('endstart', time());
        $ie_params = $this->getIeParams($_importexport);
        $params = $app->input->getVar("params");
        if (is_array($params) && $app->isClient('administrator')){
            $ie_params = $params;
        }
        $ie_params['filename'] = $this->getSafeFileName($ie_params['filename'] ?? '');
        $ie_params['encoding'] = $this->getSafeEncoding($ie_params['encoding'] ?? '');
        $this->setIeParams($_importexport, $ie_params);
        $_importexport->store();
        
        $jshopConfig = JSFactory::getConfig();
        $lang = JSFactory::getLang();
        $db = Factory::getDBO();
        
        $query = "SELECT prod.product_id, prod.product_ean, prod.product_quantity, prod.product_date_added, prod.product_price, tax.tax_value as tax, prod.`".$lang->get('name')."` as name, prod.`".$lang->get('short_description')."` as short_description,  prod.`".$lang->get('description')."` as description, cat.`".$lang->get('name')."` as cat_name
                  FROM `#__jshopping_products` AS prod
                  LEFT JOIN `#__jshopping_products_to_categories` AS categ USING (product_id)
                  LEFT JOIN `#__jshopping_categories` as cat on cat.category_id=categ.category_id
                  LEFT JOIN `#__jshopping_taxes` AS tax ON tax.tax_id = prod.product_tax_id              
                  GROUP BY prod.product_id";
        $db->setQuery($query);
        $products = $db->loadObjectList();
        
        $data = array();
        $head = array("product_id","ean","qty","date","price","tax","category","name","short_description","description");
        $data[] = $head;
        
        foreach($products as $prod){
            $row = array();
            $row[] = $prod->product_id;
            $row[] = $prod->product_ean;
            $row[] = $prod->product_quantity;
            $row[] = $prod->product_date_added;
            $row[] = $prod->product_price;        
            $row[] = $prod->tax;
            $row[] = mb_convert_encoding((string)$prod->cat_name, $ie_params['encoding'], 'UTF-8');
            $row[] = mb_convert_encoding((string)$prod->name, $ie_params['encoding'], 'UTF-8');
            $row[] = mb_convert_encoding((string)$prod->short_description, $ie_params['encoding'], 'UTF-8');
            $row[] = mb_convert_encoding((string)$prod->description, $ie_params['encoding'], 'UTF-8');
            $data[] = $row; 
        }
        
        
        $filename = $jshopConfig->importexport_path.$alias."/".$ie_params['filename'].".csv";
        
        $csv = new Csv();
        $csv->write($filename, $data);
        
        if ($app->isClient('cli')) {
            return 1;
        }
        if (!$app->input->getInt("noredirect")){
            $app->redirect("index.php?option=com_jshopping&controller=importexport&task=view&ie_id=".$ie_id);
        }
    }

    function filedelete(){
        Session::checkToken('get') or die('Invalid Token');
        $app = Factory::getApplication();
        $jshopConfig = JSFactory::getConfig();
        $ie_id = $app->input->getInt("ie_id");
        $_importexport = JSFactory::getTable('ImportExport');
        $_importexport->load($ie_id);
        $alias = $_importexport->get('alias');
        // only a file from the list of this export folder
        $file = $app->input->getString("file");
        $dir = $jshopConfig->importexport_path.$alias;
        if ($alias != '' && is_dir($dir) && in_array($file, Folder::files($dir, '.csv'), true)){
            @unlink($dir."/".$file);
        }
        $app->redirect("index.php?option=com_jshopping&controller=importexport&task=view&ie_id=".$ie_id);
    }

    protected function getSafeFileName($name){
        $name = str_replace(array('.', '/', '\\'), '', (string)$name);
        if ($name == ''){
            $name = 'export_'.bin2hex(random_bytes(8));
        }
        return $name;
    }
    
}