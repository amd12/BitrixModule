<?php

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;
use \Bitrix\Main\Localization\Loc;


Loc::loadMessages(__FILE__);

class ml_settings extends CModule
{
    var $MODULE_ID = 'ml.settings';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_GROUP_RIGHTS = 'Y';

    public function __construct()
    {
        $arModuleVersion = [];
        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_ID = str_replace("_", ".", get_class($this));
        $this->MODULE_NAME = "Настройки сайта от Medialine";
        $this->MODULE_DESCRIPTION = Loc::getMessage(MODULE_LANG_PREFIX . '_MODULE_DESCRIPTION');
        $this->MODULE_GROUP_RIGHTS = 'Y';
        $this->PARTNER_NAME = 'Medialine';
        $this->PARTNER_URI = 'http://medialine.by';
        return false;
    }

    function get_gath($notDocumentRoot = false)
    {
        if ($notDocumentRoot) {
            return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
        } else {
            return dirname(__DIR__);
        }
    }

    public function DoInstall()
    {
        try {

            ModuleManager::registerModule($this->MODULE_ID);
            $this->InstallFiles();
            $this->InstallDB();
            $this->install_events();

        } catch (Exception $exception) {
            $GLOBALS['APPLICATION']->ThrowException(
                $exception->getMessage()
            );
        }
        return true;
    }

    public function doUninstall()
    {
        try {
            $this->UnInstallFiles();
            $this->UnInstallDB();
            $this->uninstall_events();
            ModuleManager::unRegisterModule($this->MODULE_ID);
        } catch (Exception $exception) {
            $GLOBALS['APPLICATION']->ThrowException(
                $exception->getMessage()
            );
        }

        return true;
    }

    function InstallFiles()
    {
        $doc_root = Application::getDocumentRoot();

        $dir_from = sprintf('%s/local/modules/%s/install/admin', $doc_root, $this->MODULE_ID);
        $dir_to = sprintf('%s/bitrix/admin', $doc_root);
        CopyDirFiles($dir_from, $dir_to, true, true, false);

        $dir_from = sprintf('%s/local/modules/%s/install/js', $doc_root, $this->MODULE_ID);
        $dir_to = sprintf('%s/bitrix/js/%s', $doc_root, $this->MODULE_ID);
        CopyDirFiles($dir_from, $dir_to, true, true, false);

        $dir_from = sprintf('%s/local/modules/%s/install/css', $doc_root, $this->MODULE_ID);
        $dir_to = sprintf('%s/bitrix/css/%s', $doc_root, $this->MODULE_ID);
        CopyDirFiles($dir_from, $dir_to, true, true, false);

        return false;
    }

    function UnInstallFiles()
    {
        $dir_to_del = sprintf('/bitrix/css/%s', $this->MODULE_ID);
        DeleteDirFilesEx($dir_to_del);

        $dir_to_del = sprintf('/bitrix/js/%s', $this->MODULE_ID);
        DeleteDirFilesEx($dir_to_del);

        DeleteDirFiles($_SERVER["DOCUMENT_ROOT"] . '/local/modules/' . $this->MODULE_ID . '/install/admin', $_SERVER["DOCUMENT_ROOT"] . "/bitrix/admin");
    }

    function install_events()
    {
        $eventManager = EventManager::getInstance();

        $events = self::getEventsList();
        foreach ($events as $arEvent) {
            $eventManager->registerEventHandler(
                $arEvent['module'],
                $arEvent['event'],
                $this->MODULE_ID,
                $arEvent['class'],
                $arEvent['method']
            );
        }
        return true;
    }

    function uninstall_events()
    {
        $eventManager = EventManager::getInstance();

        $events = self::getEventsList();
        foreach ($events as $arEvent) {
            $eventManager->unRegisterEventHandler(
                $arEvent['module'],
                $arEvent['event'],
                $this->MODULE_ID,
                $arEvent['class'],
                $arEvent['method']
            );
        }

        return true;
    }

    function InstallDB()
    {
        try {
            Loader::includeModule($this->MODULE_ID);

            foreach ($this->getTableList() as $table) {
                $table::getEntity()->createDbTable();
            }

        } catch (Exception $exception) {
            AddMessage2Log($exception->getMessage(), $this->MODULE_ID);
            return false;
        }

        return true;
    }

    function UnInstallDB()
    {
        try {
            Loader::includeModule($this->MODULE_ID);

            foreach ($this->getTableList() as $table) {
                \Bitrix\Main\Application::getConnection()->dropTable(
                    $table::getEntity()->getDBTableName()
                );
            }
        } catch (Exception $exception) {
            AddMessage2Log($exception->getMessage(), $this->MODULE_ID);
            return false;
        }

        return true;
    }

    private function getTableList()
    {
        return [];
    }

    private function getEventsList()
    {
        return [];
    }
}
