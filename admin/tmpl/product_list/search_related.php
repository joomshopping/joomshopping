<?php
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();

$start=intval(\JFactory::getApplication()->input->getInt("start")/$this->limit)+1;
print $this->tmp_html_start;
foreach($this->rows as $row){ ?>      
<div class="block_related" id="serched_product_<?php print $row->product_id;?>">
    <div class="block_related_inner">
        <div class="name"><?php echo $row->name;?> (ID:&nbsp;<?php print $row->product_id?>)</div>
        <div class="image">
            <a href="index.php?option=com_jshopping&controller=products&task=edit&product_id=<?php print $row->product_id?>">
            <?php if ( strlen($row->image) > 0 ) { ?>
                <img src="<?php print \JSHelper::getPatchProductImage($row->image, 'thumb', 1)?>" width="90" border="0" />
            <?php } else { ?>
                <img src="<?php print \JSHelper::getPatchProductImage($this->config->noimage, '', 1)?>" width="90" border="0" />
            <?php } ?>
            </a>
            <?php if ($this->config->admin_list_related_show_prod_code){?>
                <div class="code"><?php print $row->ean?></div>
            <?php }?>
        </div>
        <div style="padding-top:5px;"><input type="button" class="btn btn-small btn-success" value="<?php print JText::_('JSHOP_ADD')?>" onclick="jshopAdmin.add_to_list_relatad(<?php print $row->product_id;?>)"></div>
    </div>
</div>
<?php
}
?>
<div class="clr"></div>

<?php if ($this->pages>1){?>
<table align="center">
<tr>    
    <td>
    <div class="pagination related">
        <div class="button2-left">
        <div class="page">
            <?php
            $pstart = $start - 9;
            if ($pstart<1) $pstart = 1;
            $pfinish = $start + 9;
            if ($pfinish>$this->pages) $pfinish = $this->pages;
            ?>
            <?php if ($pstart>1){?>
                <a onclick="jshopAdmin.releted_product_search(0, <?php print $this->no_id?>, 1);return false;" href="#">
                    <span class="icon-first"></span>
                </a>
                <span>...</span>
            <?php }?>
            <?php for($i=$pstart;$i<=$pfinish; $i++){?>
                <a class="p<?php print $i?> <?php if ($i==1){?>active<?php }?>" onclick="jshopAdmin.releted_product_search(<?php print ($i-1)*$this->limit;?>, <?php print $this->no_id?>, <?php print $i?>);return false;" href="#">
                    <?php print $i;?>
                </a>
            <?php } ?>
            <?php if ($pfinish<$this->pages){?>
                <span>...</span>
                <a onclick="jshopAdmin.releted_product_search(<?php print ($this->pages-1)*$this->limit;?>, <?php print $this->no_id?>, <?php print $this->pages?>);return false;" href="#">
                    <span class="icon-last"></span>
                </a>
            <?php }?>
        </div>
        </div>
    </div>
    </td>
</tr>    
</table>
<div class="clr"></div>
<?php }?>
<?php print $this->tmp_html_end?>