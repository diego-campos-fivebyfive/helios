<?php

namespace Tests\AppBundle\Util\Validator;

use AppBundle\Util\Validator\Document;
use Liip\FunctionalTestBundle\Test\WebTestCase;

/**
 * Class ValidatorTest
 * @group validator
 */
class ValidatorTest extends WebTestCase
{
    public function testCnpnAndCpf()
    {
        $cnpjValido = '24.557.317/0001-91';
        $cnpjInvalido = '25.333.444/2547-88';

        $this->assertTrue(Document::isCnpj($cnpjValido));
        $this->assertFalse(Document::isCnpj($cnpjInvalido));

        $this->assertTrue(Document::isCpf('063.037.269-17'));
        $this->assertFalse(Document::isCpf('5454554544545455'));
    }
}