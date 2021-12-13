<?php

namespace App\EventSubscriber\Admin\Configuration;

use App\Entity\Configuration\Config;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;

class CreateOrUpdateConfigSubscriber implements EventSubscriberInterface
{    
    /**
     * onBeforeEntityUpdatedEvent
     *
     * @param  mixed $event
     * @return void
     */
    public function onBeforeEntityUpdatedEvent(BeforeEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Config)) {
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
            BeforeEntityUpdatedEvent::class => 'onBeforeEntityUpdatedEvent',
        ];
    }
}
