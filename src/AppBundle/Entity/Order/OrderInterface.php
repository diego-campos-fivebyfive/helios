<?php
/*
 * This file is part of the SicesSolar package.
 *
 * (c) SicesSolar <http://sicesbrasil.com.br/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Entity\Order;

use AppBundle\Entity\AccountInterface;
use AppBundle\Entity\Component\ProjectInterface;
use Doctrine\ORM\Mapping as ORM;

interface OrderInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param $account
     * @return OrderInterface
     */
    public function setAccount($account);

    /**
     * @return AccountInterface
     */
    public function getAccount();

    /**
     * @param $status
     * @return int
     */
    public function setStatus($status);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param ProjectInterface $project
     * @return OrderInterface
     */
    public function addProject(ProjectInterface $project);

    /**
     * @param ProjectInterface $project
     * @return OrderInterface
     */
    public function removeProject(ProjectInterface $project);

    /**
     * @return ProjectInterface
     */
    public function getProjects();
}