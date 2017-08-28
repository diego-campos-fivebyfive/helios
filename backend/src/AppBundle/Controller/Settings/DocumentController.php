<?php

namespace AppBundle\Controller\Settings;

use AppBundle\Controller\AbstractController;
use AppBundle\Form\Settings\DocumentType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Security("has_role('ROLE_OWNER')")
 *
 * @Route("document")
 *
 * @Breadcrumb("Dashboard", route={"name"="app_index"})
 * @Breadcrumb("Settings")
 * @Breadcrumb("Proposal Settings", route={"name"="document_configure"})
 */
class DocumentController extends AbstractController
{
    /**
     * @Route("/configure", name="document_configure")
     */
    public function configureAction(Request $request)
    {
        $helper = $this->getDocumentHelper();

        $document = $helper->loadFromAccount($this->getCurrentAccount());
        $form = $this->createForm(DocumentType::class, $document);

        if($request->isMethod('post')) {
            $helper->handleRequest($form, $request);
        }

        $errors = $helper->getErrors();
        if ($form->isSubmitted() && $form->isValid() && empty($errors)) {
            $this->setNotice("Configurações atualizadas com sucesso!");
            return $this->redirectToRoute('document_configure');
        }

        foreach($errors as $error){
            $this->setNotice($error, 'error');
        }

        //$this->dd($document->all());

        return $this->render('settings.document\configure', [
            'form' => $form->createView(),
            'document' => $document->all(),
            'cover_size' => $helper->getCoverMaxSize(),
            'logo_size' => $helper->getLogoMaxSize()
        ]);
    }

    /**
     * @Route("/redefine", name="document_redefine")
     * @Method("delete")
     */
    public function redefineAction()
    {
        $this->getDocumentHelper()->redefineFromAccount($this->getCurrentAccount());

        return $this->jsonResponse([], Response::HTTP_ACCEPTED);
    }
}