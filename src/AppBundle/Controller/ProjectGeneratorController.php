<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Component\ProjectInterface;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Form\Component\GeneratorType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("generator")
 */
class ProjectGeneratorController extends AbstractController
{
    /**
     * @Route("/", name="project_generator")
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(GeneratorType::class);

        return $this->render('generator.index', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/project", name="order_generator_project")
     */
    public function projectAction(Request $request)
    {
        $generator = $this->getGenerator();

        if(null != $id = $request->get('id')){
            $project = $this->manager('project')->find($id);
            $defaults = $project->getDefaults();
        }else {

            /** @var ProjectInterface $project */
            $project = $this->manager('project')->create();

            $defaults = $generator->loadDefaults([
                'source' => 'power'
            ]);

            $project->setMember($this->member());
        }

        $form = $this->createForm(GeneratorType::class, $defaults, []);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $generator->autoSave(false);
            $generator->reset($project);

            $project->setDefaults($form->getData());

            $generator->generate($project);

            return $this->json([
                'project' => [
                    'id' => $project->getId(),
                    'power' => $project->getPower()
                ]
            ]);
        }

        return $this->render('generator.generate', [
            'form' => $form->createView(),
            'project' => $project,
            'errors' => $form->getErrors(true)->count()
        ]);
    }

    /**
     * @Route("/orders", name="generator_orders")
     */
    public function ordersAction(Request $request)
    {
        $manager = $this->manager('order');

        $qb = $manager->getEntityManager()->createQueryBuilder();

        $qb->select('o')
            ->from($manager->getClass(), 'o')
            ->where('o.account = :account')
            ->orderBy('o.id', 'desc')
            ->setParameters([
                'account' => $this->account()
            ])
        ;

        $paginator = $this->getPaginator();

        $pagination = $paginator->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            1
        );

        return $this->render('generator.orders', [
            'orders' => $pagination
        ]);
    }

    /**
     * @Route("/orders/{id}", name="generator_orders_create")
     * @Method("post")
     */
    public function createOrderAction(Project $project)
    {
        $transformer = $this->get('order_transformer');

        /** @var OrderInterface $order */
        $order = $transformer->transformFromProject($project);

        $this->manager('project')->delete($project);

        return $this->json([
            'order' => [
                'id' => $order->getId()
            ]
        ]);
    }

    /**
     * @return object|\AppBundle\Service\ProjectGenerator\ProjectGenerator
     */
    private function getGenerator()
    {
        return $this->get('project_generator');
    }
}