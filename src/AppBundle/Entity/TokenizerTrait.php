<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;

trait TokenizerTrait
{
    /**
     * @var string
     * @ORM\Column(name="token", type="string")
     */
    protected $token;

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

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }
}