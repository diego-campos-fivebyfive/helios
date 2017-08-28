<?php

namespace AppBundle\Command;

use AppBundle\Entity\BusinessInterface;
use Sonata\ClassificationBundle\Model\ContextInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AppFixContextsCommand extends ContainerAwareCommand
{
    private $contexts = [
        BusinessInterface::CONTEXT_ACCOUNT => [
            'name' => 'Platform accounts context',
            'enabled' => true
        ],
        BusinessInterface::CONTEXT_MEMBER => [
            'name' => 'Account members context',
            'enabled' => true
        ],
        BusinessInterface::CONTEXT_COMPANY => [
            'name' => 'Account company context',
            'enabled' => true
        ],
        BusinessInterface::CONTEXT_PERSON => [
            'name' => 'Account person context',
            'enabled' => true
        ],
        'contact_category' => [
            'name' => 'Contact category context',
            'enabled' => true
        ],
        'sale_stage' => [
            'name' => 'Sale stages context',
            'enabled' => true
        ]
    ];

    protected function configure()
    {
        $this
            ->setName('app:fix-contexts')
            ->setDescription('Fix all application contexts')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manager = $this->getContextManager();

        $qb = $manager->getEntityManager()->createQueryBuilder();

        $qb->select('c')->from('AppBundle:Context', 'c')->where($qb->expr()->in('c.id', array_keys($this->contexts)));

        $contexts = $qb->getQuery()->getResult();
        $ids = array_map('current', $contexts);
        $add = [];

        $count = 1;
        foreach($this->contexts as  $id => $data){

            if(!in_array($id, $ids)){

                $context = $manager->create();

                $context->setId($id);
                $context->setName($data['name']);
                $context->setEnabled($data['enabled']);
                
                $manager->save($context);

                $add[] = $context->getId();
            }

            $count++;
        }

        if(count($add)) {
            $output->writeln(sprintf('%s new contexts: %s', count($add), implode(', ', $add)));
        }else{
            $output->writeln('All contexts are fixed!');
        }
    }

    /**
     * @return \Sonata\ClassificationBundle\Entity\ContextManager
     */
    private function getContextManager()
    {
        return $this->getContainer()->get('sonata.classification.manager.context');
    }
}
