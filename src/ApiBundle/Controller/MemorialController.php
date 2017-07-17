<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Pricing\Memorial;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MemorialController extends FOSRestController
{
    public function postMemorialsAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $memorialManager = $this->get('memorial_manager');

        /** @var Memorial $memorial */
        $memorial = $memorialManager->create();
        $memorial   ->setVersion($data['version'])
                    ->setStatus($data['status']);

        $memorialManager->save($memorial);
    }

    public function postRangesAction(Request $request)
    {
        $rangeManager = $this->get('range_manager');
        $data = json_decode($request->getContent(), true);

        $markups = $data['markups'];

        foreach ($markups as $level => $config) {

            foreach ($config as $item) {
                $range = $rangeManager->create();
                $range->setCode($data['code'])
                    ->setLevel($level)
                    ->setInitialPower($item['start'])
                    ->setFinalPower($item['end'])
                    ->setMarkup($item['markup'])
                    ->setPrice(35);


                $rangeManager->save($range);
            }
        }

    }
}
