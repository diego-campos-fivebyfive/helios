<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Kit\Kit;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
USE Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Route("twig/kit")
 *
 * @Breadcrumb("Kits Fixos")
 */
class KitsController extends AbstractController
{
    /**
     * @Route("/", name="index_kit")
     * @Security("has_role('ROLE_OWNER')")
     * @Method("get")
     */
    public function indexAction(Request $request)
    {
        $manager = $this->manager('kit');

        $qb = $manager->createQueryBuilder();

        $qb
            ->orderBy('k.position', 'asc')
            ->where('k.available = true')
            ->andWhere('k.stock > 0');

        $this->overrideGetFilters();

        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1), 8
        );

        return $this->render('kits.index', array(
            'pagination' => $pagination
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
}
