<?php

namespace App\EventSubscriber\Admin\Client;

use App\Entity\Client\Shop;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;

class DeleteShopSubscriber implements EventSubscriberInterface
{
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

        // @todo : add user_shop_history table to have all latest shop of the User
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityDeletedEvent::class => 'onBeforeEntityDeletedEvent',
        ];
    }
}
