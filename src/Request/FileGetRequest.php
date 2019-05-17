<?php

namespace Fostenslave\NalogkaFilesSDK\Request;

use Fostenslave\NalogkaFilesSDK\Model\MetaData;

class FileGetRequest extends AbstractRequest
{
    private $fileName;
    
    public function fileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    protected function getHttpMethod()
    {
        return self::METHOD_GET;
    }

    protected function getHttpPath()
    {
        return "/meta/{$this->fileName}";
    }

    /**
     * @return array|MetaData
     * @throws \Fostenslave\NalogkaFilesSDK\Exception\ApiErrorException
     * @throws \Fostenslave\NalogkaFilesSDK\Exception\NalogkaSdkException
     * @throws \Fostenslave\NalogkaFilesSDK\Exception\ServerErrorException
     */
    public function request()
    {
        return parent::request();
    }
}