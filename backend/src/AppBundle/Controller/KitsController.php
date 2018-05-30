<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Kit\Kit;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1), 8
        );

        return $this->render('kit/index.html.twig', array(
            'pagination' => $pagination,
            'kits_active_val' => 1
        ));
    }

    /**
     * @Route("/config", name="kits_index")
     * @Method("get")
     * @Security("has_role('ROLE_PLATFORM_ADMIN')")
     */
    public function configAction(Request $request)
    {
        return $this->render('kit/config.html.twig', [
            'families' => $this->getComponentFamilies()
        ]);
    }

    /**
     * @Route("/components/{family}", name="kit_components_by_family")
     * @Method("get")
     */
    public function getKitComponentsByFamilyAction($family)
    {
        if (!in_array($family, $this->getComponentFamilies())) {
            return JsonResponse::create([], 404);
        }

        $manager = $this->container->get("{$family}_manager");

        $field = $family === 'module' || $family === 'inverter' ? 'model' : 'description';

        /** @var QueryBuilder $qb */
        $qb = $manager->createQueryBuilder();
        $alias = $qb->getRootAlias();

        $qb->select("{$alias}.id, {$alias}.code, {$alias}.{$field} as description");

        $results = $qb->getQuery()->getResult();

        return JsonResponse::create($results, 200);
    }

    /**
     * @return array
     */
    private function getComponentFamilies()
    {
        return $families = [
            ComponentInterface::FAMILY_MODULE => ComponentInterface::FAMILY_MODULE,
            ComponentInterface::FAMILY_INVERTER => ComponentInterface::FAMILY_INVERTER,
            ComponentInterface::FAMILY_STRING_BOX => ComponentInterface::FAMILY_STRING_BOX,
            ComponentInterface::FAMILY_STRUCTURE => ComponentInterface::FAMILY_STRUCTURE,
            ComponentInterface::FAMILY_VARIETY => ComponentInterface::FAMILY_VARIETY
        ];
    }
}
