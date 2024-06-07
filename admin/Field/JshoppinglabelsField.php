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

use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Form\Field\ListField;
use Joomla\Component\Jshopping\Site\Helper\SelectOptions;

FormHelper::loadFieldClass('list');

class JshoppinglabelsField extends ListField
{

	protected $type = 'jshoppinglabels';

	protected function getOptions() : array
	{
		// Load JoomShopping config and models
		if (!class_exists('\JSFactory'))
		{
			require_once(JPATH_SITE . '/components/com_jshopping/bootstrap.php');
		}

		$labels    = \JSFactory::getModel("Productlabels");
		$alllabels = $labels->getList();

		$options = [];
		foreach ($alllabels as $label)
		{
			$options[] = HTMLHelper::_('select.option', $label->id, $label->name);
		}

		return $options;
	}
}

?>