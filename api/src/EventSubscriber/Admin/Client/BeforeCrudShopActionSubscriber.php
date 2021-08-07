<?php

namespace App\EventSubscriber\Admin\Client;

use App\Entity\Client\Shop;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use App\Service\Admin\Permissions\PermissionsAdmin;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;

class BeforeCrudShopActionSubscriber implements EventSubscriberInterface
{
    protected $pms;

    public function __construct(PermissionsAdmin $pms)
    {
        $this->pms = $pms;
    }

    public function onBeforeGetOrEditOrDeleteEntity(BeforeCrudActionEvent $event)
    {
        $context = $event->getAdminContext();

        if (!($context->getEntity()->getInstance() instanceof Shop)) {
            return;
        }

        //DETAIL
        if ($context->getCrud()->getCurrentPage() === Crud::PAGE_DETAIL) {

            if ($this->pms->isAdmin($context->getUser())) {
                return;
            }

            if ($this->pms->canUseActions($context->getUser(), 'SHOP', 'DETAIL')) {
                foreach ($context->getEntity()->getInstance()->getAdmins() as $admin) {
                    if ($admin->getUuid()->toRfc4122() === $context->getUser()->getUuid()->toRfc4122()) {
                        return;
                    }
                }
            }

            if (
                $this->pms->canUseOwners($context->getUser(), 'SHOP', 'DETAIL')
                && $this->pms->canUseActions($context->getUser(), 'SHOP', 'DETAIL')
            ) {
                return;
            }

            throw new ForbiddenActionException($context);
        }

        //EDIT
        if ($context->getCrud()->getCurrentPage() === Crud::PAGE_EDIT) {

            if ($this->pms->isAdmin($context->getUser())) {
                return;
            }

            if ($this->pms->canUseActions($context->getUser(), 'SHOP', 'EDIT')) {
                foreach ($context->getEntity()->getInstance()->getAdmins() as $admin) {
                    if ($admin->getUuid()->toRfc4122() === $context->getUser()->getUuid()->toRfc4122()) {
                        return;
                    }
                }
            }

            if (
                $this->pms->canUseOwners($context->getUser(), 'SHOP', 'EDIT')
                && $this->pms->canUseActions($context->getUser(), 'SHOP', 'EDIT')
            ) {
                return;
            }

            throw new ForbiddenActionException($context);
        }

        //DELETE
        if ($context->getCrud()->getCurrentAction() === Action::DELETE) {

            if ($this->pms->isAdmin($context->getUser())) {
                return;
            }

            if ($this->pms->canUseActions($context->getUser(), 'SHOP', 'DELETE')) {
                foreach ($context->getEntity()->getInstance()->getAdmins() as $admin) {
                    if ($admin->getUuid()->toRfc4122() === $context->getUser()->getUuid()->toRfc4122()) {
                        return;
                    }
                }
            }

            if (
                $this->pms->canUseOwners($context->getUser(), 'SHOP', 'DELETE')
                && $this->pms->canUseActions($context->getUser(), 'SHOP', 'DELETE')
            ) {
                return;
            }

            throw new ForbiddenActionException($context);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeCrudActionEvent::class => 'onBeforeGetOrEditOrDeleteEntity',
        ];
    }
}
