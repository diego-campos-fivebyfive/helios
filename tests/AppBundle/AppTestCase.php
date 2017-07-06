<?php

namespace Tests\AppBundle;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Tests\AppBundle\Entity\DataFixtures\LoadCustomerData;
use Tests\AppBundle\Entity\DataFixtures\LoadInverterData;
use Tests\AppBundle\Entity\DataFixtures\LoadMemberData;
use Tests\AppBundle\Entity\DataFixtures\LoadModuleData;
use Tests\AppBundle\Entity\DataFixtures\LoadProjectAreaData;
use Tests\AppBundle\Entity\DataFixtures\LoadProjectData;
use Tests\AppBundle\Entity\DataFixtures\LoadProjectInverterData;
use Tests\AppBundle\Entity\DataFixtures\LoadProjectModuleData;

/**
 * Class BaseTestCase
 */
class AppTestCase extends WebTestCase
{
    /**
     * @var \Doctrine\Common\DataFixtures\ProxyReferenceRepository
     */
    protected $fixtures;

    public function setUp()
    {
        $this->fixtures = $this->loadFixtures([
            // Actors
            LoadMemberData::class,
            LoadCustomerData::class,
            // Components
            LoadModuleData::class,
            LoadInverterData::class,
            LoadProjectData::class,
            LoadProjectModuleData::class,
            LoadProjectInverterData::class,
            LoadProjectAreaData::class
        ])->getReferenceRepository();
    }

    /**
     * @param $id
     * @return object
     */
    protected function getFixture($id){
        return $this->fixtures->getReference($id);
    }
}