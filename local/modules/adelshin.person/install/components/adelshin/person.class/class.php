<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
/**
 * Базовый компонент для списков
 * version 1.0.1
 * Created by PhpStorm.
 * User: Abai Adelshin
 * www.orendev.ru
 * Date: 01.02.2016
 * Time: 11:15
 * Базовый класс компонента для формирования списков (news.list)
 * По умолчанию формирует независимый блок новостной карусели
 * Необходимо подключить фреймворк bootstrap и jquery
 *
 * Доработать - Сформировать таблицу стилей
 * Доработать Проверка условия подключения фреймворка bootstrap
 *
 */
use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;
use \Adelshin\Person\PersonTable;

class personClass extends CBitrixComponent
{

    protected $showError;

    /**
     * @return bool
     * @throws \Bitrix\Main\LoaderException
     */
    protected function checkModules()
    {
        if (!Loader::includeModule('adelshin.person'))
        {
            ShowError(Loc::getMessage('ADELSHIN_PERSON_MODULE_NOT_INSTALLED'));
            return false;
        }

        return true;
    }

    /**
     * @throws Exception
     * Основная функция обработки данных, реализуем логику запроса и формирования конечного массива $arResult
     */
    public function inWorkCacheFunction()
    {
        $rsElement = PersonTable::getList(array( //возвращает объект
            'select' => array('ID', 'FIRST_NAME', 'LAST_NAME', 'GENDER'),// выборка полей которые надо получить
            //'filter' => array(), //описание фильтра
            'order' => array('ID' => 'DESC'), // сортировка по убывание id
            //'limit' => 3, // количество записей
            //'offset' => 2, //смещение для лимит
        ));
        while ($row = $rsElement->fetch())
        {
            $result[] = $row;
        }

        return $result;
    }
    public function getArElementsRelation() // метод для вывода связанных данных
    {
        $result = PersonTable::getList(array( //возвращает объект
            'select' => array('ID', 'FIRST_NAME', 'LAST_NAME', 'GENDER', 'GROUPS.TITLE'),// выборка полей которые надо получить
            //'filter' => array(), //описание фильтра
            'order' => array('ID' => 'DESC'), // сортировка по убывание id
            //'limit' => 3, // количество записей
            //'offset' => 2, //смещение для лимит
        ));

        return $result->fetchAll();
    }
    // Вспомогательные функции для наследуемых компонентов

    public function executeComponent()
    {
        $this -> includeComponentLang('class.php'); // подключаем языковые файлы

        if ($this -> checkModules())
        {
            /* наш код */

            $this -> arResult[] = $this ->inWorkCacheFunction();

            $this->includeComponentTemplate();
        }

    }
}