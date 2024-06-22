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
use Joomla\CMS\Factory;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;

class JshoppingextrafieldvalueField extends ListField
{
	protected $type = 'jshoppingextrafieldvalue';

	protected function getOptions(): array
	{
		require_once(JPATH_SITE . '/components/com_jshopping/bootstrap.php');
		$extra_field_id = (int) $this->element['extra_field_id'] ?? false;
		$options = [];

		$productfieldvalue = JSFactory::getTable('productfieldvalue');
        $list = $productfieldvalue->getAllList();

		$default = $this->element['default'] ?? null;
        if (isset($default) && ((string)$default) === '') {
            $options[] = HTMLHelper::_('select.option', '', '');
        }
		foreach ($list as $item) {
			if ($item->field_id == $extra_field_id) {
				$options[] = HTMLHelper::_('select.option', $item->id, $item->name);
			}
		}
		return $options;
	}
}