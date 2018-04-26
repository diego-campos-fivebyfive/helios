<?php

namespace AppBundle\Entity;

interface TermInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return Term
     */
    public function setId($id);
    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     * @return Term
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $url
     * @return Term
     */
    public function setUrl($url);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param \DateTime $createdAt
     * @return Term
     */
    public function setCreatedAt($createdAt);

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();


    /**
     * @param \DateTime $updatedAt
     * @return Term
     */
    public function setUpdatedAt($updatedAt);
}
