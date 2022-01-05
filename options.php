<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Medialine\Search\Main;

Loc::loadMessages(__FILE__);
global $APPLICATION;

$request = HttpApplication::getInstance()->getContext()->getRequest();

$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);
Loader::includeModule($module_id);
Loader::includeModule("iblock");

$arBrands = [];
$iterator = \Bitrix\Iblock\ElementTable::getList([
        'filter' => ['IBLOCK_ID' => IBLOCK_BRAND],
        'select' => ['ID', 'NAME']
]);
while ($arBrand = $iterator->fetch()){
    $arBrands[$arBrand['ID']] = $arBrand['NAME'];
}

/**
 * Интерфейс меню
 * @return array[][]
 */
$interface = function () use ($arBrands) {

    $arOptions[] = Loc::getMessage(MODULE_LANG_PREFIX . '_MODULE_BRAND_SELECTION');
    $arOptions[] = array('choose_brand', Loc::getMessage(MODULE_LANG_PREFIX . '_MODULE_BRAND'), false, array('selectbox', $arBrands));
    $arOptions[] = array('note' =>	Loc::getMessage(MODULE_LANG_PREFIX . '_MODULE_EXPLANATORY_TEXT') );

    $arBlock = [
        [
            [
                "DIV" => "settings",
                "TAB" => Loc::getMessage(MODULE_LANG_PREFIX . '_MODULE_TAB_SETTINGS'),
                "TITLE" => Loc::getMessage(MODULE_LANG_PREFIX . '_MODULE_TITLE'),
                "OPTIONS" => $arOptions
            ],
            [
                "DIV" => "access",
                "TAB" => Loc::getMessage(MODULE_LANG_PREFIX . '_MODULE_TAB_ACCESS'),
                "TITLE" =>  Loc::getMessage(MODULE_LANG_PREFIX . '_MODULE_ACCESS'),
            ]
        ],
    ];
    return $arBlock;
};

/*
 * Обрабатываем данные после отправки формы
 */
if ($request->isPost() && check_bitrix_sessid()) {
    foreach ($interface() as $arTabs) {
        foreach ($arTabs as $aTab) {
            foreach ($aTab["OPTIONS"] as $arOption) {
                __AdmSettingsSaveOption($module_id, $arOption);
            }
        }
    }

    /*
     * Класс обработки.
    */
    $obBrand = new \Ml\Settings\Brand();
    $req =  $obBrand->init();

    $REQUEST_METHOD = "POST";
    ob_start();
    require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/admin/group_rights.php');
    ob_end_clean();
    LocalRedirect($APPLICATION->GetCurPage() . '?lang=' . LANGUAGE_ID . '&mid=' . $module_id);
}

/*
 * Создаем форму для редактирвания параметров модуля
 */
 foreach ($interface() as $arTabs) {
    $tabControl = new CAdminTabControl(
        "tabControl",
        $arTabs
    );
    $tabControl->Begin(); ?>
    <form action="<?= $APPLICATION->GetCurPage(); ?>?mid=<?= $module_id; ?>&lang=<?= LANG; ?>" method="post">
        <? foreach ($arTabs as $aTab) {

            if ($aTab["OPTIONS"] && $aTab["DIV"] != "access") {
                $tabControl->BeginNextTab();
                __AdmSettingsDrawList($module_id, $aTab["OPTIONS"]);
            }

            if ($aTab["DIV"] == "access") {
                $tabControl->BeginNextTab();
                require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/admin/group_rights.php");
            }
        }

        $tabControl->Buttons([
            "back_url" => $_REQUEST["back_url"],
            "btnApply" => true,
            "btnSave" => true,
        ]); ?>
        <input type="hidden" name="Update" value="Y">
        <?= bitrix_sessid_post(); ?>
    </form>
    <?
    $tabControl->End();
} ?>
