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
     * @return array
     */
    public static function getTypes();

    /**
     * @return array
     */
    public static function getTargets();
}
