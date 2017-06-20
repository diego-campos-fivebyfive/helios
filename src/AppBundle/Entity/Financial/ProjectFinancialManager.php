<?php

namespace AppBundle\Entity\Financial;

use AppBundle\Entity\Component\Kit;
use AppBundle\Entity\Component\PricingManager;
use AppBundle\Entity\Project\ProjectInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Sonata\CoreBundle\Model\BaseEntityManager;

class ProjectFinancialManager extends BaseEntityManager
{
    /**
     * @var PricingManager
     */
    private $pricingManager;

    /**
     * @inheritDoc
     */
    public function __construct($class, ManagerRegistry $registry, PricingManager $pricingManager)
    {
        parent::__construct($class, $registry);

        $this->pricingManager = $pricingManager;
    }

    public function fromProject(ProjectInterface $project)
    {
        if(!$project->getAnnualProduction())
            throw new \Exception('Invalid project info: annualProduction');
        
        $financial = $this->findOneBy(['project' => $project]);

        if(!$financial){
            /** @var ProjectFinancial $financial */
            $financial = new $this->class();
            $financial->setProject($project);
        }

        $this->save($financial);

        return $financial;
    }
}