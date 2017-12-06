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

use AppBundle\Entity\Misc\AdditiveRelationTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * ProjectAdditive
 *
 * @author João Zaqueu Chereta <joaozaqueuchereta@gmail.com>
 *
 * @ORM\Table(name="app_project_additive")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class ProjectAdditive implements ProjectAdditiveInterface
{
    use AdditiveRelationTrait;

    /**
     * @var ProjectInterface
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="projectAdditives")
     */
    private $project;

    /**
     * @inheritdoc
     */
    public function setProject(ProjectInterface $project)
    {
        $this->project = $project;

        $project->addProjectAdditive($this);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProject()
    {
        return $this->project;
    }
}
