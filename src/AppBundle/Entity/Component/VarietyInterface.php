<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Entity\Component;

/**
 * Interface StringBoxInterface
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
interface VarietyInterface
{
    const TYPE_CABLE = 'cable';
    const TYPE_CONNECTOR = 'connector';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param $type
     * @return VarietyInterface
     */
    public function setType($type);

    /**
     *  @return string
     */
    public function getType();

    /**
     *  @param $subtype
     *  @return VarietyInterface
     */
    public function setSubType($subtype);

    /**
     * @return string
     */
    public function getSubType();

    /**
     * @param $code
     * @return VarietyInterface
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param $description
     * @return VarietyInterface
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param MakerInterface $maker
     * @return VarietyInterface
     */
    public function setMaker(MakerInterface $maker);

    /**
     * @return MakerInterface
     */
    public function getMaker();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();
}