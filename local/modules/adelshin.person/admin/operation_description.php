<?
/**
 * Created by PhpStorm.
 * User: Adelshin Abai
 * Site: www.orendev.ru
 * Date: 04.02.16
 * Time: 2:59
 */
// файл с описаниями операций расширенного управления правами доступа
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

return array(
    "ADELSHIN_ORM_READ" => array(
        "title" => Loc::getMessage('OP_NAME_ADELSHIN_ORM_READ'),
        "description" => Loc::getMessage('OP_DESC_ADELSHIN_ORM_READ'),
    ),
    "ADELSHIN_ORM_SETTINGS" => array(
        "title" => Loc::getMessage('OP_NAME_ADELSHIN_ORM_SETTINGS'),
        "description" => Loc::getMessage('OP_DESC_ADELSHIN_ORM_SETTINGS'),
    ),
    "ADELSHIN_ORM_EDIT" => array(
        "title" => Loc::getMessage('OP_NAME_ADELSHIN_ORM_EDIT'),
        "description" => Loc::getMessage('OP_DESC_ADELSHIN_ORM_EDIT'),
    ),
);