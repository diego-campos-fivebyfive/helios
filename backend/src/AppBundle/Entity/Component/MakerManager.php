<?php

namespace AppBundle\Entity\Component;

use AppBundle\Entity\BusinessInterface;
use Sonata\CoreBundle\Model\BaseEntityManager;

class MakerManager extends BaseEntityManager implements MakerManagerInterface
{
    /**
     * @param $name
     * @param $context
     * @param BusinessInterface|null $account
     * @return null|object|ComponentInterface
     */
    public function findByUse($name, $context, BusinessInterface $account)
    {
        $criteria = [
            'name' => $name,
            'context' => $context,
            'account' => null
        ];

        if(null == $maker = $this->findOneBy($criteria)){
            $criteria['account'] = $account;
        }

        return $this->findOneBy($criteria);
    }
}
