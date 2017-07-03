<?php

namespace AppBundle\Entity\Project;

use Doctrine\ORM\Mapping as ORM;

/**
 * MpptOperation
 *
 * @ORM\Table(name="app_mppt_operation")
 * @ORM\Entity
 */
class MpptOperation implements MpptOperationInterface
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="mppt", type="smallint")
     */
    private $mppt;

    /**
     * @var json
     *
     * @ORM\Column(name="operation", type="json")
     */
    private $operation;

    /**
     * @return string
     */
    function __toString()
    {
        return $this->getName();
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
    public function getName($index = null)
    {
        $name = $this->generateName();

        if(is_int($index)){
            $stack = explode(self::NAME_SPACER, $name);
            if(array_key_exists($index, $stack)){
                return $stack[$index];
            }
        }

        return $name;
    }

    /**
     * @inheritDoc
     */
    public function getMppt()
    {
        return $this->mppt;
    }

    /**
     * @inheritDoc
     */
    public function setOperation(array $operation)
    {
        $this->operation = $operation;

        $this->handleOperation();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * @inheritDoc
     */
    public function count()
    {
        return count($this->operation);
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'mppt' => $this->mppt,
            'operations' => $this->operation
        ];
    }

    /**
     * @param array $operation
     * @return string
     */
    public static function parseId(array $operation)
    {
        return implode(self::ID_SPACER, $operation);
    }

    /**
     * Check Operation Types
     * Generate Mppt Value
     * Generate Mppt Id
     */
    private function handleOperation()
    {
        foreach($this->operation as $operation){
            if(!is_int($operation)){
                throw new \InvalidArgumentException(self::ERROR_UNSUPPORTED_OPERATION);
            }
        }

        rsort($this->operation);
        $this->mppt = array_sum($this->operation);
        $this->id = self::parseId($this->operation);
    }

    /**
     * @return string
     */
    private function generateName()
    {
        $block = 'MPPT(%s)';
        $stack = [];
        $index = 1;
        foreach($this->operation as $operation){
            $content = $index;
            if($operation > 1){
                for($i = 2; $i <= $operation; $i++){
                    $content .= self::LABEL_SPACER . ($index+1);
                    $index++;
                }
            }
            $stack[] = sprintf($block, $content);
            $index++;
        }

        return implode(self::NAME_SPACER, $stack);
    }
}

