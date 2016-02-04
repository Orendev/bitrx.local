<?
/**
 * Created by PhpStorm.
 * User: Abai Adelshin
 * www.orendev.ru
 */
use \Bitrix\Main\Localization\Loc;

if (!check_bitrix_sessid())
	return;

#работа с .settings.php
$install_count = \Bitrix\Main\Config\Configuration::getInstance()->get('adelshin_module_person');

$cache_type = \Bitrix\Main\Config\Configuration::getInstance()->get('cache'); // считаем данный из файла .settings.php секция 'cache'
#работа с .settings.php

Loc::loadMessages(__FILE__);
if ($ex = $APPLICATION->GetException()) //получаем обЪект который содержит последние ошибки
	echo CAdminMessage::ShowMessage(array(
		"TYPE" => "ERROR",
		"MESSAGE" => Loc::getMessage("MOD_INST_ERR"),
		"DETAILS" => $ex->GetString(),
		"HTML" => true,
	));
else 
	echo CAdminMessage::ShowNote(Loc::getMessage("MOD_INST_OK"));


#работа с .settings.php
echo CAdminMessage::ShowMessage(array("MESSAGE"=>Loc::getMessage("ADELSHIN_PERSON_INSTALL_COUNT").$install_count['install'],"TYPE"=>"OK"));

if(!$cache_type['type'] || $cache_type['type']=='none') // проверим секцию 'cache' подсекцию 'type' - если ее нет или установлена none выведем сообщение, что кеширование не настроено
    echo CAdminMessage::ShowMessage(array("MESSAGE"=>Loc::getMessage("ADELSHIN_PERSON_NO_CACHE"),"TYPE"=>"ERROR"));
#работа с .settings.php
?>


<form action="<?echo $APPLICATION->GetCurPage(); ?>">
	<input type="hidden" name="lang" value="<?echo LANG ?>">
	<input type="submit" name="" value="<?echo Loc::getMessage("MOD_BACK"); ?>">
<form>