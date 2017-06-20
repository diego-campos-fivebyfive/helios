<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Component\ComponentInterface;
use AppBundle\Entity\Component\InverterInterface;
use AppBundle\Entity\Component\InverterManager;
use AppBundle\Entity\Component\ModuleInterface;
use AppBundle\Entity\Component\ModuleManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class ComponentController extends AbstractController
{
    /**
     * @param ComponentInterface $component
     */
    protected function checkAccess(ComponentInterface $component)
    {
        $method = $this->getMethodController();

        $user = $this->getUser();
        $isAdmin = $user->isAdmin();
        $currentAccount = $this->getCurrentAccount();
        $hasAccount = null != $account = $component->getAccount();
        $isAccount = $hasAccount ? $account->getId() === $currentAccount->getId() : false;
        $hasCopy = $this->accountContainsCopy($component, $currentAccount);

        if ($isAdmin)
            return;

        switch ($method) {
            case 'updateAction':
                if ($hasAccount && $isAccount || ($user->isSuperAdmin()))
                    return;
                break;
            case 'deleteAction':
                if ($hasAccount && $isAccount)
                    return;
                break;

            case 'showAction':
            case 'previewAction':
                if (!$hasAccount || (!$isAdmin && $isAccount))
                    return;
                break;

            case 'copyAction':
                if (!$hasAccount && !$hasCopy)
                    return;
                break;
        }

        throw $this->createAccessDeniedException();
    }

    /**
     * @return array
     */
    protected function prepareIndexData()
    {
        $account = $this->getCurrentAccount();
        $request = $this->getCurrentRequest();
        $route = $request->attributes->get('_route');
        $path = str_replace('_index', '', $route);
        $entity = ucfirst(strtolower($path));
        $isModule = 'module' == $path;

        $powerField = $isModule ? 'c.maxPower' : 'c.nominalPower';

        $manager = $this->getComponentManager($path);

        $qb = $manager->getEntityManager()->createQueryBuilder();

        $qb->select('c')
            ->from(sprintf('AppBundle\Entity\Component\%s', $entity), 'c')
            ->join('c.maker', 'm', 'WITH')
            ->orderBy('m.name', 'asc')
            ->addOrderBy('c.model', 'asc');

        $parameters = [
            'account' => $account
        ];

        if (1 == $request->get('strict', 0)) {
            $qb->where('c.account = :account');
        }else{

            $qb->where(
                $qb->expr()->orX(
                    $qb->expr()->eq('c.status', ':published'),
                    $qb->expr()->eq('c.account', ':account')
                )
            );

            $parameters['published'] = ComponentInterface::STATUS_PUBLISHED;
        }

        $qb->setParameters($parameters);

        $this->makerQueryBuilderFilter($qb, $request, $powerField);

        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1),
            'grid' == $request->query->get('display', 'grid') ? 8 : 20
        );

        return [
            'pagination' => $pagination,
            'account' => $account,
            'query' => array_merge([
                'display' => 'grid',
                'strict' => 0
            ], $request->query->all())
        ];
    }

    /**
     * @param ComponentInterface $component
     * @param Request $request
     * @return RedirectResponse
     */
    protected function saveComponent(ComponentInterface &$component, Request $request)
    {
        $isAdmin = $this->getUser()->isAdmin();
        $isModule = $component->isModule();
        $hasId = $component->getId();

        $manager = $isModule ? $this->getModuleManager() : $this->getInverterManager();
        $id = $hasId ?: $manager->findOneBy([], ['id' => 'desc'])->getId() + 1;

        /**
         * Register a new component
         */
        if (!$hasId) {

            $id = $manager->findOneBy([], ['id' => 'desc'])->getId() + 1;

            $this->uploadComponentFiles($component, $request->files, $id);

            $component->setStatus(ComponentInterface::STATUS_FEATURED);

            if($isAdmin){
                $component
                    ->setStatus(ComponentInterface::STATUS_VALIDATED)
                    ->setAccount(null);
            }

            $manager->save($component);

            $this->setNotice('Componente cadastrado com sucesso!');

            $route = $isAdmin ? 'components' : ($isModule ? 'module_index' : 'inverter_index');

            return $this->redirectToRoute($route);

        } else {
            /**
             * Updating component
             */
            $copy = $this->checkComponentValidation($component, $request);

            if (!$copy) {

                /**
                 * Component is updating for account administrator
                 */

                $this->uploadComponentFiles($component, $request->files, $id);

                $manager->save($component);

                $this->setNotice('Componente atualizado com sucesso!');

                if (null == $url = $this->restore('referer')) {
                    $url = $this->generateUrl($isModule ? 'module_index' : 'inverter_index');
                }

                return $this->redirect($url);

            } else {

                /**
                 * Component is validating by platform administrator
                 * Copy a component data on another component
                 * Define the copy with status validated
                 * Define the component with status ignored
                 */

                $id = $manager->findOneBy([], ['id' => 'desc'])->getId() + 1;

                $this->uploadComponentFiles($copy, $request->files, $id);

                $this->normalizeComponentFiles($component, $copy, $id);

                $copy->setStatus(ComponentInterface::STATUS_VALIDATED);
                $component->setStatus(ComponentInterface::STATUS_IGNORED);

                $manager->save($copy);

                return $this->redirectToRoute('components');
            }
        }
    }

    /**
     * @param ComponentInterface $component
     * @param FileBag $fileBag
     * @param $id
     */
    protected function uploadComponentFiles(ComponentInterface &$component, FileBag $fileBag, $id)
    {
        if ($fileBag->count()) {

            $uploadDir = $this->getComponentsDir();

            foreach ($fileBag->all() as $meta => $uploadedFile) {
                if ($uploadedFile instanceof UploadedFile) {

                    $getter = 'get' . ucfirst($meta);
                    $setter = 'set' . ucfirst($meta);

                    $currentFile = $component->$getter();

                    $tag = $component->isModule() ? 'module' : 'inverter';
                    $ext = $uploadedFile->getClientOriginalExtension();

                    $format = 'pdf' == $ext ? '%s_%s.%s' : '%s_%s_thumb.%s';

                    $filename = sprintf($format, $tag, $id, $ext);

                    if ($currentFile) {
                        $currentFilePath = $uploadDir . $currentFile;
                        if (file_exists($currentFilePath)) {
                            unlink($currentFilePath);
                        }
                    }

                    $uploadedFile->move($uploadDir, $filename);
                    $component->$setter($filename);
                }
            }
        }
    }

    /**
     * @param ComponentInterface | ModuleInterface | InverterInterface $component
     * @param Request $request
     * @return \AppBundle\Entity\Component\InverterInterface|ModuleInterface|bool
     */
    protected function checkComponentValidation(ComponentInterface $component, Request $request)
    {
        if ($this->getUser()->isAdmin()
            && $component->isFeatured()
            && 'validate' == $request->request->get('intention')
        ) {

            $helper = $component->isModule() ? $this->getModuleHelper() : $this->getInverterHelper();

            $component->viewMode = false;

            return $helper->copyComponent($component);
        }

        return false;
    }

    /**
     * @param ComponentInterface $source
     * @param ComponentInterface $target
     * @param $id
     */
    protected function normalizeComponentFiles(ComponentInterface $source, ComponentInterface &$target, $id)
    {
        $dir = $this->getComponentsDir();

        if (null != $datasheet = $target->getDatasheet()) {
            if ($datasheet == $source->getDatasheet()) {
                $filename = $dir . $datasheet;
                if (file_exists($filename)) {
                    $destination = str_replace($source->getId(), $id, $filename);
                    copy($filename, $destination);
                    $target->setDatasheet(str_replace($source->getId(), $id, $datasheet));
                } else {
                    $target->setDatasheet(null);
                }
            }
        }

        if (null != $image = $target->getImage()) {
            if ($image == $source->getImage()) {
                $filename = $dir . $image;
                if (file_exists($filename)) {
                    $destination = str_replace($source->getId(), $id, $filename);
                    copy($filename, $destination);
                    $target->setImage(str_replace($source->getId(), $id, $image));
                } else {
                    $target->setImage(null);
                }
            }
        }

    }

    /**
     * @param QueryBuilder $qb
     * @param Request $request
     * @param $powerField
     */
    protected function makerQueryBuilderFilter(QueryBuilder &$qb, Request $request, $powerField)
    {
        $filterTokens = explode(' ', $request->query->get('filter_value'));
        if (count($filterTokens) > 1) {

            $subFilters = [];
            foreach ($filterTokens as $filterToken) {
                $subFilters[] = $qb->expr()->orX(
                    $qb->expr()->like('m.name', $qb->expr()->literal('%' . $filterToken . '%')),
                    $qb->expr()->like('c.model', $qb->expr()->literal('%' . $filterToken . '%')),
                    $qb->expr()->like($powerField, $qb->expr()->literal('%' . $filterToken . '%'))
                );
            }

            $filterX = $qb->expr()->andX();
            $filterX->addMultiple($subFilters);
            $qb->andWhere($filterX);

            unset($_GET['filter_name'], $_GET['filter_value']);

        } else {
            $this->overrideGetFilters();
        }

    }


    /*
    protected function copyComponentToPlatform(ComponentInterface $component)
    {
        $manager = $component->isModule() ? $this->getModuleManager() : $this->getInverterManager() ;
        $helper = $component->isModule() ? $this->getModuleHelper() : $this->getInverterHelper() ;

        $copy = $helper->copyComponent($component);

        $manager->save($copy);

        $componentId = $component->getId();
        $copyId = $copy->getId();
        $dir = $this->getComponentsDir();

        if(null != $image = $component->getImage()){
            $filename = $dir . $image;
            if(file_exists($filename)){
                $destination = str_replace($componentId, $copyId, $filename);
                copy($filename, $destination);
                $copy->setImage(str_replace($componentId, $copyId, $image));
            }
        }

        if(null != $datasheet = $component->getDatasheet()){
            $filename = $dir . $datasheet;
            if(file_exists($filename)){
                $destination = str_replace($componentId, $copyId, $filename);
                copy($filename, $destination);
                $copy->setDatasheet(str_replace($componentId, $copyId, $datasheet));
            }
        }

        $copy->setStatus(ComponentInterface::STATUS_VALIDATED);
        $component->setStatus(ComponentInterface::STATUS_IGNORED);

        $manager->save($copy);
        $manager->save($component);

        return $copy;
    }
    */

    /**
     * @return \AppBundle\Service\Component\Helper\ModuleHelper
     */
    protected function getModuleHelper()
    {
        return $this->get('app.module_helper');
    }

    /**
     * @return \AppBundle\Service\Component\Helper\InverterHelper
     */
    protected function getInverterHelper()
    {
        return $this->get('app.inverter_helper');
    }

    /**
     * @param ComponentInterface $component
     * @param BusinessInterface $account
     * @return bool
     */
    private function accountContainsCopy(ComponentInterface $component, BusinessInterface $account)
    {
        $isModule = $component instanceof ModuleInterface;

        return $component->getChildrens()->filter(function (ComponentInterface $component) use ($account, $isModule) {

                if (null != $cAccount = $component->getAccount()) {
                    if ($cAccount->getId() == $account->getId()) {
                        return $isModule ? $component->isModule() : !$component->isModule();
                    }
                }

                return false;
            })->count() > 0;
    }

    /**
     * @return string
     */
    private function getMethodController()
    {
        list($controller, $method) = explode('::', $this->getRequestController());
        return $method;
    }

    /**
     * @return string
     */
    private function getRequestController()
    {
        return $this->getCurrentRequest()->attributes->get('_controller');
    }

    /**
     * @param $path
     * @return ModuleManager | InverterManager
     */
    private function getComponentManager($path)
    {
        return $this->get(sprintf('app.%s_manager', $path));
    }

    /**
     * @return string
     */
    private function getComponentsDir()
    {
        return $this->get('kernel')->getRootDir() . '/../web/uploads/components/';
    }

    private function addExtraExpressionFilter($args)
    {
        dump($args);
        die;
    }
}