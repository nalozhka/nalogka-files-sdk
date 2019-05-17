<?php

namespace Fostenslave\NalogkaFilesSDK;

use Psr\Log\LoggerInterface;
use Fostenslave\NalogkaFilesSDK\Errors\AbstractError;
use Fostenslave\NalogkaFilesSDK\Exception\ApiErrorException;
use Fostenslave\NalogkaFilesSDK\Exception\NalogkaSdkException;
use Fostenslave\NalogkaFilesSDK\Exception\ServerErrorException;
use Fostenslave\NalogkaFilesSDK\Serialization\AbstractSerializationComponent;

class ApiClient
{
    private $baseUrl;

    private $parameters;

    /**
     * @var AbstractSerializationComponent
     */
    private $serializationComponent;


    private  $logger;


    public function __construct($baseUrl, $parameters = [], $serializationComponent, LoggerInterface $logger = null)
    {
        $this->baseUrl = $baseUrl;

        $this->parameters = $parameters;

        $this->serializationComponent = $serializationComponent;

        $this->logger = $logger;
    }

    /**
     * @param $method
     * @param $path
     * @param array|string $data
     * @return array|null|object
     * @throws NalogkaSdkException
     * @throws ApiErrorException
     * @throws ServerErrorException
     */
    public function request($method, $path, $data = [])
    {
        $method = strtoupper($method);

        $headers = isset($this->parameters['headers']) ? $this->parameters['headers'] : [];

        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($path, '/');

        if ($method === "GET" && $data) {
            $url .= "?" . http_build_query($data);
        }

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($method !== "GET") {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if ($data) {
                if (is_array($data)) {
                    $data_string = json_encode($data);
                    $headers['Content-Type'] = 'application/json';
                } else {
                    $data_string = $data;
                }

                $headers['Content-Length'] = strlen($data_string);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

            }
        }

        $curlReadyHeaders = [];
        foreach ($headers as $headerName => $headerValue) {
            $curlReadyHeaders[] = "{$headerName}: {$headerValue}";
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $curlReadyHeaders);

        $rawResponse = curl_exec($ch);

        $responseInfo = curl_getinfo($ch);

        if ($this->logger instanceof LoggerInterface){
            $this->logger->debug("Метод: {method} \n Данные запроса: {data} \n Ответ сервера: {rawResponse} \n Данные ответа: {responseInfo}", [
                'method' => $method,
                'data' => $data,
                'rawResponse' => $rawResponse,
                'responseInfo' => $responseInfo
            ]);
        }

        if (!$rawResponse && $this->isErrorResponse($responseInfo['http_code'])) {
            throw new ServerErrorException($responseInfo['http_code'], "Не удалось получить ответ от сервера. HTTP код: {$responseInfo['http_code']}");
        }

        $decodedResponse = json_decode($rawResponse, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ServerErrorException($responseInfo['http_code'], $rawResponse);
        }

        $deserializedResponse = $this->serializationComponent->deserialize($decodedResponse);

        if ($deserializedResponse instanceof AbstractError) {
            throw new ApiErrorException($deserializedResponse, $deserializedResponse->message, $responseInfo['http_code']);
        }

        return $deserializedResponse;
    }

    private function isErrorResponse($httpCode)
    {
        if (!in_array($httpCode, [200, 201, 202, 203, 204, 205, 206, 207, 208, 226])) {
            return true;
        }

        return false;
    }
}