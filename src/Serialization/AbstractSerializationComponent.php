<?php


namespace Fostenslave\NalogkaFilesSDK\Serialization;

use Fostenslave\NalogkaFilesSDK\Exception\NalogkaSdkException;

abstract class AbstractSerializationComponent
{
    /**
     * @param $data
     * @return object|array
     * @throws NalogkaSdkException
     */
    abstract public function deserialize($data);
}