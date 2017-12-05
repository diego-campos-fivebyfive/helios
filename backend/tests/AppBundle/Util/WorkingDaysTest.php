<?php

include 'WorkingDays.php';

class WorkingDaysTest extends PHPUnit\Framework\TestCase
{
    public function testSingleYearWorkingDay()
    {
        //$date = new DateTime('2018-01-01');
        $workingDay = WorkingDays::create();

        $this->assertInstanceOf(WorkingDays::class, $workingDay);

        $next = $workingDay->next(5);

        $this->assertInstanceOf(DateTime::class, $next);
    }
}
