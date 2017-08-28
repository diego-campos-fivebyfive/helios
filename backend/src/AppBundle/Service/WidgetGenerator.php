<?php


namespace AppBundle\Service;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Project\ProjectInterface;
use AppBundle\Entity\UserInterface;
use AppBundle\Service\Support\Widget;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\ContainerInterface;

class WidgetGenerator
{
    /**
     * @var BusinessInterface
     */
    private $member;

    /**
     * @var BusinessInterface
     */
    private $account;

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ArrayCollection
     */
    private $parameters;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->parameters = new ArrayCollection();
    }

    /**
     * @param BusinessInterface $member
     */
    public function member(BusinessInterface $member)
    {
        if(!$member->isMember())
            $this->invalidArgumentException('Invalid member');

        $this->member = $member;
        $this->user = $member->getUser();
        $this->account = $member->getAccount();

        return $this;
    }

    /**
     * @param $widget
     * @return mixed
     */
    public function generate($widget)
    {
        $method = sprintf('generateWidget%s', ucfirst($widget));

        if(!method_exists($this, $method))
            $this->invalidArgumentException(sprintf('Generator method %s() not found', $method));

        $view = $widget;

        /** @var Widget $widget */
        $widget = $this->$method();

        $widget->set('view', $view);

        return $widget;
    }

    private function generateWidgetAccounts()
    {
        $context = $this->account->getContext();

        $accounts = $this->getCustomerManager()->findBy([
            'context' => $context
        ]);

        $accountsCount = count($accounts);
        $currentWeek = $this->extractCreatedAtCount($accounts);
        $rate = $currentWeek > 0 ? round(100 / ($accountsCount / $currentWeek)) : 0 ;

        return new Widget([
            'total' => $accountsCount,
            'rate' => $rate,
            'current_week' => $currentWeek
        ]);
    }

    /**
     * @return Widget
     */
    private function generateWidgetMembers()
    {
        $members = $this->account->getMembers();

        $online = 0;
        foreach($members as $member){
            if($member instanceof BusinessInterface){
                $online = $member->isOnline() ? $online+1 : $online;
            }
        }

        return new Widget([
            'name' => 'Members',
            'total' => $members->count(),
            'online' => $online,
            'updated' => new \DateTime
        ]);
    }

    /**
     * @return Widget
     */
    private function generateWidgetProjects()
    {
        $members = $this->account->getMembers();

        $projectsCount = 0;
        $currentWeek = 0;

        foreach($members as $member) {
            if($member instanceof BusinessInterface
                && $member->isMember()){
                $projectsCount += $member->getProjects()->count();

                $currentWeek += $this->extractCreatedAtCount($member->getProjects()->toArray());
            }
        }

        $rate = $currentWeek > 0 ? round(100 / ($projectsCount / $currentWeek)) : 0 ;
        
        return new Widget([
            'name' => 'Projects',
            'total' => $projectsCount,
            'rate' => $rate,
            'current_week' => $currentWeek
        ]);
    }

    /**
     * @return Widget
     */
    private function generateWidgetContacts()
    {
        $contacts = $this->account->getAccountContacts();

        /**
         * This graph not used still
         */
        $graph = [
            'peoples' => [
                'current_week' => 0
            ],
            'companies' => [
                'current_week' => 0
            ]
        ];

        $date = new \DateTime;

        //$peoplesWeek = [];
        $peoples = $contacts->filter(function(BusinessInterface $contact) use(&$graph, $date){

            $createdAt = $contact->getCreatedAt();
            if($date->format('Y') ==  $createdAt->format('Y') ){
                if($date->format('W') == $createdAt->format('W')){
                    $root = $graph[$contact->isPerson() ? 'peoples' : 'companies']['current_week'];
                    $graph[$contact->isPerson() ? 'peoples' : 'companies']['current_week'] = $root+1;
                }
            }

            return $contact->isPerson();
        });

        $companies = $contacts->filter(function(BusinessInterface $contact){
            return $contact->isCompany();
        });

        $peoplesCount = $peoples->count();
        $companiesCount = $companies->count();
        $total = $companiesCount + $peoplesCount;
        $peoplesRate = $total > 0 ? round(100 / ($total / $peoplesCount)) : 0 ;
        $companiesRate = $total > 0 ? round(100 - $peoplesRate) : 0 ;


        return new Widget([
            'total' => $total,
            'graph' => $graph,
            'peoples' => [
                'total' => $peoplesCount,
                'rate' => $peoplesRate
            ],
            'companies' => [
                'total' => $companiesCount,
                'rate' => $companiesRate,
            ]
        ]);
    }

    /**
     * @param array $business
     */
    private function extractCreatedAtCount(array $sources = [], $interval = 'W')
    {
        $count = 0;
        $date = new \DateTime;

        foreach ($sources as $source) {
            /** @var \DateTime $createdAt */
            $createdAt = $source->getCreatedAt();

            if ($date->format('Y') == $createdAt->format('Y')
                && $date->format($interval) == $createdAt->format($interval)
            ) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @return \AppBundle\Entity\CustomerManager
     */
    private function getCustomerManager()
    {
        return $this->container->get('app.customer_manager');
    }

    /**
     * @param $message
     */
    private function invalidArgumentException($message)
    {
        throw new \InvalidArgumentException($message);
    }
}