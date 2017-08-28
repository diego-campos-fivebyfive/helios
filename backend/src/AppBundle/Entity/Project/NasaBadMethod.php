<?php

namespace AppBundle\Entity\Project;

trait NasaBadMethod
{
    /**
     * @inheritDoc
     */
    public function find($id)
    {
        $this->createBadMethodCallException();
    }

    /**
     * @inheritDoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $this->createBadMethodCallException();
    }

    /**
     * @inheritDoc
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        $this->createBadMethodCallException();
    }

    /**
     * @inheritDoc
     */
    public function findAll()
    {
        $this->createBadMethodCallException();
    }

    /**
     * @inheritDoc
     */
    public function create()
    {
        $this->createBadMethodCallException();
    }

    /**
     * @inheritDoc
     */
    public function save($entity, $andFlush = true)
    {
        $this->createBadMethodCallException();
    }

    /**
     * @inheritDoc
     */
    public function delete($entity, $andFlush = true)
    {
        $this->createBadMethodCallException();
    }

    /**
     * @throws \BadMethodCallException
     */
    private function createBadMethodCallException()
    {
        throw new \BadMethodCallException();
    }
}