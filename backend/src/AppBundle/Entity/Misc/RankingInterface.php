<?php

namespace AppBundle\Entity\Misc;


interface RankingInterface
{

    /**
     * @return int
     */
    public function getId();

    /**
     * @param $target
     * @return RankingInterface
     */
    public function setTarget($target);

    /**
     * @return string
     */
    public function getTarget();

    /**
     * @param $description
     * @return RankingInterface
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param $amount
     * @return RankingInterface
     */
    public function setAmount($amount);

    /**
     * @return int
     */
    public function getAmount();
}
