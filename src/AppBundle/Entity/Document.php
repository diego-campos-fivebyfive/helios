<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Document
 *
 * @ORM\Table(name="app_document")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Document implements DocumentInterface
{
    use TokenizerTrait;
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @var json
     *
     * @ORM\Column(name="metadata", type="json", nullable=true)
     */
    private $metadata;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Document", mappedBy="parent", cascade={"persist","remove","merge"})
     */
    private $sections;

    /**
     * @var \AppBundle\Entity\Document
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Document", inversedBy="sections")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * })
     */
    private $parent;

    function __construct()
    {
        $this->sections = new ArrayCollection();
        $this->position = 0;
        $this->metadata = [];
    }

    /**
     * @inheritDoc
     */
    function __clone()
    {
        $this->id = null;
        $this->token = null;
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
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @inheritDoc
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @inheritDoc
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @inheritDoc
     */
    public function setPosition($position)
    {
        $this->position = (int) $position;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @inheritDoc
     */
    public function setParent(DocumentInterface $parent)
    {
        $this->parent = $parent;

        if(!$this->parent->getSections()->contains($this)){
            $this->parent->getSections()->add($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @inheritDoc
     */
    public function createSection($title, $content, $position = null)
    {
        $section = new Document();

        $section
            ->setTitle($title)
            ->setContent($content)
            ->setPosition($position);

        $this->addSection($section);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addSection(DocumentInterface $section)
    {
        if(!$this->sections->contains($section)){
            $this->sections->add($section);
            $section->setParent($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeSection(DocumentInterface $section)
    {
        if($this->sections->contains($section)){
            $this->sections->removeElement($section);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSections()
    {
        $criteria = Criteria::create();
        $criteria->orderBy(['position' => 'asc']);

        return $this->sections->matching($criteria);
    }

    /**
     * @inheritDoc
     */
    public function hasChildren()
    {
        return $this->sections->count();
    }

    /**
     * @inheritDoc
     */
    public function setMetadata(array $metadata)
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addMetadata($key, $value)
    {
        $this->metadata[$key] = $value;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeMetadata($key)
    {
        if($this->hasMetadata($key)){
            unset($this->metadata[$key]);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMetadata($key = null, $default = null)
    {
        return  $key ? ($this->hasMetadata($key) ? $this->metadata[$key] : $default) : $this->metadata ;
    }

    /**
     * @inheritDoc
     */
    public function hasMetadata($key)
    {
        return array_key_exists($key, $this->metadata);
    }

    /**
     * @inheritDoc
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->generateToken();
    }
}

