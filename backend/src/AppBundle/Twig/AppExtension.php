<?php

namespace AppBundle\Twig;

use AppBundle\Configuration\App;
use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Component\ComponentInterface;
use Sonata\IntlBundle\Twig\Extension\NumberExtension;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AppExtension
 *
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 */
class AppExtension extends \Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('view', array($this, 'getView'), ["is_safe" => ["html"]]),
            new \Twig_SimpleFunction('route', array($this, 'generateUrl'), ["is_safe" => ["html"]]),
            new \Twig_SimpleFunction('app_title', array($this, 'renderTitle'/*, ['is_safe' => ['html']]*/)),
            new \Twig_SimpleFunction('icon', array($this, 'getIcon'), ["is_safe" => ["html"]]),
            new \Twig_SimpleFunction('sub_domain', array($this, 'getSubdomain'), ["is_safe" => ["html"]]),
            new \Twig_SimpleFunction('trail_message', array($this, 'trailMessage'), ["is_safe" => ["html"]])
        ];
    }

    /**
     * @param BusinessInterface $account
     * @return string
     */
    public function trailMessage(BusinessInterface $account)
    {
        $message = '';

        if($account->isExpired()){
            return 'finalizado';
        }

        if($account->isTrailAccount()) {

            $expireAt = date_diff(new \DateTime(), $account->getExpireAt());

            $days = $expireAt->d;
            $hours = $expireAt->h;
            $minutes = $expireAt->i;

            if($days > 0 || $hours > 0) {
                if ($days > 0) {
                    $message .= $days . ($days > 1 ? ' dias' : ' dia');
                }
                if ($days > 0 && $hours > 0) {
                    $message .= ' e ';
                }
                if ($hours) {
                    $message .= $hours . ($hours > 1 ? ' horas' : ' hora');
                }

                $message .= ($days > 1 && $hours > 1 ? ' restantes' : $hours > 1 ? ' restantes' : ' restante');

            }else{
                if($minutes > 0) {
                    $message .= $minutes . ($minutes > 1 ? ' minutos restantes' : ' minuto restante');
                }else{
                    $seconds = $expireAt->s;
                    $message  .= ' bloqueando conta em ' . $seconds . ($seconds > 0 ? ' segundos' : ' segundo');
                }
            }
        }

        return $message;
    }

    /**
     * @return string
     */
    public function getSubdomain()
    {
        /** @var \Symfony\Component\HttpFoundation\RequestStack $requestStack */
        $requestStack = $this->container->get('request_stack');
        $host = $requestStack->getCurrentRequest()->getHost();

        preg_match('/(app\\.inovadorsolar|sandbox\\.inovadorsolar)/', $host, $matches);

        if(!empty($matches)){
            $sep = explode('.', $matches[0]);
            $host = $sep[0];
        }else{
            $host = 'none';
        }

        return $host;
    }

    /**
     * @inheritDoc
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('json_decode', array($this, 'jsonDecode'), ['is_safe' => ['html']]),
            new \Twig_SimpleFilter('componentFilePath', array($this, 'getComponentFilePath'), ['is_safe' => ['html']])
        ];
    }

    /**
     * @param $path
     * @return array|string
     */
    public function getIcon($path)
    {
        return App::icons($path);
    }

    /**
     * @param $view
     * @return mixed
     */
    public function getView($view)
    {
        Resolver::resolveView($view);

        return $view;
    }

    public function getComponentFilePath($file)
    {
        if(!$file) $file = 'default_thumb.jpg';

        $type = strpos($file, 'thumb') ? 'image' : 'datasheet';
        $ambience = (getenv('CES_AMBIENCE') == 'production') ? 'production' : 'homolog';
        $path = "https://s3-sa-east-1.amazonaws.com/pss-{$ambience}-public/component/{$type}";

        return "{$path}/{$file}";
    }

    /**
     * @param $route
     * @param array $params
     * @return mixed
     */
    public function generateUrl($route, array $params = [])
    {
        Resolver::resolveRoute($route);

        return $this->getRouter()->generate($route, $params);
    }

    /**
     * @param $json
     * @return mixed
     */
    public function jsonDecode($json)
    {
        return json_decode($json);
    }

    /**
     * @return string
     */
    public function renderTitle()
    {
        /** @var \APY\BreadcrumbTrailBundle\BreadcrumbTrail\Trail $breadcrumbs */
        $breadcrumbs = $this->container->get('apy_breadcrumb_trail');
        $count = $breadcrumbs->count();
        $appTitle = App::APP_TITLE;

        foreach ($breadcrumbs as $key => $breadcrumb) {
            if (($key + 1) == $count) {
                $appTitle = $this->translate($breadcrumb->title);
                break;
            }
        }

        return App::APP_NAME . ' &mdash; ' . $appTitle;
    }

    public function translate($message)
    {
        return $this->container->get('translator')->trans($message);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_extension';
    }

    /**
     * @return object
     */
    private function getRouter()
    {
        return $this->container->get('router');
    }
}
