<?php

namespace AppBundle\Command;

use AppBundle\Entity\BusinessInterface;
use Sonata\ClassificationBundle\Model\ContextInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * AppCompanySetOwnerCommand
 *
 * @author Claudinei Machado <cjchamado@gmail.com>
 */
class AccountSetOwnerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:account:set-owner')
            ->setDescription('Set account owner by emails')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $accountContext = $this->getContextManager()->find(BusinessInterface::CONTEXT_ACCOUNT);
        $memberContext = $this->getContextManager()->find(BusinessInterface::CONTEXT_MEMBER);

        if(!$accountContext instanceof ContextInterface)
            throw new \Exception(sprintf('Context [%s] not found. Run command [%s] before.',
                BusinessInterface::CONTEXT_ACCOUNT, 'app:fix-contexts'
            ));

        if(!$memberContext instanceof ContextInterface)
            throw new \Exception(sprintf('Context [%s] not found. Run command [%s] before.',
                BusinessInterface::CONTEXT_MEMBER, 'app:fix-contexts'
            ));


        $manager = $this->getCustomerManager();

        $accounts = $manager->findBy([
            'context' => $accountContext
        ]);

        $data = [];
        $values = [];
        foreach($accounts as $account){
            if($account instanceof BusinessInterface){
                $data[] = $account->getName();
                $values[] = $account;
            }
        }

        if(empty($data)){
            $output->writeln('No registered accounts');
            exit;
        }

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Please select company',
            $data,
            0
        );
        $question->setErrorMessage('Company %s is invalid.');

        $option = $helper->ask($input, $output, $question);

        $extract = array_search($option, $data);

        $account = $values[$extract];

        if($account instanceof BusinessInterface){

            $output->writeln(sprintf('%s is selected', $account->getName()));

            $memberQuestion = new Question('Enter the owner email: ');

            $memberQuestion->setValidator(function($answer) use($manager){

                if(!strripos($answer, '@')){
                    throw new \Exception(sprintf('The field [%s] is invalid email', $answer));
                }

                $member = $manager->findOneBy(['email' => $answer]);

                if(!$member instanceof BusinessInterface){
                    throw new \Exception(sprintf('The owner with email [%s] not found', $answer));
                }

                return $member;
            });

            $member = $helper->ask($input, $output, $memberQuestion);
            
            if($member instanceof BusinessInterface){
                if(!$member->getAccount()){
                    $output->writeln(sprintf('Error! The owner %s not have an account associated', $member->getName()));
                    exit;
                }else{

                    //$member->setAccount($account);
                    $member->getUser()->addRole('ROLE_OWNER');
                    
                    $manager->save($member);

                }

                $output->writeln(sprintf('Yes! %s is now associated with the account %s', $member->getName(), $account->getName()));
                exit;
            }

            $output->writeln(sprintf('Error! The owner %s already have an account associated', $member->getName()));
            exit;
        }

        $output->writeln(sprintf('The process was not completed'));
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
