<?php

use Tests\AppBundle\AppTestCase;

/**
 * @group processor_events
 */
class ProcessorTest extends AppTestCase
{
    private $events = [
        //order1
        '000002672' => [
            0 => [
                "code" => '542',
                "document" => '17302990000115',
                "serial" => '115',
                "invoice" => '000002672',
                "event" => '000',
                "date" => '20022018',
                "time" => '1330'
            ],
            1 => [
                "code" => '542',
                "document" => '17774501000128',
                "serial" => '128',
                "invoice" => '000002672',
                "event" => '001',
                "date" => '20022018',
                "time" => '1345',
            ],
            2 => [
                "code" => '542',
                "document" => '17774501000128',
                "serial" => '128',
                "invoice" => '000002672',
                "event" => '002',
                "date" => '20022018',
                "time" => '1345'
            ]
        ],
        //order2
        "000012330" => [
            0 => [
                "code" => '542',
                "document" => '17774501000128',
                "serial" => '128',
                "invoice" => '000012330',
                "event" => '000',
                "date" => '20022018',
                "time" => '1345'
            ]
        ],
        //order3
        "000012344" => [
            0 => [
                "code" => '542',
                "document" => '17774501000128',
                "serial" => '128',
                "invoice" => '000012344',
                "event" => '000',
                "date" => '20022018',
                "time" => '1345'
            ],
            1 => [
                "code" => '542',
                "document" => '17774501000128',
                "serial" => '128',
                "invoice" => '000012344',
                "event" => '001',
                "date" => '20022018',
                "time" => '1345'
            ],
            2 => [
                "code" => '542',
                "document" => '17774501000128',
                "serial" => '128',
                "invoice" => '000012344',
                "event" => '002',
                "date" => '20022018',
                "time" => '1345'
            ],
            3 => [
                "code" => '542',
                "document" => '17774501000128',
                "serial" => '128',
                "invoice" => '000012344',
                "event" => '031',
                "date" => '20022018',
                "time" => '1219'
            ],
            4 => [
                "code" => '542',
                "document" => '17774501000128',
                "serial" => '128',
                "invoice" => '000012344',
                "event" => '150',
                "date" => '20022018',
                "time" => '1345'
            ]
        ]
    ];

    public function testProcessor()
    {
        $order1 = $this->createOrder(8, '000002672');
        $order2 = $this->createOrder(8, '000012330');
        $order3 = $this->createOrder(8, '000012344');

        self::assertEquals('000002672', $order1->getInvoiceNumber());
        self::assertEquals(8, $order1->getStatus());

        self::assertEquals(1, $order1->getId());
        self::assertEquals(2, $order2->getId());
        self::assertEquals(3, $order3->getId());

        $processor = $this->getContainer()->get('proceda_processor');

        $processor->processEvents($this->events);

        self::assertEquals(10, $order1->getStatus());
        self::assertEquals(9, $order2->getStatus());
        self::assertEquals(10, $order3->getStatus());
    }

    private function createOrder($status, $invoiceNumber)
    {
        $manager = $this->manager('order');

        /** @var \AppBundle\Entity\Order\Order $order */
        $order = $manager->create();
        $order->setStatus($status);
        $order->setInvoiceNumber($invoiceNumber);
        $manager->save($order);
        return $order;
    }
}
