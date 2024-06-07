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
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;

FormHelper::loadFieldClass('list');

class JshoppingextrafieldvalueField extends ListField
{

	protected $type = 'jshoppingextrafieldvalue';

	protected function getOptions(): array
	{
		// Load JoomShopping config and models
		if (!class_exists('\JSFactory'))
		{
			require_once(JPATH_SITE . '/components/com_jshopping/bootstrap.php');
		}
		$extra_field_id = (int) $this->element['extra_field_id'] ?? false;
		$options        = [];
		if ($extra_field_id)
		{
			$lang         = Factory::getApplication()->getLanguage();
			$current_lang = $lang->getTag();
			$db           = Factory::getContainer()->get('DatabaseDriver');
			$query        = $db->getQuery(true);
			$query->select($db->quoteName('name_' . $current_lang));
			$query->select($db->quoteName('id'))
				->from($db->quoteName('#__jshopping_products_extra_field_values'))
				->where($db->quoteName('field_id') . ' = ' . $db->quote($extra_field_id));
			$db->setQuery($query);
			$extra_fields_values = $db->loadAssocList();
			$name                = 'name_' . $current_lang;

			if (!empty($extra_fields_values))
			{
				foreach ($extra_fields_values as $value)
				{
					$options[] = HTMLHelper::_('select.option', $value["id"], $value[$name]);
				}
			}
		}

		return $options;
	}
}