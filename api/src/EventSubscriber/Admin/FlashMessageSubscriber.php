<?php

namespace App\EventSubscriber\Admin;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class FlashMessageSubscriber implements EventSubscriberInterface
{
    protected $request;

    protected $flash;

    public function __construct(RequestStack $request, FlashBagInterface $flash)
    {
        $this->request = $request;
        $this->flash = $flash;
    }

    public function flashMessageAfterPersist(AfterEntityPersistedEvent $event): void
    {
        if ($this->request->getCurrentRequest()->isXmlHttpRequest()) {
            return;
        }

        $this->flash->add(
            'success',
            new TranslatableMessage('admin.flash_message.create', [
                '%name%' => (string) $event->getEntityInstance(),
            ], 'admin')
        );
    }

    public function flashMessageAfterUpdate(AfterEntityUpdatedEvent $event): void
    {
        if ($this->request->getCurrentRequest()->isXmlHttpRequest()) {
            return;
        }

        $this->flash->add(
            'success',
            new TranslatableMessage('admin.flash_message.update', [
                '%name%' => (string) $event->getEntityInstance(),
            ], 'admin')
        );
    }

    public function flashMessageAfterDelete(AfterEntityDeletedEvent $event): void
    {
        // @todo add unique flash for batch delete actions

        if ($this->request->getCurrentRequest()->isXmlHttpRequest()) {
            return;
        }

        $this->flash->add(
            'success',
            new TranslatableMessage('admin.flash_message.delete', [
                '%name%' => (string) $event->getEntityInstance(),
            ], 'admin')
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AfterEntityPersistedEvent::class => ['flashMessageAfterPersist'],
            AfterEntityUpdatedEvent::class => ['flashMessageAfterUpdate'],
            AfterEntityDeletedEvent::class => ['flashMessageAfterDelete'],
        ];
    }
}
