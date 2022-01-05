<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

if (!Loader::includeModule('ml.settings')) return;

Loc::loadMessages(__FILE__);

$aMenu = array(
    "parent_menu" => "global_menu_services",
    "sort" => 159,
    'text' => "Настройки сайта от Medialine",
    'title' => "Настройки сайта от Medialine",
    "icon" => "sys_menu_icon",
    "page_icon" => "sys_menu_icon",
    "items_id" => "ml_settings",
    'section' => "ml_settings",
    'menu_id' => 'ml_settings',
    "items" => [
        [
            'sort' => 1000,
            'url' => '/bitrix/admin/settings.php?lang=ru&mid=ml.settings&mid_menu=1',
            'text' => "Настройки",
            'title' => "Настройки",
            'items_id' => 'ml_settings_modules',
            'icon' => 'sale_menu_icon',
        ],
    ]

);

return $aMenu;