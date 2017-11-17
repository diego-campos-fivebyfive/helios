<?php
/**
 * Created by PhpStorm.
 * User: kolinalabs
 * Date: 17/11/17
 * Time: 09:05
 */

namespace AppBundle\Entity;

interface TimelineInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param $target
     * @return TimelineInterface
     */
    public function setTarget($target);

    /**
     * @return string
     */
    public function getTarget();

    /**
     * @param $message
     * @return TimelineInterface
     */
    public function setMessage($message);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param $key
     * @param $value
     * @return TimelineInterface
     */
    public function addAttribute($key, $value);

    /**
     * @param $key
     * @return TimelineInterface
     */
    public function removeAttribute($key);

    /**
     * @param $key
     * @return bool
     */
    public function hasAttribute($key);

    /**
     * @param $key
     * @param null $default
     * @return TimelineInterface
     */
    public function getAttribute($key, $default = null);

    /**
     * @param array $attributes
     * @return TimelineInterface
     */
    public function setAttributes(array $attributes = []);

    /**
     * @return array
     */
    public function getAttributes();

    /**
     * @param $createdAt
     * @return TimelineInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();
}
