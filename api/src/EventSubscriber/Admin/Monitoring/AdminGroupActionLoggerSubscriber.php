<?php

namespace App\EventSubscriber\Admin\Monitoring;

use App\Entity\Security\AdminGroup;
use App\Service\Admin\Log\AdminLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AdminGroupActionLoggerSubscriber implements EventSubscriberInterface
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

        if ($entity instanceof AdminGroup) {

            // Logger for AdminGroup create
            $this->logger->adminAction(
                $this->params->get('admin.log.persist_actions_entity'),
                'CREATED',
                'NEW ADMIN_GROUP'
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

        if ($entity instanceof AdminGroup) {

            // Logger for AdminGroup update
            $this->logger->adminAction(
                $this->params->get('admin.log.persist_actions_entity'),
                'UPDATED',
                'ADMIN_GROUP [' .  $entity->getId() . ']'
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

        if ($entity instanceof AdminGroup) {

            // Logger for AdminGroup delete
            $this->logger->adminAction(
                $this->params->get('admin.log.persist_actions_entity'),
                'DELETED',
                'ADMIN_GROUP [' .  $entity->getId() . ']'
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
