<?php

namespace AppBundle\Util\KitGenerator;


class PowerEstimator
{
    /**
     * @var float
     */
    private $consumption;

    /**
     * @var float
     */

    /**
     * @var float
     */
    private $latitude;

    /**
     * @var float
     */
    private $longitude;

    /**
     * @var array
     */
    private $globalRadiation;

    /**
     * @var array
     */
    private $airTemperature;

    /**
     * @var float
     */
    private $temperatureOperation;

    /**
     * @var float
     */
    private $efficiency;

    /**
     * @var float
     */
    private $temperatureCoefficient;

    /**
     * @return float
     */
    public function getConsumption()
    {
        return $this->consumption;
    }

    /**
     * @param float $consumption
     * @return PowerEstimator
     */
    public function setConsumption($consumption)
    {
        $this->consumption = $consumption;
        return $this;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     * @return PowerEstimator
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
        return $this;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     * @return PowerEstimator
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
        return $this;
    }

    /**
     * @return array
     */
    public function getGlobalRadiation()
    {
        return $this->globalRadiation;
    }

    /**
     * @param array $globalRadiation
     * @return PowerEstimator
     */
    public function setGlobalRadiation($globalRadiation)
    {
        $this->globalRadiation = $globalRadiation;
        return $this;
    }

    /**
     * @return array
     */
    public function getAirTemperature()
    {
        return $this->airTemperature;
    }

    /**
     * @param array $airTemperature
     * @return PowerEstimator
     */
    public function setAirTemperature($airTemperature)
    {
        $this->airTemperature = $airTemperature;
        return $this;
    }

    /**
     * @return float
     */
    public function getTemperatureOperation()
    {
        return $this->temperatureOperation;
    }

    /**
     * @param float $temperatureOperation
     * @return PowerEstimator
     */
    public function setTemperatureOperation($temperatureOperation)
    {
        $this->temperatureOperation = $temperatureOperation;
        return $this;
    }

    /**
     * @return float
     */
    public function getEfficiency()
    {
        return $this->efficiency;
    }

    /**
     * @param float $efficiency
     * @return PowerEstimator
     */
    public function setEfficiency($efficiency)
    {
        $this->efficiency = $efficiency;
        return $this;
    }

    /**
     * @return float
     */
    public function getTemperatureCoefficient()
    {
        return $this->temperatureCoefficient;
    }

    /**
     * @param float $temperatureCoefficient
     * @return PowerEstimator
     */
    public function setTemperatureCoefficient($temperatureCoefficient)
    {
        $this->temperatureCoefficient = $temperatureCoefficient;
        return $this;
    }


    public function estimate($kwh, $lat, $lon, $mod_id)
    {

        // function prev_pot()
        //  {

//this script calculates an inaccurate generator power, based on a energy consumption

//------USER INPUTS------

        $cons = $kwh; //energy consumption (kWh)
        $lat_degree = $lat; //this data should be provided by Google Maps
        $lon_degree = $lon; //this data should be provided by Google Maps

//------SYSTEM INPUTS------

        $lat_nasa = floor($lat_degree); //each line of all NASA database tables, represents the lower left corner of a 1x1 region degrees (lat/lon)
        $lon_nasa = floor($lon_degree);

//global radiation (kWh/m2/day)
        $gr_rad_bd = R::getAll("SELECT * FROM nasa_global_radiation WHERE lat=$lat_nasa AND lon=$lon_nasa");
        $gr_rad = $gr_rad_bd[0];
        $gr_rad = array_slice($gr_rad, 2);
        $gr_rad = array_values($gr_rad);

//air temperature (ºC)
        $at_bd = R::getAll("SELECT * FROM nasa_air_temp WHERE lat=$lat_nasa AND lon=$lon_nasa");
        $at = $at_bd[0];
        $at = array_slice($at, 2);
        $at = array_values($at);

        $at_average = array_sum($at) / 12;


        $n = array(12); //day of the year
        $n[0] = 17;
        $n[1] = 47;
        $n[2] = 75;
        $n[3] = 105;
        $n[4] = 135;
        $n[5] = 162;
        $n[6] = 198;
        $n[7] = 228;
        $n[8] = 258;
        $n[9] = 288;
        $n[10] = 318;
        $n[11] = 344;

//$gr = 5.33; //global radiation average from NASA database
//$toa = 9.94; //top of atmosphere radiation average from NASA database
//$at = 25.78; //air temperature average from NASA database

//------CONSTANTS------
        $busca_mod_bd = R::getAll("SELECT * FROM app_component_module WHERE id = $mod_id ORDER BY max_power ASC");

        $loss = 13;
        $tnoct = $busca_mod_bd[0]["temperature_operation"];
        $stc_mod_efi = $busca_mod_bd[0]["efficiency"];
        $coef = $busca_mod_bd[0]["temp_coefficient_max_power"];
        $inv_efi = 0.95;
        $beta = deg2rad(abs($lat_degree));
        if ($lat_degree >= 0) {
            $gama = deg2rad(0);
        } else {
            $gama = deg2rad(180);
        }
        $ro = 0.2;
        $toa_acu = 0;
        $ht_acu = 0;

//------CALCULATIONS------
//converting degree to radians
        $lat = deg2rad($lat_degree);

        $solar_beam_comp = array(25); //it will contain the SOLAR BEAM radiation contribution PER DAY
        $dif_comp = array(25); //it will contain the DIFUSE radiation contribution PER DAY
        $albedo_comp = array(25); //it will contain the ALBEDO radiation contribution PER DAY
        $hth = array(25); //(SOLAR BEAM + DIFUSE + ALBEDO) PER DAY

        for ($i = 0; $i <= 11; $i++) {
            if ($i == 0 or $i == 2 or $i == 4 or $i == 6 or $i == 7 or $i == 9 or $i == 11) {
                $n_days = 31;
            } elseif ($i == 3 or $i == 5 or $i == 8 or $i == 10) {
                $n_days = 30;
            } else
                $n_days = 28;
            $delta = deg2rad(23.45) * sin(2 * pi() * ((284 + $n[$i]) / 365));
            $ws = acos(-((tan($delta)) * (tan($lat))));
            $toa_rad = (24 * 3600 * 1367 / pi()) * (1 + (0.033 * cos(360 * $n[$i] / 365))) * ((cos($lat) * cos($delta) * sin($ws)) + ($ws * sin($lat) * sin($delta)));
            $toa_rad *= 0.00000027778;
            $toa_acu += $toa_rad;
            $kt = $gr_rad[$i] / $toa_rad;
            if ($ws <= deg2rad(81.4)) {
                $dif_rad = 1.391 - (3.560 * $kt) + (4.189 * pow($kt, 2)) - (2.137 * pow($kt, 3));
                $dif_rad *= $gr_rad[$i];
            } else {
                $dif_rad = 1.311 - (3.022 * $kt) + (3.427 * pow($kt, 2)) - (1.821 * pow($kt, 3));
                $dif_rad *= $gr_rad[$i];
            }
            $a = 0.409 + (0.5016 * (sin($ws - (pi() / 3))));
            $b = 0.6609 - (0.4767 * (sin($ws - (pi() / 3))));

            //hourly calculation
            for ($h = 0; $h <= 23; $h++) {
                //$hour = $h;
                if ((-180 + ($h * 15)) == 0) {
                    $w = deg2rad(0.0001);
                } else {
                    $w = deg2rad(-180 + ($h * 15));
                }
                $rt = (pi() / 24) * ($a + ($b * cos($w))) * ((cos($w) - cos($ws)) / (sin($ws) - ($ws * cos($ws))));
                $rd = (pi() / 24) * ((cos($w) - cos($ws)) / (sin($ws) - ($ws * cos($ws))));
                $hh = $rt * $gr_rad[$i];
                $hdh = $rd * $dif_rad;
                $hb = $hh - $hdh;
                $cos_ozh = (cos($lat) * cos($delta) * cos($w)) + (sin($lat) * sin($delta));
                $ozh = acos($cos_ozh);
                $ysh = sign($w) * abs(acos((($cos_ozh * sin($lat)) - sin($delta)) / (sin($ozh) * cos($lat))));
                $cos_oh = ($cos_ozh * (cos($beta))) + (sin($ozh) * sin($beta) * cos($ysh - $gama));
                $rb = $cos_oh / $cos_ozh;

                if (($w < -$ws) or ($w > $ws)) {
                    $solar_beam_comp[$h] = 0;
                    $dif_comp[$h] = 0;
                    $albedo_comp[$h] = 0;
                } else {

                    if (($hb * $rb) < 0) {
                        $solar_beam_comp[$h] = 0;
                    } else {
                        $solar_beam_comp[$h] = $hb * $rb;
                    }
                    if (($hdh * ((1 + cos($beta)) / 2)) < 0) {
                        $dif_comp[$h] = 0;
                    } else {
                        $dif_comp[$h] = $hdh * ((1 + cos($beta)) / 2);
                    }
                    if (($hh * $ro * ((1 - cos($beta)) / 2)) < 0) {
                        $albedo_comp[$h] = 0;
                    } else {
                        $albedo_comp[$h] = $hh * $ro * ((1 - cos($beta)) / 2);
                    }
                }
                $hth[$h] = ($solar_beam_comp[$h] + $dif_comp[$h] + $albedo_comp[$h]);
            }
            // back to monthly calculation
            $ht = array_sum($hth);
            $ht_acu += $ht;
        }

        $gr = $ht_acu / 12;
        $factor = (($cons / ((100 - $loss) / 100)) * 12) / ($gr * 365);
        $kt = $gr / ($toa_acu / 12);
        $tc = $at_average + ((219 + (832 * ($kt))) * (($tnoct - 20) / 800));
        $efi_mod_final = $stc_mod_efi * (1 - ((-($coef) / 100) * ($tc - 25)));
        $area = $factor / ($efi_mod_final * $inv_efi);
        $pmax = ($area * $stc_mod_efi * 1000) / 1000;

//------OUTPUT------

        return $pmax;
    }
    //  }


}