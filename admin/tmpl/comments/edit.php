<?php 
/**
* @version      5.0.0 15.09.2018
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/

defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
?>

<script type="text/javascript">

function selectProductBehaviour(pid){
    jQuery("#product_id").val(pid);
    var url = 'index.php?option=com_jshopping&controller=products&task=loadproductinfo&product_id='+pid
    var html = '';

    jQuery.getJSON(url, function(json){
        html = json.product_name;
        html += `<a class="btn btn-secondary clear_com" href="#" onclick="clear_product_commentar()">
                    <?php print JText::_('JSHOP_CLEAR')?>
                </a>`;

        jQuery(".review_product_name").html(html);
    });

    window.parent.jQuery('#aModal').modal('hide');
}

function clear_product_commentar(){
    jQuery(".review_product_name").html('');
    jQuery(".clear_com").hide();
}

</script>
<div class="jshop_edit">
    <form action="index.php?option=com_jshopping&controller=reviews" method="post" enctype="multipart/form-data" name="adminForm" id="adminForm">
        <?php print $this->tmp_html_start?>
        <div class="col100">
            <table class="admintable" >
                <tr>
                    <td class="key" style="width:180px;">
                        <?php echo \JText::_('JSHOP_PUBLISH');?>
                    </td>
                    <td>
                        <input type="checkbox" name="publish" value="<?php echo $this->review->publish; ?>" <?php if ($this->review->publish){ echo 'checked="checked"'; } ?> />
                    </td>
                </tr>
                <?php if ($this->review->review_id){ ?>
                    <tr>
                        <td class="key" style="width:180px;">
                            <?php echo JText::_('JSHOP_NAME_PRODUCT')?>
                        </td>
                        <td>
                            <span class="review_product_name">
                                <?php echo $this->review->name?>
                                <a class="btn btn-secondary clear_com" href="#" onclick="clear_product_commentar()">
                                    <?php print JText::_('JSHOP_CLEAR')?>
                                </a>
                            </span>                               
                            <a class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#aModal" onclick="jshopAdmin.cElName=0">
                                <?php print JText::_('JSHOP_LOAD')?>
                            </a>
                         </td>
                    </tr>
                <?php } else { ?>
                    <tr>
                        <td class="key" style="width:180px;">
                            <?php echo JText::_('JSHOP_PRODUCT')?>*
                        </td>
                        <td>
                            <span class="review_product_name"></span>
                            <a class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#aModal" onclick="jshopAdmin.cElName=0">
                                <?php print JText::_('JSHOP_LOAD')?>
                            </a>
                        </td>
                   </tr>    
                <?php } ?>
                <tr>
                    <td class="key" style="width:180px;">
                        <?php echo JText::_('JSHOP_USER')?>*
                    </td>
                    <td>
                        <input type="text" class="inputbox form-control" size="50" name="user_name" value="<?php echo $this->review->user_name?>" />
                    </td>
                </tr>
                <tr>
                    <td class="key" style="width:180px;">
                        <?php echo JText::_('JSHOP_EMAIL')?>*
                    </td>
                    <td>
                        <input type="text" class="inputbox form-control" size="50" name="user_email" value="<?php echo $this->review->user_email?>" />
                    </td>
                </tr>       
                  
                <tr>
                    <td  class="key">
                        <?php echo JText::_('JSHOP_PRODUCT_REVIEW')?>*
                    </td>
                    <td>
                        <textarea name="review" class="form-control" cols="35"><?php echo $this->review->review ?></textarea>
                    </td>
                </tr>
                <?php if (!$this->config->hide_product_rating){?>
                    <tr>
                        <td class="key">
                            <?php echo JText::_('JSHOP_REVIEW_MARK')?> 
                        </td>
                        <td>
                            <?php print $this->mark?>
                        </td>
                    </tr>
                <?php }?>
                <?php $pkey="etemplatevar";if ($this->$pkey){print $this->$pkey;}?>
            </table>
        </div>
        <div class="clr"></div>
        <input type="hidden" name="product_id" id="product_id" class="form-control" value="<?php print $this->review->product_id;?>" />
        <input type="hidden" name="review_id" value="<?php echo (int)$this->review->review_id?>">
        <input type="hidden" name="task" value="<?php echo \JFactory::getApplication()->input->getVar('task', 0)?>" />
        <?php print $this->tmp_html_end?>
    </form>

<?php print HTMLHelper::_(
    'bootstrap.renderModal',
    'aModal',
    array(
        'title'       => \JText::_('Products'),
        'backdrop'    => 'static',
        'url'         => 'index.php?option=com_jshopping&controller=product_list_selectable&tmpl=component',
        'height'      => '400px',
        'width'       => '800px',
        'bodyHeight'  => 70,
        'modalWidth'  => 80        
    )
); ?>

</div>