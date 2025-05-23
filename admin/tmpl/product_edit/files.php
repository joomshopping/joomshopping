<?php
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
						<?php if ($jshopConfig->product_file_upload_via_ftp) { ?>
							<div style="padding-top:3px;"><input size="34" type="text" name="product_demo_file_name_<?php print $i; ?>" title="<?php print Text::_('JSHOP_UPLOAD_FILE_VIA_FTP') ?>" /></div>
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
						<?php if ($jshopConfig->product_file_upload_via_ftp) { ?>
							<div style="padding-top:3px;"><input size="34" type="text" name="product_file_name_<?php print $i; ?>" title="<?php print Text::_('JSHOP_UPLOAD_FILE_VIA_FTP') ?>" /></div>
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
</div>