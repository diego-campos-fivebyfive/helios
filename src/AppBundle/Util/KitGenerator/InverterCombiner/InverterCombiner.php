<?php

namespace AppBundle\Util\KitGenerator\InverterCombiner;

use AppBundle\Util\KitGenerator\Support;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class InverterCombiner
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class InverterCombiner implements InverterCombinerInterface
{
    /**
     * @var ModuleInterface
     */
    private $module;

    /**
     * @var array
     */
    private $inverters;

    /**
     * @var int
     */
    private $unions;

    /**
     * InverterCombiner constructor.
     * @param array $inverters
     */
    function __construct(array $inverters = [], ModuleInterface $module = null)
    {
        $this->module = $module;

        foreach ($inverters as $inverter){
            $this->addInverter($inverter);
        }

        $this->unions = 2;
    }

    /**
     * @inheritDoc
     */
    public function setModule(ModuleInterface $module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @inheritDoc
     */
    public function addInverter(InverterInterface $inverter)
    {
        $this->inverters[] = $inverter;

        return $this;
    }

    public function combine()
    {
        $this->validate();

        $module  = $this->module;
        $unions  = $this->unions;
        $fdi_max = 1;
        $fdi_min = 0.75;
        $lim_sup = $module->getMaxPower() * $fdi_max;
        $lim_inf = $module->getMaxPower() * $fdi_min;
        $count   = count($this->inverters);
        $max     = $count - 1;
        $cont    = [];

        $inv_index = [];

        for ($i = $unions; $i <= 50; $i++) {

            $cont = array_fill(0, $i, 0);

            //var_dump($cont);

            for ($j = 0; $j <  Support::combine($count, $i); $j++){

                $nominalPower = 0;
                for ($y = 0; $y < $i; $y++) {
                    /**
                     * TODO
                     * Observado erro de undefined offset ao considerar
                     * inversores com potências nominais abaixo de 5
                     */
                    $nominalPower += $this->inverters[$cont[$y]]->getNominalPower();
                    //var_dump($cont[$y]);
                }

                //var_dump(Support::combine($count, $i));
                //var_dump($nominalPower);

                if ($nominalPower <= $lim_sup and $nominalPower >= $lim_inf) {
                    break 2;
                }

                /*$this->inverters[$i-1]->setQuantity(
                    $this->inverters[$i -1]->getQuantity() + 1
                );*/

                //var_dump($max);
                $cont[$i - 1] += 1;
                for ($k = 1; $k < $i; $k++) {
                    if ($cont[$i - $k] > $max) {
                        $cont[$i - ($k + 1)] += 1;
                        for ($z = $k; $z >= 1; $z--) {
                            $cont[$i - $z] = $cont[$i - ($z + 1)];
                        }
                    }
                }
            }
        }

        //var_dump($cont); die;

        if (count($cont) == 50){
            return false;
        }

        //var_dump($cont); die;
        /*$inv_index[0]["inv"] = $cont[0];
        $inv_index[0]["qte"] = 1;
        $contador = 1;
        for ($i = 1; $i < count($cont); $i++) {
            if ($cont[$i] == $cont[$i - 1]) {
                $inv_index[$contador - 1]["qte"] += 1;
            } else {
                $inv_index[$contador]["inv"] = $cont[$i];
                $inv_index[$contador]["qte"] = 1;
                $contador += 1;
            }
        }*/
        
        //$this->inverters[0]->setQuantity(1);
        $index = 1;
        $inv_index[0] = ['inv' => $cont[0], 'qtde' => 1];
        //$contador = 1;
        //var_dump($this->inverters); die;
        for ($x = 1; $x < count($cont); $x++) {
            //var_dump($cont[$x-1]); die;
            if ($cont[$x] == $cont[$x - 1]) {
            //if ($cont[$x] == $this->inverters[$x-1]->getQuantity()) {
                $inv_index[$index - 1]["qte"] += 1;
                /*$this->inverters[$index - 1]->setQuantity(
                    $this->inverters[$index - 1]->getQuantity() + 1
                );*/
            } else {
                $this->inverters[$index]->setQuantity(1);
                $inv_index[$index]["inv"] = $cont[$x];
                $inv_index[$index]["qte"] = 1;
                $index++;
            }
        }

        var_dump($inv_index); die;
        //var_dump($this->inverters); die;
    }

    /**
     * Pre validate process combination
     */
    private function validate()
    {
        if(!$this->module)
            $this->exception('The module is undefined');

        if(!count($this->inverters))
            $this->exception('The combination does not have inverters');
    }

    /**
     * @param $message
     */
    private function exception($message)
    {
        throw new \InvalidArgumentException($message);
    }
}