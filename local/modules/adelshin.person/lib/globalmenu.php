<?php
/**
 * Created by PhpStorm.
 * User: Abai Adelshin
 * www.orendev.ru
 */

namespace Adelshin\Person;

class GlobalMenu
{
    function AddGlobalMenuItem(&$aGlobalMenu, &$aModuleMenu)
    {
        $aModuleMenu[] = array(
            "parent_menu" => "global_menu_custom",
            "icon" => "default_menu_icon",
            "page_icon" => "default_page_icon",
            "sort"=>"100",
            "text"=>"Управление таблицей персон",
            "title"=>"Custom Item Tille",
            "url"=>"/bitrix/admin/person_admin.php",
            "more_url"=>array(),
        );

        $arRes = array(
            "global_menu_custom" => array(
                "menu_id" => "custom",
                "page_icon" => "services_title_icon",
                "index_icon" => "services_page_icon",
                "text" => "Person",
                "title" => "Custom title",
                "sort" => 400,
                "items_id" => "global_menu_custom",
                "help_section" => "custom",
                "items" => array()
            ),
        );

        return $arRes;
    }
}

class tempModule
{
    function test()
    {

    }
}