<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Context;
use AppBundle\Entity\Customer;
use AppBundle\Form\ContactType;
use Doctrine\ORM\Query;
use Exporter\Handler;
use Exporter\Source\ArraySourceIterator;
use Exporter\Source\DoctrineORMQuerySourceIterator;
use Exporter\Writer\CsvWriter;
use Exporter\Writer\XmlExcelWriter;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;


/**
 * @Route("contact/{context}")
 *
 * @Breadcrumb("Dashboard", routeName="app_index")
 * @Breadcrumb("Contacts", routeName="contact_index", routeParameters={"context"="{context}"})
 */
class ContactController extends AbstractController
{
    /**
     * Sets whether to remove an employee of a company contact,
     * you must remain as contact
     *
     * @var bool
     */
    private $employeesAsContact = true;

    /**
     * @Route("/", name="contact_index")
     */
    public function indexAction(Request $request, Context $context)
    {
        $qb = $this->createQueryBuilder($context, $request->query->get('strict', false));

        $company = null;

        if(Customer::CONTEXT_PERSON == $context) {
            if (null != $company = $this->getCustomerReferer($request)) {
                if($company->isCompany()) {
                    $qb->andWhere('c.company = :company')->setParameter('company', $company);
                }
            }
        }

        $paginator = $this->getPaginator();

        $this->overrideGetFilters();

        $pagination = $paginator->paginate(
            $qb->getQuery(), $request->query->getInt('page', 1), 10
        );

        $view = $request->isXmlHttpRequest() ? 'contact.index_content' : 'contact.index' ;

        return $this->render($view, array(
            'context' => $context,
            'company' => $company,
            'pagination' => $pagination,
            'categories' => $this->getCategoryManager()->findBy(['account' => $this->account(), 'context' => 'contact_category'])

        ));
    }

    /**
     * @Route("/create", name="contact_create")
     * @Method({"get", "post"})
     */
    public function createAction(Request $request, Context $context)
    {
        $member = $this->getUser()->getInfo();

        $manager = $this->getCustomerManager();

        /** @var Customer $contact */
        $contact = $manager->create();


        $contact
            ->setContext($context->getId())
            ->setMember($member);
        if($contact->isPerson()) {
            if (null != $company = $this->getCustomerReferer($request)) {
                $contact->setCompany($company);
            }
        }

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->processAndPersist($contact);

            //$this->getNotificationGenerator()->createdContact($contact);

            $this->setNotice("Contato criado com sucesso !");

            //$event = $this->createWoopraEvent('contato');

            return $this->redirectToRoute('contact_show', [
                'context' => $contact->getContext(),
                'token' => $contact->getToken(),
                //'woopra_event' => $event->getId()
            ]);
        }

        $parameters = [
            'contact' => $contact,
            'form' => $form->createView(),
            'errors' => $form->getErrors(true)
        ];

        if ($request->isXmlHttpRequest()) {
            return $this->render('contact.form_ajax', $parameters);
        }

        return $this->render('contact.form', $parameters);
    }

    /**
     * @Route("/import", name="contacts_import")
     */
    public function importAction(Request $request, Context $context)
    {
        $dir = $this->get('kernel')->getRootDir() . '/../storage/temp/';
        $file = $request->files->get('file');
        $total = 0;

        if($file instanceof UploadedFile){

            $member     = $this->member();

            $category = $this->getCategoryManager()->findOneBy([
                'account' => $member->getAccount(),
                'context' => 'contact_category',
                'id' => $request->get('category')
            ]);

            $filename = $file->getClientOriginalName() . uniqid(md5(time())) . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);

            /** @var \PHPExcel $phpExcelObject */
            $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject($dir . $filename);

            $sheet = $phpExcelObject->getActiveSheet();

            /** @var \AppBundle\Service\Util\ContactManipulator $manipulator */
            $manipulator = $this->get('contact_manipulator');

            $collection = [];
            $properties = ['A' => 'firstname', 'B' => 'document', 'C' => 'email', 'D' => 'phone'];
            foreach($sheet->getRowIterator() as $row){
                if($row->getRowIndex() > 1) {

                    $data = [];
                    /** @var \PHPExcel_Cell $cell */
                    foreach ($row->getCellIterator('A', 'D') as $cell) {
                        $data[$properties[$cell->getColumn()]] = $cell->getValue();
                    }

                    $data['context']  = $context;
                    $data['member']   = $member;
                    $data['category'] = $category;

                    $collection[] = $data;
                }
            }

            $contacts = $manipulator->fromCollection($collection);

            unlink($dir . $filename);

            $total = count($contacts);
        }

        return $this->jsonResponse([
            'total' => $total
        ]);
    }

    /**
     * @Route("/export", name="contacts_export")
     */
    public function exportAction(Request $request, Context $context)
    {
        $name = sprintf('%s_%s_%s.xlsx',
            'person' == $context  ? 'Pessoas' : 'Empresas',
            $this->account()->getEmail(),
            uniqid(time())
        );

        $filename = $this->get('kernel')->getRootDir() . '/../storage/temp/' . $name;
        $qb = $this->createQueryBuilder($context);

        $fields = [
            'company' => [
                'Nome da Empresa' => 'firstname',
                'CNPJ' => 'document',
                'Email' => 'email',
                'Telefone' => 'phone'
            ],
            'person' => [
                'Nome' => 'firstname',
                'CPF' => 'document',
                'Email' => 'email',
                'Telefone' => 'phone'
            ]
        ];

        $source = new DoctrineORMQuerySourceIterator($qb->getQuery(), $fields[$context->getId()]);
        $writer = new XmlExcelWriter($filename);

        Handler::create($source, $writer)->export();

        clearstatcache(true, $filename);

        return new BinaryFileResponse(
            new File($filename), Response::HTTP_OK, [], true, ResponseHeaderBag::DISPOSITION_ATTACHMENT
        );
    }

    /**
     * @Route("/{token}/show", name="contact_show")
     */
    public function showAction(Request $request, Customer $contact, $context)
    {
        $this->checkAccess($contact);

        $storeReferer = $request->getSchemeAndHttpHost() . $request->getPathInfo();

        $this->store($storeReferer, $contact->getToken());

        return $this->render('contact.show', [
            'contact' => $contact,
            'woopraEvent' => $this->requestWoopraEvent($request)
        ]);
    }

    /**
     * @Breadcrumb("Edit")
     * @Route("/{token}/update", name="contact_update")
     * @Method({"GET", "POST"})
     */
    public function updateAction(Request $request, $context, Customer $contact)
    {
        $this->checkAccess($contact);

        $form = $this->createForm(ContactType::class, $contact);
        $employees = clone $contact->getEmployees();

        /**
         * accessors are managed only by the owner of the account
         * or by whom registered contact
         * @deprecated - Move to ContactType
         */
        if (!$this->getUser()->isOwner() && $this->getUser()->getInfo()->getId() != $contact->getMember()->getId()) {
            $form->remove('accessors');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getCustomerManager();
            
            foreach ($employees as $employee) {
                if (!$contact->getEmployees()->contains($employee)) {
                    if ($this->employeesAsContact) {
                        $employee->setCompany(null);
                        $manager->save($employee);
                    } else {
                        $manager->delete($employee);
                    }
                }
            }

            $this->processAndPersist($contact);
            $this->setNotice("Contato Atualizado com sucesso !");

            return $this->redirectToRoute('contact_show', [
                'context' => $contact->getContext(),
                'token' => $contact->getToken()
            ]);
        }

        return $this->render('contact.form', array(
            'contact' => $contact,
            'form' => $form->createView(),
            'errors' => $form->getErrors(true)
        ));
    }

    /**
     * @Route("/{token}/delete", name="contact_delete")
     * @Method("delete")
     */
    public function deleteAction(Request $request, Customer $contact, $context)
    {
        $this->checkAccess($contact);

        /*$tasks = $this->getTaskManager()->findByContact($contact);

        if(count($tasks)){
            return $this->jsonResponse([
                'error' => $this->translate('There are tasks associated with this contact')
            ], Response::HTTP_IM_USED);
        }*/

        $projects = $this->getProjectManager()->findByCustomer($contact);

        if(count($projects)){

            return $this->jsonResponse([
                'error' => $this->translate('There are projects associated with this contact')
            ], Response::HTTP_IM_USED);
        }

        if ($contact->isPerson()) {

            $this->getCustomerManager()->delete($contact);

        } elseif ($contact->isCompany()) {

            foreach ($contact->getEmployees() as $employee) {
                $employee->setCompany(null);

                $this->getCustomerManager()->save($employee);
            }

            $this->getCustomerManager()->delete($contact);
        }

        return $this->jsonResponse([], Response::HTTP_OK);
    }

    /**
     * @Route("/fast-create", name="contact_fast_create")
     */
    public function fastCreateAction(Request $request, Context $context)
    {
        $name = $request->request->get('name');

        $manager = $this->getCustomerManager();
        $company = $manager->create();

        //$context = $this->getContextManager()->find(BusinessInterface::CONTEXT_COMPANY);

        $company
            ->setContext($context)
            ->setFirstname($name)
            ->setMember($this->getUser()->getInfo());

        $manager->save($company);

        return new JsonResponse([
            'status' => 'success',
            'data' => [
                'id' => $company->getId(),
                'name' => $company->getName()
            ]
        ]);
    }

    /**
     * @param BusinessInterface $entity
     */
    private function processAndPersist(BusinessInterface $entity)
    {
        /*$context = $entity->isPerson() ? 'profile' : 'company';
        $mediaManager = $this->getMediaManager();

        if (null != $filename = $this->getSessionStorage()->get($context)) {
            $currentMedia = $entity->getMedia();
            $media = $this->getUploadHelper()->createMedia($filename, $context);
            if ($media instanceof MediaInterface) {
                $mediaManager->save($media);
                $entity->setMedia($media);
                if ($currentMedia instanceof MediaInterface) {
                    $mediaManager->delete($currentMedia);
                }
            }
        }

        $this->getSessionStorage()->remove($context);*/

        $manager = $this->getCustomerManager();

        $manager->save($entity);

        $member = $this->getUser()->getInfo();

        /**
         * Associate member at new person company [employees]
         */
        if ($entity->isCompany()) {

            $entity->getEmployees()->filter(function (BusinessInterface $employee) {
                return null == $employee->getMember();
            })->forAll(function ($key, BusinessInterface $employee) use ($manager, $member) {
                $employee->setMember($member);
                $manager->save($employee);
            });
        }
    }

    /**
     * @param BusinessInterface $contact
     */
    private function checkAccess(BusinessInterface $contact)
    {
        $this->denyAccessUnlessGranted('edit', $contact);
    }

    /**
     * @param Context $context
     * @param bool $strict
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function createQueryBuilder(Context $context, $strict = false)
    {
        $manager = $this->getCustomerManager();
        $member = $this->getUser()->getInfo();

        if (!$member instanceof BusinessInterface || !$member->isMember())
            throw $this->createAccessDeniedException();

        $qb = $manager->getEntityManager()->createQueryBuilder();

        $qb->select('c')
            ->from('AppBundle:Customer', 'c')
            ->leftJoin('c.category', 'ct');

        if($strict){
            $qb
                ->where('c.context = :context')
                ->setParameter('context', $context);
        }else{
            $qb->where(
                $qb->expr()->orX(
                    'c.context = :person',
                    'c.context = :company'
                )
            )->setParameters([
                'person' => 'person',
                'company' => 'company'
            ]);
        }

        if ($member->isOwner()) {

            $memberIds = $member->getAccount()->getMembers()->map(function (BusinessInterface $member) {
                return $member->getId();
            })->toArray();

            $qb->andWhere($qb->expr()->in('c.member', $memberIds));
        } else {

            $allowedIds = $member->getAlloweds()->map(function (BusinessInterface $allowed) {
                return $allowed->getId();
            })->toArray();

            $allowedIds[] = $member->getId();

            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->eq('c.member', ':member'), $qb->expr()->in('c.id', $allowedIds)
            ));

            /**
             * Leaders have access to registered contacts by members
             */
            if ($member->isLeader()) {

                $teamMemberIds = [];
                foreach ($member->getTeam()->getMembers() as $teamMember) {
                    if (!in_array($teamMember->getId(), $allowedIds)) {
                        $teamMemberIds[] = $teamMember->getId();
                    }
                }

                if (!empty($teamMemberIds)) {
                    $qb->orWhere($qb->expr()->andX(
                        $qb->expr()->eq('c.context', ':context'), $qb->expr()->in('c.member', $teamMemberIds)
                    ));
                }
            }

            $qb->setParameter('member', $member);
        }

        $qb->andWhere('c.deletedAt is null')->orderBy('c.firstname', 'asc');

        return $qb;
    }

    /**
     * @param Request $request
     * @return BusinessInterface|null|object
     */
    private function getCustomerReferer(Request $request)
    {
        if(null != $token = $request->query->get('contact')) {
            if(null != $contact = $this->getCustomerManager()->findByToken($token)) {

                $this->denyAccessUnlessGranted('edit', $contact);
            }

            return $contact;
        }

        return null;
    }
}
