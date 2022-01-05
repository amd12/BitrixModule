<?
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
global $APPLICATION;

if(!check_bitrix_sessid()){
    return;
}

if($errorException = $APPLICATION->GetException()){
    echo(CAdminMessage::ShowMessage($errorException->GetString()));
}else{
    echo(CAdminMessage::ShowNote(Loc::getMessage(MODULE_LANG_PREFIX."_STEP_BEFORE")." ".Loc::getMessage(MODULE_LANG_PREFIX."_STEP_AFTER")));
}
?>

<form action="<? echo($APPLICATION->GetCurPage()); ?>">
    <input type="hidden" name="lang" value="<? echo(LANG); ?>" />
    <input type="submit" value="<? echo(Loc::getMessage(MODULE_LANG_PREFIX."_STEP_SUBMIT_BACK")); ?>">
</form>