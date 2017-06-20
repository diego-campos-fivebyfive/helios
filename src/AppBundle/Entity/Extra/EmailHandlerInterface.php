<?php

namespace AppBundle\Entity\Extra;

/**
 * Interface EmailHandlerInterface
 * @package AppBundle\Entity\Extra
 */
interface EmailHandlerInterface
{
    const REDIRECT = 'redirect';
    const DOWNLOAD = 'download';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param $behavior
     * @return EmailHandlerInterface
     */
    public function setBehavior($behavior);

    /**
     * @return string
     */
    public function getBehavior();

    /**
     * @param $url
     * @return EmailHandlerInterface
     */
    public function setUrl($url);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @return bool
     */
    public function isDownload();

    /**
     * @return bool
     */
    public function isRedirect();

    /**
     * @param $behavior
     * @return bool
     */
    public function isBehavior($behavior);

    /**
     * @return EmailHandlerInterface
     */
    public function nextRequest();

    /**
     * @return int
     */
    public function getRequests();

    /**
     * @return array
     */
    public static function getBehaviors();
}