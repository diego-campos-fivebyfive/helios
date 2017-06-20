<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface DocumentInterface
 * @package AppBundle\Entity
 */
interface DocumentInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param $title
     * @return DocumentInterface
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param $content
     * @return DocumentInterface
     */
    public function setContent($content);

    /**
     * @return string
     */
    public function getContent();

    /**
     * @param $position
     * @return DocumentInterface
     */
    public function setPosition($position);

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param DocumentInterface $parent
     * @return DocumentInterface
     */
    public function setParent(DocumentInterface $parent);

    /**
     * @return DocumentInterface | null
     */
    public function getParent();

    /**
     * @param $title
     * @param $content
     * @param null $position
     * @return DocumentInterface
     */
    public function createSection($title, $content, $position = null);

    /**
     * @param DocumentInterface $section
     * @return DocumentInterface
     */
    public function addSection(DocumentInterface $section);

    /**
     * @param DocumentInterface $section
     * @return DocumentInterface
     */
    public function removeSection(DocumentInterface $section);

    /**
     * @return ArrayCollection
     */
    public function getSections();

    /**
     * @return bool
     */
    public function hasChildren();

    /**
     * @param array $metadata
     * @return DocumentInterface
     */
    public function setMetadata(array $metadata);

    /**
     * @param $key
     * @param $value
     * @return DocumentInterface
     */
    public function addMetadata($key, $value);

    /**
     * @param $key
     * @return DocumentInterface
     */
    public function removeMetadata($key);

    /**
     * @param null $key
     * @param null $default
     * @return mixed
     */
    public function getMetadata($key = null, $default = null);

    /**
     * @param $key
     * @return bool
     */
    public function hasMetadata($key);
}