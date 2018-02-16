<?php

/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Sices\Cache;

/**
 * Class JsonCache
 * @author Fabio Dukievicz <fabiojd47@gmail.com>
 */
class JsonCache
{
    /** @var array */
    private $data;

    /** @var string */
    private $path;

    /**
     * JsonCache constructor.
     * @param $context
     */
    function __construct($context)
    {
        $this->path = dirname(__FILE__).'/'.$context.'.cache';

        $this->data = [];

        $this->load();
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * @param $key
     * @param array $item
     * @return $this
     */
    public function add($key, $item)
    {
        if (!array_key_exists($key, $this->data)) {
            $this->data[$key] = [];
        }
        if (!in_array($item, $this->data[$key])) {
            $this->data[$key][] = $item;
        }

        $this->store();

        return $this;
    }

    /**
     * @param $key
     * @return $this
     */
    public function remove($key)
    {
        if (is_array($this->data) && array_key_exists($key, $this->data)) {
            unset($this->data[$key]);
        }

        $this->store();

        return $this;
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->data = [];

        $this->store();

        return $this;
    }

    /**
     * @param $context
     * @return JsonCache
     */
    public static function create($context)
    {
        return new self($context);
    }

    /**
     * load and transform json into array
     */
    private function load()
    {
        if (!file_exists($this->path)) {
            fopen($this->path, 'x');
        }

        if ($content = file_get_contents($this->path)) {
            $this->data = json_decode($content, true);
        }
    }

    /**
     * persist data
     */
    private function store()
    {
        $file = fopen($this->path, 'w');

        $data = null;
        if ($this->data != []) {
            $data = json_encode($this->data);
        }

        fwrite($file, $data);

        fclose($file);
    }
}
