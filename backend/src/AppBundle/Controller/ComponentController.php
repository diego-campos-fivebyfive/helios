<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\Inverter;
use AppBundle\Entity\Component\InverterInterface;
use AppBundle\Entity\Component\Module;
use AppBundle\Entity\Component\ModuleInterface;
use AppBundle\Entity\Precifier\Memorial;
use AppBundle\Form\Component\InverterType;
use AppBundle\Form\Component\ModuleType;
use AppBundle\Service\Precifier\ComponentsListener;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Route("components/{type}")
 *
 *
 * @Breadcrumb("Dashboard", route={"name"="app_index"})
 * @Breadcrumb("{type}s", route={"name"="components", "parameters"={"type":"{type}"}})
 */
class ComponentController extends AbstractController
{
    /**
     * @Route("/", name="components")
     */
    public function indexAction(Request $request, $type)
    {
        $manager = $this->manager($type);
        $qb = $manager->getEntityManager()->createQueryBuilder();
        $qb->select('c')
            ->from(sprintf('AppBundle\Entity\Component\%s', ucfirst($type)), 'c')
            ->leftJoin('c.maker', 'm', 'WITH')
            ->orderBy('m.name', 'asc')
            ->addOrderBy('c.model', 'asc');

        $powerField = 'module' == $type ? 'c.maxPower' : 'c.nominalPower';
        $this->makerQueryBuilderFilter($qb, $request, $powerField);

        if ($components_actives = $request->get('actives')) {
            if ((int) $components_actives == 1) {
                $expression  =
                    $qb->expr()->neq(
                        'c.generatorLevels',
                        $qb->expr()->literal('[]'));
            } else {
                $expression  =
                    $qb->expr()->eq(
                        'c.generatorLevels',
                        $qb->expr()->literal('[]'));
            }

            $qb->andWhere(
                $expression
            );
        }

        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            'grid' == $request->query->get('display', 'grid') ? 8 : 10
        );

        $account = $this->account();

        return $this->render('component.index', [
            'type' => $type,
            'pagination' => $pagination,
            'account' => $account,
            'query' => array_merge([
                'display' => 'grid',
                'strict' => 0
            ], $request->query->all()),
            'components_active_val' => $components_actives
        ]);
    }

    /**
     * @Route("/{id}/show", name="component_show")
     */
    public function showAction(Request $request, $type, $id)
    {
        $component = $this->findComponent($type, $id);

        return $this->render(
            $request->isXmlHttpRequest()
                ? sprintf('%s.show_content', $type)
                : sprintf('%s.show', $type), [
            $type => $component
        ]);
    }

    /**
     * @Security("has_role('ROLE_PLATFORM_MASTER')")
     *
     * @Route("/create", name="component_create")
     * @Method({"get","post"})
     */
    public function createAction(Request $request, $type)
    {
        $manager = $this->manager($type);

        $component = $manager->create();

        $formClass = 'module' == $type ? ModuleType::class : InverterType::class;

        $form = $this->createForm($formClass, $component);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var ComponentsListener $componentLister */
            $componentLister = $this->container->get('precifier_components_listener');

            $componentLister->action(Memorial::ACTION_TYPE_ADD_COMPONENT, $type);

            return $this->saveComponent($component, $type, $request);
        }


        return $this->render($type.'.form', [
            'form' => $form->createView(),
            $type => $component
        ]);
    }

    /**
     * @Security("has_role('ROLE_PLATFORM_MASTER')")
     *
     * @Route("/{id}/update", name="component_update")
     * @Method({"get","post"})
     */
    public function updateAction(Request $request, $type, $id)
    {
        $component = $this->findComponent($type, $id);
        $formClass = ('module' == $type) ? ModuleType::class : InverterType::class;

        $form = $this->createForm($formClass, $component);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->saveComponent($component, $type, $request);
        }

        return $this->render($type.'.form', [
            'form' => $form->createView(),
            $type => $component
        ]);
    }

    /**
     * @Security("has_role('ROLE_PLATFORM_MASTER')")
     *
     * @Route("/{id}/delete/", name="component_delete")
     * @Method({"delete"})
     */
    public function deleteAction($type, $id)
    {
        $component = $this->findComponent($type, $id);

        $projects = $this->manager(sprintf('project_%s', $type))->findBy([$type => $component->getId()]);

        if(count($projects)) {
            return $this->json([
                'error' => 'Este módulo está sendo utilizado projetos'
            ], Response::HTTP_IM_USED);
        }

        $dataSheet = $this->getComponentsDir() . $component->getDataSheet();
        $image = $this->getComponentsDir() . $component->getImage();

        if(is_file($image)) unlink($image);
        if(is_file($dataSheet)) unlink($dataSheet);

        $this->manager($type)->delete($component);

        /** @var ComponentsListener $componentListener */
        $componentListener = $this->container->get('precifier_components_listener');

        $componentListener->action(Memorial::ACTION_TYPE_REMOVE_COMPONENT, $type);

        return $this->json([]);
    }

    /**
     * @Security("has_role('ROLE_PLATFORM_MASTER')")
     *
     * @Route("/{id}/relationship/", name="component_relationship")
     */
    public function loadRelationshipAction(Inverter $inverter)
    {
        $manager = $this->manager('module');
        $qb = $manager->getEntityManager()->createQueryBuilder();
        $qb->select('m')
            ->from(Module::class, 'm')
            ->orderBy('m.model', 'asc');

        return $this->render('inverter.relationship', [
            'modules' => $qb->getQuery()->getResult(),
            'inverter' => $inverter
        ]);
    }

    /**
     * @Security("has_role('ROLE_PLATFORM_MASTER')")
     *
     * @Route("/{id}/{module}/modules_association", name="modules_association")
     */
    public function modulesAssociationAction(Inverter $inverter, Module $module)
    {
        $modules = $inverter->getModules();

        if (is_null($modules))
            $modules = [];

        if (in_array($module->getId(), $modules))
            unset($modules[array_search($module->getId(), $modules)]);
        else
            array_push($modules, $module->getId());

        $inverter->setModules($modules);

        $this->manager('inverter')->save($inverter);

        return $this->json([]);
    }

    /**
     * @param ModuleInterface|InverterInterface|object $component
     * @param Request $request
     * @return RedirectResponse
     */
    private function saveComponent($component, $type, Request $request)
    {
        $manager = $this->manager($type);
        $manager->save($component);

        $this->get('component_file_handler')->upload($component, $request->files);

        $manager->save($component);

        $this->setNotice('Componente atualizado com sucesso!');

        if (null == $url = $this->restore('referer')) {
            $url = $this->generateUrl('components', ['type' => $type]);
        }

        return $this->redirect($url);
    }

    /**
     * @param QueryBuilder $qb
     * @param Request $request
     * @param $powerField
     */
    protected function makerQueryBuilderFilter(QueryBuilder &$qb, Request $request, $powerField)
    {
        $filterTokens = explode(' ', $request->query->get('filter_value'));
        if (count($filterTokens) > 1) {

            $subFilters = [];
            foreach ($filterTokens as $filterToken) {
                $subFilters[] = $qb->expr()->orX(
                    $qb->expr()->like('m.name', $qb->expr()->literal('%' . $filterToken . '%')),
                    $qb->expr()->like('c.model', $qb->expr()->literal('%' . $filterToken . '%')),
                    $qb->expr()->like($powerField, $qb->expr()->literal('%' . $filterToken . '%'))
                );
            }

            $filterX = $qb->expr()->andX();
            $filterX->addMultiple($subFilters);
            $qb->andWhere($filterX);

            unset($_GET['filter_name'], $_GET['filter_value']);

        } else {
            $this->overrideGetFilters();
        }
    }

    /**
     * @param $type
     * @param $id
     * @return null|object|ModuleInterface|InverterInterface
     */
    private function findComponent($type, $id)
    {
        $manager = $this->manager($type);
        $component = $manager->find($id);

        if(!$component){
            throw $this->createNotFoundException('Component not found');
        }

        return $component;
    }

    /**
     * @return string
     */
    private function getComponentsDir()
    {
        return $this->get('kernel')->getRootDir() . '/../web/uploads/components/';
    }
}
