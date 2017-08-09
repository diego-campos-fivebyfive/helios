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
interface StringBoxInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param $code
     * @return StringBoxInterface
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param $description
     * @return StringBoxInterface
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param $inputs
     * @return StringBoxInterface
     */
    public function setInputs($inputs);

    /**
     * @return int
     */
    public function getInputs();

    /**
     * @param $outputs
     * @return StringBoxInterface
     */
    public function setOutputs($outputs);

    /**
     * @return int
     */
    public function getOutputs();

    /**
     * @param $fuses
     * @return StringBoxInterface
     */
    public function setFuses($fuses);

    /**
     * @return int
     */
    public function getFuses();

    /**
     * @param $status
     * @return StringBoxInterface
     */
    public function setStatus($status);

    /**
     * @return boolean
     */
    public function getStatus();

    /**
     * @param MakerInterface $maker
     * @return StringBoxInterface
     */
    public function setMaker(MakerInterface $maker);

    /**
     * @return MakerInterface
     */
    public function getMaker();

    /**
     * @param $datasheet
     * @return StringBoxInterface
     */
    public function setDatasheet($datasheet);

    /**
     * @return string
     */
    public function getDatasheet();

    /**
     * @param $image
     * @return StringBoxInterface
     */
    public function setImage($image);

    /**
     * @return string
     */
    public function getImage();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();
}