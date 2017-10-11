<?php

namespace AdminBundle\Controller;

use AdminBundle\Form\SettingsType;
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
 * @Route("/settings")
 */
class SettingsController extends AdminController
{
    /**
     * @Route("/", name="platform_settings")
     */
    public function settingsAction(Request $request)
    {
        $parameter = $this->findSettings();

        $form = $this->createForm(SettingsType::class, $parameter);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $this->manager('parameter')->save($parameter);

            return $this->json();
        }

        $errors = $form->getErrors(true);

        if($errors->count()){
            return $this->json([
                'error' => $errors->current()->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->render('admin/settings/index.html.twig', [
            'form' => $form->createView()
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
}
