<?php

namespace Tests\AppBundle\Service\Order;


use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\UserInterface;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Tests\AppBundle\AppTestCase;
use Tests\AppBundle\Helpers\ObjectHelperTest;

/**
 * Class OrderTimelineTest
 * @group order_timeline
 */
class OrderTimelineTest extends AppTestCase
{
    use ObjectHelperTest;

    public function testCreate()
    {
        $manager = $this->manager('order');

        $master = $manager->create();
        $manager->save($master);

        $this->createMember();

        $orderTimeline = $this->getContainer()->get('order_timeline');

        $timeline = $orderTimeline->create($master);

        $this->assertNotNull($timeline);
        $this->assertEquals('AppBundle\Entity\Order\Order::1',$timeline->getTarget());
        $this->assertEquals(1,$timeline->getId());
        $this->assertEquals(0,$timeline->getAttributes()['status']);

        return [
            'timeline' => $timeline,
            'order' => $master
        ];
    }

    public function testLoad()
    {
        $create = self::testCreate();

        $timeline = $create['timeline'];
        $order = $create['order'];

        $this->assertNotNull($timeline);
        $this->assertEquals('AppBundle\Entity\Order\Order::1',$timeline->getTarget());
        $this->assertEquals(1,$timeline->getId());
        $this->assertEquals(0,$timeline->getAttributes()['status']);

        $orderTimeline = $this->getContainer()->get('order_timeline');

        $loadTimeline = $orderTimeline->load($order);

        $this->assertNotNull($loadTimeline);
        $this->assertCount(1,$loadTimeline);
        $this->assertArrayHasKey('status',$loadTimeline[0]->getAttributes());
    }

    private function createMember()
    {
        $user = $this->createUser();

        $member = $this->getFixture('member');

        $member->setFirstname('joao');
        $member->setEmail($user->getEmail());
        $member->setContext(BusinessInterface::CONTEXT_MEMBER);
        $member->setUser($user);

        $this->assertNotNull($member);

        return $member;
    }

    private function createUser()
    {
        $manager = $this->getContainer()->get('fos_user.user_manager');

        /** @var UserInterface $user */
        $user = $manager->createUser();

        $user->addRole(UserInterface::ROLE_OWNER)
            ->setEmail(self::randomString(10))
            ->setPlainPassword('123')
            ->setEnabled(1);

        $user->setUsername($user->getEmail());

        $manager->updateUser($user);

//        $client = static::createClient();
//
//        $session = $client->getContainer()->get('session');
//
//        $firewallContext = 'main';
//
//        $token = new UsernamePasswordToken('admin', null, $firewallContext, array(UserInterface::ROLE_OWNER));
//        $session->set('_security_'.$firewallContext, serialize($token));
//
//        $session->save();
//
//        $cookie = new Cookie($user->getEmail(), $user->getId());
//        $client->getCookieJar()->set($cookie);

        return $user;
    }
}
