<?php

namespace AppBundle\Service\ProjectGenerator;

class AreaDebugger
{
    const INCREASE_TEMP_MAX = 10;
    const DECREASE_TEMP_MIN = 5;

    /**
     * @var array
     */
    private $metadata;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var array
     */
    private $result = [];

    /**
     * @inheritDoc
     */
    public function setMetadata(array $metadata)
    {
        $this->metadata = $this->validateMetadata($metadata);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @inheritDoc
     */
    public function isValid()
    {
        return empty($this->errors);
    }

    /**
     * @inheritDoc
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @inheritDoc
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @inheritDoc
     */
    public function getMetadataSchema()
    {
        return [
            'module' => [
                'stc_power_max' => 'int',
                'stc_vmp' => 'float',
                'stc_imp' => 'float',
                'stc_voc' => 'float',
                'temp_noct' => 'int',
                'coef_voc' => 'float'
            ],
            'inverter' => [
                'max_dc_power' => 'float',
                'max_dc_voltage' => 'int',
                'max_dc_current' => 'int',
                'mppt_min' => 'int',
                'mppt_max' => 'int',
                'mppt_number' => 'int'
            ],
            'global' => [ 'min' => 'float', 'max' => 'float' ],
            'atmosphere' => [ 'min' => 'float', 'max' => 'float' ],
            'temperature' => [ 'min' => 'float', 'max' => 'float' ],
            'mppt_factor' => 'int',
            'n_string' => 'int',
            'n_mod_string' => 'int'
        ];
    }

    /**
     * @inheritDoc
     */
    public function getArea()
    {
    }

    /**
     * @return $this
     */
    public function debug()
    {
        //inverter data
        $max_dc_power = $this->metadata['inverter']['max_dc_power'];        //from inverter data
        $max_dc_voltage = $this->metadata['inverter']['max_dc_voltage'];    //from inverter data
        $max_dc_current = $this->metadata['inverter']['max_dc_current'];    //from inverter data
        $mppt_min = $this->metadata['inverter']['mppt_min'];                // from inverter data
        $mppt_max = $this->metadata['inverter']['mppt_max'];                //from inverter data
        $mppt_number = $this->metadata['inverter']['mppt_number'];          //from inverter data

        //modules data
        $stc_pmax = $this->metadata['module']['stc_power_max']; //from modules data
        $stc_vmp = $this->metadata['module']['stc_vmp'];        //from modules data
        $stc_imp = $this->metadata['module']['stc_imp'];        //from modules data
        $stc_voc = $this->metadata['module']['stc_voc'];        //from modules data
        $tnoct = $this->metadata['module']['temp_noct'];        //from modules data
        $coef_voc = $this->metadata['module']['coef_voc'];      //from modules data

        //global radiation NASA
        $gr_max = $this->metadata['global']['max']; //the highest radiation of the year
        $gr_min = $this->metadata['global']['min']; //the lowest radiation of the year

        //toa radiation NASA
        $toa_max = $this->metadata['atmosphere']['max']; //the highest radiation of the year
        $toa_min = $this->metadata['atmosphere']['min']; //the lowest radiation of the year

        //max air temperature data form NASA
        $at_max_max = $this->metadata['temperature']['max'] + self::INCREASE_TEMP_MAX; //the highest temperature of the year

        //min air temperature data form NASA
        $at_min_min = $this->metadata['temperature']['min'] - self::DECREASE_TEMP_MIN; //the lowest temperature of the year

        //area data
        $mppt_factor = $this->metadata['mppt_factor']; //number of MPPTs in the same area (from MPPT possibles list)
        $n_string = $this->metadata['n_string']; //number of strings
        $n_mod_string = $this->metadata['n_mod_string']; //number of modules per string

        //------CALCULATIONS------
        //MPPT limits
        $kt_max = $gr_max / $toa_max;
        $kt_min = $gr_min / $toa_min;
        $tc_min = $at_min_min + ((219 + (832 * ($kt_min))) * (($tnoct - 20) / 800));
        $tc_max = $at_max_max + ((219 + (832 * ($kt_max))) * (($tnoct - 20) / 800));
        $vmp_max_op = $stc_vmp * $n_mod_string * (1 + (($coef_voc / 100) * ($tc_min - 25)));
        $vmp_min_op = $stc_vmp * $n_mod_string * (1 + (($coef_voc / 100) * ($tc_max - 25)));

        // Levels
        $danger  = 0;
        $warning = 0;
        $success = 0;

        if (($vmp_min_op < $mppt_min) || ($vmp_min_op > $mppt_max) || ($vmp_max_op < $mppt_min) || ($vmp_max_op > $mppt_max)) {
            $mpptOperable = false;
            $danger++;
        } else {
            $mpptOperable = true;
            $success++;
        }

        # MPPT
        $this->result['mppt'] = [
            'min' => $mppt_min,
            'max' => $mppt_max,
            'vmp_min_operation' => $vmp_min_op,
            'vmp_max_operation' => $vmp_max_op,
            'air_temp_min' => $at_min_min,
            'air_temp_max' => $at_max_max,
            'operable' => $mpptOperable
        ];

        //max voltage limits
        $voc_op = $stc_voc * $n_mod_string;
        if ($voc_op > $max_dc_voltage) {
            $voltageOperable = false;
            $danger++;
        } else {
            $voltageOperable = true;
            $success++;
        }

        #VOLTAGE
        $this->result['voltage'] = [
            'max_dc_voltage' => $max_dc_voltage,
            'voc_operation' => $voc_op,
            'operable' => $voltageOperable
        ];

        //max current limits
        $max_dc_current_op = $max_dc_current * $mppt_factor;
        $imp_op = $stc_imp * $n_string;
        if ($imp_op > $max_dc_current_op) {
            $currentOperable = false;
            $danger++;
        } else {
            $currentOperable = true;
            $success++;
        }

        # CURRENT
        $this->result['current'] = [
            'max_dc_current' => $max_dc_current_op,
            'imp_operation' => $imp_op,
            'operable' => $currentOperable
        ];

        //max dc power limits
        $max_dc_power_op = ($max_dc_power / $mppt_number) * $mppt_factor;
        $pmax_op = $stc_pmax * $n_string * $n_mod_string / 1000;
        $pmax_tolerance_op_1 = $max_dc_power_op * 1.1;
        $pmax_tolerance_op_2 = $max_dc_power_op * 1.2;
        $danger_limit = $max_dc_power_op * 1.3;

        $powerOperable = true;
        $powerLevel = 'success';
        $powerColor = 'green';

        //if($pmax_op > $max_dc_power_op){
        if($pmax_op > $pmax_tolerance_op_1){

            $isDanger = $pmax_op > $pmax_tolerance_op_2;

            $powerOperable = !$isDanger;
            $powerLevel = $isDanger ? 'danger'  : 'warning';
            $powerColor = $isDanger ? 'red'     : 'yellow';

            $isDanger ? $danger++ : $warning++ ;
        }

        $this->result['power'] = [
            /*'max_dc_operation' => $max_dc_power_op,
            'warning_tolerance' => $pmax_tolerance_op_1,
            'danger_tolerance' => $pmax_tolerance_op_2,*/
            'max_dc_operation' => $pmax_tolerance_op_1,
            'warning_tolerance' => $pmax_tolerance_op_2,
            'danger_tolerance' => $danger_limit,
            'max_operation' => $pmax_op,
            'max_dc_power' => $max_dc_power_op,
            'operable' => $powerOperable,
            'level' => $powerLevel,
            'color' => $powerColor
        ];
        
        $this->result['operable'] = !$danger > 0;
        $this->result['level'] = $danger > 0 ? 'danger' : (($warning > 0) ? 'warning' : 'success');
        $this->result['danger'] = $danger;
        $this->result['warning'] = $warning;
        $this->result['success'] = $success;

        return $this;
    }
    
    private function validateMetadata(array $metadata = null, array $schema = null)
    {
        $schema = !$schema ? $this->getMetadataSchema() : $schema;
        $metadata = !$metadata ? $this->metadata : $metadata;

        foreach($schema as $key => $data){

            if(!array_key_exists($key, $metadata)){

                $this->errors[] = sprintf('Undefined metadata key %s', $key);
            }else {

                if (is_array($metadata[$key])) {
                    $metadata[$key] = $this->validateMetadata($metadata[$key], $schema[$key]);
                }else{

                    switch($data){
                        case 'float': $value = (float) $metadata[$key]; break;
                        case 'int': $value = (int) $metadata[$key]; break;
                        default: $value = (float) $metadata[$key]; break;
                    }

                    $metadata[$key] = $value;
                }
            }
        }

        return $metadata;
    }
}