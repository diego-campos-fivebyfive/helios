<?php

namespace AppBundle\Service\PhpImap;

/**
 * Interface PathInterface
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
interface PathInterface
{
    /**
     * PATH
     * 1. domain
     * 2. port
     * 3. extras
     * Example:         1          2       3
     *          [imap.gmail.com]:[993][/imap/ssl]
     */
    const PATH = '{%s:%s%s}INBOX';

    /**
     * @param null $domain
     * @param null $port
     * @param array $extras
     */
    public function __construct($domain = null, $port = null, array $extras = []);

    /**
     * @return string
     */
    public function __toString();
    
    /**
     * @param $domain
     * @return PathInterface
     */
    public function setDomain($domain);

    /**
     * @return string
     */
    public function getDomain();

    /**
     * @param $port
     * @return PathInterface
     */
    public function setPort($port);

    /**
     * @return string
     */
    public function getPort();

    /**
     * @param array $extras
     * @return PathInterface
     */
    public function setExtras(array $extras);

    /**
     * @param $extra
     * @return PathInterface
     */
    public function addExtra($extra);

    /**
     * @param $extra
     * @return PathInterface
     */
    public function removeExtra($extra);

    /**
     * @return array
     */
    public function getExtras();

    /**
     * @return string
     */
    public function format();
}