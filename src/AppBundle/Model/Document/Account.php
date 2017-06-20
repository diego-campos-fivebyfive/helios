<?php

namespace AppBundle\Model\Document;

class Account
{
    private $sections;

    public function setSections(array $sections = [])
    {
        $this->sections = $sections;
        return $this;
    }
    
    public function getSections()
    {
        return $this->sections;
    }

    public static function fromArray(array $data)
    {
        $document = new self;
        $document->sections = $data['sections'];

        return $document;
    }

    public function toArray()
    {
        return [
            'cover' => $this->cover,
            'header' => ['logo' => $this->logo, 'text' => null],
            'sections' => [['title' => null, 'content' => null, 'order' => null ]]
        ];
    }
}