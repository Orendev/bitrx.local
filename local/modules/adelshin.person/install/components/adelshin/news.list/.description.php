<?
/**
 * Created by PhpStorm.
 * User: Abai Adelshin
 * www.orendev.ru
 */
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
    "NAME" => GetMessage("IT_COMPONENT_NAME"),
    "DESCRIPTION" => "",
    "ICON" => "/images/icon.gif",
    "SORT" => 10,
    "CACHE_PATH" => "Y",
    "PATH" => array(
        "ID" => GetMessage("IT_COMPONENT_ID"),
    ),
    "COMPLEX" => "N",
);

?>