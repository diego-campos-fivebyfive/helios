<?php

namespace AppBundle\Entity;


interface ThemeInterface
{

    /**
     * @return int
     */
    public function getId();

    /**
     * @param $accountId
     * @return ThemeInterface
     */
    public function setAccountId($accountId);

    /**
     * @return int
     */
    public function getAccountId();

    /**
     * @param $name
     * @return ThemeInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param $filename
     * @return ThemeInterface
     */
    public function setFilename($filename);

    /**
     * @return string
     */
    public function getFilename();

    /**
     * @param $theme
     * @return ThemeInterface
     */
    public function setTheme($theme);

    /**
     * @return bool
     */
    public function getTheme();

    /**
     * @param $content
     * @return ThemeInterface
     */
    public function setContent($content);

    /**
     * @return string
     */
    public function getContent();
}
