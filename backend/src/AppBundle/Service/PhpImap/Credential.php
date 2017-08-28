<?php
/**
 * Created by PhpStorm.
 * User: Claudinei
 * Date: 07/09/2016
 * Time: 00:48
 */

namespace AppBundle\Service\PhpImap;

class Credential implements CredentialInterface
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'username' => $this->username,
            'password' => $this->password
        ];
    }

    /**
     * @inheritDoc
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @inheritDoc
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }
}