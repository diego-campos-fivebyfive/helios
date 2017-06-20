<?php

namespace AppBundle\Entity;

interface EmailAccountInterface
{
    const ERROR_UNSUPPORTED_OWNER_TYPE = 'Invalid owner type';
    const ERROR_UNSUPPORTED_DEFINITION = 'Unsupported definition';

    const TYPE_IMAP = 'imap';
    const TYPE_POP3 = 'pop3';

    const ENCODE_UTF8 = 'utf-8';

    const ENCRYPTION_SSL  = 'ssl';
    const ENCRYPTION_TLS  = 'tls';
    const ENCRYPTION_NONE = 'none';
    const ENCRYPTION_AUTO = 'auto';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getToken();

    /**
     * @param $name
     * @return EmailAccountInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param $type
     * @return EmailAccountInterface
     */
    public function setType($type);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $encode
     * @return EmailAccountInterface
     */
    public function setEncode($encode);

    /**
     * @return string
     */
    public function getEncode();

    /**
     * @return EmailAccountInterface
     */
    public function setEmail($email);

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $password
     * @return EmailAccountInterface
     */
    public function setPassword($password);

    /**
     * @return string
     */
    public function getPassword();

    /**
     * @param boolean $current
     * @return EmailAccountInterface
     */
    public function setCurrent($current);

    /**
     * @return bool
     */
    public function isCurrent();

    /**
     * @param BusinessInterface $owner
     * @return EmailAccountInterface
     */
    public function setOwner(BusinessInterface $owner);

    /**
     * @return BusinessInterface
     */
    public function getOwner();

    /**
     * @param $inputServer
     * @return EmailAccountInterface
     */
    public function setInputServer($inputServer);

    /**
     * @return string
     */
    public function getInputServer();

    /**
     * @param $inputPort
     * @return EmailAccountInterface
     */
    public function setInputPort($inputPort);

    /**
     * @return int
     */
    public function getInputPort();

    /**
     * @param $inputEncryption
     * @return EmailAccountInterface
     */
    public function setInputEncryption($inputEncryption);

    /**
     * @return string
     */
    public function getInputEncryption();

    /**
     * @param $outputServer
     * @return EmailAccountInterface
     */
    public function setOutputServer($outputServer);

    /**
     * @return string
     */
    public function getOutputServer();

    /**
     * @param $outputPort
     * @return EmailAccountInterface
     */
    public function setOutputPort($outputPort);

    /**
     * @return int
     */
    public function getOutputPort();

    /**
     * @param $outputEncryption
     * @return EmailAccountInterface
     */
    public function setOutputEncryption($outputEncryption);

    /**
     * @return int
     */
    public function getOutputEncryption();

    /**
     * @return string
     */
    public function getEmailDomain();

    /**
     * @param $option
     * @return mixed
     */
    //public function getFolderName($option);

    /**
     * @param array $folders
     * @return EmailAccountInterface
     */
    public function setFolders(array $folders = []);

    /**
     * @return array
     */
    public function getFolders();

    /**
     * @param $id
     * @return array
     */
    public function getFolder($id);

    /**
     * @return bool
     */
    public function hasFolders();

    /**
     * @return EmailAccountInterface
     */
    public function prePersist();

    /**
     * @return EmailAccountInterface
     */
    public function preUpdate();

    /**
     * @return array
     */
    public static function getTypeList();

    /**
     * @return array
     */
    public static function getEncodeList();

    /**
     * @return array
     */
    public static function getEncryptionList();
}
