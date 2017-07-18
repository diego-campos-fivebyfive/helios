<?php
/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Entity\Component;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectModule
 *
 * @author João Zaqueu <joaozaqueu@kolinalabs.com>
 *
 * @ORM\Table(name="app_project_string_box")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class ProjectStringBox implements ProjectStringBoxInterface
{
    use ProjectElementTrait;

    /**
     * @var ProjectInterface
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="projectStringBoxes")
     */
    private $project;

    /**
     * @var StringBoxInterface
     *
     * @ORM\ManyToOne(targetEntity="StringBox")
     */
    private $stringBox;

    /**
     * ProjectStringBox constructor.
     */
    public function __construct()
    {
        $this->quantity      = 1;
        $this->unitCostPrice = 0;
        $this->unitSalePrice = 0;
    }

    /**
     * @inheritDoc
     */
    public function setProject(ProjectInterface $project)
    {
        $this->project = $project;

        $project->addProjectStringBox($this);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @return StringBoxInterface
     */
    public function getStringBox()
    {
        return $this->stringBox;
    }

    /**
     * @inheritDoc
     */
    public function setStringBox(StringBoxInterface $stringBox)
    {
        return $this->stringBox;
    }
}