<?php

namespace AppBundle\Manager;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Customer;

class CustomerManager extends AbstractManager
{
    /**
     * @inheritDoc
     */
    public function delete($entity, $andFlush = true)
    {
        if($entity instanceof BusinessInterface){

            if($entity->isMember() && $entity->getUser()){
                $entity->setStatus(AccountInterface::LOCKED);
                $entity->getUser()->setEnabled(false);
            }

            /**
             * When member is invited
             */
            if($entity->isMember() && $entity->isInvited()){
                $entity
                    ->isLeader(false)
                    ->setAttributes([])
                    ->setConfirmationToken(null)
                    ->setStatus(AccountInterface::LOCKED)
                    ->setEmail(null);
            }

            $this->save($entity);

            parent::delete($entity, $andFlush);
        }
    }

    /**
     * @param Customer $entity
     */
    public function restore(Customer $entity)
    {
        $entity->restore();

        if($entity->isMember() && $entity->getUser()){
            $entity->getUser()->setEnabled(true);
        }

        parent::save($entity);
    }

    /**
     * @param $token
     * @return null|object|\AppBundle\Entity\BusinessInterface
     */
    public function findByToken($token)
    {
        return $this->findOneBy(['token' => $token]);
    }
}
