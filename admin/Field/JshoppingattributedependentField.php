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
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;

FormHelper::loadFieldClass('list');

class JshoppingattributedependentField extends ListField
{

	protected $type = 'jshoppingattributedependent';

	protected function getOptions(): array
	{
		$options = [];

		// Load JoomShopping config and models
		if (!class_exists('\JSFactory'))
		{
			require_once(JPATH_SITE . '/components/com_jshopping/bootstrap.php');
		}

		$attributes = \JSFactory::getTable('attribut');
		$rows       = $attributes->getAllAttributes();

		if (!empty($rows))
		{
			foreach ($rows as $attr)
			{
				$options[] = HTMLHelper::_('select.option', $attr->attr_id, $attr->name);
			}
		}

		return $options;
	}
}