<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class AppReviewCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:review')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $question = new ChoiceQuestion(
            'Select the module review:',
            ['Classifications', 'Users'],
            0
        );

        $question->setErrorMessage('The option is invalid');

        $module = $helper->ask($input, $output, $question);

        $output->writeln('Process review with module: ' . $module);

        //$contexts = $this->getContextManager()->findAll();

        /*$argument = $input->getArgument('argument');

        if ($input->getOption('option')) {
            // ...
        }
        $output->writeln('Command result.');
        */
    }



    /**
     * @return \Sonata\ClassificationBundle\Model\ContextManagerInterface
     */
    private function getContextManager()
    {
        return $this->getContainer()->get('sonata.classification.manager.context');
    }
}
