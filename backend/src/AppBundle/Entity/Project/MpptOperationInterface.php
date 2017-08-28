<?php

namespace AppBundle\Entity\Project;

interface MpptOperationInterface
{
    const ID_ENTROPY = 255;
    const ID_SPACER  = '.';
    const LABEL_SPACER = '+';
    const NAME_SPACER = ', ';

    const ERROR_UNSUPPORTED_OPERATION = 'Unsupported operation value';

    /**
     * @return string
     */
    public function getId();

    /**
     * @return string
     */
    public function getName($index = null);

    /**
     * @return int
     */
    public function getMppt();

    /**
     * @param array $operation
     * @return MpptOperationInterface
     */
    public function setOperation(array $operation);

    /**
     * @return array
     */
    public function getOperation();

    /**
     * @return int
     */
    public function count();

    /**
     * @return array
     */
    public function toArray();
}