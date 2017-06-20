<?php

namespace AppBundle\Command;

use AppBundle\Entity\BusinessInterface;
use Kolina\CustomerBundle\Entity\CustomerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * AppCompanyCreateCommand
 *
 * @author Claudinei Machado <cjchamado@gmail.com>
 */
class CreateAccountCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:create:account')
            ->setDescription('Create a new account registry')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $fields = [
            'firstname' => true,
            'lastname' => false,
            'email' => true,
            'phone' => true,
            'state' => true
        ];

        Helper::questions($helper, $fields, $input, $output);

        $manager = $this->getCustomerManager();

        $company = $manager->create();

        $context = $this->getContextManager()->find(BusinessInterface::CONTEXT_ACCOUNT);

        if($company instanceof CustomerInterface){

            $company
                    //->setType(BusinessInterface::TYPE_COMPANY)
                    ->setFirstname($fields['firstname'])
                    ->setLastname($fields['lastname'])
                    ->setEmail($fields['email'])
                    ->setPhone($fields['phone'])
                    ->setState($fields['state'])
                    ->setContext($context)
            ;

            try {

                $manager->save($company);

            }catch (\Exception $e){

                $output->writeln($e->getMessage());
            }

            if($company->getId()) {

                $output->writeln(sprintf('The account %s is successfully created!', $company->getFirstname()));
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
