<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * @version      5.8.0 09.06.2025
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */
defined('_JEXEC') or die();
?>
<form action="index.php?option=com_jshopping&controller=productimages&task=salefiles&tmpl=component" method="post">
    <div class="js-stools clearfix jshop_block_filter mb-3 sticky-top py-1" style="background-color: var(--body-bg);">
        <div class="js-stools-container-bar">
            <div class="btn-toolbar" role="toolbar">
                <div class="btn-group mr-2">
                    <div class="input-group">
                        <input name="filter" value="<?php echo htmlspecialchars($this->filter); ?>" class="form-control"
                               placeholder="<?php print Text::_('JSHOP_SEARCH') ?>" type="text">
                        <span class="input-group-append">
                        <button type="submit" class="btn btn-primary hasTooltip" title="<?php print Text::_('JSHOP_SEARCH') ?>">
                            <span class="icon-search" aria-hidden="true"></span>
                        </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5 g-3">
		<?php foreach ($this->list as $file): ?>
            <div class="col">
                <div class="card border border-secondary h-100">
                    <div class="card-body">
                        <a href="#" onclick="parent.jshopAdmin.setSalefileFromFolder('<?php echo $file?>');return false;">
                            <?php echo $file?>
                        </a>
                    </div>
                </div>
            </div>
		<?php endforeach; ?>
    </div>
</form>