<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\SettingsType;
use AppBundle\Entity\Order\Order;
use AppBundle\Entity\Parameter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;

/**
 * @Security("has_role('ROLE_PLATFORM_ADMIN')")
 *
 * @Breadcrumb("Configurações Gerais")
 *
 * @Route("twig/settings")
 */
class SettingsController extends AdminController
{
    /**
     * @Route("/", name="platform_settings")
     */
    public function settingsAction(Request $request)
    {
        $parameter = $this->formatValues($this->findSettings());

        $form = $this->createForm(SettingsType::class, $parameter);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $parameters = $parameter->getParameters();

            foreach ($parameters['order_expiration_days'] as $key => $expiration) {
                if (is_null($expiration['status']) || is_null($expiration['days'])) {
                    unset($parameters['order_expiration_days'][$key]);
                }
            }

            $parameter->setParameters($parameters);

            $parameter = $this->formatValues($parameter, true);

            $this->manager('parameter')->save($parameter);

            return $this->json();
        }

        $errors = $form->getErrors(true);

        if($errors->count()){
            return $this->json([
                'error' => $errors->current()->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }

        $statusList = Order::getStatusNames();

        if ($form->getData()->has('order_expiration_days'))
            foreach ($form->getData()->get('order_expiration_days') as $expiration)
                unset($statusList[$expiration['status']]);

        return $this->render('admin/settings/index.html.twig', [
            'form' => $form->createView(),
            'statusList' => $statusList,
            'statusNames' => Order::getStatusNames()
        ]);
    }

    /**
     * @return Parameter
     */
    private function findSettings()
    {
        $manager = $this->manager('parameter');

        /** @var Parameter $parameter */
        $parameter = $manager->findOrCreate('platform_settings');

        return $parameter;
    }

    private function formatValues($parameter, $toDb = false)
    {
        $fields = [
            'max_order_discount',
            'max_commercial_discount',
            'fdi_max',
            'fdi_min',
            'shipping_included_max_power',
            'finame_shipping_included_max_power',
            'coupon_order_percent'
        ];

        foreach ($fields as $field) {
            if ($toDb) {
                $parameter->set($field, str_replace( ',', '.', $parameter->get($field)));
            } else {
                $parameter->set($field, str_replace( '.', ',', $parameter->get($field)));
            }
        }

        $account_level_handler = $parameter->get('account_level_handler');

        foreach ($account_level_handler['levels'] as $key => $level) {
            if ($toDb) {
                $value = str_replace(',', '.', $level['amount']);
            } else {
                $value = str_replace('.', ',', $level['amount']);
            }

            $account_level_handler['levels'][$key]['amount'] = $value;
        }

        $parameter->set('account_level_handler', $account_level_handler);

        return $parameter;
    }
}
