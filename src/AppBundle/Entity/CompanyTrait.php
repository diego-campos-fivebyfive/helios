<?php

namespace AppBundle\Entity;

trait CompanyTrait
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Customer", mappedBy="company", cascade={"persist"})
     */
    protected $employees;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $relatedEmployees;

    /**
     * @inheritDoc
     */
    public function addEmployee(BusinessInterface $employee)
    {
        if (!$this->isCompany() || !$employee->isPerson())
            $this->unsupportedContextException();

        if (!$this->employees->contains($employee)) {
            $this->employees->add($employee);
            $employee->setCompany($this);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function removeEmployee(BusinessInterface $employee)
    {
        if ($this->employees->contains($employee)) {
            $this->employees->removeElement($employee);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getEmployees()
    {
        return $this->employees;
    }

    public function addRelatedEmployee(BusinessInterface $employee){
        //dump($employee); die;
    }

    /**
     * @inheritDoc
     */
    public function setRelatedEmployees(array $relatedEmployees = [])
    {
        /*foreach($this->getMember()->getAllowedPersons() as $person){
            if(in_array($person->getId(), array_values($relatedEmployees))){
                $person->setCompany($this);
            }
        }*/
    }

    public function getRelatedEmployees()
    {
        //return $this->relatedEmployees;
    }
}