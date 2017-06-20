<?php

namespace AppBundle\Entity\Financial;

interface TaxInterface
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

    function __construct(ProjectFinancialInterface $financial);

    /**
     * @return ProjectFinancialInterface
     */
    public function getFinancial();

    /**
     * @param $name
     * @return TaxInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param $operation
     * @return TaxInterface
     */
    public function setOperation($operation);

    /**
     * @return string
     */
    public function getOperation();

    /**
     * @param $target
     * @return TaxInterface
     */
    public function setTarget($target);

    /**
     * @return string
     */
    public function getTarget();

    /**
     * @param $type
     * @return TaxInterface
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param $value
     * @return TaxInterface
     */
    public function setValue($value);

    /**
     * @return float
     */
    public function getValue();

    /**
     * @return string
     */
    public function getAmount();

    /**
     * @param string $symbol
     * @return string
     */
    public function getAmountFormatted($symbol = 'R$');

    /**
     * @return string
     */
    public function getPercent($decimal = true);

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
    public function isTargetService();

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
}