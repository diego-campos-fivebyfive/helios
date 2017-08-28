<?php

namespace AppBundle\Entity;

use Sonata\ClassificationBundle\Model\CategoryInterface as BaseCategoryInterface;

interface CategoryInterface extends BaseCategoryInterface
{
    const CONTEXT_SALE_STAGE = 'sale_stage';
    const CONTEXT_CONTACT = 'contact_category';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getSortedName();

    /**
     * @param BusinessInterface $account
     * @return CategoryInterface
     */
    public function setAccount(BusinessInterface $account);

    /**
     * @return BusinessInterface
     */
    public function getAccount();
}