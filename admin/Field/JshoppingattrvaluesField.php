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
use Joomla\Component\Jshopping\Site\Lib\JSFactory;

class JshoppingattrvaluesField extends ListField
{
	protected $type = 'jshoppingattrvalues';

	protected function getOptions(): array
	{
		$options = [];
		require_once(JPATH_SITE . '/components/com_jshopping/bootstrap.php');

		$attr_id = (int)$this->element['attr_id'] ?? false;

		$default = $this->element['default'] ?? null;
        if (isset($default) && ((string)$default) === '') {
            $options[] = HTMLHelper::_('select.option', '', '');
        }

		if ($attr_id){
			$attribute_values = JSFactory::getTable('AttributValue', 'jshop');
			$rows = $attribute_values->getAllValues($attr_id);
			foreach ($rows as $value) {
				$options[] = HTMLHelper::_('select.option', $value->value_id, $value->name);
			}
		}

		return $options;
	}
}