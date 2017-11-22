<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Pricing\Memorial;
use AppBundle\Entity\Pricing\MemorialInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("catalog")
 * @Breadcrumb("Lista de Sistemas")
 */
class CatalogController extends AbstractController
{
    /**
     * @Route("/", name="index_catalog")
     */
    public function indexAction()
    {
        return $this->render('catalog/index.html.twig', []);
    }

    /**
     * @Route("/config", name="catalog_config")
     */
    public function configAction()
    {
        $memorials = $this->manager('memorial')->findAll();
        $memorial = $this->container->get('memorial_loader')->load();

        $levels = Memorial::getDefaultLevels();
        unset($levels[Memorial::LEVEL_PROMOTIONAL]);

        return $this->render('catalog/config.html.twig', [
            'allLevels' => $levels,
            'allMemorials' => $memorials,
            'member' => $this->member(),
            'level' => $this->account()->getLevel(),
            'memorial' => $memorial
        ]);
    }

    /**
     * @Route("/catalog", name="catalog_list")
     */
    public function catalogAction()
    {
        $member = $this->member();

        $qb = $this->manager('project')->createQueryBuilder();

        $qb
            ->where($qb->expr()->in('p.member', $member->getId()))
            ->orderBy('p.id', 'desc')
            ->andWhere('p.source = :source')->setParameter('source', 2);

        $this->overrideGetFilters();

        return $this->render('catalog/catalog.html.twig', [
            'projects' => $qb->getQuery()->getResult()
        ]);
    }
}
