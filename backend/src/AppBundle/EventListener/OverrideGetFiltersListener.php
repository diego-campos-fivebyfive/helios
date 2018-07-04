<?php

/**
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * OverrideGetFiltersListener
 *
 * @author Jonadabe de Souza Nascimento <jhonndabi.s.n@gmail.com>
 */
class OverrideGetFiltersListener implements EventSubscriberInterface
{
    const PRIORITY = 0;

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', self::PRIORITY],
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($request->query->has('filter_name')) {
            $_GET['filter_value'] = '%' . $request->query->get('filter_value') . '%';
        }
    }
}
