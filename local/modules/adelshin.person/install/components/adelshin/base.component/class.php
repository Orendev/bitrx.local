<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
use Bitrix\Main\Loader;
/*
 * Базовый класс для создания своих компонентов для Bitrix
 * Автор max22, 2015 год, http://max22.ru, maxim@max22.ru
 */


class TBaseComponent extends CBitrixComponent{

    const CACHE_TIME_DEFAULT = 36000000; // время кэшироавния по умолчанию
    protected $IBLOCK_ID = 0;
    protected $SECTION_ID = 0;
    protected $elementsSortDefault = array('SORT' => 'ASC', 'active_from'=>'DESC', 'NAME'=>'ASC');
    protected $sectionsSortDefault = array('SORT' => 'ASC', 'NAME'=>'ASC');
    protected $arSaveInCacheKeys = array('META','CHAIN');
    protected $elementArSelect = array(
        'IBLOCK_ID',
        'ID',
        'NAME',
        'ACTIVE_FROM',
        'ACTIVE_TO',
        'PREVIEW_TEXT',
        'PREVIEW_PICTURE',
        'DETAIL_TEXT',
        'DETAIL_PICTURE',
        'CODE',
        'XML_ID',
        'DETAIL_PAGE_URL',
        'LIST_PAGE_URL');
    protected $sectionArSelect = array(
        'IBLOCK_ID',
        'ID',
        'NAME',
        'CODE',
        'SECTION_PAGE_URL',
        'XML_ID',
        'PICTURE',
        'DESCRIPTION',
        'IBLOCK_SECTION_ID',
        'DETAIL_PICTURE',
        'DEPTH_LEVEL',);
    protected $set404 = false;
    protected $showError = false;


    protected $includeComponentTemplateInCache = true; // флаг место подключения шаблона компонента, по умолчанию подключаем внутри шаблона


    /**
     * @param $arParams
     * @return mixed
     */
    public function onPrepareComponentParams($arParams)
    {
        $arParams['CACHE_TIME'] = isset($arParams['CACHE_TIME']) ? $arParams['CACHE_TIME'] : self::CACHE_TIME_DEFAULT;
        return $arParams;
    }

    public function executeComponent() {
        try {
            if ($this->notCache() || $this->StartResultCache(false)){

                if(!Loader::includeModule('iblock'))
                {
                    ShowError(GetMessage("IT_MODULE_NOT_INSTALLED"));
                    return;
                }

                $this->inCacheFunction();

                if ($this->includeComponentTemplateInCache)
                {
                    $this->SetResultCacheKeys($this->arSaveInCacheKeys);
                    $this->IncludeComponentTemplate();
                } else {
                    $this->EndResultCache();
                }
            }

            $this->lastCacheFunction();

            if (!$this->includeComponentTemplateInCache)
            {
                $this->IncludeComponentTemplate();
            }

        } catch (Exception $exc) {
            if ($this->set404)
            {
                @define("ERROR_404","Y");
            } elseif($this->showError)
            {
                $this->__showError($exc->getMessage());
            }
            $this->AbortResultCache();
        }
    }

    /**
     * Основная рабочая функция
     */
    protected function inCacheFunction(){

    }

    /**
     * Функция будет выполнять всегда не зависимо от того закеширован компонент или нет
     */
    protected function lastCacheFunction(){
        $this->setChainAndMeta();
    }

    /**
     * Функция для заполнения мета тегов и хлебных крошек
     */
    private function setChainAndMeta(){
        if (isset($this->arResult['CHAIN'])){
            foreach ($this->arResult['CHAIN'] as $arChain){
                $GLOBALS['APPLICATION']->AddChainItem($arChain['NAME'], $arChain['LINK']);
            }
        }
        if (isset($this->arResult['META'])){
            $arMeta = $this->arResult['META'];
            if( isset($arMeta['H1']) && strlen($arMeta['H1']) ){
                $GLOBALS['APPLICATION']->SetTitle($arMeta['H1']);
                unset($arMeta['H1']);
            }
            foreach ($arMeta as $key=>$value){
                if( strlen($arMeta[$key]) ){
                    $GLOBALS['APPLICATION']->SetPageProperty($key, $value);
                }
            }
            //facebook_appid
            $fbApiId = COption::GetOptionString('socialservices','facebook_appid');
            if( $fbApiId && strlen($fbApiId)){
                $GLOBALS['APPLICATION']->SetPageProperty('fb:app_id', $fbApiId);
            }
        }
    }

    /**
     * @return bool
     * Определяем условия для кеширования
     */
    protected function notCache(){
        return false;
    }

    //Вспомогательные функции для наследуемых компонентов
    protected function getNavChainBySectionId($sectionId){
        $nav = CIBlockSection::GetNavChain(false, $sectionId);
        while($ar_result = $nav->GetNext()){
            $this->arResult['CHAIN'][] = array(
                'NAME' => $ar_result['NAME'],
                'LINK' => $ar_result['SECTION_PAGE_URL'],
            );
        }
    }

    /**
     * @param $arOrder
     * @param $arFilter
     * @param bool $arGroupBy
     * @param bool $arNavStartParams
     * @param $arSelect
     * @param bool $getProp
     * @param bool $navString
     * @return array
     * @throws Exception
     */
    protected function getArElements($arOrder, $arFilter, $arGroupBy=false, $arNavStartParams = false, $arSelect, $getProp=false, &$navString = false ){
        $arRes = array();
        $rsElement = CIBlockElement::GetList($arOrder, $arFilter, $arGroupBy, $arNavStartParams, $arSelect);
        //Проведем проверку на запрос коректной страницы
        if ($arNavStartParams && isset($arNavStartParams['nPageSize']) && isset($arNavStartParams['iNumPage']) ){//Задана постраничка
            if ( ($arNavStartParams['iNumPage']-1) * $arNavStartParams['nPageSize'] >= $rsElement->SelectedRowsCount()){
                throw new Exception('Запрашиваемая страница не существует');
            }
        }
        while($obElement = $rsElement->GetNextElement()){
            $arItem = $obElement->GetFields();
            if ($getProp){
                $arItem['PROP'] = $obElement->GetProperties();
            }

            if(isset($arItem["PREVIEW_PICTURE"]))
            {
                $arItem["PREVIEW_PICTURE"] = (0 < $arItem["PREVIEW_PICTURE"] ? CFile::GetFileArray($arItem["PREVIEW_PICTURE"]) : false);
                if ($arItem["PREVIEW_PICTURE"])
                {
                    $arItem["PREVIEW_PICTURE"]["ALT"] = $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_ALT"];
                    if ($arItem["PREVIEW_PICTURE"]["ALT"] == "")
                        $arItem["PREVIEW_PICTURE"]["ALT"] = $arItem["NAME"];
                    $arItem["PREVIEW_PICTURE"]["TITLE"] = $arItem["IPROPERTY_VALUES"]["ELEMENT_PREVIEW_PICTURE_FILE_TITLE"];
                    if ($arItem["PREVIEW_PICTURE"]["TITLE"] == "")
                        $arItem["PREVIEW_PICTURE"]["TITLE"] = $arItem["NAME"];
                }
            }
            if(isset($arItem["DETAIL_PICTURE"]))
            {
                $arItem["DETAIL_PICTURE"] = (0 < $arItem["DETAIL_PICTURE"] ? CFile::GetFileArray($arItem["DETAIL_PICTURE"]) : false);
                if ($arItem["DETAIL_PICTURE"])
                {
                    $arItem["DETAIL_PICTURE"]["ALT"] = $arItem["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_ALT"];
                    if ($arItem["DETAIL_PICTURE"]["ALT"] == "")
                        $arItem["DETAIL_PICTURE"]["ALT"] = $arItem["NAME"];
                    $arItem["DETAIL_PICTURE"]["TITLE"] = $arItem["IPROPERTY_VALUES"]["ELEMENT_DETAIL_PICTURE_FILE_TITLE"];
                    if ($arItem["DETAIL_PICTURE"]["TITLE"] == "")
                        $arItem["DETAIL_PICTURE"]["TITLE"] = $arItem["NAME"];
                }
            }

            $arRes[$arItem['ID']] = $arItem;
        }
        if ($navString!== false){
            $navString = $rsElement->GetPageNavString('', $navString);
        }
        return $arRes;
    }

    protected function getArSections($arOrder, $arFilter, $arSelect, $arNavStartParams = false,$bIncCnt = false){
        $arRes = array();
        $rsSections = CIBlockSection::GetList($arOrder, $arFilter, $bIncCnt, $arSelect, $arNavStartParams);
        while($arSection = $rsSections->GetNext()){
            $arRes[$arSection['ID']] = $arSection;
        }
        return $arRes;
    }
}
