<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;

/**
 * @ORM\HasLifecycleCallbacks()
 */
trait TokenizerTrait
{
    /**
     * @var string
     * @ORM\Column(name="token", type="string")
     */
    protected $token;

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function fireEvents()
    {
        $this->generateToken();
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Generate if Token is null
     */
    protected function generateToken()
    {
        if(!$this->token)
            $this->regenerateToken();
    }

    /**
     * Generate or Override Token
     */
    protected function regenerateToken()
    {
        $this->token = $this->getTokenGenerator()->generateToken();
    }

    /**
     * @return UriSafeTokenGenerator
     */
    protected function getTokenGenerator()
    {
        return new UriSafeTokenGenerator(200);
    }
}