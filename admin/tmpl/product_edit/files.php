<?php

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

/**
 * @version      5.3.0 15.09.2018
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */
defined('_JEXEC') or die();
?>
<div id="product_files" class="tab-pane">
	<table class="admintable">
		<?php foreach ($lists['files'] as $file) { ?>
			<?php if ($jshopConfig->product_admin_demo_file) {?>
				<tr class="rows_file_prod_<?php print $file->id ?>">
					<td class="key" style="width:250px;"><?php print Text::_('JSHOP_DEMO_FILE') ?></td>
					<td id='product_demo_<?php print $file->id ?>'>
						<?php if ($file->demo) { ?>
							<a target="_blank" href="<?php print $jshopConfig->demo_product_live_path . "/" . $file->demo ?>"><?php print $file->demo ?></a>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<a class="btn btn-sm btn-danger" href="#" onclick="if (confirm('<?php print Text::_('JSHOP_DELETE') ?>')) jshopAdmin.deleteFileProduct('<?php echo $file->id ?>','demo');return false;"><?php print Text::_('JSHOP_DELETE') ?></a>
						<?php } ?>
					</td>
				</tr>
				<tr class="rows_file_prod_<?php print $file->id ?>">
					<td class="key">
						<?php echo Text::_('JSHOP_DESCRIPTION_DEMO_FILE') ?>
					</td>
					<td>
						<input type="text" class="form-control" size="100" name="product_demo_descr[<?php print $file->id; ?>]" value="<?php print htmlspecialchars($file->demo_descr); ?>" />
					</td>
				</tr>
			<?php } ?>
			<?php if ($jshopConfig->product_admin_demo_file && $jshopConfig->product_admin_sale_file) {?>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<?php } ?>
			<?php if ($jshopConfig->product_admin_sale_file) {?>
				<tr class="rows_file_prod_<?php print $file->id ?>">
					<td class="key"><?php print Text::_('JSHOP_FILE_SALE') ?></td>
					<td id='product_file_<?php print $file->id ?>'>
						<?php if ($file->file) { ?>
							<a target="_blank" href="index.php?option=com_jshopping&controller=products&task=getfilesale&id=<?php print $file->id ?>">
								<?php print $file->file ?>
							</a>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<a class="btn btn-sm btn-danger" href="#" onclick="if (confirm('<?php print Text::_('JSHOP_DELETE') ?>')) jshopAdmin.deleteFileProduct('<?php echo $file->id ?>','file');return false;"><?php print Text::_('JSHOP_DELETE') ?></a>
						<?php } ?>
					</td>
				</tr>
				<tr class="rows_file_prod_<?php print $file->id ?>">
					<td class="key">
						<?php echo Text::_('JSHOP_DESCRIPTION_FILE_SALE') ?>
					</td>
					<td>
						<input type="text" class="form-control" size="100" name="product_file_descr[<?php print $file->id; ?>]" value="<?php print htmlspecialchars($file->file_descr); ?>" />
					</td>
				</tr>
			<?php }?>
			<tr class="rows_file_prod_<?php print $file->id ?>">
				<td class="key">
					<?php echo Text::_('JSHOP_ORDERING') ?>
				</td>
				<td>
					<input type="text" class="form-control" size="25" name="product_file_sort[<?php print $file->id; ?>]" value="<?php print $file->ordering; ?>" />
				</td>
			</tr>
			<?php
			if (isset($file->tmp_edit_data_tr)) {
				print $file->tmp_edit_data_tr;
			}
			?>
			<tr class="rows_file_prod_<?php print $file->id ?>">
				<td style="height:20px;" colspan="2">
					<hr />
				</td>
			</tr>
		<?php } ?>

		<?php
		$sort = count($lists['files']);
		for ($i = 0; $i < $jshopConfig->product_file_upload_count; $i++) { ?>
			<?php if ($jshopConfig->product_admin_demo_file) {?>
				<tr>
					<td class="key" style="width:250px;"><?php print Text::_('JSHOP_DEMO_FILE') ?></td>
					<td>
						<?php if ($jshopConfig->product_file_upload_via_ftp != 1) { ?>
							<input type="file" name="product_demo_file_<?php print $i; ?>" />
						<?php } ?>
						<?php if ($jshopConfig->product_file_upload_via_ftp == 2) { ?>
							<div class="small"> - <?php echo Text::_('JSHOP_OR')?> -</div>
						<?php } ?>
						<?php if ($jshopConfig->product_file_upload_via_ftp) { ?>
							<div class="pt-1">
								<input type="text" class="form-control form-control-inline" name="product_demo_file_name_<?php print $i; ?>" title="<?php print Text::_('JSHOP_UPLOAD_FILE_VIA_FTP') ?>" />
								<input type="button" value="<?php echo Text::_('JSHOP_FILE_SELECT')?>" class="btn btn-primary"
                        		data-bs-toggle="modal" data-bs-target="#demofilesModal" onclick="jshopAdmin.cElName=<?php echo $i?>">
							</div>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('JSHOP_DESCRIPTION_DEMO_FILE') ?>
					</td>
					<td>
						<input type="text" class="form-control" size="100" name="product_demo_descr_<?php print $i; ?>" value="" />
					</td>
				</tr>
			<?php }?>
			<?php if ($jshopConfig->product_admin_demo_file && $jshopConfig->product_admin_sale_file) {?>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<?php } ?>
			<?php if ($jshopConfig->product_admin_sale_file) {?>
				<tr>
					<td class="key"><?php print Text::_('JSHOP_FILE_SALE') ?></td>
					<td>
						<?php if ($jshopConfig->product_file_upload_via_ftp != 1) { ?>
							<input type="file" name="product_file_<?php print $i; ?>" />
						<?php } ?>
						<?php if ($jshopConfig->product_file_upload_via_ftp == 2) { ?>
							<div class="small"> - <?php echo Text::_('JSHOP_OR')?> -</div>
						<?php } ?>
						<?php if ($jshopConfig->product_file_upload_via_ftp) { ?>
							<div class="pt-1">
								<input type="text" class="form-control form-control-inline" name="product_file_name_<?php print $i; ?>" title="<?php print Text::_('JSHOP_UPLOAD_FILE_VIA_FTP') ?>" />
								<input type="button" value="<?php echo Text::_('JSHOP_FILE_SELECT')?>" class="btn btn-primary"
                        		data-bs-toggle="modal" data-bs-target="#salefilesModal" onclick="jshopAdmin.cElName=<?php echo $i?>">
							</div>
						<?php } ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<?php echo Text::_('JSHOP_DESCRIPTION_FILE_SALE') ?>
					</td>
					<td>
						<input type="text" class="form-control" size="100" name="product_file_descr_<?php print $i; ?>" value="" />
					</td>
				</tr>
			<?php } ?>
			<tr>
				<td class="key">
					<?php echo Text::_('JSHOP_ORDERING') ?>
				</td>
				<td>
					<input type="text" class="form-control" size="25" name="product_file_sort_<?php print $i; ?>" value="<?php print $sort + $i ?>" />
				</td>
			</tr>
			<?php
			if (isset($this->tmp_product_file_edit_data_tr[$i])) {
				print $this->tmp_product_file_edit_data_tr[$i];
			}
			?>
			<tr>
				<td style="height:20px;" colspan="2">
					<hr />
				</td>
			</tr>
		<?php } ?>
		<?php $pkey = 'plugin_template_files';
		if ($this->$pkey) {
			print $this->$pkey;
		} ?>
	</table>

	<div class="helpbox mt-3">
		<div class="head"><?php echo Text::_('JSHOP_ABOUT_UPLOAD_FILES') ?></div>
		<div class="text">
			<?php print sprintf(Text::_('JSHOP_SIZE_FILES_INFO'), ini_get("upload_max_filesize"), ini_get("post_max_size")); ?>
		</div>
	</div>

	<?php print HTMLHelper::_(
        'bootstrap.renderModal',
        'demofilesModal',
        array(
            'title'       => Text::_('JSHOP_FILE_SELECT'),
            'backdrop'    => 'static',
            'url'         => 'index.php?option=com_jshopping&controller=productimages&task=demofiles&tmpl=component',
            'height'      => '400px',
            'width'       => '800px',
            'bodyHeight'  => 70,
            'modalWidth'  => 80
        )
    );?>

	<?php print HTMLHelper::_(
        'bootstrap.renderModal',
        'salefilesModal',
        array(
            'title'       => Text::_('JSHOP_FILE_SELECT'),
            'backdrop'    => 'static',
            'url'         => 'index.php?option=com_jshopping&controller=productimages&task=salefiles&tmpl=component',
            'height'      => '400px',
            'width'       => '800px',
            'bodyHeight'  => 70,
            'modalWidth'  => 80
        )
    );?>
</div>