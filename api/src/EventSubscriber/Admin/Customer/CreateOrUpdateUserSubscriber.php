<?php

namespace App\EventSubscriber\Admin\Customer;

use App\Entity\Customer\User;
use App\Entity\Customer\UserInfo;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateOrUpdateUserSubscriber implements EventSubscriberInterface
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    
    public function onBeforeEntityPersistedEvent(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof User)) {
            return;
        }

        $user_info = new UserInfo();
        $user_info->setUser($entity);
        $entity->setuserInfo($user_info);

        $password = $this->encoder->encodePassword($entity, $entity->getPassword());
        $entity->setPassword($password);
        $entity->setRoles(['ROLE__USER']);

        $entity->setCreatedAt(new \DateTime());
        $entity->setUpdatedAt(new \DateTime());

        // @todo : add user_shop_history table to have all latest shop of the User
    }

    public function onBeforeEntityUpdatedEvent(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof User)) {
            return;
        }

        if (null != $entity->getPlainPassword()) {
            $password = $this->encoder->encodePassword($entity, $entity->getPlainPassword());
            $entity->setPassword($password);
        }

        $entity->setUpdatedAt(new \DateTime());

        // @todo : add user_shop_history table to have all latest shop of the User
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => 'onBeforeEntityPersistedEvent',
            BeforeEntityUpdatedEvent::class => 'onBeforeEntityUpdatedEvent',
        ];
    }
}
