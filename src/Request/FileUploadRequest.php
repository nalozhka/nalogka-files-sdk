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

    public function file($fileContent, $contentType = null)
    {
        $this->requestData = $fileContent;

        if ($contentType !== null) {
            $this->requestHeaders['Content-Type'] = $contentType;
        }

        return $this;
    }

    protected function getHttpMethod()
    {
        return self::METHOD_POST;
    }

    protected function getHttpPath()
    {
        return "/upload?filename=" . urlencode($this->fileName) . "&description=" . urlencode($this->description);
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