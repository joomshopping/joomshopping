<?php
/**
* @version      5.6.0 14.03.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

namespace Joomla\Component\Jshopping\Administrator\Model;

use Joomla\CMS\Language\Text;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\Component\Jshopping\Site\Helper\Error as JSError;

defined('_JEXEC') or die();

class SysmsgModel extends BaseadminModel{

    public function show() {
        $config = JSFactory::getConfig();
        if (!$config->generate_pdf && !$config->sysmsg_use_order_pdf) {
            JSError::raiseNotice(100, Text::_('JSHOP_SYSMSG_USE_ORDER_PDF'));
            $config = JSFactory::getTable('Config');
		    $config->id = 1;
            $config->sysmsg_use_order_pdf = 1;
            $config->store();
        }
    }

}