<?php

namespace AppBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * AppUserCreateCommand
 *
 * @author Claudinei Machado <cjchamado@gmail.com>
 */
class Helper
{
    /**
     * @param $helper
     * @param array $fields
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public static function questions($helper, array &$fields, InputInterface $input, OutputInterface $output)
    {
        foreach($fields as $field => $data){

            $label = ucfirst($field);
            $question = new Question(sprintf('%s: ', $label));

            $question->setValidator(function($answer) use($label, $data){

                if($data && (!$answer || 0 == strlen($answer))){
                    throw new \Exception(sprintf('The field [%s] is required', $label));
                }
                if($data && ('Email' == $label && !strripos($answer, '@'))){
                    throw new \Exception(sprintf('The field [%s] is invalid', $label));
                }

                return $answer;
            });

            $fields[$field] = $helper->ask($input, $output, $question);
        }
    }
}