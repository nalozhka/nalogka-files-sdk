<?php


namespace Fostenslave\NalogkaFilesSDK\Model;


class Metadata
{
    /**
     * @var string Идентификатор файла
     */
    public $id;

    /**
     * @var string Краткое описание файла
     */
    public $description;

    /**
     * @var string Полный url по которому к файлу можно получить публичный доступ
     */
    public $publicUrl;

    /**
     * @var string MIME-тип файла
     */
    public $mimeType;

    /**
     * @var integer Размер файла в байтах
     */
    public $size;

    /**
     * @var string Момент первой загрузки файла
     */
    public $createdAt;

    /**
     * @var string Момент обновления файла
     */
    public $updatedAt;

    /**
     * @var string Момент последнего чтения файла
     */
    public $readAt;

}