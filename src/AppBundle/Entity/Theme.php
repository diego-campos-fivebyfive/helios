<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Theme
 *
 * @ORM\Table(name="app_theme")
 * @ORM\Entity
 */
class Theme implements ThemeInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="account_id", type="integer")
     */
    private $accountId;

    /**
     * @var bool
     *
     * @ORM\Column(name="theme", type="boolean")
     */
    private $theme;

    /**
     * @var bool
     *
     * @ORM\Column(name="theme_sices", type="boolean")
     */
    private $themeSices;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     */
    private $content;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set accountId
     *
     * @param integer $accountId
     *
     * @return Theme
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;

        return $this;
    }

    /**
     * Get accountId
     *
     * @return int
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * Set theme
     *
     * @param boolean $theme
     *
     * @return Theme
     */
    public function setTheme($theme)
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * Get theme
     *
     * @return bool
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @return bool
     */
    public function isThemeSices()
    {
        return $this->themeSices;
    }

    /**
     * @param bool $themeSices
     * @return Theme
     */
    public function setThemeSices($themeSices)
    {
        $this->themeSices = $themeSices;
        return $this;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Theme
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
}

