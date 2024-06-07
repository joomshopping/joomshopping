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

FormHelper::loadFieldClass('list');

class JshoppingextrafieldsField extends ListField
{

	protected $type = 'jshoppingextrafields';

	protected function getOptions(): array
	{

		// Load JoomShopping config and models
		if (!class_exists('\JSFactory'))
		{
			require_once(JPATH_SITE . '/components/com_jshopping/bootstrap.php');
		}
		$lang         = Factory::getApplication()->getLanguage();
		$current_lang = $lang->getTag();
		$db           = Factory::getContainer()->get('DatabaseDriver');
		$query        = $db->getQuery(true);
		$query->select($db->quoteName('name_' . $current_lang));
		$query->select($db->quoteName('id'))
			->from($db->quoteName('#__jshopping_products_extra_fields'));
		$db->setQuery($query);
		$extra_fields = $db->loadAssocList();
		$name         = 'name_' . $current_lang;
		$options      = [];
		if (!empty($extra_fields))
		{
			foreach ($extra_fields as $extra_field)
			{
				$options[] = HTMLHelper::_('select.option', $extra_field['id'], $extra_field[$name]);
			}
		}

		return $options;
	}
}