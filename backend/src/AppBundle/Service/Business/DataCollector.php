<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Business;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Customer;
use AppBundle\Entity\MemberInterface;
use AppBundle\Entity\Order\Order;
use AppBundle\Entity\UserInterface;
use Doctrine\ORM\AbstractQuery;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DataCollector
 * This class collects analysis data for tracking services, based on the logged-in user
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class DataCollector
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * DataCollector constructor.
     * @param ContainerInterface $container
     */
    private function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param ContainerInterface $container
     * @return DataCollector
     */
    public static function create(ContainerInterface $container)
    {
        return new self($container);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->has($key) ? $this->parameters[$key] : $this->$key();
    }

    /**
     * @return array
     */
    public function data()
    {
        if(!$this->has('data')) {

            $account = $this->account();
            $member = $this->member();
            $request = $this->request();

            $host = $request->getHost();

            $data = [
                'host' => $host,
                'name' => $member->getFirstname(),
                'email' => $member->getEmail(),
                'account' => $account->getEmail(),
                'company' => $account->getFirstname(),
                'cnpj' => $account->getDocument(),
                'owner' => $account->getOwner()->getFirstname(),
                'phone' => $account->getPhone(),
                'created_at' => $account->getCreatedAt()->getTimestamp(),
                'profile' => $this->profile($member),
                'n_projects' => $this->projects(),
                'n_contacts' => $this->contacts()
            ];

            $this->orders($data);

            $this->set('data', $data);
        }

        return $this->get('data');
    }

    /**
     * @return BusinessInterface|AccountInterface
     */
    private function account()
    {
        if(!$this->has('account'))
            $this->set('account', $this->member()->getAccount());

        return $this->get('account');
    }

    /**
     * @return BusinessInterface|MemberInterface
     */
    private function member()
    {
        if(!$this->has('member'))
            $this->set('member', $this->user()->getInfo());

        return $this->get('member');
    }

    /**
     * @return UserInterface
     */
    private function user()
    {
        if(!$this->has('user'))
            $this->set('user', $this->container->get('security.token_storage')->getToken()->getUser());

        return $this->get('user');
    }

    /**
     * @return Request
     */
    private function request()
    {
        if(!$this->has('request'))
            $this->set('request', $this->container->get('request_stack')->getCurrentRequest());

        return $this->get('request');
    }

    /**
     * @return int
     */
    private function projects()
    {
        if(!$this->has('projects'))
            $this->set('projects', $this->countEntitiesByMemberIds(Project::class));

        return $this->get('projects');
    }

    /**
     * @return int
     */
    private function contacts()
    {
        if(!$this->has('contacts'))
            $this->set('contacts', $this->countEntitiesByMemberIds(Customer::class));

        return $this->get('contacts');
    }

    /**
     * @return \Doctrine\ORM\EntityManagerInterface
     */
    private function manager()
    {
        if(!$this->has('manager'))
            $this->set('manager', $this->container->get('doctrine.orm.entity_manager'));

        return $this->get('manager');
    }

    /**
     * @param MemberInterface $member
     * @return string
     */
    private function profile(MemberInterface $member)
    {
        if(!$this->has('profile')){

            $profile = $member->isMasterOwner() ? 'Dono da Conta' : ($member->isOwner() ? 'Admin' : 'Agente');

            $this->set('profile', $profile);
        }

        return $this->get('profile');
    }

    /**
     * @param array $data
     */
    private function orders(array &$data)
    {
        $statuses = Order::getStatusNames();

        $ignoreForParameters = [Order::STATUS_BUILDING];

        $data['n_orders'] = 0;
        foreach ($statuses as $status => $name){
            if(!in_array($status, $ignoreForParameters))
                $data[sprintf('n_orders_%s', $name)] = 0;
        }

        $qb = $this->manager()->createQueryBuilder();

        $qb->select(' o.status, count(o.id) as total')->from(Order::class, 'o')->where(
            $qb->expr()->andX(
                $qb->expr()->eq('o.account', $this->account()->getId()),
                $qb->expr()->in('o.status', array_keys($statuses))
            )
        )->groupBy('o.status');

        $orders = $qb->getQuery()->getResult();

        foreach ($orders as $order){

            if(!in_array($order['status'], $ignoreForParameters)){
                $key = sprintf('n_orders_%s', $statuses[$order['status']]);
                $data[$key] = (int) $order['total'];
            }

            $data['n_orders'] += $order['total'];
        }
    }

    /**
     * @return array
     */
    private function memberIds()
    {
        if(!$this->has('memberIds')) {

            $memberIds = $this->account()->getMembers()->map(function (MemberInterface $member) {
                return $member->getId();
            })->toArray();

            $this->set('memberIds', $memberIds);
        }

        return $this->get('memberIds');
    }

    /**
     * @param $class
     * @return int
     */
    private function countEntitiesByMemberIds($class)
    {
        $manager = $this->manager();

        $qb = $manager->createQueryBuilder();

        $qb->select('count(e.id)')->from($class, 'e')->where(
            $qb->expr()->in('e.member', $this->memberIds())
        );

        return (int)$qb->getQuery()->getResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    private function set($key, $value)
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    /**
     * @param $key
     * @return bool
     */
    private function has($key)
    {
        return array_key_exists($key, $this->parameters);
    }
}
