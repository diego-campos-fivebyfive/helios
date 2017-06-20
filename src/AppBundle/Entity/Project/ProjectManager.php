<?php

namespace AppBundle\Entity\Project;

use AppBundle\Entity\BusinessInterface;
use Doctrine\ORM\Query\Expr\Join;
use Sonata\CoreBundle\Model\BaseEntityManager;

class ProjectManager extends BaseEntityManager implements ProjectManagerInterface
{
    /**
     * @inheritDoc
     */
    public function create(BusinessInterface $member = null)
    {
        if(!$member instanceof BusinessInterface)
            throw new \InvalidArgumentException('Please, define a project member');

        /**
         * @var ProjectInterface
         */
        $project = parent::create();

        $project->setMember($member);

        return $project;
    }

    /**
     * @inheritDoc
     */
    public function save($entity, $andFlush = true)
    {
        parent::save($entity, $andFlush);

        $this->synchronizeClearModules();
    }

    /**
     * This process is necessary because orphanRemoval and cascade-remove
     * behaviors is not applicable
     * @internal Caution: not change encapsulation this method
     */
    private function synchronizeClearModules()
    {
        $rollbackClass = $this->class;
        $this->class = ProjectModule::class;

        $orphans = $this->findBy(['inverter' => null]);

        if(count($orphans)){
            foreach ($orphans as $orphan) {
                if($orphan instanceof ProjectModuleInterface){
                    $this->delete($orphan);
                }
            }
        }

        $this->class = $rollbackClass;
    }

    /**
     * @param BusinessInterface $contact
     * @return array
     */
    public function findByCustomer(BusinessInterface $customer)
    {
        return $this->findBy(['customer' => $customer]);
    }

    /**
     * @param BusinessInterface $account
     * @return array
     */
    public function findByAccount(BusinessInterface $account)
    {
        if(!$account->isAccount()){
            throw new \InvalidArgumentException('Invalid account context');
        }

        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('p')
            ->from($this->class, 'p')
            ->join('p.member', 'm')
            ->join('m.account', 'a', Join::WITH, $qb->expr()->eq('a.id', $account->getId()))
        ;

        return $qb->getQuery()->getResult();
    }
}