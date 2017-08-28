<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Project\BudgetSection;

/**
 * Class BudgetSectionController
 * @package AppBundle\Controller
 *
 * @Route("project/budget/section")
 */
class BudgetSectionController extends AbstractController
{

    /**
     * @Route("/create", name="budgetSection_create")
     */
    public function createAction(Request $request)
    {
        if ($request->isMethod('post'))
        {
            $sections = $request->get('sections');
            $em = $this->getDoctrine()->getManager();
            foreach ($sections as $section)
            {
                $budgetSection = new BudgetSection();
                $budgetSection->setPosition($section['position']);
                $budgetSection->setContent($section['content']);
                $budgetSection->setTitle($section['title']);
                $em->persist($budgetSection);
            }
            $em->flush();
            return new Response('ok');
        }
        return $this->render('project.budget-section');
    }

    /**
     * @Route("/create", name="budgetSection_create")
     */
    public function updateAction(Request $request)
    {
        
    }

}
