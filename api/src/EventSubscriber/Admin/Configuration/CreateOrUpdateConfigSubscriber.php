<?php

namespace App\EventSubscriber\Admin\Configuration;

use App\Entity\Configuration\Config;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;

class CreateOrUpdateConfigSubscriber implements EventSubscriberInterface
{
    public function onBeforeEntityUpdatedEvent(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Config)) {
            return;
        }

        $entity->setUpdatedAt(new \DateTime());
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityUpdatedEvent::class => 'onBeforeEntityUpdatedEvent',
        ];
    }
}
