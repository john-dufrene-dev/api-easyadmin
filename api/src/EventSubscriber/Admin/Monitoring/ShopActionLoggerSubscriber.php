<?php

namespace App\EventSubscriber\Admin\Monitoring;

use App\Entity\Client\Shop;
use App\Service\Admin\Log\AdminLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ShopActionLoggerSubscriber implements EventSubscriberInterface
{
    /**
     * logger
     *
     * @var mixed
     */
    protected $logger;

    /**
     * params
     *
     * @var mixed
     */
    protected $params;

    public function __construct(AdminLogger $adminLogger, ParameterBagInterface $params)
    {
        $this->logger = $adminLogger;
        $this->params = $params;
    }
    
    /**
     * onAfterEntityPersistedEvent
     *
     * @param  mixed $event
     * @return void
     */
    public function onAfterEntityPersistedEvent(AfterEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if ($entity instanceof Shop) {

            // Logger for Shop create
            $this->logger->adminAction(
                $this->params->get('admin.log.persist_actions_entity'),
                'CREATED',
                'NEW SHOP'
            );
        }

        return;
    }
    
    /**
     * onAfterEntityUpdatedEvent
     *
     * @param  mixed $event
     * @return void
     */
    public function onAfterEntityUpdatedEvent(AfterEntityUpdatedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if ($entity instanceof Shop) {

            // Logger for Shop update
            $this->logger->adminAction(
                $this->params->get('admin.log.persist_actions_entity'),
                'UPDATED',
                'SHOP [' .  $entity->getId() . ']'
            );
        }

        return;
    }
    
    /**
     * onAfterEntityDeletedEvent
     *
     * @param  mixed $event
     * @return void
     */
    public function onAfterEntityDeletedEvent(AfterEntityDeletedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if ($entity instanceof Shop) {

            // Logger for Shop delete
            $this->logger->adminAction(
                $this->params->get('admin.log.persist_actions_entity'),
                'DELETED',
                'SHOP [' .  $entity->getId() . ']'
            );
        }

        return;
    }
    
    /**
     * getSubscribedEvents
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            AfterEntityPersistedEvent::class => 'onAfterEntityPersistedEvent',
            AfterEntityUpdatedEvent::class => 'onAfterEntityUpdatedEvent',
            AfterEntityDeletedEvent::class => 'onAfterEntityDeletedEvent',
        ];
    }
}
