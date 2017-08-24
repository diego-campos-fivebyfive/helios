<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Entity\Pricing\Range;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MemorialController extends FOSRestController
{
    public function postMemorialsAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $dataRanges = $data['range'];

        /** @var Memorial $memorialManager */
        $memorialManager = $this->get('memorial_manager');
        $rangeManager = $this->get('range_manager');

        $existentMemorial = $memorialManager->findOneBy(['version' => $data['version']]);

        if ($existentMemorial) {
            $data = "This Memorial Already Existing!";
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;

            $view = View::create($data)->setStatusCode($status);
            return $this->handleView($view);
        }

        $currentMemorial = $memorialManager->findOneBy(array(), array('id' => 'DESC'));
        $currentMemorial->setEndAt(new \DateTime('now'));

        foreach ($dataRanges as $ranges) {

            /** @var Memorial $memorial */
            $memorial = $memorialManager->create();
            $memorial
                ->setVersion($data['version'])
                ->setStatus($data['status'])
                ->setStartAt(new \DateTime('now'));
            $memorialManager->save($memorial);

            $markups = $ranges['markups'];

            foreach ($markups as $level) {

                $config = $level['levels'];

                foreach ($config as $item) {

                    /** @var Range $range */
                    $range = $rangeManager->create();
                    $range
                        ->setCode($ranges['code'])
                        ->setMemorial($memorial)
                        ->setLevel($item['level'])
                        ->setInitialPower($level['initial'])
                        ->setFinalPower($level['final'])
                        ->setPrice($item['price']);
                    $rangeManager->save($range);
                }
            }
            $view = View::create();
            return $this->handleView($view);
        }
    }
}
