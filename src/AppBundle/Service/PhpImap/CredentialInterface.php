<?php

namespace AppBundle\Service\PhpImap;

interface CredentialInterface
{
    /**
     * @return array
     */
    public function toArray();

    /**
     * @param $username
     * @return CredentialInterface
     */
    public function setUsername($username);

    /**
     * @return string
     */
    public function getUsername();

    /**
     * @param $password
     * @return CredentialInterface
     */
    public function setPassword($password);

    /**
     * @return string
     */
    public function getPassword();
}