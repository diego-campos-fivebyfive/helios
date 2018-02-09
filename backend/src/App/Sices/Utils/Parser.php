<?php

namespace App\Sices\Utils;

class Parser
{
    /**
     * @var array
     */
    private $mapping;

    /**
     * Parser constructor.
     * @param array $mapping
     */
    private function __construct(array $mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * @param string $base
     * @return array
     */
    public function parse(string $base)
    {
        $data = [];
        $offset = 0;
        foreach ($this->mapping as $key => $size){
            $data[$key] = substr($base, $offset, $size);
            $offset += $size;
        }

        return $data;
    }

    /**
     * @param array $mapping
     * @param string|null $base
     * @return Parser|array
     */
    public static function from(array $mapping, string $base = null)
    {
        $parser = new self($mapping);

        if(!is_null($base)){
            return $parser->parse($base);
        }

        return $parser;
    }
}
