<?php

namespace Fostenslave\NalogkaFilesSDK\Serialization;

use Fostenslave\NalogkaFilesSDK\Errors\AccessDeniedError;
use Fostenslave\NalogkaFilesSDK\Errors\UnauthorizedError;
use Fostenslave\NalogkaFilesSDK\Errors\ItemValidationError;
use Fostenslave\NalogkaFilesSDK\Errors\NotFoundError;
use Fostenslave\NalogkaFilesSDK\Errors\ValidationError;
use Fostenslave\NalogkaFilesSDK\Errors\ServerError;
use Fostenslave\NalogkaFilesSDK\Exception\NalogkaSdkException;
use Fostenslave\NalogkaFilesSDK\Model\Metadata;
use Fostenslave\NalogkaFilesSDK\Model\TemporaryAccessToken;


class SerializationComponent extends AbstractSerializationComponent
{
    private $dataMapping = [
        'Metadata' => Metadata::class,
        'TemporaryAccessToken' => TemporaryAccessToken::class,

        'UnauthorizedError' => UnauthorizedError::class,
        'AccessDeniedError' => AccessDeniedError::class,
        'ItemValidationError' => ItemValidationError::class,
        'NotFoundError' => NotFoundError::class,
        'ValidationError' => ValidationError::class,
        'ServerError' => ServerError::class,
    ];

    /**
     * @param $data
     * @return object|array
     * @throws NalogkaSdkException
     */
    public function deserialize($data)
    {
        if (empty($data)) {
            return $data;
        }

        if (!isset($data['~type'])) {
            try {
                return $this->deserializeCollection($data);
            } catch (\Exception $exception) {
                return (object)$data;
            }
        }

        $type = $data['~type'];

        if ($type === 'Collection') {
            return $this->deserializeCollection($data);
        }

        return $this->deserializeObject($data);
    }

    /**
     * @param $collectionData
     * @return array
     * @throws NalogkaSdkException
     */
    public function deserializeCollection($collectionData)
    {
        if (!isset($collectionData['collection'])) {
            if (isset($collectionData[0])) {
                $collectionElements = $collectionData;
            } else {
                throw new NalogkaSdkException("Неправильная структура collection");
            }
        } else {
            $collectionElements = $collectionData['collection'];
        }

        $collectionOfObjects = [];

        foreach ($collectionElements as $collectionElement) {
            $collectionOfObjects[] = $this->deserialize($collectionElement);
        }

        return $collectionOfObjects;
    }

    /**
     * @param $data
     * @return object
     * @throws NalogkaSdkException
     */
    public function deserializeObject($data)
    {
        if (!isset($data['~type'])) {
            throw new NalogkaSdkException("Не найдено поле type в структуре");
        }

        $type = $data['~type'];
        unset($data['~type']);

        if (!isset($this->dataMapping[$type])) {
            throw new NalogkaSdkException("Не найден тип {$type}");
        }

        if (isset($data['~id'])) {
            $data['id'] = $data['~id'];
            unset($data['~id']);
        }

        $object = new $this->dataMapping[$type];

        foreach ($data as $key => $value) {
            $result = $value;
            if (is_array($value)) {
                $result = $this->deserialize($value);
            }

            $propertyName = $this->underScoreToCamelCase($key);

            $object->$propertyName = $result;
        }

        return $object;
    }

    public function underScoreToCamelCase($string)
    {
        $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));

        $str[0] = strtolower($str[0]);

        return $str;
    }

    function camelCaseToUnderScore($input) {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

}