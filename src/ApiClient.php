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
    const CONTENT_TYPE_JSON = "application/json";

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
     * @param array $headers
     * @return array|null|object
     * @throws ApiErrorException
     * @throws NalogkaSdkException
     * @throws ServerErrorException
     */
    public function request($method, $path, $data = [], $headers = [], $dataToLogging = ['method', 'data', 'rawResponse', 'responseInfo'])
    {
        $method = strtoupper($method);

        if (isset($this->parameters['headers'])) {
            $headers = array_merge($this->parameters['headers'], $headers);
        }

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
                    if (empty($headers['Content-Type'])) {
                        $headers['Content-Type'] = self::CONTENT_TYPE_JSON;
                    }

                    if ($headers['Content-Type'] === self::CONTENT_TYPE_JSON) {
                        $data_string = json_encode($data);
                    } else {
                        throw new NalogkaSdkException("Неизвестный content-type данных в запросе: {$headers['Content-Type']}");
                    }
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
        
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POSTREDIR, CURL_REDIR_POST_ALL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $curlReadyHeaders);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $rawResponse = curl_exec($ch);

        $responseInfo = curl_getinfo($ch);

        $dataToLogging[] = 'curlError';
        if ($this->logger instanceof LoggerInterface) {
            $debugFormatString = '';
            $debugData = [];

            if (in_array('method', $dataToLogging)) {
                $debugFormatString .= "Метод: {method} \n";
            }

            if (in_array('data', $dataToLogging)) {
                $debugFormatString .= "Данные запроса: {data} \n";
            }

            if (in_array('rawResponse', $dataToLogging)) {
                $debugFormatString .= "Ответ сервера: {rawResponse} \n";
            }

            if (in_array('responseInfo', $dataToLogging)) {
                $debugFormatString .= "Данные ответа: {responseInfo} \n";
            }

            if (in_array('curlError', $dataToLogging)) {
                $curlError = curl_error($ch);
                $debugFormatString .= "Ошибка CURL: {curlError} \n";
            }
            
            $debugData = compact($dataToLogging);

            if ($debugFormatString && $debugData) {
                 $this->logger->debug($debugFormatString, $debugData);
            }
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