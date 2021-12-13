<?php

namespace App\EventSubscriber\Admin\Security\AdminGroup;

use App\Entity\Security\AdminGroup;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;

class CreateOrUpdateAdminGroupSubscriber implements EventSubscriberInterface
{    
    /**
     * onBeforeEntityPersistedEvent
     *
     * @param  mixed $event
     * @return void
     */
    public function onBeforeEntityPersistedEvent(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof AdminGroup)) {
            return;
        }

        $entity->setCreatedAt(new \DateTime());
        $entity->setUpdatedAt(new \DateTime());
    }
    
    /**
     * onBeforeEntityUpdatedEvent
     *
     * @param  mixed $event
     * @return void
     */
    public function onBeforeEntityUpdatedEvent(BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof AdminGroup)) {
            return;
        }

        $entity->setUpdatedAt(new \DateTime());
    }
    
    /**
     * getSubscribedEvents
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => 'onBeforeEntityPersistedEvent',
            BeforeEntityUpdatedEvent::class => 'onBeforeEntityUpdatedEvent',
        ];
    }
}
