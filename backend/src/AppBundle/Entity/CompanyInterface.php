<?php

namespace AppBundle\Entity;

interface CompanyInterface
{
    /**
     * Accepted only $this->isCompany and $employee->isPerson()
     *
     * @param BusinessInterface $employee
     * @return BusinessInterface
     */
    public function addEmployee(BusinessInterface $employee);

    /**
     * @param BusinessInterface $employee
     * @return BusinessInterface
     */
    public function removeEmployee(BusinessInterface $employee);

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getEmployees();

    /**
     * @see \AppBundle\Form\ContactType $builder->add('relatedEmployees')
     * @param \Doctrine\Common\Collections\ArrayCollection $relatedEmployees
     * @return BusinessInterface
     */
    public function setRelatedEmployees(array $relatedEmployees = []);

    /**
     * @see \AppBundle\Form\ContactType $builder->add('relatedEmployees')
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getRelatedEmployees();

    /**
     * @return string
     */
    public function getContext();
}