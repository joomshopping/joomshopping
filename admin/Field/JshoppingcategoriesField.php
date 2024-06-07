<?php
/**
 * @version      5.4.2 07.06.2024
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */

namespace Joomla\Component\Jshopping\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use \Joomla\Component\Jshopping\Site\Helper\SelectOptions;

class JshoppingcategoriesField extends ListField
{

	protected $type = 'jshoppingcategories';

	protected function getOptions():array
	{

		// Load JoomShopping config and models
		if (!class_exists('\JSFactory'))
		{
			require_once(JPATH_SITE . '/components/com_jshopping/bootstrap.php');
		}

		$options = [];

		$allcats = \JSHelper::buildTreeCategory(0);

		foreach ($allcats as $category)
		{
			if($category->category_id == 0){
				unset($category);
			} else{
				$options[] = HTMLHelper::_('select.option', $category->category_id, $category->name);
			}
		}

		return $options;
	}
}