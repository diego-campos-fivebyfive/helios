<?php

namespace AppBundle\Entity\Extra;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * @deprecated This table will be removed
 */
class AccountRegister
{
    use ORMBehaviors\Timestampable\Timestampable;

    const STAGE_INIT = 'init';
    const STAGE_INFO = 'info';
    const STAGE_DONE = 'done';

    /**
     * @var string
     *
     * @ORM\Column(name="confirmation_token", type="string", length=200, nullable=true)
     */
    private $confirmationToken;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
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
     * @ORM\Column(name="phone", type="string", length=25, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="company_status", type="string", length=20, nullable=true)
     */
    private $companyStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="company_name", type="string", length=100, nullable=true)
     */
    private $companyName;

    /**
     * @var string
     *
     * @ORM\Column(name="company_sector", type="string", length=20, nullable=true)
     */
    private $companySector;

    /**
     * @var string
     *
     * @ORM\Column(name="company_members", type="string", length=5, nullable=true)
     */
    private $companyMembers;

    /**
     * @var json
     *
     * @ORM\Column(name="parameters", type="json", nullable=true)
     */
    private $parameters;

    /**
     * @var string
     *
     * @ORM\Column(name="stage", type="string", length=10)
     */
    private $stage;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    function __construct()
    {
        $this->parameters = [];
        $this->stage = self::STAGE_INIT;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * @param string $confirmationToken
     * @return AccountRegister
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;
        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return AccountRegister
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return AccountRegister
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return AccountRegister
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getCompanyStatus()
    {
        return $this->companyStatus;
    }

    /**
     * @param string $companyStatus
     * @return AccountRegister
     */
    public function setCompanyStatus($companyStatus)
    {
        $this->companyStatus = $companyStatus;
        return $this;
    }

    /**
     * @return string
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @param string $companyName
     * @return AccountRegister
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
        return $this;
    }

    /**
     * @return string
     */
    public function getCompanySector()
    {
        return $this->companySector;
    }

    /**
     * @param string $companySector
     * @return AccountRegister
     */
    public function setCompanySector($companySector)
    {
        $this->companySector = $companySector;
        return $this;
    }

    /**
     * @return int
     */
    public function getCompanyMembers()
    {
        return $this->companyMembers;
    }

    /**
     * @param int $companyMembers
     * @return AccountRegister
     */
    public function setCompanyMembers($companyMembers)
    {
        $this->companyMembers = $companyMembers;
        return $this;
    }

    /**
     * @return bool
     */
    public function isInit()
    {
        return self::STAGE_INIT == $this->stage;
    }

    /**
     * @return bool
     */
    public function isInfo()
    {
        return self::STAGE_INFO == $this->stage;
    }

    /**
     * @return bool
     */
    public function isDone()
    {
        return self::STAGE_DONE == $this->stage;
    }

    /**
     * Set parameters
     *
     * @param array $parameters
     * @return AccountRegister
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function addParameter($key, $value)
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function removeParameter($key)
    {
        if($this->hasParameter($key)){
            unset($this->parameters[$key]);
        }

        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setParameter($key, $value)
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function getParameter($key, $default = null)
    {
        return $this->hasParameter($key) ?  $this->parameters[$key] : $default ;
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasParameter($key)
    {
        return array_key_exists($key, $this->parameters);
    }

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set stage
     *
     * @param string $stage
     * @return AccountRegister
     */
    public function setStage($stage)
    {
        $this->stage = $stage;

        return $this;
    }

    /**
     * Get stage
     *
     * @return string
     */
    public function getStage()
    {
        return $this->stage;
    }

    /**
     * @return array
     */
    public static function getSupportedCompanyStatus()
    {
        return [
            'has_company' => 'I have company in the area of ​​photovoltaic solar energy',
            'open_company' => 'I want to open a company in the area of ​​photovoltaic solar energy'
        ];
    }

    /**
     * @return array
     */
    public static function getSupportedCompanySector()
    {
        return [
            'commerce' => 'Commercialization of equipment',
            'services' => 'Provision of services',
            'both' => 'Both'
        ];
    }

    /**
     * @return array
     */
    public static function getSupportedCompanyMembers()
    {
        return [
            '1-2' => '1 a 2',
            '3-4' => '3 a 4',
            '5-n' => 'Acima de 5'
        ];
    }
}

