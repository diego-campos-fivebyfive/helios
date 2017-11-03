<?php

namespace Tests\AppBundle\Service\Component;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Symfony\Component\HttpFoundation\Request;
use Tests\AppBundle\AppTestCase;

/**
 * Class MultiQueryTest
 * @group component_query
 */
class MultiQueryTest extends AppTestCase
{
    public function testPaginationReturnForAllComponentFamilies()
    {
        $modules = $this->getModules();
        $inverters = $this->manager('inverter')->findAll();
        $stringBoxes = $this->manager('string_box')->findAll();
        $structures = $this->manager('structure')->findAll();
        $varieties = $this->manager('variety')->findAll();

        $count = count(array_merge($modules, $inverters, $stringBoxes, $structures, $varieties));

        $request = Request::createFromGlobals();

        $query = $this->service('component_query');

        $pagination = $query->fromRequest($request);

        $this->assertInstanceOf(SlidingPagination::class, $pagination);
        $this->assertCount($count, $pagination);
    }

    public function testPaginationReturnForSingleComponentFamily()
    {
        $_GET['family'] = 'module';

        $modules = $this->getModules();

        $request = Request::createFromGlobals();

        $query = $this->service('component_query');

        $pagination = $query->fromRequest($request);

        $this->assertInstanceOf(SlidingPagination::class, $pagination);
        $this->assertCount(count($modules), $pagination);
    }

    public function testPaginationReturnForSingleComponentFamilyAndLikeSearch()
    {
        $_GET['family'] = 'module';

        $modules = $this->getModules();

        $module = $modules[0];

        $description = $module->getDescription();
        $descriptionLiked = substr($description, 5, 15);

        // Check description liked is less than description and...
        // Check descriptionLiked is part of description
        $this->assertLessThan($descriptionLiked, $description);
        $this->assertContains($descriptionLiked, $description);

        // Add descriptionLiked on global vars
        $_GET['like'] = $descriptionLiked;

        $request = Request::createFromGlobals();

        $query = $this->service('component_query');

        $pagination = $query->fromRequest($request);

        $this->assertInstanceOf(SlidingPagination::class, $pagination);
        $this->assertCount(count($modules), $pagination);

        // Test with no existent description
        $_GET['like'] = 'I hope this is never part of a description';

        $request = Request::createFromGlobals();
        $pagination = $query->fromRequest($request);

        $this->assertInstanceOf(SlidingPagination::class, $pagination);
        $this->assertCount(0, $pagination);
    }

    /**
     * @return array
     */
    private function getModules()
    {
        return $this->manager('module')->findAll();
    }
}
