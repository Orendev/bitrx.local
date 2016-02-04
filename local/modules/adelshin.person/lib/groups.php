<?php
/**
 * Created by PhpStorm.
 * User: Adelshin Abai
 * Site: www.orendev.ru
 * Date: 04.02.16
 * Time: 10:02
 */
namespace Adelshin\Person;

use \Bitrix\Main\Entity;
use \Bitrix\Main\Type;

class GroupsTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'groups';
    }

    public static function getUfId()
    {
        return 'GROUPS';
    }

    public static function getMap()
    {
        return array(
            //ID
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true
            )),

            new Entity\StringField('TITLE'),

        );
    }
}