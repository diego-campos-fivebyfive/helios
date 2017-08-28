<?php

namespace AppBundle\Util\Validator\Constraints;

use AppBundle\Util\Validator\Document;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ContainsCnpjValidator extends ConstraintValidator
{

    public function validate($value, Constraint $constraint)
    {
        if (!Document::isCnpj($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }

}