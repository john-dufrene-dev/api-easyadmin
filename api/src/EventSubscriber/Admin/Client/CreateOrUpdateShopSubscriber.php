<?php

namespace App\EventSubscriber\Admin\Client;

use App\Entity\Client\Shop;
use App\Entity\Client\ShopInfo;
use App\Service\Traits\Entity\ShopHourTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;

class CreateOrUpdateShopSubscriber implements EventSubscriberInterface
{
    use ShopHourTrait;
    
    /**
     * onBeforeEntityPersistedEvent
     *
     * @param  mixed $event
     * @return void
     */
    public function onBeforeEntityPersistedEvent(BeforeEntityPersistedEvent $event): void
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

        if (!($entity instanceof Shop)) {
            return;
        }

        $entity->setUpdatedAt(new \DateTime());
    }

    // @todo : add user_shop_history table to have all latest shop of the User
    
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
