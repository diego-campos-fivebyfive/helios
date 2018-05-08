<?php
/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Business;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\Term;
use AppBundle\Entity\TermInterface;
use AppBundle\Manager\AccountManager;
use AppBundle\Manager\TermManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TermsChecker
{
    /**
     * @var array
     */
    private $terms;

    /**
     * @var array
     */
    private $allTerms;

    /**
     * @var TermManager
     */
    private $termManager;

    /**
     * @var AccountManager
     */
    private $accountManager;

    /**
     * TermsChecker constructor.
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->accountManager = $container->get('account_manager');

        $this->termManager = $container->get('term_manager');

        $this->allTerms = $this->termManager->findAll();
    }

    /**
     * @param AccountInterface $account
     * @return $this
     */
    public function synchronize(AccountInterface $account)
    {
        $terms = $account->getTerms() ? $account->getTerms() : [];

        /** @var TermInterface $term */
        foreach ($this->allTerms as $term) {
            $timestamp = $term->getPublishedAt()->getTimestamp();
            $id = $term->getId();

            $currentTimestamp = (new \DateTime())->getTimestamp();

            if ($timestamp <= $currentTimestamp) {
                if (!array_key_exists($id, $terms) || $terms[$id] <= $timestamp) {
                    $terms[$id] = null;
                }
            }
        }

        $this->cleanup($terms);

        $this->terms = $terms;

        $account->setTerms($this->terms);

        $this->accountManager->save($account);

        return $this;
    }

    /**
     * @return array
     */
    public function checked()
    {
        return array_filter($this->terms, function ($term) {
            return !is_null($term);
        });
    }

    /**
     * @return array
     */
    public function unchecked()
    {
        return array_filter($this->terms, function ($term) {
            return is_null($term);
        });
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->allTerms;
    }

    /**
     * @param $terms
     */
    private function cleanup(&$terms)
    {
        $allTermsIds = [];

        /** @var Term $term */
        foreach ($this->allTerms as $term) {
            $allTermsIds[] = $term->getId();
        }

        $deletedTerms = array_diff(array_keys($terms), $allTermsIds);

        foreach ($deletedTerms as $id) {
            unset($terms[$id]);
        }
    }
}
