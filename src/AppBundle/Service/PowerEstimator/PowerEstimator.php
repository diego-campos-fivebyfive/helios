<?php

namespace AppBundle\Service\PowerEstimator;

/**
 * Class PowerEstimator
 */
class PowerEstimator implements PowerEstimatorInterface
{
    /**
     * @var DataProviderInterface
     */
    private $provider;

    /**
     * PowerEstimator constructor.
     * @param DataProviderInterface $provider
     */
    function __construct(DataProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @inheritdoc
     */
    public function estimate(
        $kwh,
        $latitude,
        $longitude,
        $efficiency = self::EFFICIENCY,
        $temperature = self::TEMPERATURE_OPERATION,
        $coefficient = self::TEMPERATURE_COEFFICIENT
    )
    {
        $cons       = $kwh;
        $lat_degree = $latitude;
        $gr_rad     = array_values($this->provider->getGlobalRadiation($latitude, $longitude));
        $at         = array_values($this->provider->getAirTemperature($latitude, $longitude));
        $at_average = array_sum($at) / 12;

        $n = [17, 47, 75, 105, 135, 162, 198, 228, 258, 288, 318, 344];

        $loss = 12;
        $tnoct = $temperature;
        $stc_mod_efi = $efficiency;
        $coef = $coefficient;
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

        $lat = deg2rad($lat_degree);

        $solar_beam_comp = array(25);
        $dif_comp = array(25);
        $albedo_comp = array(25);
        $hth = array(25);

        for ($i = 0; $i <= 11; $i++) {

            //$n_days = cal_days_in_month(CAL_GREGORIAN, ($i+1), date('Y'));

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
                //$ysh = sign($w) * abs(acos((($cos_ozh * sin($lat)) - sin($delta)) / (sin($ozh) * cos($lat))));
                $ysh = self::sign($w) * abs(acos((($cos_ozh * sin($lat)) - sin($delta)) / (sin($ozh) * cos($lat))));
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
        $power = ($area * $stc_mod_efi * 1000) / 1000;

        return $power;
    }

    /**
     * @param $number
     * @return int
     */
    private static function sign($number)
    {
        if ($number < 0) {
            return -1;
        } else {
            return 1;
        }
    }
}