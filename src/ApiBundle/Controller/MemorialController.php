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

        $startAt = new \DateTime($data['start_at']);
        $endAt = new \DateTime($data['end_at']);

        /** @var Memorial $memorial */
        $memorial = $memorialManager->create();
        $memorial   ->setVersion($data['version'])
                    ->setStatus($data['status'])
                    ->setStartAt($startAt)
                    ->setEndAt($endAt);
        $memorialManager->save($memorial);

        $view = View::create();
        return $this->handleView($view);
    }

    public function postRangesAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $rangeManager = $this->get('range_manager');

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
