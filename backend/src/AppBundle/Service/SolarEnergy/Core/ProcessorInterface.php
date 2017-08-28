<?php

namespace AppBundle\Service\SolarEnergy\Core;

interface ProcessorInterface
{
    /**
     * Fixed year because the base days february is 28
     */
    const YEAR = 2015;

    /**
     * ProcessorInterface constructor.
     * @param ProjectInterface $module
     */
    function __construct(ProjectInterface $project);

    /**
     * @return mixed
     */
    public function compute();

    /**
     * @return bool
     */
    public function isComputable();
}