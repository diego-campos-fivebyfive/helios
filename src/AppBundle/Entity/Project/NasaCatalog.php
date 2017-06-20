<?php

namespace AppBundle\Entity\Project;

use Doctrine\ORM\Mapping as ORM;

use AppBundle\Entity\BusinessInterface;

/**
 * NasaCatalog
 *
 * @ORM\Table(name="app_nasa_catalog")
 * @ORM\Entity
 */
class NasaCatalog implements NasaCatalogInterface
{
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
     * @ORM\Column(name="context", type="string", length=25)
     */
    private $context;

    /**
     * @var integer
     *
     * @ORM\Column(name="latitude", type="smallint", nullable=true)
     */
    private $latitude;

    /**
     * @var integer
     *
     * @ORM\Column(name="longitude", type="smallint", nullable=true)
     */
    private $longitude;

    /**
     * @var json
     *
     * @ORM\Column(name="months", type="json")
     */
    private $months;

    /**
     * @var \AppBundle\Entity\Customer
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Customer")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="account_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $account;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->months = [];
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
     * @inheritDoc
     */
    public function toArray()
    {
        return $this->getMonths();
    }

    /**
     * @inheritDoc
     */
    public function setAccount(BusinessInterface $account)
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set context
     *
     * @param string $context
     * @return NasaCatalog
     */
    public function setContext($context)
    {
        if(!array_key_exists($context, self::getContextList()))
            throw new \InvalidArgumentException(self::ERROR_UNSUPPORTED_CONTEXT);

        $this->context = $context;

        return $this;
    }

    /**
     * Get context
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set latitude
     *
     * @param integer $latitude
     * @return NasaCatalog
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * Get latitude
     *
     * @return integer
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param integer $longitude
     * @return NasaCatalog
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * Get longitude
     *
     * @return integer
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set months
     *
     * @param array $months
     * @return NasaCatalog
     */
    public function setMonths(array $months)
    {
        foreach ($months as $month => $value){
            $months[$month] = (float) str_replace(',', '.', $value);
        }

        $this->months = $months;

        return $this;
    }

    /**
     * Get months
     *
     * @return array
     */
    public function getMonths()
    {
        return array_map('floatval', $this->months);
    }

    /**
     * @inheritDoc
     */
    public function getContextList()
    {
        return [
            self::AIR_TEMPERATURE => self::AIR_TEMPERATURE,
            self::AIR_TEMPERATURE_MIN => self::AIR_TEMPERATURE_MIN,
            self::AIR_TEMPERATURE_MAX => self::AIR_TEMPERATURE_MAX,
            self::DAYLIGHT_HOURS => self::DAYLIGHT_HOURS,
            self::RADIATION_DIFFUSE => self::RADIATION_DIFFUSE,
            self::RADIATION_GLOBAL  => self::RADIATION_GLOBAL,
            self::RADIATION_ATMOSPHERE => self::RADIATION_ATMOSPHERE,
            self::SOLAR_NOON => self::SOLAR_NOON
        ];
    }
}

