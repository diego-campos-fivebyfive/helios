<?php

namespace AppBundle\Service\Business;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Customer;
use AppBundle\Entity\MemberInterface;
use AppBundle\Entity\Order\Order;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class Intercom
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * Intercom constructor.
     * @param EntityManagerInterface $manager
     */
    function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param MemberInterface $member
     * @param Request $request
     * @return array
     */
    public function extractInfo(MemberInterface $member, Request $request)
    {
        $this->initializeParameters($member);


        /** @var AccountInterface $account */
        $account = $this->parameters['account'];
        $host = $request->getHost();

        $info = [
            'host' => $host,
            'name' => $member->getName(),
            'email' => $member->getEmail(),
            'account' => $account->getEmail(),
            'company' => $account->getFirstname(),
            'owner' => $account->getOwner()->getName(),
            'phone' => $account->getPhone(),
            'created_at' => $account->getCreatedAt()->getTimestamp(),
            'profile' => $this->resolveProfile($member),
            'n_projects' => $this->resolveProjects(),
            'n_contacts' => $this->resolveContacts()
        ];

        $this->resolveOrders($info);

        return $info;
    }

    /**
     * @param MemberInterface $member
     * @return string
     */
    private function resolveProfile(MemberInterface $member)
    {
        return $member->isMasterOwner() ? 'Dono da Conta' : ($member->isOwner() ? 'Admin' : 'Agente');
    }

    /**
     * @return int
     */
    private function resolveProjects()
    {
        return $this->countEntitiesByMemberIds(Project::class);
    }

    /**
     * @return int
     */
    private function resolveContacts()
    {
        return $this->countEntitiesByMemberIds(Customer::class);
    }

    /**
     * Resolve and merge order status info
     */
    private function resolveOrders(array &$info)
    {
        $statusList = Order::getStatusList();
        $statusNames = Order::getStatusNames();

        unset($statusList[Order::STATUS_BUILDING]);
        unset($statusNames[Order::STATUS_BUILDING]);

        $info['n_orders'] = 0;
        foreach ($statusNames as $statusName){
            $info[sprintf('n_orders_%s', $statusName)] = 0;
        }

        $qb = $this->manager->createQueryBuilder();

        $qb->select(' o.status, count(o.id) as total')->from(Order::class, 'o')->where(
            $qb->expr()->andX(
                $qb->expr()->eq('o.account', $this->parameters['account']->getId()),
                $qb->expr()->in('o.status', $statusList)
            )
        )->groupBy('o.status');

        $orders = $qb->getQuery()->getResult();

        foreach ($orders as $order){

            $key = sprintf('n_orders_%s', $statusNames[$order['status']]);

            $info[$key] = (int) $order['total'];
            $info['n_orders'] += $order['total'];
        }
    }

    /**
     * @param $class
     * @return int
     */
    private function countEntitiesByMemberIds($class)
    {
        $qb = $this->manager->createQueryBuilder();

        $qb->select('count(e.id)')->from($class, 'e')->where(
            $qb->expr()->in('e.member', $this->parameters['member_ids'])
        );

        return (int)$qb->getQuery()->getResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);
    }

    /**
     * @param MemberInterface $member
     */
    private function initializeParameters(MemberInterface $member)
    {
        $account = $member->getAccount();

        $this->parameters = [
            'account' => $account,
            'member_ids' => $account->getMembers()->map(function (MemberInterface $member) {
                return $member->getId();
            })->toArray(),
        ];
    }
}
