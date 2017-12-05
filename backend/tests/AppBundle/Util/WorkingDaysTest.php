<?php

namespace Tests\AppBundle\Util;

use AppBundle\Util\WorkingDays;
use Tests\AppBundle\AppTestCase;

/**
 * Class WorkingDaysTest
 * @group util_working_days
 */
class WorkingDaysTest extends AppTestCase
{
    public function testSingleYearWorkingDay()
    {
        $workingDay = WorkingDays::create();

        $this->assertInstanceOf(WorkingDays::class, $workingDay);

        $next = $workingDay->next(5);

        $this->assertInstanceOf(\DateTime::class, $next);
    }
}
