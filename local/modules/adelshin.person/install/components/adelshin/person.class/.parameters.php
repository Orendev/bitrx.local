<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitrix\Main\Loader;
if (!Loader::includeModule('iblock'))
{
    ShowMessage(GetMessage('IBLOCK_ERROR'));
    return;
}

$iblockExits = (!empty($arCurrentValues['IBLOCK_ID']) && intval($arCurrentValues['IBLOCK_ID']) > 0);
$arIBlockType = CIBlockParameters::GetIBlockTypes(array('-' => ' '));//Получение списка типов инфоблоков

$arIBlock = array();
$iblockFilter = (
    !empty($arCurrentValues['IBLOCK_TYPE'])
        ? array('TYPE' => $arCurrentValues['IBLOCK_TYPE'], 'ACTIVE' => 'Y')
        : array('ACTIVE' => 'Y')
);
$dbIBlock = CIBlock::GetList(array('SORT' => 'ASC'), $iblockFilter);//Получение списка инфоблока заданного типа
while ($arr = $dbIBlock -> Fetch())
{
    $arIBlock[$arr['ID']] = '[' . $arr['ID'] . '] ' . $arr['NAME'];
}
unset($arr, $dbIBlock, $iblockFilter); // Освобождаем переменные

/**
 * Формируем массив переменных
 */
$arComponentParameters = array(
    "GROUPS" => array(
    ),
    "PARAMETERS" => array(
        "SEF_MODE" => array(),
        "IBLOCK_TYPE" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("IT_IBLOCK_TYPE"),
            "TYPE" => "LIST",
            "ADDITIONAl_VALUES" => "Y",
            "VALUES" => $arIBlockType,
            "REFRESH" => "Y",
        ),
        "IBLOCK_ID" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("IT_IBLOCK_ID"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arIBlock,
            "REFRESH" => "Y"
        ),
        "CACHE_TIME" => array(
            "DEFAULT" => 360000000
        ),
        "COUNT_ITEM_PAGE" => array(
            "PARENT" => "VISUAL",
			"NAME" => GetMessage("IT_COUNT_ITEM_PAGE"),
			"TYPE" => "STRING",
			"DEFAULT" => 4
        ),
        "COUNT_ITEM" => array(
            "PARENT" => "VISUAL",
            "NAME" => GetMessage("IT_COUNT_ITEM"),
            "TYPE" => "STRING",
            "DEFAULT" => 16
        ),
        "WIDTH_PICTURE_SMALL" => array(
            "PARENT" => "VISUAL",
            "NAME" => GetMessage("IT_WIDTH_PICTURE_SMALL"),
            "TYPE" => "STRING",
            "DEFAULT" => 360
        ),
        "HEIGHT_PICTURE_SMALL" => array(
            "PARENT" => "VISUAL",
            "NAME" => GetMessage("IT_HEIGHT_PICTURE_SMALL "),
            "TYPE" => "STRING",
            "DEFAULT" => 240
        ),
    ),
);