<?php

namespace Ml\Settings;

use Bitrix\Main\Config\Option;

class Module
{
    const MODULE_ID = 'ml.settings';
    const LOG_PATH = '/local/logs/';

    public function GetOptions(){
        return Option::getForModule(self::MODULE_ID, SITE_ID);
    }

    /**
     * @return bool
     */
    public static function isAdminSection () {
        return (defined('ADMIN_SECTION') && ADMIN_SECTION === true);
    }

    /**
     * @param $file_name
     * @param $message
     * Добавляет файл с логом
     * Путь \local\logs\'Имя модуля'\...
     */
    public function addLog($file_name, $message){
        $path = $_SERVER["DOCUMENT_ROOT"].self::LOG_PATH.self::MODULE_ID."/";
        if (!file_exists($path)) { // создаем папку для временных файлов.
            mkdir($path, 0775, true);
        }

        $data = date("Y-m-d H:i:s")."\n";
        $data .= print_r($message,1)."\n\n";
        file_put_contents($path.$file_name.".log", $data, FILE_APPEND);
    }
}