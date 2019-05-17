<?php

namespace Fostenslave\NalogkaFilesSDK\Request;

use Fostenslave\NalogkaFilesSDK\Model\MetaData;

class TemporaryAccessTokenRequest extends AbstractRequest
{

    public function endpoint($endpoint)
    {
        $this->requestData['endpoint'] = $endpoint;

        return $this;
    }

    protected function getHttpMethod()
    {
        return self::METHOD_POST;
    }

    protected function getHttpPath()
    {
        return "/temporary-access-token";
    }

    /**
     * @return array|TemporaryAccessToken
     * @throws \Fostenslave\NalogkaFilesSDK\Exception\ApiErrorException
     * @throws \Fostenslave\NalogkaFilesSDK\Exception\NalogkaSdkException
     * @throws \Fostenslave\NalogkaFilesSDK\Exception\ServerErrorException
     */
    public function request()
    {
        return parent::request();
    }
}