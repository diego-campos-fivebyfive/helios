<?php

namespace AppBundle\Entity\Project;

interface BudgetSectionInterface
{

    /**
     * Get id
     *
     * @return int
     */
    public function getId();

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Set title
     *
     * @param string $title
     *
     * @return BudgetSection
     */
    public function setTitle($title);

    /**
     * Get content
     *
     * @return string
     */
    public function getContent();

    /**
     * Set content
     *
     * @param string $content
     *
     * @return BudgetSection
     */
    public function setContent($Content);

    /**
     * Get type
     *
     * @return int
     */
    public function getType();

    /**
     * Set type
     *
     * @param integer $type
     *
     * @return BudgetSection
     */
    public function setType($Type);

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition();

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return BudgetSection
     */
    public function setPosition($Position);

    /**
     * Get project
     * 
     * @return ProjectInterface
     */
    public function getProject();

    /**
     * Set project
     * 
     * @return BudgetSectionInterface
     */
    public function setProject(ProjectInterface $project);
}
