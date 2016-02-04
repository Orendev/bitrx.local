<?
/**
 * Created by PhpStorm.
 * User: Abai Adelshin
 * www.orendev.ru
 */
include_once(dirname(__DIR__) . '/lib/main.php');

use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\EventManager;
use \Bitrix\Main\ModuleManager;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Loader;
use \Bitrix\Main\IO\Directory;
use \Bitrix\Main\Entity\Base;
use \Adelshin\Person\Main;
use \Bitrix\Main\Application;
use \Bitrix\Main\Config as Conf;

Loc::loadMessages(__FILE__);
Class adelshin_person extends CModule
{
	var $exclusionAdminFiles;
	var $arDefaultGroups = ['Группа 1','Группа 2','Группа 3'];

	function __construct()
	{
		$arModuleVersion = array();
		include(__DIR__ . "/version.php");
		$this -> exclusionAdminFiles = array(//формируем список файлов которые исключаем
			'..',
			'.',
			'menu.php',
			'operation_description.php',
			'task_description.php'
		);

		$this -> MODULE_ID = 'adelshin.person';
		$this->MODULE_VERSION = $arModuleVersion["VERSION"];
		$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		$this->MODULE_NAME = Loc::getMessage("ADELSHIN_PERSON_MODULE_NAME");
		$this->MODULE_DESCRIPTION = Loc::getMessage("ADELSHIN_PERSON_MODULE_DESC");

		$this->PARTNER_NAME = Loc::getMessage("ADELSHIN_PERSON_PARTNER_NAME");
		$this->PARTNER_URI = Loc::getMessage("ADELSHIN_PERSON_PARTNER_URI");

		$this -> MODULE_SORT = 1;
		/*$this -> SHOW_SUPER_ADMIN_GROUP_RIGHTS = "Y";
		$this -> MODULE_GROUP_RIGHTS = "Y";*/
	}

	/**
	 * @param bool $notDocumentRoot
	 * @return mixed|string
	 */
	public function GetPath($notDocumentRoot = false) // метод возвращает путь до корня модуля
	{
		if($notDocumentRoot)
			return str_ireplace(Application::getDocumentRoot(),'',dirname(__DIR__)); // исключаем документ роот
		else
			return dirname(__DIR__); // текущая папка с полным путем можем узнать с помощью константы __DIR__ а путь то родительского каталога узнаем ф-ей dirname()
	}
	/**
	 * @return bool
	 * Проверяем что система поддерживает D7
	 */
	public function isVersionD7()
	{
		return CheckVersion(ModuleManager::getVersion('main'), '14.00.00');
	}

	function InstallDB()
	{
		Loader::includeModule($this->MODULE_ID);

		// Create PersonTable
		if(!Application::getConnection(\Adelshin\Person\PersonTable::getConnectionName()) -> isTableExists(
			Base::getInstance('\Adelshin\Person\PersonTable') -> getDBTableName()) )
		{
			Base::getInstance('\Adelshin\Person\PersonTable') -> createDbTable();
		}

		// Create GroupTable
		if(!Application::getConnection(\Adelshin\Person\GroupsTable::getConnectionName()) -> isTableExists(
			Base::getInstance('\Adelshin\Person\GroupsTable') -> getDBTableName()) )
		{
			Base::getInstance('\Adelshin\Person\GroupsTable') -> createDbTable();

			foreach ($this->arDefaultGroups as $value) { //заплняем предустановленными значениями
				\Adelshin\Person\GroupsTable::add(array(
					'TITLE' => $value
				));
			}


		}
	}

	function UnInstallDB()
	{
		Loader::includeModule($this->MODULE_ID);

		// Drop PersonTable
		Application::getConnection(\Adelshin\Person\PersonTable::getConnectionName())->
			queryExecute('drop table if exists '.Base::getInstance('\Adelshin\Person\PersonTable')->getDBTableName());

		// Drop GroupTable
		Application::getConnection(\Adelshin\Person\GroupsTable::getConnectionName())->
			queryExecute('drop table if exists '.Base::getInstance('\Adelshin\Person\GroupsTable')->getDBTableName());

		Option::delete($this->MODULE_ID);
	}

	/**
	 *
	 */
	function InstallEvents()
	{
        EventManager::getInstance()->registerEventHandler($this->MODULE_ID, 'TestEventPerson', $this->MODULE_ID, '\Adelshin\Person\Event', 'eventHandler');
        EventManager::getInstance()->registerEventHandler('main', 'OnBuildGlobalMenu', $this->MODULE_ID, '\Adelshin\Person\GlobalMenu', 'AddGlobalMenuItem');
	}

	/**
	 *
	 */
	function UnInstallEvents()
	{
		EventManager::getInstance()->unRegisterEventHandler($this->MODULE_ID, 'TestEventPerson', $this->MODULE_ID, '\Adelshin\Person\Event', 'eventHandler');
        EventManager::getInstance()->unRegisterEventHandler('main', 'OnBuildGlobalMenu', $this->MODULE_ID, '\Adelshin\Person\GlobalMenu', 'AddGlobalMenuItem');
	}

	/**
	 * @param array $arParams
	 * @return bool
	 * @throws \Bitrix\Main\IO\InvalidPathException
	 */
	function InstallFiles($arParams = array())
	{
		$path = $this->GetPath()."/install/components";

		if (Directory::isDirectoryExists($path))
		{
			CopyDirFiles($path, $_SERVER["DOCUMENT_ROOT"]."/local/components", true, true);
		}
		else
		{
			throw new \Bitrix\Main\IO\InvalidPathException($path);
		}

		if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath().'/admin'))
		{
			CopyDirFiles($this->GetPath()."/install/admin/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/admin"); //если есть файлы для копирования
			if ($dir = opendir($path))
			{
				while (false !== $item = readdir($dir)) // получаем список файлов в диретктории
				{
					if (in_array($item, $this->exclusionAdminFiles)) // если файл есть в списке исключение, то сним ни чего не делаем
						continue;
					file_put_contents($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'.$this->MODULE_ID.'_'.$item, // создаем файл в данной директории к имени добавим префикс MODULE_ID_
						'<'.'? require($_SERVER["DOCUMENT_ROOT"]."'.$this->GetPath(true).'/admin/'.$item.'");?'.'>'); //в файл записываем подключение файла со скриптами админки нашего модуля
				}
				closedir($dir);
			}
		}
        return true;
	}

	/**
	 * @return bool
	 */
	function UnInstallFiles()
	{
		\Bitrix\Main\IO\Directory::deleteDirectory($_SERVER["DOCUMENT_ROOT"] . '/bitrix/components/adelshin/');

		if (\Bitrix\Main\IO\Directory::isDirectoryExists($path = $this->GetPath().'/admin')) { // удаляем административные файлы
			DeleteDirFiles($_SERVER["DOCUMENT_ROOT"].$this->GetPath().'/install/admin/', $_SERVER["DOCUMENT_ROOT"].'/bitrix/admin');
			if ($dir = opendir($path)) {
				while (false !== $item = readdir($dir)) {
					if (in_array($item, $this->exclusionAdminFiles))
						continue;
					\Bitrix\Main\IO\File::deleteFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/admin/'. $this->MODULE_ID.'_'.$item);
				}
				closedir($dir);
			}
		}
		return true;
	}





	/**
	 *
	 */
	function DoInstall()
	{
		global $APPLICATION;
        if($this->isVersionD7())
        {
			ModuleManager::registerModule($this -> MODULE_ID);

			$this->InstallDB();
			$this->InstallEvents();
            $this->InstallFiles();

			#работа с .settings.php
			$configuration = Conf\Configuration::getInstance();
			$adelshin_module_person =$configuration->get('adelshin_module_person');
			$adelshin_module_person['install'] = $adelshin_module_person['install']+1;
			$configuration->add('adelshin_module_person', $adelshin_module_person);
			$configuration->saveConfiguration(); //Необходим для сохранения наших конфигурации
			#работа с .settings.php

        }
        else
        {
            $APPLICATION->ThrowException(Loc::getMessage("ADELSHIN_PERSON_INSTALL_ERROR_VERSION"));
        }

        $APPLICATION->IncludeAdminFile(Loc::getMessage("ADELSHIN_PERSON_INSTALL_TITLE"), $this->GetPath()."/install/step.php");
	}

	/**
	 *
	 */
	function DoUninstall()
	{
		global $APPLICATION;

		$context = Application::getInstance() -> getContext();
		$request = $context -> getRequest();

		if ($request["step"] < 2)
		{
			$APPLICATION->IncludeAdminFile(Loc::getMessage("ADELSHIN_PERSONE_UNINSTALL_TITLE"), $this->GetPath()."/install/unstep1.php");
		}
		elseif($request["step"] == 2 )
		{
			$this->UnInstallFiles();
			$this->UnInstallEvents();

			if($request['savedata'] != 'Y')
				$this->UnInstallDB();

			ModuleManager::unRegisterModule($this -> MODULE_ID);

			#работа с .settings.php
			$configuration = Conf\Configuration::getInstance();
			$adelshin_module_person=$configuration->get('adelshin_module_person');
			$adelshin_module_person['uninstall']=$adelshin_module_person['uninstall']+1;
			$configuration->add('adelshin_module_person', $adelshin_module_person);
			$configuration->saveConfiguration();
			#работа с .settings.php

			$APPLICATION->IncludeAdminFile(Loc::getMessage("ADELSHIN_PERSONE_UNINSTALL_TITLE"), $this->GetPath()."/install/unstep2.php");
		}

	}
	/*function GetModuleRightList()
	{
		return array(
			"reference_id" => array("D","K","S","W"),
			"reference" => array(
				"[D] ".Loc::getMessage("ADELSHIN_PERSONE_DENIED"),
				"[K] ".Loc::getMessage("ADELSHIN_PERSONE_READ_COMPONENT"),
				"[S] ".Loc::getMessage("ADELSHIN_PERSONE_WRITE_SETTINGS"),
				"[W] ".Loc::getMessage("ADELSHIN_PERSONE_FULL"))
		);
	}*/
}