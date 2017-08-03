<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Entity\Pricing\Range;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class MemorialController extends FOSRestController
{
    public function postMemorialsAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $dataRanges = $data['range'];


        $memorialManager = $this->get('memorial_manager');
        $rangeManager = $this->get('range_manager');

        foreach ($dataRanges as $ranges) {
            $startAt = new \DateTime($data['start_at']);
            $endAt = new \DateTime($data['end_at']);

            /** @var Memorial $memorial */
            $memorial = $memorialManager->create();
            $memorial
                ->setVersion($data['version'])
                ->setStatus($data['status'])
                ->setStartAt($startAt)
                ->setEndAt($endAt);
            $memorialManager->save($memorial);

            $markups = $ranges['markups'];

            foreach ($markups as $level => $config) {

                foreach ($config as $item) {

                    /** @var Range $range */
                    $range = $rangeManager->create();
                    $range
                        ->setCode($ranges['code'])
                        ->setMemorial($memorial)
                        ->setLevel($level)
                        ->setInitialPower($item['start'])
                        ->setFinalPower($item['end'])
                        ->setMarkup($item['markup'])
                        ->setPrice($ranges['price']);
                    $rangeManager->save($range);
                }
            }
            $view = View::create();
            return $this->handleView($view);
        }
    }
}
