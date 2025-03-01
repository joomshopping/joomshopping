<?php
/**
* @version      5.6.0 01.03.2025
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Table;
defined('_JEXEC') or die();

class AddondependenciesTable extends ShopbaseTable{
    
    function __construct(&$_db){
        parent::__construct('#__jshopping_addons_dependencies', 'id', $_db);
    }

    function loadAlias($alias, $parent){
        $this->load(['alias'=>$alias, 'parent'=>$parent]);
        $this->alias = $alias;
        $this->parent = $parent;
    }

}