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