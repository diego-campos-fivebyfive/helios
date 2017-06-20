<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\TeamInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use APY\BreadcrumbTrailBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\TeamType;
use AppBundle\Entity\Team;
use AppBundle\Entity\Customer;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * TeamController
 * @author Claudinei Machado <claudinei@kolinalabs.com>
 *
 * @Route("team")
 * @Security("has_role('ROLE_OWNER')")
 *
 * @Breadcrumb("Dashboard", route={"name"="app_index"})
 * @Breadcrumb("Teams", route={"name"="team_index"})
 */
class TeamController extends AbstractController
{
    /**
     * @Route("/", name="team_index")
     */
    public function indexAction(Request $request)
    {
        $paginator = $this->get('knp_paginator');
        $manager = $this->getTeamManager();

        $account = $this->getCurrentAccount();

        $query = $manager
                ->getEntityManager()
                ->createQueryBuilder()
                ->select('t')
                ->from('AppBundle:Team', 't')
                ->where('t.account = :account')
                ->setParameter('account', $account->getId())
                ->getQuery();

        $pagination = $paginator->paginate(
            $query, $request->query->getInt('page', 1), 4
        );

        return $this->render('team.index', array(
            'pagination' => $pagination,
            'display' => $request->get('display', 'grid')
        ));
    }

    /**
     * @Route("/create", name="team_create")
     * @Method({"get", "post"})
     */
    public function createAction(Request $request)
    {
        $manager = $this->getTeamManager();
        $team = $manager->create();
        $team->setAccount($this->getCurrentAccount());

        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $manager->save($team);

            $this->refreshTeamMembers($team);

            $this->setNotice("Equipe criada com sucesso !");

            return $this->redirectToRoute('team_index');
        }

        return $this->render('team.form', array(
                    'team' => $team,
                    'form' => $form->createView(),
        ));
    }

    /**
     * @Breadcrumb("Edit", routeName="team_update", routeParameters={"token"="{token}"})
     * @Route("/update/{token}", name="team_update")
     * @Method({"GET", "POST"})
     */
    public function updateAction(Request $request, Team $team)
    {
        $editForm = $this->createForm('AppBundle\Form\TeamType', $team);

        $beforeMembers = clone $team->getMembers();

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid())
        {

            $this->getTeamManager()->save($team);

            $this->refreshTeamMembers($team, $beforeMembers);

            $this->setNotice("Equipe Atualizada com sucesso !");

            return $this->redirectToRoute('team_index');
        }
        return $this->render('team.form', array(
                    'team' => $team,
                    'form' => $editForm->createView(),
        ));
    }

    /**
     * @Route("/delete/{token}", name="team_delete")
     */
    public function deleteAction(Request $request, Team $team)
    {
        $resolve['result'] = 1;
        $members = $team->getMembers();
        if (count($members) > 0)
        {
            $resolve['result'] = 0;
            return new JsonResponse($resolve);
        }
        $this->getTeamManager()->delete($team);
        return new JsonResponse($resolve);
    }

    /**
     * @Method("POST")
     * @Route("/{token}/leader", name="team_leader")
     */
    public function leaderAction(Customer $member)
    {
        if (null != $team = $member->getTeam()) {

            $customerManager = $this->getCustomerManager();

            $leader = $team->getLeader();

            if ($leader && $leader->getId() != $member->getId()) {
                $leader->isLeader(false);
                $customerManager->save($leader);
            }

            $member->isLeader(!$member->isLeader());
            $customerManager->save($member);
        }

        return $this->jsonResponse([
            'is_leader' => $member->isLeader()
        ]);
    }

    /**
     * @Method("POST")
     * @Route("/fast-create", name="team_fast_create")
     */
    public function fastCreateAction(Request $request)
    {
        $name = $request->request->get('name');

        $manager = $this->getTeamManager();
        $team = $manager->create();
        $status = 'error';

        if ($team instanceof TeamInterface)
        {

            $team->setName($name)
                    ->setAccount($this->getCurrentAccount())
                    ->setEnabled(true)
            ;

            $manager->save($team);

            $status = 'success';
        }

        return new JsonResponse([
            'status' => $status,
            'data' => [
                'id' => $team->getId(),
                'name' => $team->getName()
            ]
        ]);
    }

    /**
     * Remove | Add - Team member associations
     * @param TeamInterface $team
     */
    private function refreshTeamMembers(TeamInterface $team, $beforeMembers = null)
    {
        $customerManager = $this->getCustomerManager();

        if ($beforeMembers) {
            foreach ($beforeMembers as $beforeMember) {
                if ($beforeMember instanceof BusinessInterface && !$team->getMembers()->contains($beforeMember)) {
                    $beforeMember->setTeam(null);
                    $customerManager->save($beforeMember);
                }
            }
        }

        foreach ($team->getMembers() as $member) {
            if ($member instanceof BusinessInterface && $member->isMember()) {
                $member->setTeam($team);
                $customerManager->save($member);
            }
        }
    }

}
