<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Component\MakerInterface;
use AppBundle\Entity\Component\Project;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Form\Component\GeneratorType;
use AppBundle\Service\ProjectGenerator\AbstractConfig;
use AppBundle\Service\ProjectGenerator\MakerDetector;
use AppBundle\Service\ProjectGenerator\ModuleProvider;
use AppBundle\Service\ProjectGenerator\ProjectGenerator;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Prefix;

/**
 * @Prefix("generator")
 */
class GeneratorController extends AbstractApiController
{
    /**
     * @Post("/generate")
     */
    public function postGenerateAction(Request $request)
    {
        /** @var \AppBundle\Service\ProjectGenerator\ProjectGenerator $generator */
        $generator = $this->get('project_generator');
        $generator->autoSave(false);

        /** @var \AppBundle\Service\Order\OrderTransformer $transformer */
        $transformer = $this->get('order_transformer');

        /** @var \AppBundle\Service\ProjectGenerator\MakerDetector $makerDetector */
        $makerDetector = $this->get('maker_detector');

        $settings = json_decode($request->getContent(), true);

        $form = $this->createForm(GeneratorType::class);
        $request->request->set('generator', $settings);
        $form->handleRequest($request);

        $data = [];
        $status = Response::HTTP_UNPROCESSABLE_ENTITY;

        if($form->isSubmitted() && $form->isValid()) {

            $defaults = $generator->loadDefaults($form->getData());
            $makers = $makerDetector->fromDefaults($defaults);

            if(in_array($defaults['inverter_maker'], $makers)) {

                $project = $this->manager('project')->create();
                $project->setDefaults($defaults);

                $project = $generator->generate($project);

                $defaults = $project->getDefaults();

                if(!count($defaults['errors'])) {

                    if(null != $accountId = $defaults['account_id']){

                        $account = $this->manager('account')->find($accountId);
                        $owner = $account->getOwner();
                        $project->setMember($owner);

                        $generator->priceCost($project);
                    }

                    $order = $transformer->transformFromProject($project, false);

                    $data = $this->sanitizeOrderData($order);
                    $status = Response::HTTP_CREATED;

                }else{

                    if(in_array('exhausted_inverters', $defaults['errors'])){
                        $data['errors'][] = new FormError(
                            sprintf('Não foi possível combinar inversores para a potência %s kWp', $defaults['power'])
                        );
                    }
                }
            }else{

                $data['errors'][] = new FormError(
                    sprintf('Incompatible inverter maker [%s]. Allow [%s]', $defaults['inverter_maker'], implode(',', $makers))
                );
            }
        }

        $errors = $form->getErrors(true);

        if($errors->count()){
            $data['errors'] = [];
            foreach($errors as $error) {
                $name = $error->getOrigin()->getName();
                $value = $settings[$name];
                $data['errors'][] = sprintf('%s [%s => %s]', $error->getMessage(), $name, $value);
            }
        }

        $view = View::create($data, $status);

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getOptionsAction(Request $request)
    {
        $data = [
            'defaults' => $this->getDefaults(),
            'roof_type' => array_values(Project::getRoofTypes()),
            'grid_voltage' => AbstractConfig::getVoltages(),
            'grid_phase_number' => AbstractConfig::getPhaseNumbers(),
            'module' => $this->resolveModuleOptions($request),
            'inverter_maker' => $this->resolveInverterMakerOptions($request),
            'structure_maker' => $this->resolveStructureMakerOptions($request),
            'string_box_maker' => $this->resolveStringBoxMakerOptions($request)
        ];

        if(null != $option = $request->get('option')){
            if(array_key_exists($option, $data)) {
                $data = $data[$option];
            }else{
                throw $this->createNotFoundException(sprintf('The option [%s] is undefined', $option));
            }
        }

        $view = View::create($data);

        return $this->handleView($view);
    }

    /**
     * @param OrderInterface $order
     * @return array
     */
    private function sanitizeOrderData(OrderInterface $order)
    {
        $data = [];

        if(null != $account = $order->getAccount()){
            $data['account_id'] = $account->getId();
        }

        $data = [
            'description' => $order->getDescription(),
            'elements' => []
        ];

        $elements = [];
        foreach ($order->getElements() as $element){

            $elements[] = [
                'code' => $element->getCode(),
                'description' => $element->getDescription(),
                'quantity' => $element->getQuantity(),
                'unitPrice' => $element->getUnitPrice()
            ];
        }

        $data['products'] = $elements;

        return array_filter($data);
    }

    /**
     * @return array
     */
    private function resolveModuleOptions(Request $request)
    {
        if('module' == $request->query->get('option')) {

            /** @var \AppBundle\Manager\ModuleManager $manager */
            $manager = $this->manager('module');
            $provider = new ModuleProvider($manager);

            $modules = $this->getFormatter()->format($provider->getAvailable(), [
                'maker' => 'id'
            ]);

            return $modules;
        }

        return $this->getDefaults('module');
    }

    /**
     * @param Request $request
     * @return array
     */
    private function resolveInverterMakerOptions(Request $request)
    {
        if('inverter_maker' == $request->query->get('option', null)) {

            /** @var \AppBundle\Manager\InverterManager $manager */
            $manager = $this->manager('inverter');
            $provider = new MakerDetector($manager);

            $query = $request->query->all();

            $defaults = ProjectGenerator::getDefaults($query);

            $makers = $provider->fromDefaults($defaults, 1);

            return $this->getFormatter()->format($makers);
        }

        return $this->getDefaults('inverter_maker');
    }

    /**
     * @param $request
     * @return array
     */
    private function resolveStructureMakerOptions(Request $request)
    {
        if('structure_maker' == $request->query->get('option', null)) {

            $makers = $this->manager('maker')->findBy([
                'context' => MakerInterface::CONTEXT_STRUCTURE
            ]);

            return $this->getFormatter()->format($makers);
        }

        return $this->getDefaults('structure_maker');
    }

    /**
     * @param Request $request
     * @return array
     */
    private function resolveStringBoxMakerOptions(Request $request)
    {
        if('string_box_maker' == $request->query->get('option', null)) {

            $makers = $this->manager('maker')->findBy([
                'context' => MakerInterface::CONTEXT_STRING_BOX
            ]);

            return $this->getFormatter()->format($makers);
        }

        return $this->getDefaults('string_box_maker');
    }

    /**
     * @return object|\ApiBundle\Common\Formatter
     */
    private function getFormatter()
    {
        return $this->get('api_formatter');
    }

    /**
     * @return array
     */
    private function getDefaults($key = null)
    {
        $defaults = ProjectGenerator::getDefaults();

        if($key && array_key_exists($key, $defaults)){
            return $defaults[$key];
        }

        return $defaults;
    }
}