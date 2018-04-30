<?php

namespace Tests\AppBundle\Service\Business;

use AppBundle\Entity\TermInterface;
use AppBundle\Manager\TermManager;
use AppBundle\Service\Business\TermsChecker;
use Liip\FunctionalTestBundle\Test\WebTestCase;


/**
 * Class TermsCheckerTest
 * @group terms_checker
 */
class TermsCheckerTest extends WebTestCase
{

    public function testSynchronize()
    {
        /** @var TermManager $manager */
        $manager = $this->getContainer()->get('term_manager');

        foreach ($manager->findAll() as $item) {
            $manager->delete($item);
        }

        /** @var TermInterface $term */
        $term = $this->createTerm('2017-01-01');

        /** @var TermInterface $term1 */
        $term1 = $this->createTerm('2018-01-01');

        /** @var TermInterface $term2 */
        $term2 = $this->createTerm('now');

        /** @var TermInterface $term2 */
        $term2 = $this->createTerm('2019-01-01');

        /** @var TermsChecker $checker */
        $checker = $this->getContainer()->get('terms_checker');

        $terms = [
            $term->getId() => ['checkedAt' => 1483236001], // aceito
            $term1->getId() => ['checkedAt' => 1514771000], // precisa aceitar
        ];

        self::assertEquals(2, count($terms));
        $all = $checker->synchronize($terms)->all();
        self::assertEquals(4, count($all));
        self::assertEquals(3, count($terms));

        $terms = [
            $term->getId() => ['checkedAt' => 1483236001], // aceito
            $term1->getId() => ['checkedAt' => 1514771000], // precisa aceitar
        ];
        $checked = $checker->synchronize($terms)->checked();
        self::assertEquals(1, count($checked));
        self::assertEquals(3, count($terms));

        $terms = [
            $term->getId() => ['checkedAt' => 1483236001], // aceito
            $term1->getId() => ['checkedAt' => 1514771000], // precisa aceitar
        ];
        $unchecked = $checker->synchronize($terms)->unchecked();
        self::assertEquals(2, count($unchecked));
        self::assertEquals(3, count($terms));
    }

    private function createTerm($date)
    {
        /** @var TermManager $manager */
        $manager = $this->getContainer()->get('term_manager');

        /** @var TermInterface $term */
        $term = $manager->create();

        $term->setTitle('termo x');
        $term->setUrl('www');
        $term->setPublishedAt(new \DateTime($date));

        $manager->save($term);

        return $term;
    }
}
