<?php
/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Entity\Misc;

use Doctrine\ORM\Mapping as ORM;

interface AdditiveInterface
{
    const TARGET_FIXED = 1;
    const TARGET_PERCENT = 2;

    const TYPE_INSURANCE = 1;

    /**
     * @return int
     */
    public function getId();

    /**
     * @param $type
     * @return AdditiveInterface
     */
    public function setType($type);

    /**
     * @return int
     */
    public function getType();

    /**
     * @param $name
     * @return AdditiveInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param $description
     * @return AdditiveInterface
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param $target
     * @return AdditiveInterface
     */
    public function setTarget($target);

    /**
     * @return int
     */
    public function getTarget();

    /**
     * @param $value
     * @return AdditiveInterface
     */
    public function setValue($value);

    /**
     * @return float
     */
    public function getValue();

    /**
     * @param $requiredLevels
     * @return AdditiveInterface
     */
    public function setRequiredLevels($requiredLevels);

    /**
     * @return array
     */
    public function getRequiredLevels();

    /**
     * @param $availableLevels
     * @return AdditiveInterface
     */
    public function setAvailableLevels($availableLevels);

    /**
     * @return array
     */
    public function getAvailableLevels();

    /**
     * @param $enabled
     * @return AdditiveInterface
     */
    public function setEnabled($enabled);

    /**
     * @return bool
     */
    public function isEnabled();

    /**
     * @param $level
     * @return bool
     */
    public function isRequiredByLevel($level);

    /**
     * @param $level
     * @return bool
     */
    public function isAvailableByLevel($level);

    /**
     * @return array
     */
    public static function getTypes();

    /**
     * @return array
     */
    public static function getTargets();

    /**
     * @param $power
     * @return AdditiveInterface
     */
    public function setMinPower($power);

    /**
     * @return float
     */
    public function getMinPower();

    /**
     * @param $power
     * @return AdditiveInterface
     */
    public function setMaxPower($power);

    /**
     * @return float
     */
    public function getMaxPower();

    /**
     * @param $price
     * @return AdditiveInterface
     */
    public function setMinPrice($price);

    /**
     * @return float
     */
    public function getMinPrice();

    /**
     * @param $price
     * @return AdditiveInterface
     */
    public function setMaxPrice($price);

    /**
     * @return float
     */
    public function getMaxPrice();


}
