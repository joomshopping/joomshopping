<?php
/**
* @version      5.10.0 10.10.2026
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
defined('_JEXEC') or die();
?>
<div class="myaccount_menu_block">
    <ul class="myaccount_menu">
        <?php foreach($this->account_menu as $key => $item){?>
            <li class="myaccount_menu_<?php print $key?>">
                <a href="<?php print $item['href']?>"<?php if ($item['active']){?> class="active" aria-current="page"<?php }?>>
                    <?php if ($item['icon']){?><span class="fas fa-fw <?php print $item['icon']?>" aria-hidden="true"></span><?php }?>
                    <?php print $item['title']?>
                </a>
            </li>
        <?php }?>
    </ul>
    <?php print $this->tmpl_my_account_html_content ?? ''?>
</div>
