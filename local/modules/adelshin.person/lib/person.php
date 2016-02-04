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

class PersonTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'person';
    }

    public static function getUfId()
    {
        return 'PERSON';
    }

    public static function getMap()
    {
        return array(
            //ID
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true
            )),
            new Entity\IntegerField('GROUP_ID'),
            new Entity\ReferenceField(
                'GROUP',
                '\Adelshin\Person\GroupsTable',
                array('=this.GROUPS_ID' => 'ref.ID')
            ),

            new Entity\StringField('FIRST_NAME', array(
                'required' => true,
            )),
            new Entity\StringField('LAST_NAME'),
            new Entity\EnumField('GENDER', array(
                'values' => array('f', 'm')
            )),
        );
    }
}