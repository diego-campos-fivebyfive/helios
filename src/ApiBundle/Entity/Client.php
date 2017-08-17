<?php

namespace ApiBundle\Entity;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table("api_client")
 */
class Client extends BaseClient
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="AccessToken", mappedBy="client", cascade={"remove"})
     */
    protected $accessTokens;

    /**
     * @ORM\OneToMany(targetEntity="RefreshToken", mappedBy="client", cascade={"remove"})
     */
    protected $refreshTokens;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
}