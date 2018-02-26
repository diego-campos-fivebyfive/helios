<?php

namespace AppBundle\Service\Timeline;

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use AppBundle\Entity\Timeline;
use AppBundle\Manager\TimelineManager;
use Doctrine\Common\Util\ClassUtils;

/**
 * Class Resource
 * @author Fabio Dukievicz <fabiojd47@gmail.com>
 */
class Resource
{
    /** @var TimelineManager */
    private $manager;

    private $flush = true;

    /**
     * Resource constructor.
     * @param TimelineManager $manager
     */
    public function __construct(TimelineManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param $timelineCollection
     * @return array
     */
    public function createByArray($timelineCollection)
    {
        $timelines = [];

        $this->flush = false;

        foreach ($timelineCollection as $timeline) {
            $target = $timeline['target'];
            $message = $timeline['message'];
            $attributes = isset($timeline['createdAt']) ? $timeline['attributes'] : [];
            $createdAt = array_key_exists('createdAt', $timeline) ? $timeline['createdAt'] : null;

            $timelines[] = $this->create($target, $message, $attributes, $createdAt);
        }

        $this->manager->flush();

        $this->flush = true;

        return $timelines;
    }

    /**
     * @param $target
     * @param $message
     * @param array $attributes
     * @param $createdAt
     * @return Timeline|mixed|object
     */
    public function create($target, $message, $attributes = [], $createdAt = null)
    {
        /** @var Timeline $timeline */
        $timeline = $this->manager->create();

        $timeline
            ->setTarget($target)
            ->setMessage($message)
            ->setAttributes($attributes)
            ->setCreatedAt($createdAt instanceof \DateTime ? $createdAt : new \DateTime());

        $this->manager->save($timeline, $this->flush);

        return $timeline;
    }

    /**
     * @param $target
     * @return array
     */
    public function loadByTarget($target)
    {
        return array_reverse($this->manager->findBy([
            'target' => $target
        ]));
    }

    /**
     * @param $object
     * @return array
     */
    public function loadByObject($object)
    {
        return $this->loadByTarget(self::getObjectTarget($object));
    }

    /**
     * @param $object
     * @return string
     */
    public static function getObjectTarget($object)
    {
        return sprintf('%s::%s', ClassUtils::getClass($object), $object->getId());
    }
}
