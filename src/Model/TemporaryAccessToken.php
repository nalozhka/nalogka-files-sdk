<?php


namespace Fostenslave\NalogkaFilesSDK\Model;


class TemporaryAccessToken
{
    /**
     * @var string Идентификатор
     */
    public $id;

    /**
     * @var string Значение токена
     */
    public $token;

    /**
     * @var string Метод, к которому предоставляется доступ (<HTTP-метод> " " <URL>)
     */
    public $endpoint;

    /**
     * @var string Дата и время истечения срока действия токена
     */
    public $expiredAt;
    
}