<?php

namespace AppBundle\Service\Util;

use AppBundle\Manager\CustomerManager as ContactManager;

class ContactManipulator
{
    /**
     * @var ContactManager
     */
    private $manager;

    /**
     * @inheritDoc
     */
    public function __construct(ContactManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param array $data
     * @param bool $save
     * @return mixed|object|\AppBundle\Entity\Customer
     */
    public function fromArray(array $data, $save  = true)
    {
        $contact = $this->manager->create();

        foreach ($data as $property => $value){
            $setter = 'set' . ucfirst($property);
            $contact->$setter($value);
        }

        $this->manager->save($contact, $save);

        return $contact;
    }

    /**
     * @param array $collection
     * @return array
     */
    public function fromCollection(array $collection)
    {
        $contacts = [];
        foreach ($collection as $key => $data){
            $contacts[] = $this->fromArray($data, ($key == count($collection)-1));
        }

        return $contacts;
    }
}