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
     * @return TermInterface
     */
    public function setId($id);
    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     * @return TermInterface
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $url
     * @return TermInterface
     */
    public function setUrl($url);

    /**
     * @param $publishedAt
     * @return TermInterface
     */
    public function setPublishedAt($publishedAt);

    /**
     * @return \DateTime
     */
    public function getPublishedAt();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();
}
