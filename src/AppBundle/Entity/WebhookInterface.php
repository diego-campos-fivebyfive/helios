<?php

namespace AppBundle\Entity;

interface WebhookInterface
{
    /**
     * @param $type
     * @return WebhookInterface
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param $id
     * @return WebhookInterface
     */
    public function setSubscriptionId($id);

    /**
     * @return int
     */
    public function getSubscriptionId();

    /**
     * @param $content
     * @return WebhookInterface
     */
    public function setContent($content);

    /**
     * @return string[json]
     */
    public function getContent();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();
}