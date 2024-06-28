<?php
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * @version      5.5.0 28.06.2024
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */
defined('_JEXEC') or die();
?>
<form action="index.php?option=com_jshopping&controller=productimages&task=display&tmpl=component" method="post">
    <div class="js-stools clearfix jshop_block_filter mb-3 sticky-top py-1" style="background-color: var(--body-bg);">
        <div class="js-stools-container-bar">
            <div class="btn-toolbar" role="toolbar">
                <div class="btn-group mr-2">
                    <div class="input-group">
                        <input name="filter" value="<?php echo htmlspecialchars($this->filter); ?>" class="form-control"
                               placeholder="<?php print Text::_('JSHOP_SEARCH') ?>" type="text">
                        <span class="input-group-append">
                        <button type="submit" class="btn btn-primary hasTooltip"
                                title="<?php print Text::_('JSHOP_SEARCH') ?>">
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
					<?php
					$img_url     = $this->config->image_product_live_path . '/thumb_' . $file;
					$img_attribs = [
						'class'   => 'img-fluid select-image',
						'loading' => 'lazy',
						'title'   => $file,
					];
					echo HTMLHelper::image($img_url, $file, $img_attribs);
					?>
                    <div class="card-body">
						<?php
						$link_attribs = [
							'onclick' => 'parent.jshopAdmin.setImageFromFolder(\'' . $file . '\');return false;',
							'class'   => 'stretched-link'
						];
						echo HTMLHelper::link('#', $file, $link_attribs);
						?>
                    </div>
                </div>
            </div>
		<?php endforeach; ?>
    </div>
</form>