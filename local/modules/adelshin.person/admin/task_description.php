<?
/**
 * Created by PhpStorm.
 * User: Adelshin Abai
 * Site: www.orendev.ru
 * Date: 04.02.16
 * Time: 2:59
 */
// файл с описанием уровней доступа для расширенного управлением доступом
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

return array(
    "ADELSHIN_ORM_DENIED" => array(
        "title" => Loc::getMessage('TASK_NAME_ADELSHIN_ORM_DENIED'),
        "description" => Loc::getMessage('TASK_DESC_ADELSHIN_ORM_DENIED'),
    ),
    "ADELSHIN_ORM_READ" => array(
        "title" => Loc::getMessage('TASK_NAME_POKOEV_ORM_READ'),
        "description" => Loc::getMessage('TASK_DESC_ADELSHIN_ORM_READ'),
    ),
    "ADELSHIN_ORM_FULL_ACCESS" => array(
        "title" => Loc::getMessage('TASK_NAME_POKOEV_ORM_FULL_ACCESS'),
        "description" => Loc::getMessage('TASK_DESC_ADELSHIN_FULL_ACCESS'),
    ),
    "MODULE" => array(
        "title" => Loc::getMessage("TASK_BINDING_MODULE"),
    ),
);
