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
        if ($currentMemorial) {
            $currentMemorial->setEndAt(new \DateTime('now'));
        }

        /** @var Memorial $memorial */
        $memorial = $memorialManager->create();
        $memorial
            ->setVersion($data['version'])
            ->setIsquikId($data['isquik_id'])
            ->setStatus($data['status'])
            ->setStartAt(new \DateTime('now'));

        $memorialManager->save($memorial);

        $offset = count($data['ranges']) -1;

        foreach ($data['ranges'] as $key => $config) {

            foreach ($config['markups'] as $markup) {

                foreach ($markup['levels'] as $level) {

                    /** @var Range $range */
                    $range = $rangeManager->create();
                    $range
                        ->setMemorial($memorial)
                        ->setCode($config['code'])
                        ->setInitialPower($markup['initial'])
                        ->setFinalPower($markup['final'])
                        ->setLevel($level['level'])
                        ->setPrice($level['price']);

                    $rangeManager->save($range, $offset == $key);
                }
            }
        }
        $view = View::create();
        return $this->handleView($view);
    }
}
