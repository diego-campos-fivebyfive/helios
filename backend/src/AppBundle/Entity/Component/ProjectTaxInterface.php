<?php

namespace AppBundle\Entity\Component;

interface ProjectTaxInterface
{
    /**
     * Tax Operations
     */
    const OPERATION_DISCOUNT = 'discount';
    const OPERATION_ADDITION = 'addition';

    /**
     * Tax Targets
     */
    const TARGET_GENERAL = 'general';
    const TARGET_EQUIPMENTS = 'equipments';
    const TARGET_SERVICES = 'services';

    /**
     * Tax Types
     */
    const TYPE_ABSOLUTE = 'absolute';
    const TYPE_PERCENT = 'percent';
    
    public function setProject(ProjectInterface $project);
    
    /**
     * @return ProjectInterface
     */
    public function getProject();

    /**
     * @param $name
     * @return ProjectTaxInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param $operation
     * @return ProjectTaxInterface
     */
    public function setOperation($operation);

    /**
     * @return string
     */
    public function getOperation();

    /**
     * @param $target
     * @return ProjectTaxInterface
     */
    public function setTarget($target);

    /**
     * @return string
     */
    public function getTarget();

    /**
     * @param $type
     * @return ProjectTaxInterface
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param $value
     * @return ProjectTaxInterface
     */
    public function setValue($value);

    /**
     * @return float
     */
    public function getValue();

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @param string $symbol
     * @return string
     */
    public function getAmountFormatted($symbol = 'R$');

    /**
     * @param bool $decimal
     * @return float
     */
    public function getPercent($decimal = true);

    /**
     * @return bool
     */
    public function isAbsolute();

    /**
     * @return bool
     */
    public function isDiscount();

    /**
     * @return bool
     */
    public function isTargetServices();

    /**
     * @return bool
     */
    public function isTargetEquipments();

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return string json
     */
    public function toJson();

    /**
     * @param $value
     * @return string
     */
    public static function currency($value);

    /**
     * @return array
     */
    public static function getTaxOperations();

    /**
     * @return array
     */
    public static function getTaxTargets();

    /**
     * @return array
     */
    public static function getTaxTypes();
}