<?php

namespace AppBundle\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Session Attributes Store
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class SessionStorage
{
    /**
     * @var int
     */
    private $tokenEntropy = 32;

    /**
     * @var string
     */
    private $storageKey = 'app_session_storage_key';

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var AttributeBagInterface
     */
    private $attributes;

    /**
     * @var bool
     */
    private $isLoaded = false;

    /**
     * SessionStorage constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param SessionInterface $session
     */
    public function __construct(TokenStorageInterface $tokenStorage, SessionInterface $session)
    {
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;

        if(!$this->session->has($this->storageKey)){
            $this->session->set($this->storageKey, $this->getStorageKey());
        }

        $this->load();
    }

    /**
     * Load | Reload Attribute Bag
     *
     * @param $key
     * @return mixed|ParameterBag
     */
    public function load()
    {
        if(!$this->isLoaded) {

            $attributes = $this->session->get($this->storageKey);

            if (!$attributes instanceof AttributeBagInterface)
                $attributes = new AttributeBag($this->storageKey);

            $this->attributes = $attributes;
        }

        $this->isLoaded = true;

        return $this;
    }

    /**
     * Check the attribute is defined
     *
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return $this->attributes->has($name);
    }

    /**
     * Set | Override attribute value
     *
     * @param $name
     * @param $value
     * @return SessionStorage
     */
    public function set($name, $value)
    {
        $this->attributes->set($name, $value);

        $this->session->set($this->storageKey, $this->attributes);

        return $this;
    }

    /**
     * Return specific attribute value | null
     *
     * @param $name
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return $this->attributes->get($name, $default);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->attributes->all();
    }

    /**
     * Set null specific attribute
     *
     * @param $name
     * @return SessionStorage
     */
    public function remove($name)
    {
        $this->attributes->remove($name);

        return $this;
    }

    /**
     * Reset all attributes
     *
     * @return $this
     */
    public function clear()
    {
        $this->attributes->clear();

        return $this;
    }

    /**
     * @return null|\Symfony\Component\Security\Core\Authentication\Token\TokenInterface
     */
    private function getToken()
    {
        return $this->tokenStorage->getToken();
    }

    /**
     * @return string
     */
    private function getStorageKey()
    {
        $token = $this->getToken();

        if($token)
            return substr(base64_encode($this->getToken()->__toString()), 0, $this->tokenEntropy);

        $date = new \DateTime;

        return md5($date->format('Y-m-d'));
    }
}