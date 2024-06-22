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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Form\Field\ListField;
use Joomla\Component\Jshopping\Site\Lib\JSFactory;

class JshoppinglabelsField extends ListField
{
	protected $type = 'jshoppinglabels';

	protected function getOptions(): array
	{		
		require_once(JPATH_SITE . '/components/com_jshopping/bootstrap.php');

		$labels = JSFactory::getModel("Productlabels");
		$alllabels = $labels->getList();

		$options = [];
		$default = $this->element['default'] ?? null;		
        if (isset($default) && ((string)$default) === '') {
            $options[] = HTMLHelper::_('select.option', '', '');
        }
		foreach ($alllabels as $label) {
			$options[] = HTMLHelper::_('select.option', $label->id, $label->name);
		}
		return $options;
	}
}