<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Term;
use AppBundle\Manager\TermManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use AppBundle\Entity\TermInterface;
use Tests\AppBundle\AppTestCase;

/**
 * @group term_manager
 */
class TermManagerTest extends WebTestCase
{
    public function testTermManager()
    {
        $this->cleanup();

        /** @var TermManager $manager */
        $manager = $this->getContainer()->get('term_manager');

        /** @var Term $term */
        $term = $manager->create();

        $term->setTitle('Teste');
        $term->setUrl('http://www.teste.com');
        $term->setCreatedAt((new \DateTime()));
        $term->setUpdatedAt(new \DateTime());

        // CREATE
        $manager->save($term);

        $this->assertNotNull($term);
        $this->assertNotNull($term->getId());
        $this->assertEquals('Teste', $term->getTitle());
        $this->assertEquals('http://www.teste.com', $term->getUrl());
        $this->assertNotNull($term->getCreatedAt());
        $this->assertNotNull($term->getUpdatedAt());

        // UPDATE
        $term->setTitle('Outro');

        $manager->save($term);

        $this->assertEquals('Outro', $term->getTitle());

        // READ
        $terms = $manager->findAll();

        $this->assertEquals(1, count($terms));

        // DELETE
        $manager->delete($term);

        $terms = $manager->findAll();

        $this->assertEquals(0, count($terms));
    }

    private function cleanup() {
        /** @var TermManager $manager */
        $manager = $this->getContainer()->get('term_manager');

        $terms = $manager->findAll();

        foreach ($terms as $term) {
            $manager->delete($term);
        }
    }
}
