<?php

namespace Fostenslave\NalogkaFilesSDK\Request;

use Fostenslave\NalogkaFilesSDK\Model\Metadata;

class FileUploadRequest extends AbstractRequest
{

    private $fileName;
    private $description;

    public function fileName($fileName = '')
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function description($description = '')
    {
        $this->description = $description;

        return $this;
    }

    public function file($file)
    {
        $this->requestData = $file;

        return $this;
    }

    protected function getHttpMethod()
    {
        return self::METHOD_POST;
    }

    protected function getHttpPath()
    {
        return "/upload?filename={$this->fileName}&description={$this->description}";
    }

    /**
     * @return array|Metadata
     * @throws \Fostenslave\NalogkaFilesSDK\Exception\ApiErrorException
     * @throws \Fostenslave\NalogkaFilesSDK\Exception\NalogkaSdkException
     * @throws \Fostenslave\NalogkaFilesSDK\Exception\ServerErrorException
     */
    public function request()
    {
        return parent::request();
    }
}