<?php
/**
 * @version      5.9.3 08.05.2026
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

    public function getSourceFiles(string $alias): array {
        $jsPath = JPATH_SITE . '/components/com_jshopping/';
        $result = ['view' => [], 'js' => [], 'css' => []];

        $viewFolder = $jsPath . 'templates/addons/' . $alias;
        if (is_dir($viewFolder)) {
            $result['view'] = Folder::files($viewFolder, '.', false, false) ?: [];
        }

        $pattern = '^' . preg_quote($alias, '/');

        $jsFolder = $jsPath . 'js/addons';
        if (is_dir($jsFolder)) {
            $result['js'] = Folder::files($jsFolder, $pattern, false, false) ?: [];
        }

        $cssFolder = $jsPath . 'css/addons';
        if (is_dir($cssFolder)) {
            $result['css'] = Folder::files($cssFolder, $pattern, false, false) ?: [];
        }

        return $result;
    }

    public function getOverrideFiles(string $alias, int $templateId, string $templateName, array $config, int $clientId = 0): array {
        $result = [
            'view' => [], 'js' => [], 'css' => [],
            'use_custom_view' => false, 'use_custom_js' => false, 'use_custom_css' => false,
        ];

        $useCustomView = !empty($config['folder_overrides_view']);
        $useCustomJs   = !empty($config['folder_overrides_js']);
        $useCustomCss  = !empty($config['folder_overrides_css']);

        $result['use_custom_view'] = $useCustomView;
        $result['use_custom_js']   = $useCustomJs;
        $result['use_custom_css']  = $useCustomCss;

        $tmplBase = JPATH_SITE . '/' . $this->getTemplateRelBase($clientId) . '/';

        $viewFolder = $useCustomView
            ? JPATH_SITE . '/' . $config['folder_overrides_view']
            : ($templateName ? $tmplBase . $templateName . '/html/com_jshopping/addons/' . $alias : '');

        $jsFolder = $useCustomJs
            ? JPATH_SITE . '/' . $config['folder_overrides_js']
            : ($templateName ? $tmplBase . $templateName . '/js/addons' : '');

        $cssFolder = $useCustomCss
            ? JPATH_SITE . '/' . $config['folder_overrides_css']
            : ($templateName ? $tmplBase . $templateName . '/css/addons' : '');
        

        if ($viewFolder && is_dir($viewFolder)) {
            foreach (Folder::files($viewFolder, '.', false, false) ?: [] as $f) {
                $editUrl = !$useCustomView ? $this->getEditUrl($templateId, '/html/com_jshopping/addons/' . $alias . '/' . $f) : '';
                $result['view'][] = ['name' => $f, 'edit_url' => $editUrl];
            }
        }

        $pattern = '^' . preg_quote($alias, '/');

        if ($jsFolder && is_dir($jsFolder)) {
            foreach (Folder::files($jsFolder, $pattern, false, false) ?: [] as $f) {
                $editUrl = !$useCustomJs ? $this->getEditUrl($templateId, '/js/addons/' . $f) : '';
                $result['js'][] = ['name' => $f, 'edit_url' => $editUrl];
            }
        }

        if ($cssFolder && is_dir($cssFolder)) {
            foreach (Folder::files($cssFolder, $pattern, false, false) ?: [] as $f) {
                $editUrl = !$useCustomCss ? $this->getEditUrl($templateId, '/css/addons/' . $f) : '';
                $result['css'][] = ['name' => $f, 'edit_url' => $editUrl];
            }
        }

        return $result;
    }

    private function getEditUrl(int $templateId, string $filePath): string {
        if (!$templateId) return '';
        return 'index.php?option=com_templates&view=template&id=' . $templateId . '&file=' . base64_encode($filePath);
    }

    public function deleteOverrideFile(string $alias, string $fileType, string $templateName, string $fileName, string $customFolder = '', int $clientId = 0): bool {
        $this->validateAlias($alias);
        $this->validateTemplateName($templateName);
        $this->validateCustomFolder($customFolder);
        $tmplBase = JPATH_SITE . '/' . $this->getTemplateRelBase($clientId) . '/';
        switch ($fileType) {
            case 'view':
                $folder = $customFolder
                    ? JPATH_SITE . '/' . $customFolder
                    : $tmplBase . $templateName . '/html/com_jshopping/addons/' . $alias;
                break;
            case 'js':
                $folder = $customFolder
                    ? JPATH_SITE . '/' . $customFolder
                    : $tmplBase . $templateName . '/js/addons';
                break;
            case 'css':
                $folder = $customFolder
                    ? JPATH_SITE . '/' . $customFolder
                    : $tmplBase . $templateName . '/css/addons';
                break;
            default:
                throw new \RuntimeException("Invalid file type.");
        }

        $filePath = $folder . '/' . basename($fileName);
        $realPath = realpath($filePath);
        if ($realPath === false || strpos($realPath, realpath(JPATH_SITE)) !== 0) {
            throw new \RuntimeException("Invalid file path.");
        }
        if (!is_file($realPath)) {
            throw new \RuntimeException("File not found: $fileName");
        }

        return File::delete($realPath);
    }

    public function overrideView($alias, $customFolder, $templateName = '', $fileName = '', int $clientId = 0) {
        $this->validateAlias($alias);
        $this->validateTemplateName($templateName);
        $this->validateCustomFolder($customFolder);
        $config = JSFactory::getConfig();
        $template = $templateName ?: $this->getFrontTemplate();

        $addon_folder = $config->path . 'templates/addons/' . $alias;
        if (!is_dir($addon_folder)) {
            throw new \RuntimeException("Addon folder '$addon_folder' does not exist.");
        }

        $relBase     = $this->getTemplateRelBase($clientId);
        $destination = $customFolder ? $customFolder : "$relBase/$template/html/com_jshopping/addons/$alias";
        $destination = JPATH_SITE . '/' . $destination;

        if (!is_dir($destination)) {
            if (!Folder::create($destination)) {
                throw new \RuntimeException("Cannot create destination folder '$destination'.");
            }
        }

        if (!is_writable($destination)) {
            throw new \RuntimeException("Folder '$destination' is not writable.");
        }

        if ($fileName) {
            $sourceFile = $addon_folder . '/' . basename($fileName);
            if (!is_file($sourceFile)) {
                throw new \RuntimeException("Source file '$fileName' not found.");
            }
            if (!File::copy($sourceFile, $destination . '/' . basename($fileName))) {
                throw new \RuntimeException("Failed to copy file.");
            }
            return ['destination' => $destination, 'files' => [basename($fileName)], 'total' => 1];
        }

        $addonFiles = Folder::files($addon_folder, '.', true, true);
        if (empty($addonFiles)) {
            throw new \RuntimeException("No files found in addon folder.");
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

    public function overrideJsOrCss($alias, $customFolder, $fileType, $templateName = '', $fileName = '', int $clientId = 0) {
        $this->validateAlias($alias);
        $this->validateTemplateName($templateName);
        $this->validateCustomFolder($customFolder);
        $config = JSFactory::getConfig();
        $template = $templateName ?: $this->getFrontTemplate();

        $relBase      = $this->getTemplateRelBase($clientId);
        $sourceFolder = $config->path . $fileType . '/addons/';
        $destination  = $customFolder ? $customFolder : "$relBase/$template/$fileType/addons/";
        $destination  = JPATH_SITE . '/' . $destination;

        if (!is_dir($destination)) {
            if (!Folder::create($destination)) {
                throw new \RuntimeException("Cannot create destination folder '$destination'.");
            }
        }

        if (!is_writable($destination)) {
            throw new \RuntimeException("Folder '$destination' is not writable.");
        }

        if ($fileName) {
            $sourceFile = $sourceFolder . basename($fileName);
            if (!is_file($sourceFile)) {
                throw new \RuntimeException("Source file '$fileName' does not exist.");
            }
            if (!File::copy($sourceFile, $destination . '/' . basename($fileName))) {
                throw new \RuntimeException("Failed to copy $fileType file to destination.");
            }
            return ['destination' => $destination, 'files' => [basename($fileName)], 'total' => 1];
        }

        $pattern    = '^' . preg_quote($alias, '/');
        $sourceFiles = Folder::files($sourceFolder, $pattern, false, false) ?: [];
        if (empty($sourceFiles)) {
            throw new \RuntimeException("No $fileType files found for alias '$alias'.");
        }

        $copiedFiles = [];
        foreach ($sourceFiles as $f) {
            if (!File::copy($sourceFolder . $f, $destination . '/' . $f)) {
                throw new \RuntimeException("Failed to copy $fileType file '$f' to destination.");
            }
            $copiedFiles[] = $f;
        }

        return [
            'destination' => $destination,
            'files' => $copiedFiles,
            'total' => count($copiedFiles)
        ];
    }

    public function getPaths(string $alias, string $templateName, array $config, int $clientId = 0): array {
        $useCustomView = !empty($config['folder_overrides_view']);
        $useCustomJs   = !empty($config['folder_overrides_js']);
        $useCustomCss  = !empty($config['folder_overrides_css']);
        $tmplBase      = $this->getTemplateRelBase($clientId);

        return [
            'source' => [
                'view' => 'components/com_jshopping/templates/addons/' . $alias,
                'js'   => 'components/com_jshopping/js/addons',
                'css'  => 'components/com_jshopping/css/addons',
            ],
            'override' => [
                'view' => $useCustomView
                    ? $config['folder_overrides_view']
                    : ($templateName ? $tmplBase . '/' . $templateName . '/html/com_jshopping/addons/' . $alias : ''),
                'js'   => $useCustomJs
                    ? $config['folder_overrides_js']
                    : ($templateName ? $tmplBase . '/' . $templateName . '/js/addons' : ''),
                'css'  => $useCustomCss
                    ? $config['folder_overrides_css']
                    : ($templateName ? $tmplBase . '/' . $templateName . '/css/addons' : ''),
            ],
        ];
    }

    public function getTemplates(): array {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select([
                $db->qn('extension_id', 'id'),
                $db->qn('element', 'template'),
                $db->qn('name', 'title'),
                $db->qn('client_id'),
            ])
            ->from($db->qn('#__extensions'))
            ->where($db->qn('type') . ' = ' . $db->q('template'))
            ->where($db->qn('enabled') . ' = 1')
            ->order($db->qn('client_id') . ' ASC, ' . $db->qn('name') . ' ASC');
        $db->setQuery($query);
        return $db->loadObjectList() ?: [];
    }

    public function getDefaultTemplate(): ?object {
        $frontTemplate = $this->getFrontTemplate();
        foreach ($this->getTemplates() as $t) {
            if ($t->template === $frontTemplate) {
                return $t;
            }
        }
        return null;
    }

    private function getTemplateRelBase(int $clientId): string {
        return $clientId === 1 ? 'administrator/templates' : 'templates';
    }

    private function validateAlias(string $alias): void {
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $alias)) {
            throw new \RuntimeException("Invalid alias.");
        }
    }

    private function validateTemplateName(string $templateName): void {
        if ($templateName === '') return;
        foreach ($this->getTemplates() as $t) {
            if ($t->template === $templateName) return;
        }
        throw new \RuntimeException("Invalid template.");
    }

    private function validateCustomFolder(string $folder): void {
        if ($folder !== '' && str_contains($folder, '..')) {
            throw new \RuntimeException("Invalid folder path.");
        }
    }

    private function getFrontTemplate(): string {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->qn('template'))
            ->from($db->qn('#__template_styles'))
            ->where('client_id = 0 AND home = 1');
        $db->setQuery($query);
        return $db->loadResult();
    }
}
