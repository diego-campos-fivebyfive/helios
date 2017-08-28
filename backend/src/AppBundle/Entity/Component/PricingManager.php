<?php

namespace AppBundle\Entity\Component;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\ParameterManagerInterface;
use AppBundle\Entity\UserInterface;
use AppBundle\Model\KitPricing;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PricingManager
{
    /**
     * @var string
     */
    private $parameterId = 'kit-pricing';

    /**
     * @var ParameterManagerInterface
     */
    private $parameterManager;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var array
     */
    private $limits = [];

    /**
     * @var BusinessInterface $account
     */
    private $account;

    /**
     * @inheritDoc
     */
    public function __construct(ParameterManagerInterface $parameterManager, TokenStorageInterface $tokenStorage)
    {
        $this->parameterManager = $parameterManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param $id
     * @return KitPricing
     */
    public function find($id)
    {
        return $this->create($this->load()->get($id));
    }

    /**
     * @return array
     */
    public function findAll()
    {
        $parameters = [];
        foreach ($this->load()->all() as $parameter) {
            $parameters[] = $this->create($parameter);
        }

        return $parameters;
    }

    /**
     * @param array $data
     * @return KitPricing
     */
    public function create(array $data = [])
    {
        return new KitPricing($data);
    }

    /**
     * @param KitPricing $pricing
     */
    public function save(KitPricing $pricing)
    {
        $parameters = $this->parameterManager->findOrCreate($this->parseId());

        $parameters->set($pricing->id, $pricing->toArray());

        $this->checkLimits($parameters->all());

        if($this->limits['accepted']) {
            $this->parameterManager->save($parameters);
            return true;
        }

        return false;
    }

    /**
     * @param KitPricing $pricing
     */
    public function delete(KitPricing $pricing)
    {
        $parameters = $this->load();

        $parameters->remove($pricing->id);

        $this->parameterManager->save($parameters);
    }

    /**
     * @return array
     */
    public function getLimits()
    {
        return $this->limits;
    }

    /**
     * @param BusinessInterface $account
     * @return string
     */
    public function generateAccountId(BusinessInterface $account)
    {
        return strtoupper(md5($this->parameterId)) . $account->getToken();
    }

    /**
     * @return \AppBundle\Entity\Parameter
     */
    private function load()
    {
        return $this->parameterManager->findOrCreate($this->parseId());
    }

    /**
     * @return string
     */
    private function parseId()
    {
        $account = $this->getAccount();

        return $this->generateAccountId($account);
    }

    /**
     * @param BusinessInterface $account
     * @return $this
     */
    public function setAccount(BusinessInterface $account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return \AppBundle\Entity\BusinessInterface
     */
    public function getAccount()
    {
        if(!$this->account) {

            $user = $this->tokenStorage->getToken()->getUser();

            if ($user instanceof UserInterface) {
                $this->account = $user->getInfo()->getAccount();
            }
        }

        return $this->account;
    }

    /**
     * @param $parameters
     */
    private function checkLimits($parameters)
    {
        $services = 0;
        $equipments = 0;
        $general = 0;

        foreach($parameters as $parameter){

            $parameter = $this->create($parameter);

            if($parameter instanceof KitPricing){
                switch ($parameter->target){
                    case KitPricing::TARGET_EQUIPMENTS:
                        $equipments += $parameter->percent;
                        break;
                    case KitPricing::TARGET_SERVICES:
                        $services += $parameter->percent;
                        break;
                    case KitPricing::TARGET_GENERAL:
                        $general += $parameter->percent;
                        break;
                }
            }
        }

        $equipments += $general;
        $services += $general;

        $this->limits = [
            KitPricing::TARGET_EQUIPMENTS => $equipments,
            KitPricing::TARGET_SERVICES => $services
        ];

        $this->limits['accepted'] = ($equipments < 99 && $services < 99);
    }
}