<?php

namespace AppBundle\Service;

use AppBundle\Entity\Document;
use AppBundle\Entity\DocumentInterface;
use AppBundle\Entity\Financial\ProjectFinancialInterface;
use AppBundle\Entity\Project\Project;
use AppBundle\Entity\Project\ProjectInterface;
use AppBundle\Entity\Project\ProjectModuleInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ProposalHelper
 * @package AppBundle\Service
 */
class ProposalHelper
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var \JMS\Serializer\Serializer
     */
    private $serializer;

    /**
     * ProposalHelper constructor.
     * @param ContainerInterface $container
     */
    function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->serializer = $this->container->get('jms_serializer');
    }

    /**
     * @param ProjectFinancialInterface $financial
     * @return \AppBundle\Entity\DocumentInterface
     */
    public function load(ProjectFinancialInterface $financial)
    {
        $this->loadOrCreate($financial);

        return $financial->getProposal();
    }

    /**
     * @param ProjectFinancialInterface $financial
     */
    private function loadOrCreate(ProjectFinancialInterface &$financial)
    {
        if (null == $financial->getProposal()) {
            $financial->setProposal($this->create());
        }

        $this->synchronizeAccount($financial);
    }

    /**
     * @param ProjectFinancialInterface $financial
     */
    private function synchronizeAccount(ProjectFinancialInterface &$financial)
    {
        $helper = $this->getDocumentHelper();
        $documentManager = $this->getDocumentManager();

        $parameter = $helper->loadFromAccount($financial->getProject()->getMember()->getAccount());

        $sections = $parameter->get('sections');
        $proposal = $financial->getProposal();
        $project = $financial->getProject();

        $sources = [];
        foreach ($proposal->getSections() as $proposalSection) {
            $uid = $proposalSection->getMetadata('uid');
            if (is_string($uid)) {
                $uid = sprintf('%s%s%s', '[fixed_section][', $uid, ']');
            }
            $sources[] = $uid;
        }

        $targets = [];
        foreach ($sections as $key => $section) {
            $targets[] = $section['fixed'] ? $section['content'] : $key;
        }

        $exclusions = array_values(array_diff($sources, $targets));

        if (count($exclusions)) {
            /** @var \Doctrine\ORM\EntityManager $manager */
            $manager = $this->container->get('doctrine')->getManager();
            foreach ($exclusions as $exKey => $exUid) {

                $exSection = $proposal->getSections()->filter(function (Document $document) use ($exUid) {

                    $assertSection = $document->getMetadata('uid') == $exUid;

                    if (!$assertSection) {
                        $nextUid = str_replace(['[fixed_section][', ']'], '', $exUid);
                        $assertSection = $document->getMetadata('uid') == $nextUid;
                    }

                    return $assertSection;

                })->current();

                if ($exSection) {

                    /**
                     * TODO
                     * Ao eliminar uma seção no configurador e atualizar a proposta no projeto
                     * Este processo removia a referência de imagem de capas e logo
                     */
                    $manager->detach($parameter);

                    $proposal->removeSection($exSection);
                    $manager->remove($exSection);
                    $manager->flush();
                }
            }
        }

        foreach ($sections as $key => $section) {

            $uid = $section['fixed'] ? str_replace(['[fixed_section][', ']'], '', $section['content']) : $key;
            $fixed = $section['fixed'];
            $editable = array_key_exists('editable', $section) ? $section['editable'] : false;

            switch ($uid) {
                case 'generation_data':
                    $data = $this->configureProjectGeneration($project);
                    break;

                case 'financial_analysis':
                    $data = $financial;
                    break;

                case 'project_composition':
                    $data = $this->configureProjectComposition($project);
                    break;

                case 'customer_data':
                    $data = $project->getCustomer()->toArray();
                    break;

                default:
                    /*if (!is_int($uid)) {
                        throw new \InvalidArgumentException(sprintf('The uid %s is not supported', $uid));
                    }*/
                    $data = (string)$section['content'];
                    break;
            }

            $newSection = $proposal->getSections()->filter(function (Document $document) use ($uid) {
                return $uid == $document->getMetadata('uid');
            })->current();

            if (!$newSection) {
                /** @var DocumentInterface $newSection */
                $newSection = $documentManager->create();
            }

            $title = $fixed
                ? $section['title']
                : ($editable
                    ? ($newSection->getId() ? $newSection->getTitle() : $section['title'])
                    : $section['title']);

            $content = $fixed
                ? $this->serializeContent($data)
                : ($editable
                    ? ($newSection->getId() ? $newSection->getContent() : $section['content'])
                    : $section['content']);

            $order = (int)$section['order'];

            $newSection
                ->setTitle($title)
                ->setPosition($order)
                ->setContent($content)
                ->setMetadata([
                    'uid' => $uid,
                    'fixed' => $fixed,
                    'editable' => $editable
                ]);

            if (!$newSection->getId()) {
                $proposal->addSection($newSection);
            }
        }


        // TODO: Error - Image on parameters was removed after save
        $documentManager->getObjectManager()->detach($parameter);

        $documentManager->save($proposal);
    }

    /**
     * @return \AppBundle\Service\DocumentHelper
     */
    private function getDocumentHelper()
    {
        return $this->container->get('app.document_helper');
    }

    /**
     * @param $data
     * @return mixed|string
     */
    private function serializeContent($data)
    {
        if($data instanceof ProjectFinancialInterface){
            //dump($data); die;
            $data = $data->toArray();
        }

        return $this->serializer->serialize($data, 'json');
    }

    private function configureProjectGeneration(Project $project)
    {
        $data = $project->toArray(false);

        $data['areas'] = [];
        foreach ($project->getInverters() as $projectInverter) {
            foreach ($projectInverter->getModules() as $area) {
                if ($area instanceof ProjectModuleInterface) {
                    $data['areas'][] = $area->toArray();
                }
            }
        }

        return $data;
    }

    /**
     * @param ProjectInterface $project
     * @return array
     */
    private function configureProjectComposition(ProjectInterface $project)
    {
        $kit = $project->getKit();

        $inverters = [];
        foreach ($kit->getInverters() as $inverter) {
            $inverters[] = [
                'model' => $inverter->getModel(),
                'quantity' => $inverter->getQuantity(),
                'unit_price_sale' => $inverter->getUnitPriceSale(),
                'total_price_sale' => $inverter->getTotalPriceSale(),
            ];
        }

        $modules = [];
        foreach ($kit->getModules() as $module) {
            $modules[] = [
                'model' => $module->getModel(),
                'quantity' => $module->getQuantity(),
                'unit_price_sale' => $module->getUnitPriceSale(),
                'total_price_sale' => $module->getTotalPriceSale(),
            ];
        }

        $items = [];
        foreach ($kit->getElementItems() as $item) {
            $items[] = [
                'name' => $item->getName(),
                'quantity' => $item->getQuantity(),
                'unit_price_sale' => $item->getUnitPriceSale(),
                'total_price_sale' => $item->getTotalPriceSale(),
            ];
        }

        $services = [];
        foreach ($kit->getElementServices() as $service) {
            $services[] = [
                'name' => $service->getName(),
                'unit_price_sale' => $service->getUnitPriceSale(),
                'total_price_sale' => $service->getTotalPriceSale(),
            ];
        }

        return [
            'inverters' => $inverters,
            'modules' => $modules,
            'items' => $items,
            'services' => $services
        ];
    }
}