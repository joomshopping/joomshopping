<?php
/**
 * @version      5.5.1
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */
namespace Joomla\Component\Jshopping\Administrator\Field;

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Form\Field\ListField;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;

class JshoppingfreeattrField extends ListField
{

	protected $type = 'jshoppingfreeattr';

	protected function getOptions(): array
	{
		require_once(JPATH_SITE . '/components/com_jshopping/bootstrap.php');
		$options = [];
		$freeattributes = JSFactory::getTable('freeattribut');
		$namesfreeattributes = $freeattributes->getAllNames();

		$default = $this->element['default'] ?? null;
		if (isset($default) && ((string)$default) === '') {
			$options[] = HTMLHelper::_('select.option', '', '');
		}
		foreach ($namesfreeattributes as $attr_id => $attr_name) {
			$options[] = HTMLHelper::_('select.option', $attr_id, $attr_name);
		}

		return $options;
	}
}