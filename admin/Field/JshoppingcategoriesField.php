<?php
/**
 * @version      5.5.0 07.06.2024
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */

namespace Joomla\Component\Jshopping\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Jshopping\Site\Helper\Helper;

class JshoppingcategoriesField extends ListField
{
	protected $type = 'jshoppingcategories';

	protected function getOptions(): array
	{
		require_once(JPATH_SITE . '/components/com_jshopping/bootstrap.php');

		$options = [];
        
        $default = $this->element['default'] ?? null;

		$allcats = Helper::buildTreeCategory(0);
        if (isset($default) && ((string)$default) === '') {
            $options[] = HTMLHelper::_('select.option', '', '');
        }
		foreach ($allcats as $category) {
			$options[] = HTMLHelper::_('select.option', $category->category_id, $category->name);
		}

		return $options;
	}
}