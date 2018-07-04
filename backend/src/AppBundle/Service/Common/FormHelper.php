<?php

/**
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Service\Common;

use Symfony\Component\Form\Form;

/**
 * FormHelper
 *
 * @author Jonadabe de Souza Nascimento <jhonndabi.s.n@gmail.com>
 */
class FormHelper
{
    /**
     * @param Form $form
     * @param array $data
     */
    public static function setDataForm(Form &$form, array $data)
    {
        array_walk($data, ['self', 'bindData'], $form);
    }

    /**
     * @param $value
     * @param $key
     * @param Form $form
     */
    private static function bindData($value, $key, Form &$form)
    {
        $form->get($key)->setData($value);
    }
}
