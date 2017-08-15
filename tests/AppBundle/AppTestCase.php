<?php

namespace Tests\AppBundle;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Tests\AppBundle\Entity\DataFixtures as Fixtures;

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
            Fixtures\AccountData::class,
            Fixtures\LoadMemberData::class,
            Fixtures\LoadCustomerData::class,

            // Components
            Fixtures\Component\MakerData::class,
            Fixtures\Component\ExtraProductData::class,
            Fixtures\Component\ExtraServiceData::class,
            Fixtures\Component\ModuleData::class,
            Fixtures\Component\InverterData::class,
            Fixtures\Component\StringBoxData::class,
            Fixtures\Component\VarietyData::class,
            Fixtures\LoadProjectData::class,
            Fixtures\LoadProjectModuleData::class,
            Fixtures\LoadProjectInverterData::class,
            Fixtures\LoadProjectAreaData::class
        ])->getReferenceRepository();
    }

    /**
     * @param $id
     * @return object
     */
    protected function getFixture($id){
        return $this->fixtures->getReference($id);
    }

    /**
     * @param $id
     * @return object|\AppBundle\Manager\AbstractManager
     */
    protected function manager($id)
    {
        return $this->getContainer()->get(sprintf('%s_manager', $id));
    }
}