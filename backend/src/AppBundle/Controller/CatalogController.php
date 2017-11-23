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
    const CATALOG_MAX_PROJECTS = 10;

    /**
     * @Route("/", name="index_catalog")
     */
    public function indexAction()
    {
        return $this->render('catalog/index.html.twig', [
            'maxQuantity' => $this->getMaxProjects()
        ]);
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
            'maxQuantity' => $this->getMaxProjects(),
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
        $qb = $this->getCatalogQueryBuilder();

        $this->overrideGetFilters();

        return $this->render('catalog/catalog.html.twig', [
            'projects' => $qb->getQuery()->getResult()
        ]);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getCatalogQueryBuilder()
    {
        $member = $this->member();

        $qb = $this->manager('project')->createQueryBuilder();
        $qb
            ->where($qb->expr()->in('p.member', $member->getId()))
            ->orderBy('p.id', 'desc')
            ->andWhere('p.source = :source')->setParameter('source', 2);

        return $qb;
    }

    /**
     * @return mixed
     */
    private function countProjectsCatalog()
    {
        $qb = $this->getCatalogQueryBuilder();
        $qb->select('count(p.id)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @return int
     */
    private function getMaxProjects()
    {
        $projectsCount = $this->countProjectsCatalog();

        $maxQuantity = (self::CATALOG_MAX_PROJECTS - $projectsCount);

        return $maxQuantity;
    }


}
