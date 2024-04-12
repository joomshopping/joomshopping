<?php
/**
* @version      5.1.0 15.09.2022
* @author       MAXXmarketing GmbH
* @package      Jshopping
* @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
* @license      GNU/GPL
*/
namespace Joomla\Component\Jshopping\Site\Helper;
defined('_JEXEC') or die();

class File {

    public static function rename($dir, $old_file_name, $name_new, $autoname = 1) {
        $pathinfo = pathinfo($old_file_name);
        $i = 0;
        if ($autoname) {
            do {
                $nr = $i > 0 ? $i : '';
                $new_file_name = self::renameFileFilter($name_new) . $nr . '.' . $pathinfo['extension'];
                $i++;
            } while (is_file($dir."/".$new_file_name));
        } else {
            $new_file_name = $name_new;
        }
        if (rename($dir."/".$old_file_name, $dir."/".$new_file_name)) {
            return $new_file_name;
        } else {
            return null;
        }
    }

    public static function renameFileFilter($name){
        $name = strtr($name, array('ü'=>'u','ä'=>'a','ö'=>'o','Ü'=>'U','Ä'=>'A','Ö'=>'O','ß'=>'ss','а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>'','і'=>'i','є'=>'e','ї'=>'y'));
        $name = preg_replace("/[^a-zA-Z0-9\.\-]/", "_", $name);
    return $name;
    }
}