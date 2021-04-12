<?php

namespace App\EventSubscriber\Api\Auth;

use App\Entity\Customer\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\Customer\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\HttpFoundation\RequestStack;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class JWTCreatedSubscriber implements EventSubscriberInterface
{
    /**
     * requestStack
     *
     * @var mixed
     */
    private $requestStack;

    /**
     * security
     *
     * @var mixed
     */
    private $em;

    /**
     * __construct
     *
     * @param  mixed $requestStack
     * @param  mixed $security
     * @return void
     */
    public function __construct(RequestStack $requestStack, EntityManagerInterface $em)
    {
        $this->requestStack = $requestStack;
        $this->em = $em;
    }

    /**
     * onJWTCreated
     *
     * @param  mixed $event
     * @return void
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();

        $payload       = $event->getData();
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $payload['username']]);

        // Add your custom values to Payload
        $payload['ip'] = $request->getClientIp();
        
        if (null !== $user) {
            $payload['is_active'] = $user->getIsActive();
            $payload['is_verified'] = $user->getIsVerified();
            // @todo : Add shop item selected for user
            // if(null !== $user->getShop())
            // $payload['shop'] = $user->getShop();
        }

        $event->setData($payload);
    }

    /**
     * getSubscribedEvents
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::JWT_CREATED => ['onJWTCreated'],
        ];
    }
}
