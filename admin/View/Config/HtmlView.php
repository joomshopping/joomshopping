<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Config;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{

    function display($tpl=null){
        $layout = $this->getLayout();
        $title = \JText::_('JSHOP_EDIT_CONFIG');
        
        $exttitle = '';
        switch ($layout){
            case 'general': $exttitle = \JText::_('JSHOP_GENERAL_PARAMETERS'); break;
            case 'categoryproduct': $exttitle = \JText::_('JSHOP_CAT_PROD'); break;
            case 'checkout': $exttitle = \JText::_('JSHOP_CHECKOUT'); break;
            case 'fieldregister': $exttitle = \JText::_('JSHOP_REGISTER_FIELDS'); break;
            case 'currency': $exttitle = \JText::_('JSHOP_CURRENCY_PARAMETERS'); break;
            case 'image': $exttitle = \JText::_('JSHOP_IMAGE_VIDEO_PARAMETERS'); break;
            case 'storeinfo': $exttitle = \JText::_('JSHOP_STORE_INFO'); break;
            case 'adminfunction': $exttitle = \JText::_('JSHOP_SHOP_FUNCTION'); break;
            case 'otherconfig': $exttitle = \JText::_('JSHOP_OC'); break;
        }
        if ($exttitle!=''){
            $title .= ' / '.$exttitle;
        }
        
        \JToolBarHelper::title($title, 'generic.png' );
        \JToolBarHelper::save();
        \JToolBarHelper::spacer();
        \JToolBarHelper::apply();
		\JToolBarHelper::spacer();
		\JToolBarHelper::cancel();
		\JToolBarHelper::spacer();
		\JToolBarHelper::custom('panel', 'home', 'home', 'JSHOP_PANEL', false);
        \JSHelperAdmin::btnHome();
         
        \JToolBarHelper::divider();
        if (\JFactory::getUser()->authorise('core.admin')){
            \JToolBarHelper::preferences('com_jshopping');        
            \JToolBarHelper::divider();
        }

       

        parent::display($tpl);
	}
    
    function displayListSeo($tpl=null){        
        \JToolBarHelper::title( \JText::_('JSHOP_SEO'), 'generic.png' );
        \JToolBarHelper::addNew("seoedit");
        \JToolBarHelper::custom('panel', 'home', 'home', \JText::_('JSHOP_PANEL'), false);
        \JSHelperAdmin::btnHome();
        parent::display($tpl);
    }
    
    function displayEditSeo($tpl=null){
        $title = \JText::_('JSHOP_SEO');        
        if (defined("\JText::_('JSHP_SEOPAGE_')".$this->row->alias)) $titleext = constant("\JText::_('JSHP_SEOPAGE_')".$this->row->alias); else $titleext = $this->row->alias;
        if ($titleext) $title.=' / '.$titleext;
        \JToolBarHelper::title($title, 'generic.png' );
        \JToolBarHelper::save("saveseo");
        \JToolBarHelper::spacer();
        \JToolBarHelper::apply("applyseo");
        \JToolBarHelper::spacer();
        \JToolBarHelper::cancel("seo");
        \JToolBarHelper::spacer();
        \JToolBarHelper::custom('panel', 'home', 'home', \JText::_('JSHOP_PANEL'), false);
        parent::display($tpl);
    }
    
    function displayListStatictext($tpl=null){
        \JToolBarHelper::title( \JText::_('JSHOP_STATIC_TEXT'), 'generic.png' );
        \JToolBarHelper::addNew("statictextedit");
        \JToolBarHelper::custom('panel', 'home', 'home', \JText::_('JSHOP_PANEL'), false);
        \JSHelperAdmin::btnHome();
        parent::display($tpl);
    }
    
    function displayEditStatictext($tpl=null){
        $title = \JText::_('JSHOP_STATIC_TEXT');        
        if (defined("\JText::_('JSHP_STPAGE_')".$this->row->alias)) $titleext = constant("\JText::_('JSHP_STPAGE_')".$this->row->alias); else $titleext = $this->row->alias;
        if ($titleext) $title.=' / '.$titleext;
        \JToolBarHelper::title($title, 'generic.png' );
        \JToolBarHelper::save("savestatictext");
        \JToolBarHelper::spacer();
        \JToolBarHelper::apply("applystatictext");
        \JToolBarHelper::spacer();
        \JToolBarHelper::cancel("statictext");
        \JToolBarHelper::spacer();
        \JToolBarHelper::custom('panel', 'home', 'home', \JText::_('JSHOP_PANEL'), false);
        parent::display($tpl);
    }
    
}
?>