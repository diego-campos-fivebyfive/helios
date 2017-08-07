<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\MemberInterface;
use AppBundle\Model\Document\Account;
use FOS\RestBundle\Controller\FOSRestController;
use AppBundle\Entity\Customer;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AccountsController extends FOSRestController
{
    public function postAccountAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'];

        $em = $this->getDoctrine()->getManager();
        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $em->createQueryBuilder();
        $qb->select('a')
            ->from(Customer::class, 'a')
            ->where('a.email = :email')
            ->setParameter('email', $email);
        $em = $qb->getQuery();
        $emails = $em->getMaxResults(2);

        if (isset($emails)) {
            $data = "This account already exists!";
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;

            $view = View::create($data)->setStatusCode($status);
            return $this->handleView($view);
        }

        /** @var AccountInterface $accountManager */
        $accountManager = $this->get('account_manager');
        $account = $accountManager->create();
        $account
            ->setDocument($data['document'])
            ->setExtraDocument($data['extraDocument'])
            ->setFirstName($data['firstname'])
            ->setLastName($data['lastname'])
            ->setPostcode($data['postcode'])
            ->setState($data['state'])
            ->setCity($data['city'])
            ->setDistrict($data['district'])
            ->setStreet($data['street'])
            ->setNumber($data['number'])
            ->setEmail($data['email'])
            ->setPhone($data['phone'])
            ->setStatus($data['status'])
            ->setContext(Customer::CONTEXT_ACCOUNT);
        try {
            $accountManager->save($account);
            $status = Response::HTTP_CREATED;
            $data = [
                'id' => $account->getId(),
                'firstname' => $account->getFirstName(),
                'lastname' => $account->getLastName(),
                'extraDocument' => $account->getExtraDocument(),
                'document' => $account->getDocument(),
                'email' => $account->getEmail(),
                'state' => $account->getState(),
                'city' => $account->getCity(),
                'phone' => $account->getPhone(),
                'district' => $account->getDistrict(),
                'street' => $account->getStreet(),
                'number' => $account->getNumber(),
                'postcode' => $account->getPostcode(),
                'status' => $account->getStatus()
            ];
        }
        catch (\Exception $exception) {
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            $data = 'Can not create Account';
        }

        $view = View::create($data)->setStatusCode($status);

        return $this->handleView($view);

    }
    /**
     * @ApiDoc(
     *  resource=true,
     *  description="This method return a specific account"
     * )
     */
    public function getAccountAction(Customer $id)
    {
        $data = [];
        $account = $id;
        if($account->isAccount()) {

            $data = [
                'id' => $account->getId(),
                'firstname' => $account->getFirstname(),
                'lastname' => $account->getLastname(),
                'email' => $account->getEmail(),
                'phone' => $account->getPhone(),
                'document' => $account->getDocument(),
                'extradocument' => $account->getExtraDocument(),
                'state' => $account->getState(),
                'city' => $account->getCity(),
                'street' => $account->getStreet(),
                'number' => $account->getNumber(),
                'created_at' => $account->getCreatedAt(),
                'owner' => $account->getOwner()->getId()
            ];

            $members = $account->getMembers()->map(function (MemberInterface $member) {
                return $member->getId();
            });

            $data['users'] = $members;
        }
        $view = View::create($data);

        return $this->handleView($view);
    }

    public function putAccountAction(Request $request, Customer $id)
    {
        $data = json_decode($request->getContent(), true);

        $account = $id;

        if (!$account->isAccount()) {
            return JsonResponse::create("Invalid Account ID",Response::HTTP_NOT_FOUND);
        }

        /** @var AccountInterface $accountManager */
        $accountManager = $this->get('account_manager');
        $account
            ->setFirstName($data['firstname'])
            ->setLastName($data['lastname'])
            ->setExtraDocument($data['extraDocument'])
            ->setDocument($data['document'])
            ->setEmail($data['email'])
            ->setState($data['state'])
            ->setCity($data['city'])
            ->setPhone($data['phone'])
            ->setDistrict($data['district'])
            ->setStreet($data['street'])
            ->setNumber($data['number'])
            ->setPostcode($data['postcode'])
            ->setStatus($data['status']);

        try {
            $accountManager->save($account);
            $status = Response::HTTP_ACCEPTED;
            $data = [
                'id' => $account->getId(),
                'firstname' => $account->getFirstName(),
                'lastname' => $account->getLastName(),
                'extraDocument' => $account->getExtraDocument(),
                'document' => $account->getDocument(),
                'email' => $account->getEmail(),
                'state' => $account->getState(),
                'city' => $account->getCity(),
                'phone' => $account->getPhone(),
                'district' => $account->getDistrict(),
                'street' => $account->getStreet(),
                'number' => $account->getNumber(),
                'postcode' => $account->getPostcode(),
                'status' => $account->getStatus(),
                'owner' => $account->getOwner()->getId()
            ];
        }
        catch (\Exception $exception ) {
            $status = Response::HTTP_UNPROCESSABLE_ENTITY;
            $data = 'Can not update Account';
        }

        $view = View::create($data)->setStatusCode($status);

        return $this->handleView($view);
    }
}
