<?php

namespace AppBundle\Twig;

use Sonata\IntlBundle\Twig\Extension\NumberExtension as BaseNumberExtension;

/**
 * Class NumberExtension
 * @package AppBundle\Twig
 */
class NumberExtension extends \Twig_Extension
{
    /**
     * @var BaseNumberExtension
     */
    private $numberExtension;

    /**
     * NumberExtension constructor.
     * @param BaseNumberExtension $numberExtension
     */
    function __construct(BaseNumberExtension $numberExtension)
    {
        $this->numberExtension = $numberExtension;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('currency', [$this, 'formatCurrency'], ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('decimal', [$this, 'formatDecimal', ['is_safe' => ['html']]])
        ];
    }

    /**
     * @param $number
     * @param string $default
     * @return string
     */
    public function formatCurrency($number, $default = false)
    {
        if(is_null($number) && $default)
            return $default;

        $currency = 'BRL';

        $format = $this->numberExtension->formatCurrency($number, $currency);

        if(0 == strpos($format, '(')){
            $format = str_replace(['(R$',')', 'R$'], ['-R$','','R$ '], $format);
        }

        return $format;
    }

    /**
     * @param $number
     * @param string $separator
     * @return string
     */
    public function formatDecimal($number, $separator = ',')
    {
        return $this->numberExtension->formatDecimal($number);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'app.number_extension';
    }
}
