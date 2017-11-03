<?php

namespace AppBundle\Entity\Component;

use AppBundle\Entity\BusinessInterface;
use Doctrine\Common\Collections\ArrayCollection;

interface MakerInterface
{
    const CONTEXT_INVERTER = 'inverter';
    const CONTEXT_MODULE = 'module';
    const CONTEXT_STRUCTURE = 'structure';
    const CONTEXT_STRING_BOX = 'string_box';
    const CONTEXT_VARIETY = 'variety';

    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    const ERROR_UNSUPPORTED_CONTEXT = 'The context is not supported';

    const REMOVE_ZERO_CHILD = true;

    /**
     * @return int
     */
    public function getId();

    /**
     * @param $context
     * @return MakerInterface
     */
    public function setContext($context);

    /**
     * @return string
     */
    public function getContext();

    /**
     * @param $name
     * @return MakerInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param $enabled
     * @return MakerInterface
     */
    public function setEnabled($enabled);

    /**
     * @return bool
     */
    public function isEnabled();

    /**
     * @return bool
     */
    public function isMakerInverter();

    /**
     * @return bool
     */
    public function isMakerModule();

    /**
     * @return bool
     */
    public function isMakerStructure();

    /**
     * @return bool
     */
    public function isMakerAll();

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt();

    /**
     * @return \DateTimeInterface
     */
    public function getUpdatedAt();

    /**
     * @return array
     */
    public static function getContextList();

    /**
     * @throws \InvalidArgumentException
     */
    public static function unsupportedMakerContextException();
}
