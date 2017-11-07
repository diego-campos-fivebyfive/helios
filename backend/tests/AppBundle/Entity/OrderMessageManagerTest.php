<?php
/**
 * Created by PhpStorm.
 * User: kolinalabs
 * Date: 07/11/17
 * Time: 11:14
 */

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\Order\MessageInterface;
use AppBundle\Entity\Order\OrderInterface;
use AppBundle\Entity\UserInterface;
use Tests\AppBundle\AppTestCase;

/**
 * Class OrderMessageManagerTest
 * @group order_message
 */
class OrderMessageManagerTest extends AppTestCase
{
    public function testOrderMessage()
    {
        $message = $this->createMessage();

        $this->assertNotNull($message->getId());
    }

    private function createMessage()
    {
        $container = $this->getContainer();

        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $container->get('fos_user.user_manager');
        $manager = $this->manager('member');

        $user = $userManager->createUser();

        $user->setEmail('joao@joao.com')
            ->setUsername('joao@joao.com')
            ->setPlainPassword(uniqid())
            ->setRoles([
                UserInterface::ROLE_PLATFORM_COMMERCIAL
            ]);

        $member = $manager->create();
        $member->setFirstname('joao');
        $member->setEmail('joao@joao.com');
        $member->setContext(BusinessInterface::CONTEXT_MEMBER);
        $member->setUser($user);

        $manager->save($member);

        $manager = $this->manager('order_message');

        /** @var MessageInterface $message */
        $message = $manager->create();

        $orderManager = $this->getOrderManager();
        /** @var OrderInterface $order */
        $order = $orderManager->create();
        $order->addMessage($message);
        $orderManager->save($order);

        $messages = 'Teste Mensagem - teste 1';


        $message
            ->setOrder($order)
            ->setAuthor($member)
            ->setContent($messages);

        $manager->save($message);

        return $message;
    }

    /**
     * @return \AppBundle\Manager\OrderManager|object
     */
    private function getOrderManager()
    {
        return $this->getContainer()->get('order_manager');
    }
}
