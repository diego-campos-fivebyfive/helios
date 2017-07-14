<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/proposal")
 */
class ProposalController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Proposal:index.html.twig', array(

        ));
    }

    /**
     * @Route("/editor", name="proposal_editor")
     */
    public function editorAction()
    {
        /*projeto fake*/

        $project = [
            'customer' => [
                'name' => '2-Uzumaki Naruto',
                'averageConsumption' => '572 kWh',
                'document' => '123.456.789-10',
                'phone' => '(42) 9 1234-5678',
                'email' => 'name@mail.com',

                'tableAreas' => 'tabela com as areas',
                'generationChart' => [5200,550,347,805,474,647,864,275,759,584,967,847],
                'annualGeneration' => 'geração anual',
                'monthlyAverageGeneration' => 'geração média mensal',

                'lifetime' => '20 anos',
                'inflation' => 'inflação',
                'lossOfEfficiency' => 'perda de eficiencia',
                'annualOperatingCost' => 'custo anual de operação',
                'priceOfKWh' => 'preço do Kwh mais impostos',
                'proposalValue' => 'valor da proposta',
                'accumulatedCash' => 'caixa acumulado',
                'vpl' => 'vpl',
                'tir' => 'tir',
                'simplePayback' => 'payback simples',
                'discountedPayback' => 'payback descontado',
                'accumulatedCashChart' => [50.0,-30.0,-1.7,15.05,25.74,35.7,43.4,50.75,57.59,66.84,76.7,84.7],

                'tableEquipmentAndServices' => 'tabela de equipamentos e serviços'
            ]
        ];

        //$tagCustomer = $this->render('proposal.tag_customer', ['project' => $project])->getContent();
        /*dump($tagCustomer);
        die;*/

        return $this->render('AppBundle:Proposal:editor.html.twig', ['project' => $project]);
    }

    /**
     * @Route("/pdf", name="proposal_pdf_legacy")
     */
    public function pdfAction()
    {
        return $this->render('AppBundle:Proposal:pdf.html.twig', array(

        ));
    }

    /**
     * @Route("/proposalPDF", name="proposal_pdf")
     */
    public function proposalPDFAction()
    {
        return $this->render('AppBundle:Proposal:proposalPDF.html.twig', array(

        ));
    }

}
