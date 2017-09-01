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
    public function postMemorialAction(Request $request)
    {
        $responseHandler = function($data, $status) {
            $view = View::create($data)->setStatusCode($status);
            return $this->handleView($view);
        };

        $data = json_decode($request->getContent(), true);

        if($data['status'] != 1) {
            $data = "This Memorial is not active!";
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            return $responseHandler($data, $status);
        }

        /** @var Memorial $memorialManager */
        $memorialManager = $this->get('memorial_manager');
        $rangeManager = $this->get('range_manager');

        $existentMemorial = $memorialManager->findOneBy(['version' => $data['version']]);
        if ($existentMemorial) {
            $data = "This Memorial Already Existing!";
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            return $responseHandler($data, $status);
        }

        $currentMemorial = $memorialManager->findOneBy(array(), array('id' => 'DESC'));
        if ($currentMemorial) {
            $currentMemorial
                ->setStatus(0)
                ->setEndAt(new \DateTime('now'));
        }

        /** @var Memorial $memorial */
        $memorial = $memorialManager->create();
        $memorial
            ->setVersion($data['version'])
            ->setIsquikId($data['isquik_id'])
            ->setStatus($data['status'])
            ->setStartAt(new \DateTime('now'));

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

                    $memorial->addRange($range, $offset == $key);
                }
            }
        }

        try {
            $memorialManager->save($memorial);
            $status = Response::HTTP_CREATED;
            $data = [
                'Id' => $memorial->getId(),
                'Isquik_id' => $memorial->getIsquikId(),
                'Version' => $memorial->getVersion(),
                'Status' => $memorial->getStatus(),
                'StartAt' => $memorial->getStartAt(),
                'EndAt' => $memorial->getEndAt()
            ];
        } catch (\Exception $exception) {
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            $data = $exception;
        }

        return $responseHandler($data, $status);
    }
}
