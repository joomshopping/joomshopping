<?php
/**
* @version      5.4.0 13.04.2024
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Administrator\View\Config;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Jshopping\Administrator\Helper\HelperAdmin;

defined('_JEXEC') or die();

class HtmlView extends BaseHtmlView{

    function display($tpl=null){
        $layout = $this->getLayout();
        $title = Text::_('JSHOP_EDIT_CONFIG');
        
        $exttitle = '';
        switch ($layout){
            case 'general': $exttitle = Text::_('JSHOP_GENERAL_PARAMETERS'); break;
            case 'categoryproduct': $exttitle = Text::_('JSHOP_CAT_PROD'); break;
            case 'checkout': $exttitle = Text::_('JSHOP_CHECKOUT'); break;
            case 'fieldregister': $exttitle = Text::_('JSHOP_REGISTER_FIELDS'); break;
            case 'currency': $exttitle = Text::_('JSHOP_CURRENCY_PARAMETERS'); break;
            case 'image': $exttitle = Text::_('JSHOP_IMAGE_VIDEO_PARAMETERS'); break;
            case 'storeinfo': $exttitle = Text::_('JSHOP_STORE_INFO'); break;
            case 'adminfunction': $exttitle = Text::_('JSHOP_SHOP_FUNCTION'); break;
            case 'otherconfig': $exttitle = Text::_('JSHOP_OC'); break;
        }
        if ($exttitle!=''){
            $title .= ' / '.$exttitle;
        }
        
        ToolBarHelper::title($title, 'generic.png' );
        ToolBarHelper::save();
        ToolBarHelper::spacer();
        ToolBarHelper::apply();
		ToolBarHelper::spacer();
		ToolBarHelper::cancel();
		ToolBarHelper::spacer();
		ToolBarHelper::custom('panel', 'home', 'home', 'JSHOP_PANEL', false);
        HelperAdmin::btnHome();
         
        ToolBarHelper::divider();
        if (Factory::getUser()->authorise('core.admin')){
            ToolBarHelper::preferences('com_jshopping');        
            ToolBarHelper::divider();
        }

       

        parent::display($tpl);
	}
    
    function displayListSeo($tpl=null){        
        ToolBarHelper::title( Text::_('JSHOP_SEO'), 'generic.png' );
        ToolBarHelper::addNew("seoedit");
        ToolBarHelper::custom('panel', 'home', 'home', Text::_('JSHOP_PANEL'), false);
        HelperAdmin::btnHome();
        parent::display($tpl);
    }
    
    function displayEditSeo($tpl=null){
        $title = Text::_('JSHOP_SEO');

        if (Text::_('JSHP_SEOPAGE_'.$this->row->alias) != 'JSHP_SEOPAGE_'.$this->row->alias) 
            $titleext = Text::_('JSHP_SEOPAGE_'.$this->row->alias); 
        else 
            $titleext = $this->row->alias;

        if ($titleext) $title.=' / '.$titleext;
        ToolBarHelper::title($title, 'generic.png' );
        ToolBarHelper::save("saveseo");
        ToolBarHelper::spacer();
        ToolBarHelper::apply("applyseo");
        ToolBarHelper::spacer();
        ToolBarHelper::cancel("seo");
        ToolBarHelper::spacer();
        ToolBarHelper::custom('panel', 'home', 'home', Text::_('JSHOP_PANEL'), false);
        parent::display($tpl);
    }
    
    function displayListStatictext($tpl=null){
        ToolBarHelper::title( Text::_('JSHOP_STATIC_TEXT'), 'generic.png' );
        ToolBarHelper::addNew("statictextedit");
        ToolBarHelper::custom('panel', 'home', 'home', Text::_('JSHOP_PANEL'), false);
        HelperAdmin::btnHome();
        parent::display($tpl);
    }
    
    function displayEditStatictext($tpl=null){
        $title = Text::_('JSHOP_STATIC_TEXT');

        if (Text::_('JSHP_STPAGE_'.$this->row->alias) != 'JSHP_STPAGE_'.$this->row->alias) 
            $titleext = Text::_('JSHP_STPAGE_'.$this->row->alias); 
        else 
            $titleext = $this->row->alias;

        if ($titleext) $title.=' / '.$titleext;
        ToolBarHelper::title($title, 'generic.png' );
        ToolBarHelper::save("savestatictext");
        ToolBarHelper::spacer();
        ToolBarHelper::apply("applystatictext");
        ToolBarHelper::spacer();
        ToolBarHelper::cancel("statictext");
        ToolBarHelper::spacer();
        ToolBarHelper::custom('panel', 'home', 'home', Text::_('JSHOP_PANEL'), false);
        parent::display($tpl);
    }

}