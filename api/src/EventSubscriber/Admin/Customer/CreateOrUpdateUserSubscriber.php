<?php

namespace App\EventSubscriber\Admin\Customer;

use App\Entity\Customer\User;
use App\Entity\Customer\UserInfo;
use App\Entity\Customer\UserShopHistory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateOrUpdateUserSubscriber implements EventSubscriberInterface
{
    private $encoder;

    private $em;

    public function __construct(UserPasswordHasherInterface $encoder, EntityManagerInterface $em)
    {
        $this->encoder = $encoder;
        $this->em = $em;
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

        if (!($entity instanceof User)) {
            return;
        }

        $user_info = new UserInfo();
        $user_info->setUser($entity);
        $entity->setuserInfo($user_info);

        // Insert history User Shop
        if (null !== $entity->getShop()) {
            $user_history = new UserShopHistory();
            $user_history->setUserReference($entity->getReference());
            $user_history->setShopReference($entity->getShop()->getReference());
            $this->em->persist($user_history);
            $this->em->flush();
        }

        // Encode password
        $password = $this->encoder->hashPassword($entity, $entity->getPassword());
        $entity->setPassword($password);
        $entity->setRoles(['ROLE__USER']);

        $entity->setCreatedAt(new \DateTime());
        $entity->setUpdatedAt(new \DateTime());
    }
    
    /**
     * onBeforeEntityUpdatedEvent
     *
     * @param  mixed $event
     * @return void
     */
    public function onBeforeEntityUpdatedEvent(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof User)) {
            return;
        }

        // Encode password
        if (null != $entity->getPlainPassword()) {
            $password = $this->encoder->hashPassword($entity, $entity->getPlainPassword());
            $entity->setPassword($password);
        }

        // Insert history User Shop
        if (null !== $entity->getShop()) {
            $user_history = $this->em->getRepository(UserShopHistory::class);
            $check = $user_history->findOneBy([
                'shop_reference' => $entity->getShop()->getReference(),
                'user_reference' => $entity->getReference()
            ]);

            if (!$check) {
                $user_history = new UserShopHistory();
                $user_history->setUserReference($entity->getReference());
                $user_history->setShopReference($entity->getShop()->getReference());
                $this->em->persist($user_history);
                $this->em->flush();
            }
        }

        $entity->setUpdatedAt(new \DateTime());
    }

    // @todo: remove user history linked shop if user is deleted
    
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
