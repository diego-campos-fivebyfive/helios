<?php

namespace ApiBundle\Model\Error;

class ErrorIterator implements \ArrayAccess
{
    /**
     * @var array
     */
    private $errors;

    /**
     * @param array $errors
     */
    function __construct(array $errors = [])
    {
        foreach ($errors as $error){
            if(!$error instanceof ErrorInterface){
                throw new \InvalidArgumentException(sprintf('The errors must be instance of %s', Error::class));
            }
        }

        $this->errors = $errors;
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return isset($this->errors[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        if(!$this->offsetExists($offset)){
            throw new \OutOfBoundsException(sprintf('Undefined offset %s', $offset));
        }

        return $this->errors[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {

    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
    }
}