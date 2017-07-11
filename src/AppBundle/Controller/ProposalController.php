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
                'name' => '1 Uzumaki Naruto',
                'averageConsumption' => '2 572 kWh',
                'document' => '3 123.456.789-10',
                'phone' => '4 (42) 9 1234-5678',
                'email' => '5 name@mail.com',

                'tableAreas' => '6 tabela com as areas',
                'generationChart' => '7 grafico de geração',
                'annualGeneration' => '8 geração anual',
                'monthlyAverageGeneration' => '9 geração média mensal',

                'lifetime' => '10 20 anos',
                'inflation' => '11 inflação',
                'lossOfEfficiency' => '12 perda de eficiencia',
                'annualOperatingCost' => '13 custo anual de operação',
                'priceOfKWh' => ' 14 preço do Kwh mais impostos',
                'proposalValue' => '15 valor da proposta',
                'accumulatedCash' => '16 caixa acumulado',
                'vpl' => '17 vpl',
                'tir' => '18 tir',
                'simplePayback' => '19 payback simples',
                'discountedPayback' => '20 payback descontado',
                'accumulatedCashChart' => '21 grafico de caixa acumulado',

                'tableEquipmentAndServices' => '22 tabela de equipamentos e serviços'
            ]
        ];

        //$tagCustomer = $this->render('proposal.tag_customer', ['project' => $project])->getContent();
        /*dump($tagCustomer);
        die;*/

        return $this->render('AppBundle:Proposal:editor.html.twig', ['project' => $project]);
    }

    /**
     * @Route("/pdf", name="proposal_pdf")
     */
    public function pdfAction()
    {
        return $this->render('AppBundle:Proposal:pdf.html.twig', array(

        ));
    }
    
}
