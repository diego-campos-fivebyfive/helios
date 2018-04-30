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

use AppBundle\Entity\TermInterface;
use AppBundle\Manager\TermManager;

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
    private $manager;

    /**
     * TermsChecker constructor.
     * @param TermManager $manager
     */
    public function __construct($manager)
    {
        $this->manager = $manager;

        $this->allTerms = $this->manager->findAll();
    }

    /**
     * @param array $terms
     * @return $this
     */
    public function synchronize(array &$terms)
    {
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

        $this->terms = $terms;

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
}
