<?php

namespace AppBundle\Service\PhpImap;

use PhpImap\Mailbox;

abstract class MailboxFactory
{
    public static function create(PathInterface $path, CredentialInterface $credential, $attachmentDir = null){
        return new Mailbox($path->format(), $credential->getUsername(), $credential->getPassword(), $attachmentDir);
    }
}