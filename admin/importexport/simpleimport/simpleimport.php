<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
use Joomla\Component\Jshopping\Site\Lib\Csv;
use Joomla\Component\Jshopping\Site\Lib\UploadFile;
defined('_JEXEC') or die();
jimport('joomla.filesystem.folder');

class IeSimpleImport extends IeController{
    
    function view(){
        $app = \JFactory::getApplication();
        $jshopConfig = \JSFactory::getConfig();
        $ie_id = $this->ie_id;
        $_importexport = \JSFactory::getTable('ImportExport'); 
        $_importexport->load($ie_id);
        $name = $_importexport->get('name');                        
            
        JToolBarHelper::title(\JText::_('JSHOP_IMPORT'). ' "'.$name.'"', 'generic.png' ); 
        JToolBarHelper::custom("backtolistie", "arrow-left", 'arrow-left', \JText::_('JSHOP_BACK_TO').' "'.\JText::_('JSHOP_PANEL_IMPORT_EXPORT').'"', false );        
        JToolBarHelper::spacer();
        JToolBarHelper::save("save", \JText::_('JSHOP_IMPORT'));    
        
        include(dirname(__FILE__)."/form.php");  
    }

    function save(){
        $app = \JFactory::getApplication();
        $jshopConfig = \JSFactory::getConfig();
        
        $ie_id = $app->input->getInt("ie_id");
        if (!$ie_id) $ie_id = $this->get('ie_id');
        
        $lang = \JSFactory::getLang();
        $db = \JFactory::getDBO();
        
        $_importexport = \JSFactory::getTable('ImportExport'); 
        $_importexport->load($ie_id);
        $alias = $_importexport->get('alias');
        $_importexport->set('endstart', time());
        $_importexport->store();
                
        //get list tax
        $query = "SELECT tax_id, tax_value FROM `#__jshopping_taxes`";
        $db->setQuery($query);        
        $rows = $db->loadObjectList();
        $listTax = array();
        foreach($rows as $row){
            $listTax[intval($row->tax_value)] = $row->tax_id;
        }
        
        //get list category
        $query = "SELECT category_id as id, `".$lang->get("name")."` as name FROM `#__jshopping_categories`";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        $listCat = array();
        foreach($rows as $row){
            $listCat[$row->name] = $row->id;
        }
        
        $_products = \JSFactory::getModel('products');
        
        $dir = $jshopConfig->importexport_path.$alias."/";
        
        $upload = new UploadFile($_FILES['file']);
        $upload->setAllowFile(array('csv'));
        $upload->setDir($dir);
        if ($upload->upload()){
            $filename = $dir."/".$upload->getName();
            @chmod($filename, 0777);
            $csv = new Csv();
            $data = $csv->read($filename);
            if (is_array($data)){                
                foreach($data as $k=>$row){                    
                    if (count($row)<2 || $k==0) continue;
                                        
                    $tax_value = intval($row[5]);                    
                    if (!isset($listTax[$tax_value])){
                        $tax = \JSFactory::getTable('tax');
                        $tax->set('tax_name', $tax_value);
                        $tax->set('tax_value', $tax_value);
                        $tax->store();
                        $listTax[$tax_value] = $tax->get("tax_id");                        
                    }
                    
                    $category_name = $row['6'];
                    if (!isset($listCat[$category_name]) && $category_name!=""){
                        $cat = \JSFactory::getTable("category");
                        $query = "SELECT max(ordering) FROM `#__jshopping_categories`";
                        $db->setQuery($query);        
                        $ordering = $db->loadResult() + 1;
                        $cat->set($lang->get("name"), $category_name);
                        $cat->set("products_page", $jshopConfig->count_products_to_page);
                        $cat->set("products_row", $jshopConfig->count_products_to_row);
                        $cat->set("category_publish", 0);
                        $cat->set("ordering", $ordering);                        
                        $cat->store();
                        $listCat[$category_name] = $cat->get("category_id");                        
                    }
                    
                    
                    $product = \JSFactory::getTable('product');
                    $product->set("product_ean", $row[1]);
                    $product->set("product_quantity", $row[2]);
                    $product->set("product_date_added", $row[3]);
                    $product->set("product_price", $row[4]);
                    $product->set("min_price", $row[4]);
                    $product->set("product_tax_id", $listTax[$tax_value]);                                        
                    $product->set("currency_id", $jshopConfig->mainCurrency);
                    $product->set($lang->get("name"), utf8_encode($row[7]));
                    $product->set($lang->get("short_description"), utf8_encode($row[8]));
                    $product->set($lang->get("description"), utf8_encode($row[9]));
                    $product->store();
                    $product_id = $product->get("product_id");
                    $category_id = $listCat[$category_name];
                    if ($category_name!="" && $category_id){
                        $_products->setCategoryToProduct($product_id, array($category_id));
                    }
                    
                    unset($product);
                }
            }
            @unlink($filename);
        }else{            
            \JSError::raiseWarning("", \JText::_('JSHOP_ERROR_UPLOADING'));
        }
                
        if (!$app->input->getInt("noredirect")){
            $app->redirect("index.php?option=com_jshopping&controller=importexport&task=view&ie_id=".$ie_id);
        }
    }
    
}