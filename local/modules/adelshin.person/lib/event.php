<?php
/**
 * Created by PhpStorm.
 * User: Adelshin Abai
 * Site: www.orendev.ru
 * Date: 04.02.16
 * Time: 2:59
 */
namespace Adelshin\Person;

class event
{
    public function eventHandler(\Bitrix\Main\Entity\Event $event)
    {
        //die();
        $result = new \Bitrix\Main\Entity\EventResult;

        echo'Тело события<br>';


        $result->modifyFields(array('result' => 'Сообщение вернул обработчик'));

        return $result;
    }
}