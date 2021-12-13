<?php

namespace App\EventSubscriber\Admin\Security\Admin;

use App\Entity\Security\Admin;
use App\Entity\Security\AdminConfig;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateOrUpdateAdminSubscriber implements EventSubscriberInterface
{
    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    
    /**
     * onBeforeEntityPersistedEvent
     *
     * @param  mixed $event
     * @return void
     */
    public function onBeforeEntityPersistedEvent(BeforeEntityPersistedEvent $event): void
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof Admin)) {
            return;
        }

        $password = $this->encoder->hashPassword($entity, $entity->getPassword());
        $entity->setPassword($password);

        $admin_config = new AdminConfig();
        $admin_config->setAdmin($entity);

        // @todo : add default AdminConfig values
        $entity->setAdminConfig($admin_config);

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

        if (!($entity instanceof Admin)) {
            return;
        }

        if (null != $entity->getPlainPassword()) {
            $password = $this->encoder->hashPassword($entity, $entity->getPlainPassword());
            $entity->setPassword($password);
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
