<?php

namespace AppBundle\Entity\Extra;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\TokenizerTrait;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * EmailHandler
 *
 * @ORM\Table(name="app_email_handler")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class EmailHandler implements EmailHandlerInterface
{
    use TokenizerTrait;
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var string
     *
     * @ORM\Column(name="behavior", type="string", length=255)
     */
    private $behavior;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="text")
     */
    private $url;

    /**
     * @var integer
     *
     * @ORM\Column(name="requests", type="integer")
     */
    private $requests;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->requests = 0;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function setBehavior($behavior)
    {
        if(!in_array($behavior, self::getBehaviors()))
            throw new \InvalidArgumentException(sprintf('Invalid behavior [%s]', $behavior));

        $this->behavior = $behavior;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBehavior()
    {
        return $this->behavior;
    }

    /**
     * @inheritDoc
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @inheritDoc
     */
    public function isDownload()
    {
        return $this->isBehavior(self::DOWNLOAD);
    }

    /**
     * @inheritDoc
     */
    public function isRedirect()
    {
        return $this->isBehavior(self::REDIRECT);
    }

    /**
     * @inheritDoc
     */
    public function isBehavior($behavior)
    {
        return strtolower($behavior) === $this->behavior;
    }

    /**
     * @inheritDoc
     */
    public function nextRequest()
    {
        $this->requests += 1;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * LifecycleCallbacks
     */
    public function prePersist()
    {
        $this->generateToken();
    }

    /**
     * @inheritDoc
     */
    public static function getBehaviors()
    {
        return [
            self::DOWNLOAD => self::DOWNLOAD,
            self::REDIRECT => self::REDIRECT
        ];
    }
}