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

use AppBundle\Entity\MemberInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class MessageInterface
 * @package AppBundle\Entity\Order
 */
interface MessageInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @param $content
     * @return MessageInterface
     */
    public function setContent($content);

    /**
     * @return string
     */
    public function getContent();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param OrderInterface $order
     * @return MessageInterface
     */
    public function setOrder($order);

    /**
     * @return mixed
     */
    public function getOrder();

    /**
     * @param MemberInterface $author
     * @return MessageInterface
     */
    public function setAuthor($author);

    /**
     * @return mixed
     */
    public function getAuthor();
}
