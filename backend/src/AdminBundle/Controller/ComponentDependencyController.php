<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\Component\DependencyType;
use AppBundle\Entity\Component\ComponentInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("component/{family}/dependency/{component}")
 */
class ComponentDependencyController extends AdminController
{
    /**
     * @Route("/panel", name="component_dependency_panel")
     */
    public function panelAction($family, $component)
    {
        $component = $this->filterComponent($family, $component);

        return $this->render('admin/components/dependencies/panel.html.twig', [
            'family' => $family,
            'component' => $component
        ]);
    }

    /**
     * @Route("/", name="component_dependencies")
     */
    public function indexAction($family, $component)
    {
        $component = $this->filterComponent($family, $component);

        $dependencies = $this->filterDependencies($component);

        return $this->render('admin/components/dependencies/index.html.twig', [
            'family' => $family,
            'component' => $component,
            'dependencies' => $dependencies
        ]);
    }

    /**
     * @Route("/create", name="component_dependency_create")
     */
    public function createAction(Request $request, $family, $component)
    {
        $component = $this->filterComponent($family, $component);

        $form = $this->createForm(DependencyType::class, ['ratio' => 1], [
            'component' => $component,
            'source' => DependencyType::SOURCE_CREATE
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $dependencies = $component->getDependencies();

            $dependencies[] = $form->getData();

            $this->saveDependencies($component, $dependencies, $family);

            return $this->json();
        }

        return $this->render('admin/components/dependencies/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/update/{index}", name="component_dependency_update")
     */
    public function updateAction(Request $request, $family, $component, $index)
    {
        $component = $this->filterComponent($family, $component);
        $dependencies = $component->getDependencies();

        $data = $dependencies[$index];
        $data['id'] = $this->filterComponent('variety', $data['id']);

        $form = $this->createForm(DependencyType::class, $data, [
            'component' => $component,
            'source' => DependencyType::SOURCE_UPDATE
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $dependencies[$index] = $form->getData();

            $this->saveDependencies($component, $dependencies, $family);

            return $this->json();
        }

        return $this->render('admin/components/dependencies/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{index}", name="component_dependency_delete")
     * @Method("delete")
     */
    public function deleteAction(Request $request, $family, $component, $index)
    {
        $component = $this->filterComponent($family, $component);

        $dependencies = $component->getDependencies();

        unset($dependencies[$index]);

        $this->saveDependencies($component, $dependencies, $family);

        return $this->json();
    }

    /**
     * @param ComponentInterface $component
     * @param array $dependencies
     * @param $family
     */
    private function saveDependencies(ComponentInterface $component, array $dependencies, $family)
    {
        $component->setDependencies(array_values($dependencies));

        $this->manager($family)->save($component);
    }

    /**
     * @param ComponentInterface $component
     * @return array
     */
    private function filterDependencies(ComponentInterface $component)
    {
        $dependencies = $component->getDependencies();
        foreach ($dependencies as $key => $dependency) {
            $dependencies[$key]['component'] = $this->filterComponent($dependency['type'], $dependency['id']);
        }

        return $dependencies;
    }

    /**
     * @param $family
     * @param $id
     * @return null|object|ComponentInterface
     */
    private function filterComponent($family, $id)
    {
        return $this->manager($family)->find($id);
    }
}
