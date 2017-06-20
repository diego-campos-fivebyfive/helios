<?php

namespace AppBundle\Entity;

use Sonata\CoreBundle\Model\BaseEntityManager;

class TaskManager extends BaseEntityManager implements TaskManagerInterface
{
    /**
     * @param BusinessInterface $contact
     * @return array
     */
    public function findByContact(BusinessInterface $contact)
    {
        return $this->findBy(['contact' => $contact]);
    }
}
