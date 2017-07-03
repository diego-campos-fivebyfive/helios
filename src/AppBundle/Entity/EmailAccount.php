<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmailAccount
 *
 * @ORM\Table(name="app_email_account")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class EmailAccount implements EmailAccountInterface
{
    use TokenizerTrait;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=10)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=100)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="encode", type="string", length=10)
     */
    private $encode;

    /**
     * @var json
     *
     * @ORM\Column(name="folders", type="json", nullable=true)
     */
    private $folders;

    /**
     * @var string
     *
     * @ORM\Column(name="input_server", type="string", length=100)
     */
    private $inputServer;

    /**
     * @var integer
     *
     * @ORM\Column(name="input_port", type="integer")
     */
    private $inputPort;

    /**
     * @var string
     *
     * @ORM\Column(name="input_encryption", type="string", length=5)
     */
    private $inputEncryption;

    /**
     * @var string
     *
     * @ORM\Column(name="output_server", type="string", length=100)
     */
    private $outputServer;

    /**
     * @var integer
     *
     * @ORM\Column(name="output_port", type="integer")
     */
    private $outputPort;

    /**
     * @var string
     *
     * @ORM\Column(name="output_encryption", type="string", length=5)
     */
    private $outputEncryption;

    /**
     * @var boolean
     *
     * @ORM\Column(name="current", type="boolean")
     */
    private $current;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false)
     */
    private $updatedAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Customer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer", inversedBy="emailAccounts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="owner", referencedColumnName="id", nullable=false)
     * })
     */
    private $owner;

    function __construct()
    {
        $this->encode = self::ENCODE_UTF8;
        $this->type = self::TYPE_IMAP;
        $this->inputEncryption = self::ENCRYPTION_AUTO;
        $this->outputEncryption = self::ENCRYPTION_AUTO;
        $this->current = false;
        $this->folders = [];
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
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @inheritDoc
     */
    public function setEncode($encode)
    {
        $this->checkDefinition('encode', $encode);
        $this->encode = $encode;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getEncode()
    {
        return $this->encode;
    }

    /**
     * @inheritDoc
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @inheritDoc
     */
    public function setPassword($password)
    {
        $this->password = $password;
        $this->encodePassword();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->decodePassword();
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function setType($type)
    {
        $this->checkDefinition('type', $type);
        $this->type = $type;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function setInputServer($inputServer)
    {
        $this->inputServer = $inputServer;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInputServer()
    {
        return $this->inputServer;
    }

    /**
     * @inheritDoc
     */
    public function setInputPort($inputPort)
    {
        $this->inputPort = $inputPort;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInputPort()
    {
        return $this->inputPort;
    }

    /**
     * @inheritDoc
     */
    public function setInputEncryption($inputEncryption)
    {
        $this->checkDefinition('encryption', $inputEncryption);
        $this->inputEncryption = $inputEncryption;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInputEncryption()
    {
        return $this->inputEncryption;
    }

    /**
     * @inheritDoc
     */
    public function setOutputServer($outputServer)
    {
        $this->outputServer = $outputServer;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOutputServer()
    {
        return $this->outputServer;
    }

    /**
     * @inheritDoc
     */
    public function setOutputPort($outputPort)
    {
        $this->outputPort = $outputPort;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOutputPort()
    {
        return $this->outputPort;
    }

    /**
     * @inheritDoc
     */
    public function setOutputEncryption($outputEncryption)
    {
        $this->checkDefinition('encryption', $outputEncryption);
        $this->outputEncryption= $outputEncryption;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOutputEncryption()
    {
        return  $this->outputEncryption;
    }

    /**
     * @inheritDoc
     */
    public function getEmailDomain()
    {
        return substr(strrchr($this->email, "@"), 1);
    }

    /**
     * @inheritDoc
     */
    public function setFolders(array $folders = [])
    {
        $this->folders = $folders;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFolders()
    {
        return $this->folders;
    }

    /**
     * @inheritDoc
     */
    public function getFolder($id)
    {
        return $this->folders[$id];
    }

    /**
     * @inheritDoc
     */
    public function hasFolders()
    {
        return !empty($this->folders);
    }

    /**
     * @inheritDoc
     */
    public function setCurrent($current)
    {
        $this->current = $current;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isCurrent()
    {
        return $this->current;
    }

    /**
     * @inheritDoc
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @inheritDoc
     */
    public function setOwner(BusinessInterface $owner)
    {
        if(!$owner->isMember())
            throw new \InvalidArgumentException(self::ERROR_UNSUPPORTED_OWNER_TYPE);

        $this->owner = $owner;
    }

    /**
     * @inheritDoc
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime;
        $this->updatedAt = new \DateTime;
        $this->generateToken();
    }

    /**
     * @inheritDoc
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime;
        $this->generateToken();
    }

    /**
     * @inheritDoc
     */
    public static function getTypeList()
    {
        return [
            self::TYPE_IMAP => self::TYPE_IMAP,
            self::TYPE_POP3 => self::TYPE_POP3
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getEncodeList()
    {
        return [
            self::ENCODE_UTF8 => self::ENCODE_UTF8
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getEncryptionList()
    {
        return [
            self::ENCRYPTION_NONE => self::ENCRYPTION_NONE,
            self::ENCRYPTION_SSL => self::ENCRYPTION_SSL,
            self::ENCRYPTION_TLS => self::ENCRYPTION_TLS,
            self::ENCRYPTION_AUTO => self::ENCRYPTION_AUTO
        ];
    }

    /**
     * @return array
     */
    public static function getDefaultMails()
    {
        return [
            'outlook' => [
                'name' => 'Outlook / Hotmail',
                'type' => 'imap',
                'inputServer' => 'imap-mail.outlook.com',
                'inputPort' => 993,
                'inputEncryption' => 'ssl',
                'outputServer' => 'smtp-mail.outlook.com',
                'outputPort' => 587,
                'outputEncryption' => 'auto',
                'icon' => 'outlook.png'
            ],
            'gmail' => [
                'name' => 'Gmail',
                'type' => 'imap',
                'inputServer' => 'imap.gmail.com',
                'inputPort' => 993,
                'inputEncryption' => 'ssl',
                'outputServer' => 'smtp.gmail.com',
                'outputPort' => 443,
                'outputEncryption' => 'auto',
                'icon' => 'gmail.png'
            ],
            'yahoo' => [
                'name' => 'Yahoo',
                'type' => 'imap',
                'inputServer' => 'imap.mail.yahoo.com',
                'inputPort' => 993,
                'inputEncryption' => 'ssl',
                'outputServer' => 'smtp.mail.yahoo.com',
                'outputPort' => 465,
                'outputEncryption' => 'auto',
                'icon' => 'yahoo.png'
            ]
        ];
    }

    public static function getDefaultFolders()
    {
        return [
            'gmail' => [
                'Sent' => '[Gmail]'
            ]
        ];
    }

    private function encodePassword()
    {
        $this->password = base64_encode($this->password);
    }

    private function decodePassword()
    {
        return base64_decode($this->password);
    }

    /**
     * Check definition values
     * @param $name
     * @param $value
     * Used by: setType, setEncode, setEncryption
     */
    private function checkDefinition($name, $value)
    {
        $method = 'get' . ucfirst($name) . 'List';
        $definitions = self::$method();

        if(!array_key_exists($value, $definitions))
            throw new \InvalidArgumentException(self::ERROR_UNSUPPORTED_DEFINITION);
    }
}

