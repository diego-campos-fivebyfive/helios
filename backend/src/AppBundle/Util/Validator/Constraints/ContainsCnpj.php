<?php


namespace AppBundle\Util\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class ContainsCnpj extends Constraint
{

    public $message = 'O CNPJ "{{ string }}" não é válido!';

}