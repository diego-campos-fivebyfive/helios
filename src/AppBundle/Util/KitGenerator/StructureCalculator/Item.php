<?php

namespace AppBundle\Util\KitGenerator\StructureCalculator;

/**
 * Class Component
 * @author Daniel Martins <daniel@kolinalabs.com>
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class Item implements ItemInterface
{
    use MakerDefinition;

    /**
     * @var int|string
     */
    private $id;

    /**
     * @var float
     */
    private $width;

    /**
     * @var float
     */
    private $length;

    /**
     * @var float
     */
    private $size;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $subtype;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        $this->quantity = 1;
        $this->maker = Structure::MAKER_SICES_SOLAR;
    }

    /**
     * @inheritDoc
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @inheritDoc
     */
    public function setLength($length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @inheritDoc
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @inheritDoc
     */
    public function setType($type)
    {
        if(!in_array($type, self::getAcceptedTypes())){
            throw new \InvalidArgumentException(sprintf(
                'The type %s is not accepted. Accepted: %s', $type, implode('-', self::getAcceptedTypes())
            ));
        }

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
    public function setSubtype($subtype)
    {
        if(!$this->type){
            $this->exceptionArgument('The type of this item is not defined');
        }

        if(!in_array($subtype, self::getAcceptedSubtypes($this->type))){
            $this->exceptionArgument(sprintf('The [%s] subtype is not valid for the type [%s]', $subtype, $this->type));
        }

        $this->subtype = $subtype;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSubtype()
    {
        return $this->subtype ? $this->subtype : $this->type ;
    }

    /**
     * @inheritDoc
     */
    public function is($assert)
    {
        return $assert == $this->type || $assert == $this->subtype;
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function setQuantity($quantity)
    {
        if($quantity < 0) $quantity *= -1;

        $this->quantity = (int) $quantity;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function increase($quantity)
    {
        $this->quantity += (int) $quantity;
    }

    /**
     * @inheritDoc
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @inheritDoc
     */
    public function isValid()
    {
        return !count($this->getErrors());
    }

    /**
     * @inheritDoc
     */
    public function getErrors()
    {
        $errors = [];
        if(!$this->type){
            $errors[] = 'The field [type] is not defined';
        }

        if(!in_array($this->type, [self::TYPE_JUNCTION, self::TYPE_MODULE]) && !$this->subtype){
            $errors[] = 'The field [subtype] is not defined';
        }

        if(!$this->id){
            $errors[] = 'The field [id] is not defined';
        }

        if(self::TYPE_TERMINAL == $this->type && is_null($this->size)){
            $errors[] = 'The size is undefined';
        }

        if(!$this->is(self::TYPE_CATCH) && !is_int($this->maker)){
            $errors[] = 'The maker is undefined';
        }

        return $errors;
    }

    /**
     * @inheritDoc
     */
    public static function getAcceptedTypes()
    {
        return array_keys(self::getMappingTypes());
    }

    /**
     * @inheritDoc
     */
    public static function getAcceptedSubtypes($type = null)
    {
        $types = self::getMappingTypes();

        if(!$type){
            return $types;
        }

        if(!array_key_exists($type, $types)){
            throw new \InvalidArgumentException('Invalid type');
        }

        return $types[$type];
    }

    /**
     * @return array
     */
    private static function getMappingTypes()
    {
        return [
            self::TYPE_BASE => [
                self::BASE_TAPE,
                self::BASE_HOOK,
                self::BASE_SCREW_DRILLING,
                self::BASE_SCREW_STRUCTURAL,
                self::BASE_SPEED_CLIP,
                self::BASE_TRIANGLE_HORIZONTAL,
                self::BASE_TRIANGLE_VERTICAL
            ],

            self::TYPE_CATCH => [
                self::CATCH_BAND,
                self::CATCH_SPEED_CLIP
            ],

            self::TYPE_FIXER => [
                self::FIXER_SCREW,
                self::FIXER_NUT
            ],

            self::TYPE_JUNCTION => [],

            self::TYPE_PROFILE => [
                self::PROFILE_INDUSTRIAL,
                self::PROFILE_ROMAN,
                self::PROFILE_MIDDLE
            ],

            self::TYPE_TERMINAL => [
                self::TERMINAL_FINAL,
                self::TERMINAL_MIDDLE
            ],

            self::TYPE_MODULE => []
        ];
    }

    /**
     * @param $message
     */
    private function exceptionArgument($message)
    {
        throw new \InvalidArgumentException($message);
    }
}