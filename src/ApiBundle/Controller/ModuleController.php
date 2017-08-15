<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Component\Module;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ModuleController extends AbstractApiController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function getModulesAction(Request $request)
    {
        $qb = $this->manager('module')->createQueryBuilder();

        $pagination = $this->paginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            20
        );

        // TODO: This method is temporary, you will soon be moved to a service
        $currentPage = $pagination->getCurrentPageNumber();
        $itemsPerPage = $pagination->getItemNumberPerPage();
        $totalCount = $pagination->getTotalItemCount();
        $modules = $pagination->getItems();

        if(count($modules)) {
            $formatter = $this->get('data_formatter');
            $modules = $formatter->format($modules, ['maker' => 'id']);
        }

        $data = [
            'page' => $currentPage,
            'per_page' => $itemsPerPage,
            'total' => $totalCount,
            'modules' => $modules
        ];

        $view = View::create($data);

        return $this->handleView($view);
    }

    public function postModulesAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $moduleManager = $this->get('module_manager');

        /** @var Module $module */
        $module = $moduleManager->create();
        $module
            ->setCode($data['code'])
            ->setModel($data['model']);

        try {
            $moduleManager->save($module);
            $status = Response::HTTP_CREATED;
            $data = [
                'id' => $module->getId(),
                'code' => $module->getCode(),
                'model' => $module->getModel()
            ];
        }
        catch (\Exception $exception) {
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            $data = 'Can not create Module';
        }

        $view = View::create($data)->setStatusCode($status);

        return $this->handleView($view);
    }

    public function getModuleAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();

        $qb->select('m')
            ->from(Module::class, 'm')
            ->where('m.id = :id')
            ->setParameters([
                'id' => $id
            ]);
        $query = $qb->getQuery();

        $modules = $query->getArrayResult();

        $response = new Response(json_encode($modules));
        $response->headers->set('module', 'aplication/json');

        return $response;
    }
}
