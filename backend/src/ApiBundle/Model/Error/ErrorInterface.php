<?php

namespace ApiBundle\Model\Error;

interface ErrorInterface
{
    /**
     * @param $code
     * @return mixed
     */
    public function setCode($code);

    /**
     * @return mixed
     */
    public function getCode();

    /**
     * @param $type
     * @return mixed
     */
    public function setType($type);

    /**
     * @return mixed
     */
    public function getType();

    /**
     * @param $message
     * @return mixed
     */
    public function setMessage($message);

    /**
     * @return mixed
     */
    public function getMessage();

    /**
     * @param $info
     * @return ErrorInterface
     */
    public function setInfo($info);

    /**
     * @return string
     */
    public function getInfo();

    /**
     * @return array
     */
    public function toArray();
}