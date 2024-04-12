<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
use Joomla\Component\Jshopping\Site\Lib\Csv;
defined('_JEXEC') or die();
jimport('joomla.filesystem.folder');

class IeSimpleExport extends IeController{
    
    function view(){	
        $app = \JFactory::getApplication();
        $jshopConfig = \JSFactory::getConfig();
        $ie_id = $this->ie_id;
        $_importexport = \JSFactory::getTable('ImportExport'); 
        $_importexport->load($ie_id);
        $name = $_importexport->get('name');
        $ie_params_str = $_importexport->get('params');
        $ie_params = \JSHelper::parseParamsToArray($ie_params_str);
                
        $files = \JFolder::files($jshopConfig->importexport_path.$_importexport->get('alias'), '.csv');
        $count = count($files);
            
        JToolBarHelper::title(\JText::_('JSHOP_EXPORT'). ' "'.$name.'"', 'generic.png' ); 
        JToolBarHelper::custom("backtolistie", "arrow-left", 'arrow-left', \JText::_('JSHOP_BACK_TO').' "'.\JText::_('JSHOP_PANEL_IMPORT_EXPORT').'"', false );
        JToolBarHelper::spacer();
        JToolBarHelper::save("save", \JText::_('JSHOP_EXPORT'));                
        
        include(dirname(__FILE__)."/list_csv.php");  
    }

    function save(){
        $app = \JFactory::getApplication();        
        
        $ie_id = $this->get('ie_id');
        $_importexport = \JSFactory::getTable('ImportExport'); 
        $_importexport->load($ie_id);
        $alias = $_importexport->get('alias');
        $_importexport->set('endstart', time());        
        $params = $app->input->getVar("params");        
        if (is_array($params)){        
            $paramsstr = \JSHelper::parseArrayToParams($params);
            $_importexport->set('params', $paramsstr);
        }        
        $_importexport->store();
        
        $ie_params_str = $_importexport->get('params');
        $ie_params = \JSHelper::parseParamsToArray($ie_params_str);
        
        $jshopConfig = \JSFactory::getConfig();
        $lang = \JSFactory::getLang();
        $db = \JFactory::getDBO();
        
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
            $row[] = utf8_decode($prod->cat_name);
            $row[] = utf8_decode($prod->name);
            $row[] = utf8_decode($prod->short_description);
            $row[] = utf8_decode($prod->description);
            $data[] = $row; 
        }
        
        
        $filename = $jshopConfig->importexport_path.$alias."/".$ie_params['filename'].".csv";
        
        $csv = new Csv();
        $csv->write($filename, $data);
                
        if (!$app->input->getInt("noredirect")){
            $app->redirect("index.php?option=com_jshopping&controller=importexport&task=view&ie_id=".$ie_id);
        }
    }

    function filedelete(){
        $app = \JFactory::getApplication();
        $jshopConfig = \JSFactory::getConfig();
        $ie_id = $app->input->getInt("ie_id");
        $_importexport = \JSFactory::getTable('ImportExport'); 
        $_importexport->load($ie_id);
        $alias = $_importexport->get('alias');
        $file = $app->input->getVar("file");
        $filename = $jshopConfig->importexport_path.$alias."/".$file;
        @unlink($filename);
        $app->redirect("index.php?option=com_jshopping&controller=importexport&task=view&ie_id=".$ie_id);
    }
    
}