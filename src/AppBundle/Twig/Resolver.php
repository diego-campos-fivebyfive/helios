<?php

namespace AppBundle\Twig;

use AppBundle\Configuration\App;


/**
 * Class Resolver
 *
 * @author Claudinei Machado <cjchamado@gmail.com>
 */
class Resolver
{
    /**
     * @param $route
     * @return mixed
     */
    public static function resolveRoute(&$route)
    {
        $routes = App::getRouteMapping();

        if(array_key_exists($route, $routes))
            $route = $routes[$route];
    }

    /**
     * @param $view
     */
    public static function resolveView(&$view)
    {
        if(!strripos($view, ':')){
            if(0 < $colons = substr_count($view, '.')) {
                $view = ucfirst(strtolower($view));
            }
            $view = App::BUNDLE . (0 == $colons ? '::' : ':') . str_replace('.', ':', $view) . '.' . App::VIEW_EXTENSION;
        }

        if(strpos($view, '\\')) {
            $sections = explode('\\', $view);
            foreach($sections as $key => $section) {
                $sections[$key] = ucfirst($section);
            }

            $lastIndex = count($sections)-1;
            $sections[$lastIndex] = strtolower($sections[$lastIndex]);

            $sections[0] = (ucwords($sections[0], ':'));

            $view = implode('\\', $sections);
        }
    }
}