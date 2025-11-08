<?php

/**
 * @version      5.8.3 01.03.2025
 * @author       MAXXmarketing GmbH
 * @package      Jshopping
 * @copyright    Copyright (C) 2010 webdesigner-profi.de. All rights reserved.
 * @license      GNU/GPL
 */

namespace Joomla\Component\Jshopping\Administrator\Model;

use Joomla\Component\Jshopping\Site\Lib\JSFactory;
use Joomla\CMS\Factory;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\File;

defined('_JEXEC') or die();

class AddonsOverrideModel extends BaseadminModel {

    public function overrideView($alias, $customFolder) {
        $config = JSFactory::getConfig();
        $template = $this->getFrontTemplate();

        $addon_folder = $config->path . 'templates/addons/' . $alias;
        if (!is_dir($addon_folder)) {
            throw new \RuntimeException("Addon folder '$addon_folder' does not exist.");
        }

        $addonFiles = Folder::files($addon_folder, '.', true, true);
        if (empty($addonFiles)) {
            throw new \RuntimeException("No files found in addon folder.");
        }

        $destination = $customFolder ? $customFolder : "templates/$template/html/com_jshopping/addons/$alias";
        $destination = JPATH_SITE . '/' . $destination;

        if (!is_dir($destination)) {
            if (!Folder::create($destination)) {
                throw new \RuntimeException("Cannot create destination folder '$destination'.");
            }
        }

        if (!is_writable($destination)) {
            throw new \RuntimeException("Folder '$destination' is not writable.");
        }

        if (!Folder::copy($addon_folder, $destination, '', true)) {
            throw new \RuntimeException("Failed to copy files from addon to destination.");
        }

        $copiedFiles = [];
        foreach ($addonFiles as $file) {
            $copiedFiles[] = basename($file);
        }

        return [
            'destination' => $destination,
            'files' => $copiedFiles,
            'total' => count($copiedFiles)
        ];
    }

    public function overrideJsOrCss($alias, $customFolder, $fileType) {
        $config = JSFactory::getConfig();
        $template = $this->getFrontTemplate();

        $addon_file = $config->path . $fileType . '/addons/' . $alias . '.' . $fileType;
        if (!is_file($addon_file)) {
            throw new \RuntimeException("Addon file '$addon_file' does not exist.");
        }

        $destination = $customFolder ? $customFolder : "templates/$template/" .$fileType. "/addons/";
        $destination = JPATH_SITE . '/' . $destination;

        if (!is_dir($destination)) {
            if (!Folder::create($destination)) {
                throw new \RuntimeException("Cannot create destination folder '$destination'.");
            }
        }

        if (!is_writable($destination)) {
            throw new \RuntimeException("Folder '$destination' is not writable.");
        }

        $dest_file = $destination . '/' . basename($addon_file);
        if (!File::copy($addon_file, $dest_file)) {
            throw new \RuntimeException("Failed to copy $fileType file to destination.");
        }

        $copiedFiles = [basename($addon_file)];

        return [
            'destination' => $destination,
            'files' => $copiedFiles,
            'total' => count($copiedFiles)
        ];    
    }

    private function getFrontTemplate() {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('template'))
            ->from($db->quoteName('#__template_styles'))
            ->where('client_id = 0 AND home = 1');
        $db->setQuery($query);
        return $db->loadResult();
    }
}
