<?php

namespace AppBundle\Service\SolarEnergy\Core;

class Processor implements ProcessorInterface
{
    /**
     * @var ProjectInterface
     */
    private $project;

    /**
     * @inheritDoc
     */
    public function __construct(ProjectInterface $project)
    {
        $this->project = $project;
    }

    /**
     * @inheritDoc
     */
    public function compute()
    {
        if (!$this->isComputable())
            $this->createException('This processor is not computable');

        $latRadian = $this->project->getLatRadian();
        $areas = $this->project->getAreas();

        $radiationGlobal = $this->project->getRadiationGlobal()->toArray();
        //$radiationDiffuse = $this->project->getRadiationDiffuse()->toArray();
        //$radiationAtmosphere = $this->project->getRadiationAtmosphere()->toArray();
        $airTemperature = $this->project->getAirTemperature()->toArray();
        //$solarDeclination = $this->project->getSolarDeclination()->toArray();
        //$solarNoon = $this->project->getSolarNoon()->toArray();
        $soloReflectance = $this->project->getSoloReflectance();
        //$daylightHours = $this->project->getDaylightHours()->toArray();

        $n = [17, 47, 75, 105, 135, 162, 198, 228, 258, 288, 318, 344];

        foreach ($areas as $area) {

            if ($area instanceof AreaInterface) {

                $inverterEfficiency = $area->getInverterEfficiency();
                //$moduleTemperature = $area->getModuleTemperature();
                $moduleEfficiency = $area->getModuleEfficiency();
                $moduleCoefficientTemperature = $area->getModuleCoefficientTemperature();
                $totalArea = $area->totalArea();
                $leftLoss = $area->getInverterSideLoss();
                $rightLoss = $area->getModuleSideLoss();
                $inclination = $area->getInclinationRadian();
                $orientation = $area->getOrientationRadian();

                $aggregationDay = [];
                $aggregationMonth = [];

                $metadataMonths = [];
                for ($month = 1; $month <= 12; $month++) {

                    $days = cal_days_in_month(CAL_GREGORIAN, $month, self::YEAR);

                    $solarDeclination = deg2rad(23.45) * sin(2 * pi() * ((284 + $n[$month - 1]) / 365));

                    $ws = acos(-(tan($solarDeclination)) * tan($latRadian));

                    $radiationAtmosphere = (24 * 3600 * 1367 / pi()) * (1 + (0.033 * cos(360 * $n[$month - 1] / 365))) * ((cos($latRadian) * cos($solarDeclination) * sin($ws)) + ($ws * sin($latRadian) * sin($solarDeclination)));
                    $radiationAtmosphere *= 0.00000027778;

                    $a = 0.409 + (0.5016 * (sin($ws - (pi() / 3))));
                    $b = 0.6609 - (0.4767 * (sin($ws - (pi() / 3))));

                    /*$ns = $solarNoon[$month] - ($solarNoon[$month]/2);
                    $ps = $solarNoon[$month] + ($solarNoon[$month]/2);*/

                    //$ns = $solarNoon[$month] - ($daylightHours[$month]/2);
                    //$ps = $solarNoon[$month] + ($daylightHours[$month]/2);

                    $kt = $radiationGlobal[$month] / $radiationAtmosphere;

                    if ($ws <= deg2rad(81.4)) {
                        $radiationDiffuse = 1.391 - (3.560 * $kt) + (4.189 * pow($kt, 2)) - (2.137 * pow($kt, 3));
                        $radiationDiffuse *= $radiationGlobal[$month];
                    } else {
                        $radiationDiffuse = 1.311 - (3.022 * $kt) + (3.427 * pow($kt, 2)) - (1.821 * pow($kt, 3));
                        $radiationDiffuse *= $radiationGlobal[$month];
                    }

                    /*$tc = $airTemperature[$month] + ((219 +(832*($kt)))*(($moduleTemperature-20)/800));
                    $finalModuleEfficiency = $moduleEfficiency * (1-((-$moduleCoefficientTemperature/100)*($tc-25)));
                    //$factor = $totalArea * $finalModuleEfficiency * ((100-$leftLoss)/100) * $inverterEfficiency * ((100-$rightLoss)/100);
                    $factor = $totalArea * $finalModuleEfficiency * ((100-$leftLoss)/100) * ($inverterEfficiency * 0.99) * ((100-$rightLoss)/100);*/

                    // Hours
                    $metadataHours = [];
                    for ($hour = 0; $hour <= 24; $hour++) {
                        $w = 0 == (-180 + ($hour * 15)) ? $w = deg2rad(0.0001) : deg2rad(-180 + ($hour * 15));
                        $rt = (pi() / 24) * ($a + ($b * cos($w))) * ((cos($w) - cos($ws)) / (sin($ws) - ($ws * cos($ws))));
                        $rd = (pi() / 24) * ((cos($w) - cos($ws)) / (sin($ws) - ($ws * cos($ws))));
                        $hh = $rt * $radiationGlobal[$month];
                        $hdh = $rd * $radiationDiffuse;
                        $hb = $hh - $hdh;
                        $cosOzh = (cos($latRadian) * cos($solarDeclination) * cos($w)) + (sin($latRadian) * sin($solarDeclination));
                        $ozh = acos($cosOzh);
                        $ysh = self::sign($w) * abs(acos((($cosOzh * sin($latRadian)) - sin($solarDeclination)) / (sin($ozh) * cos($latRadian))));
                        $cosOh = ($cosOzh * (cos($inclination))) + (sin($ozh) * sin($inclination) * cos($ysh - $orientation));
                        $rb = $cosOh / $cosOzh;

                        //dump($radiationDiffuse);
                        //dump('CosOH: ' . $cosOh);

                        /*
                         * V1 - BEFORE BUG acos() 25/10/2016
                         * $solarBeamDay = (($hour < $ns) or ($hour >= floor($ps)) or (($hb * $rb) < 0)) ? 0 : $hb * $rb * $factor ;
                        $diffuseDay = (($hour < $ns) or ($hour >= floor($ps)) or (($hdh*((1+cos($inclination))/2)) < 0)) ? 0 : $hdh*((1+cos($inclination))/2) * $factor ;
                        $albedoDay = (($hour < $ns) or ($hour >= floor($ps)) or (($hh*$soloReflectance*((1-cos($inclination))/2))<0)) ? 0 : $hh*$soloReflectance*((1-cos($inclination))/2) * $factor ;*/
                        //$aggregationDay[$hour] = $solarBeamDay + $diffuseDay + $albedoDay;


                        /**
                         * FIX 1 - SAME FIX 2 [REFACTORING]
                         *
                         * if (($w < -$ws) or ($w > $ws)){
                         * $solarBeamDay[$hour] = 0;
                         * $diffuseDay[$hour] = 0;
                         * $albedoDay[$hour] = 0;
                         * }else{
                         * if (($hb  * $rb) < 0){
                         * $solarBeamDay[$hour] = 0;
                         * }else{
                         * $solarBeamDay[$hour] = $hb  * $rb * $factor;
                         * }
                         * if (($hdh*((1+cos($inclination))/2)) < 0){
                         * $diffuseDay[$hour] = 0;
                         * }else{
                         * $diffuseDay[$hour] = $hdh*((1+cos($inclination))/2) * $factor;
                         * }
                         * if (($hh*$soloReflectance*((1-cos($inclination))/2)) < 0){
                         * $albedoDay[$hour] = 0;
                         * }else{
                         * $albedoDay[$hour] = $hh*$soloReflectance*((1-cos($inclination))/2) * $factor;
                         * }
                         * }
                         * $aggregationDay[$hour] = $solarBeamDay[$hour] + $diffuseDay[$hour] + $albedoDay[$hour];
                         */

                        // NEW SCRIPT
                        /*if (($w < -$ws) or ($w > $ws)){
                            $solar_beam_comp[$h] = 0;
                            $dif_comp[$h] = 0;
                            $albedo_comp[$h] = 0;
                        }else{
                            if (($hb  * $rb) < 0){
                                $solar_beam_comp[$h] = 0;
                            }else{
                                $solar_beam_comp[$h] = $hb  * $rb * $factor;
                            }
                            if (($hdh*((1+cos($beta))/2)) < 0){
                                $dif_comp[$h] = 0;
                            }else{
                                $dif_comp[$h] = $hdh*((1+cos($beta))/2) * $factor;
                            }
                            if (($hh*$ro*((1-cos($beta))/2)) < 0){
                                $albedo_comp[$h] = 0;
                            }else{
                                $albedo_comp[$h] = $hh*$ro*((1-cos($beta))/2) * $factor;
                            }
                        }*/

                        /**
                         * FIX 2 - CORRECT WAY CALC
                         */
                        $commonZero = ($w < -$ws) || ($w > $ws);
                        $solarBeamDay = $commonZero || (($hb * $rb) < 0) ? 0 : ($hb * $rb);
                        // Error here!
                        $diffuseDay = $commonZero || (($hdh * ((1 + cos($inclination)) / 2)) < 0) ? 0 : ($hdh * ((1 + cos($inclination)) / 2));
                        $albedoDay = $commonZero || (($hh * $soloReflectance * ((1 - cos($inclination)) / 2)) < 0) ? 0 : ($hh * $soloReflectance * ((1 - cos($inclination)) / 2));

                        $aggregationDay[$hour] = $solarBeamDay + $diffuseDay + $albedoDay;

                        //dump('Df: ' . $diffuseDay);

                        $tc = $airTemperature[$month] + ((45 - 20) * ($aggregationDay[$hour] * 1000 / 800) * (1 - (0.083 / 0.9)));

                        $finalModuleEfficiency = $moduleEfficiency * (1 - ((-$moduleCoefficientTemperature / 100) * ($tc - 25)));
                        $factor = $totalArea * $finalModuleEfficiency * ((100 - $leftLoss) / 100) * ($inverterEfficiency * 0.99) * ((100 - $rightLoss) / 100);

                        $aggregationDay[$hour] *= $factor;

                        // increase metadata hours
                        $metadataHours[$hour] = [
                            'solar_beam' => $solarBeamDay,
                            'diffuse' => $albedoDay,
                            'albedo' => $albedoDay,
                            'aggregation' => $aggregationDay[$hour]
                        ];
                    }

                    $ht = array_sum($aggregationDay);
                    $aggregationMonth[$month] = round($ht * $days);

                    // increase metadata months
                    $metadataMonths[$month] = [
                        'days' => $days,
                        'total_day' => $ht,
                        'total_month' => $aggregationMonth[$month],
                        'hours' => $metadataHours
                    ];
                }

                $areaKwhYear = array_sum($aggregationMonth);
                $areaKwhMonth = round($areaKwhYear / 12);
                $areaKwhKwpYear = round($areaKwhYear / $area->totalPower());
                $areaKwhKwpMonth = round($areaKwhKwpYear / 12);
                $areaId = $area->getId();

                // increase metadata area
                $area->setMetadata([
                    'total_area' => $totalArea,
                    'kwh_year' => $areaKwhYear,
                    'kwh_month' => $areaKwhMonth,
                    'kwh_kwp_year' => $areaKwhKwpYear,
                    'kwh_kwp_month' => $areaKwhKwpMonth,
                    'months' => $metadataMonths
                ]);

                /*
                echo "Area = $areaId\n";
                echo "kWh/ano = $areaKwhYear\n";
                echo "kWh/mes = $areaKwhMonth\n";
                echo "kWh/kWp/ano = $areaKwhKwpYear\n";
                echo "kWh/kWp/mes = $areaKwhKwpMonth\n";
                */
            }
        }

        $this->project->isComputed(true);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isComputable()
    {
        if ($this->project)
            return $this->project->isComputable();

        return false;
    }

    /**
     * @param $number
     * @return int
     */
    private function sign($number)
    {
        return $number < 0 ? -1 : 1;
    }

    /**
     * @param $message
     * @throws \Exception
     */
    private function createException($message)
    {
        throw new \Exception($message);
    }
}