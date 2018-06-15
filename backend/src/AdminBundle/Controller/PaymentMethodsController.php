<?php

namespace AdminBundle\Controller;

use AppBundle\Entity\Parameter;
use Symfony\Component\HttpFoundation\Request;
use AdminBundle\Form\PaymentMethod\PaymentMethodType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("has_role('ROLE_PLATFORM_ADMIN')")
 *
 * @Route("twig/payment-methods")
 */
class PaymentMethodsController extends AdminController
{
    /**
     * @Route("/", name="payment_methods")
     */
    public function indexAction()
    {
        return $this->render('admin/payment-methods/index.html.twig');
    }

    /**
     * @Route("/list", name="payment_methods_list")
     */
    public function listAction(Request $request)
    {
        $parameter = $this->findParameter();
        $results = $parameter->all();

        if (null != $search = trim($request->get('filter_value'))) {
            $results = array_filter($results, function ($result) use ($search){
                $name = strtolower($result['name']);
                if (strpos($name, $search) !== false) {
                    return true;
                }
            });
        }

        $this->overrideGetFilters();

        $itemsPerPage = 10;
        $pagination = $this->getPaginator()->paginate(
            $results,
            $request->query->getInt('page', 1),
            $itemsPerPage);

        return $this->render('admin/payment-methods/list.html.twig', [
            'paymentMethods' => $pagination
        ]);
    }

    /**
     * @Route("/create", name="payment_methods_create")
     */
    public function createAction(Request $request)
    {
        $parameter = $this->findParameter();

        $form = $this->initializeForm($request, []);

        if($form->isSubmitted() && $form->isValid()){

            $parameter->set($parameter->count(), $form->getData());

            $this->manager('parameter')->save($parameter);

            return $this->json([]);
        }

        return $this->render('admin/payment-methods/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/update", name="payment_methods_update")
     */
    public function updateAction(Request $request, $id)
    {
        $parameter = $this->findParameter();
        $parameters = $parameter->all();
        $data = $parameters[$id];

        $form = $this->initializeForm($request, $data);

        if($form->isSubmitted() && $form->isValid()){

            $data = $form->getData();

            foreach ($data['quotas'] as $key => $quota){
                if(empty(array_filter($quota))){
                    unset($data['quotas'][$key]);
                }
            }

            $data['quotas'] = array_values($data['quotas']);

            $parameters[$id] = $data;

            $parameter->setParameters($parameters);

            $this->manager('parameter')->save($parameter);

            return $this->json($parameter->get($id));
        }

        return $this->render('admin/payment-methods/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/delete", name="payment_methods_delete")
     * @Method("delete")
     */
    public function deleteAction($id)
    {
        $parameter = $this->findParameter();

        if($parameter->has($id)){

            $parameter->remove($id);

            $data = $parameter->all();

            $parameter->setParameters(array_values($data));

            $this->manager('parameter')->save($parameter);
        }

        return $this->json();
    }

    /**
     * @param Request $request
     * @param $data
     * @return \Symfony\Component\Form\Form
     */
    private function initializeForm(Request $request, $data)
    {
        $form = $this->createForm(PaymentMethodType::class, $data, [
            'action' => $request->getUri()
        ]);

        $form->handleRequest($request);

        return $form;
    }

    /**
     * @return Parameter
     */
    private function findParameter()
    {
        $manager = $this->manager('parameter');

        /** @var Parameter $parameter */
        $parameter = $manager->findOrCreate('payment_methods');

        return $parameter;
    }
}
