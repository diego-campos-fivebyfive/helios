<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Query\Expr\Join;
use Sonata\CoreBundle\Model\BaseEntityManager;
use AppBundle\Entity\BusinessInterface as MemberInterface;

class NotificationManager extends BaseEntityManager
{
    /**
     * @param $type
     * @param $icon
     * @param $title
     * @param $content
     * @param array $members
     */
    public function generate($type, $icon, $title, $content, array $members = [])
    {
        /** @var NotificationInterface $notification */
        $notification = $this->create();

        $notification
            ->setType($type)
            ->setIcon($icon)
            ->setTitle($title)
            ->setContent($content);

        if (!empty($members)) {
            foreach ($members as $member) {
                if ($member instanceof MemberInterface) {
                    $this->subscribe($notification, $member, false);
                }
            }
        }

        $this->save($notification);

        return $notification;
    }

    /**
     * @param NotificationInterface $notification
     * @param BusinessInterface $member
     * @param bool $save
     */
    public function subscribe(NotificationInterface &$notification, MemberInterface $member, $save = true)
    {
        if (!$member->isMember()) {
            $this->unsupportedContextException();
        }

        $subscriber = new NotificationSubscriber();

        $subscriber
            ->setNotification($notification)
            ->setSubscriber($member);

        $notification->addSubscriber($subscriber);

        if ($save) {
            $this->save($notification);
        }
    }

    /**
     * @param NotificationInterface $notification
     * @param BusinessInterface $member
     * @param bool $save
     */
    public function unsubscribe(NotificationInterface &$notification, MemberInterface $member, $save = true)
    {
        if (!$member->isMember()) {
            $this->unsupportedContextException();
        }

        if (null != $subscriber = $notification->getSubscriber($member)) {
            $notification->removeSubscriber($subscriber);
        }

        if ($save) {
            $this->save($notification);
        }
    }

    /**
     * @param BusinessInterface $member
     * @param array $parameters
     * @return array
     */
    public function subscriptions(MemberInterface $member, array $parameters = [])
    {
        $options = array_merge([
            'type' => NotificationInterface::TYPE_TIMELINE,
            'viewed' => false,
            'sort' => 'n.createdAt',
            'order' => 'desc',
            'limit' => null
        ], $parameters);

        $qb = $this->getEntityManager()->createQueryBuilder();

        $viewedWhere = $options['viewed'] ? 's.viewedAt is not null' : 's.viewedAt is null';

        $qb->select('s')
            ->from(NotificationSubscriber::class, 's')
            ->join('s.notification', 'n')
            ->join('s.subscriber', 'm', Join::WITH, $qb->expr()->eq('m.id', $member->getId()))
            ->where('n.type = :type')
            ->andWhere($viewedWhere)
            ->orderBy($options['sort'], $options['order'])
            //->groupBy('n')
            ->setParameters([
                'type' => $options['type']
            ]);

        if(null != $limit = $options['limit']){
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Only members allow subscribe notifications
     */
    private function unsupportedContextException()
    {
        throw new \InvalidArgumentException('Unsupported member context');
    }
}