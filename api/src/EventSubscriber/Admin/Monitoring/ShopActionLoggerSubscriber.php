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

    public function onAfterEntityPersistedEvent(AfterEntityPersistedEvent $event)
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

    public function onAfterEntityUpdatedEvent(AfterEntityUpdatedEvent $event)
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

    public function onAfterEntityDeletedEvent(AfterEntityDeletedEvent $event)
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

    public static function getSubscribedEvents()
    {
        return [
            AfterEntityPersistedEvent::class => 'onAfterEntityPersistedEvent',
            AfterEntityUpdatedEvent::class => 'onAfterEntityUpdatedEvent',
            AfterEntityDeletedEvent::class => 'onAfterEntityDeletedEvent',
        ];
    }
}
