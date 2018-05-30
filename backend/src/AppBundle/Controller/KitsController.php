<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Kit\Kit;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
USE Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Route("kit")
 *
 * @Breadcrumb("Kits Fixos")
 */
class KitsController extends AbstractController
{
    /**
     * @Route("/", name="kits_index")
     * @Method("get")
     */
    public function indexAction(Request $request)
    {
        $manager = $this->manager('kit');

        $qb = $manager->createQueryBuilder();

        $qb->orderBy('k.position', 'asc');

        if ($actives = $request->get('actives')) {
            if ((int) $actives == 1) {
                $expression  =
                    $qb->expr()->eq(
                        'k.available',
                        $qb->expr()->literal(1));
            } else {
                $expression  =
                    $qb->expr()->eq(
                        'k.available',
                        $qb->expr()->literal(0));
            }

            $qb->andWhere(
                $expression
            );
        }

        $this->overrideGetFilters();

        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1), 8
        );

        return $this->render('kit/index.html.twig', array(
            'pagination' => $pagination,
            'kits_active_val' => $actives
        ));
    }

    /**
     * @Route("/{id}", name="kit_show")
     * @Method("GET")
     */
    public function showAction(Kit $kit)
    {
        return $this->render('kit/show.html.twig', array(
            'kit' => $kit
        ));
    }

    /**
     *
     * @Security("has_role('ROLE_PLATFORM_ADMIN')")
     *
     * @Route("/{id}/delete/", name="delete_kit")
     * @Method("delete")
     */
    public function deleteAction(Kit $kit)
    {
        try {
            $this->manager('kit')->delete($kit);
            $message = 'Kit excluído com sucesso';
            $status = Response::HTTP_OK;
        } catch (\Exception $exception) {
            $message = 'Falha ao excluir este Kit';
            $status = Response::HTTP_CONFLICT;
        }

        return $this->json(['message' => $message], $status);
    }
}
