<?php

namespace App\EventSubscriber\Admin\Client;

use App\Entity\Client\Shop;
use App\Entity\Customer\UserShopHistory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;

class DeleteShopSubscriber implements EventSubscriberInterface
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onBeforeEntityDeletedEvent(BeforeEntityDeletedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Shop)) {
            return;
        }

        $users = $entity->getusers();

        // Delete all Shop relations of the User
        if (count($users) !== 0) {
            foreach ($users as $user) {
                $user->setShop(null);
            }
        }

        // Delete all shops histories relations
        $shops_history_manager = $this->em->getRepository(UserShopHistory::class);
        $users_histories = $shops_history_manager->findBy(['shop_reference' => $entity->getReference()]);

        if (count($users_histories) !== 0) {
            foreach ($users_histories as $user) {
                $this->em->remove($user);
            }
            $this->em->flush();
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityDeletedEvent::class => 'onBeforeEntityDeletedEvent',
        ];
    }
}
