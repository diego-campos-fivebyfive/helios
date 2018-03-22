<?php

namespace Tests\AppBundle\Service\Slack;

use AppBundle\Service\Slack\ExceptionNotifier;
use Tests\App\AppTest;

/**
 * @group exception_notifier
 */
class ExceptionNotifierTest extends AppTest
{
    public function testDefaultScenario()
    {
        $notifier = new ExceptionNotifier('developers');
        $exception = new \Exception(sprintf('Slack test notification %s', get_class($this)));

        $status = $notifier->notify($exception);

        $this->assertEquals(200, $status);
    }
}
