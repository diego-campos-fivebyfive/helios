<?php

namespace AppBundle\Command;

use AppBundle\Entity\BusinessInterface;
use AppBundle\Entity\User;
use Kolina\CustomerBundle\Entity\CustomerInterface;
use Sonata\ClassificationBundle\Model\ContextInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * AppUserCreateCommand
 *
 * @author Claudinei Machado <cjchamado@gmail.com>
 */
class CreateMemberCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:create:member')
            ->setDescription('Create User Application [KolinaCustomerBundle Integration]')
        ;

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $fields = [
            'firstname' => true,
            'lastname' => true,
            'email' => true,
            'username' => true,
            'password' => true
        ];

        Helper::questions($helper, $fields, $input, $output);

        $customerManager = $this->getCustomerManager();
        $customer = $customerManager->create();

        $context = $this->getContextManager()->find(BusinessInterface::CONTEXT_MEMBER);

        if($customer instanceof BusinessInterface){

            //dump($context);

            if($context instanceof ContextInterface) {

                $customer
                        ->setContext($context)
                        ->setFirstname($fields['firstname'])
                        ->setLastname($fields['lastname'])
                        ->setEmail($fields['email'])
                ;

                $user = new User();

                $user->setUsername($fields['username'])
                    ->setEmail($fields['email'])
                    ->setPlainPassword($fields['password'])
                    ->setEnabled(true);

                $customer->setUser($user);

                try {

                    $customerManager->save($customer);

                } catch (\Exception $e) {

                    $output->writeln($e->getMessage());
                }

                if ($customer->getId()) {

                    $output->writeln(sprintf('The customer %s is successfully created!', $customer->getFirstname()));
                }
            }else{
                $output->writeln(sprintf('The context %s not found!', BusinessInterface::CONTEXT_MEMBER));
            }
        }else{

            $output->writeln('Failed to execute the process, check the customer manager');
        }
    }

    /**
     * @return \AppBundle\Entity\CustomerManager
     */
    private function getCustomerManager()
    {
        return $this->getContainer()->get('app.customer_manager');
    }

    /**
     * @return \Sonata\ClassificationBundle\Entity\ContextManager
     */
    private function getContextManager()
    {
        return $this->getContainer()->get('sonata.classification.manager.context');
    }
}