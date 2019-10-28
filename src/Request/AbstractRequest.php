<?php

namespace Fostenslave\NalogkaFilesSDK\Request;

use Fostenslave\NalogkaFilesSDK\ApiClient;

abstract class AbstractRequest
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PATCH = 'PATCH';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    public $requestData = [];

    public $requestHeaders = [];

    public $dataToLogging = ['method','data','rawResponse','responseInfo'];

    /**
     * @var ApiClient
     */
    private $apiClient;

    function __construct($apiClient)
    {
        $this->apiClient = $apiClient;
    }

    abstract protected function getHttpMethod();

    abstract protected function getHttpPath();

    /**
     * @return array|object|null
     * @throws \Fostenslave\NalogkaFilesSDK\Exception\ApiErrorException
     * @throws \Fostenslave\NalogkaFilesSDK\Exception\NalogkaSdkException
     * @throws \Fostenslave\NalogkaFilesSDK\Exception\ServerErrorException
     */
    public function request()
    {
        return $this->apiClient->request($this->getHttpMethod(), $this->getHttpPath(), $this->requestData, $this->requestHeaders, $this->dataToLogging);
    }
}