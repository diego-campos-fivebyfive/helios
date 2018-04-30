<?php

namespace AppBundle\Controller;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Term;
use AppBundle\Manager\AccountManager;
use AppBundle\Manager\TermManager;
use AppBundle\Service\Business\TermsChecker;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * @Route("/api/v1/terms")
 */
class TermController extends AbstractController
{
    /**
     * @Route("/", name="list_terms_account")
     * @Method("get")
     */
    public function getTermsAction(Request $request)
    {
        /** @var TermManager $termManager */
        $termManager = $this->get('term_manager');

        $qb = $termManager->createQueryBuilder();

        $qb->andWhere('DATE_DIFF(t.updatedAt, CURRENT_DATE()) <= 0');

        $accountTerms = $this->account()->getTerms() ? $this->account()->getTerms() : [];

        /** @var TermsChecker $termChecker */
        $termChecker = $this->get('terms_checker');

        $uncheckedTerms = $termChecker->synchronize($accountTerms)->unchecked();

        $itemsPerPage = 10;
        $pagination = $this->getPaginator()->paginate(
            $qb->getQuery(),
            $request->query->getInt('page', 1), $itemsPerPage
        );

        $data = $this->formatCollection($pagination);

        $terms = $data['results'];
        /** @var Term $term */
        foreach ($terms as $key => $term) {
            if (array_key_exists($term['id'], $uncheckedTerms)) {
                $data['results'][$key]['isAgree'] = false;
            } else {
                $data['results'][$key]['isAgree'] = true;
            }
        }

        return $this->json($data, Response::HTTP_OK);
    }

    /**
     * @Route("/agree/{id}", name="agree_term_account")
     * @Method("post")
     */
    public function postAgreeTermAction(Term $term) {
        $account = $this->account();

        $accountTerms = $account->getTerms() ? $account->getTerms() : [];

        /** @var AccountManager $accountManager */
        $accountManager = $this->get('account_manager');

        $currentTimestamp = (new \DateTime())->getTimestamp();

        $accountTerms[$term->getId()] = $currentTimestamp;

        $account->setTerms($accountTerms);

        $accountManager->save($account);
    }

    /**
    * @param $termCollection
    * @return array
    */
    private function formatEntity($termCollection)
    {
        return array_map(function(Term $term) {
            /** @var \DateTime $createDate */
            $createDate = $term->getCreatedAt()->format('Y-m-d H:i:s ');
            $updatedAt = $term->getUpdatedAt()->format('Y-m-d H:i:s ');

            return [
                'id' => $term->getId(),
                'title' => $term->getTitle(),
                'url' => $term->getUrl(),
                'updatedAt' => $updatedAt,
                'createdAt' => $createDate
            ];
        }, $termCollection);
    }

    /**
     * @param $pagination
     * @param $position
     * @return bool|string
     */
    private function getPaginationLinks($pagination, $position)
    {
        if ($position == 'previous') {
            return $pagination['current'] > 1 ? "/terms/?page={$pagination[$position]}" : false;
        }

        if ($position == 'next') {
            return $pagination['current'] < $pagination['last'] ? "/terms/?page={$pagination[$position]}" : false;
        }

        return "/terms/?page={$pagination[$position]}";
    }

    /**
     * @param $collection
     * @return array
     */
    private function formatCollection($collection)
    {
        $pagination = $collection->getPaginationData();

        return [
            'page' => [
                'total' => $pagination['pageCount'],
                'current'=> $pagination['current'],
                'links' => [
                    'prev' => $this->getPaginationLinks($pagination, 'previous'),
                    'self' => $this->getPaginationLinks($pagination, 'current'),
                    'next' => $this->getPaginationLinks($pagination, 'next')
                ]
            ],
            'size' => $pagination['totalCount'],
            'limit' => $pagination['numItemsPerPage'],
            'results' => $this->formatEntity($collection->getItems())
        ];
    }
}
