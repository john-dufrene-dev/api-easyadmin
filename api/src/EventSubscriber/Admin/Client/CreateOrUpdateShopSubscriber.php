<?php

namespace App\EventSubscriber\Admin\Client;

use App\Entity\Client\Shop;
use App\Entity\Client\ShopInfo;
use App\Service\Admin\Log\AdminLogger;
use App\Service\Traits\Entity\ShopHourTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CreateOrUpdateShopSubscriber implements EventSubscriberInterface
{
    use ShopHourTrait;

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

    public function onBeforeEntityPersistedEvent(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Shop)) {
            return;
        }

        $shop_info = new ShopInfo();
        $shop_info->setShopHour($this->getShopHourFormattedValues());
        $shop_info->setShop($entity);

        $entity->setShopInfo($shop_info);
        $entity->setCreatedAt(new \DateTime());
        $entity->setUpdatedAt(new \DateTime());

        // Logger for Shop create
        $this->logger->adminAction(
            $this->params->get('admin.log.persist_actions_entity'),
            'CREATED',
            'NEW SHOP'
        );
    }

    public function onBeforeEntityUpdatedEvent(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Shop)) {
            return;
        }

        $entity->setUpdatedAt(new \DateTime());

        // Logger for Shop update
        $this->logger->adminAction(
            $this->params->get('admin.log.persist_actions_entity'),
            'UPDATED',
            'SHOP [' .  $entity->getId() . ']'
        );
    }

    public function onAfterEntityDeletedEvent(AfterEntityDeletedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Shop)) {
            return;
        }

        // Logger for Shop delete
        $this->logger->adminAction(
            $this->params->get('admin.log.persist_actions_entity'),
            'DELETED',
            'SHOP [' .  $entity->getId() . ']'
        );
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => 'onBeforeEntityPersistedEvent',
            BeforeEntityUpdatedEvent::class => 'onBeforeEntityUpdatedEvent',
            AfterEntityDeletedEvent::class => 'onAfterEntityDeletedEvent',
        ];
    }
}
