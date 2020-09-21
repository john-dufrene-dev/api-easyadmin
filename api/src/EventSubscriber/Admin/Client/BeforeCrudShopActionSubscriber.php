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
    public function onBeforeGetOrEditOrDeleteEntity(BeforeCrudActionEvent $event)
    {
        $context = $event->getAdminContext();

        if (!($context->getEntity()->getInstance() instanceof Shop)) {
            return;
        }

        //DETAIL
        if ($context->getCrud()->getCurrentPage() === Crud::PAGE_DETAIL) {

            if (PermissionsAdmin::checkAdmin($context->getUser())) {
                return;
            }

            if (PermissionsAdmin::checkActions($context->getUser(), 'SHOP', 'DETAIL')) {
                foreach ($context->getEntity()->getInstance()->getAdmins() as $admin) {
                    if ($admin->getUuid()->toString() === $context->getUser()->getUuid()->toString()) {
                        return;
                    }
                }
            }

            if (
                PermissionsAdmin::checkOwners($context->getUser(), 'SHOP', 'DETAIL')
                && PermissionsAdmin::checkActions($context->getUser(), 'SHOP', 'DETAIL')
            ) {
                return;
            }

            throw new ForbiddenActionException($context);
        }

        //EDIT
        if ($context->getCrud()->getCurrentPage() === Crud::PAGE_EDIT) {

            if (PermissionsAdmin::checkAdmin($context->getUser())) {
                return;
            }

            if (PermissionsAdmin::checkActions($context->getUser(), 'SHOP', 'EDIT')) {
                foreach ($context->getEntity()->getInstance()->getAdmins() as $admin) {
                    if ($admin->getUuid()->toString() === $context->getUser()->getUuid()->toString()) {
                        return;
                    }
                }
            }

            if (
                PermissionsAdmin::checkOwners($context->getUser(), 'SHOP', 'EDIT')
                && PermissionsAdmin::checkActions($context->getUser(), 'SHOP', 'EDIT')
            ) {
                return;
            }

            throw new ForbiddenActionException($context);
        }

        //DELETE
        if ($context->getCrud()->getCurrentAction() === Action::DELETE) {

            if (PermissionsAdmin::checkAdmin($context->getUser())) {
                return;
            }

            if (PermissionsAdmin::checkActions($context->getUser(), 'SHOP', 'DELETE')) {
                foreach ($context->getEntity()->getInstance()->getAdmins() as $admin) {
                    if ($admin->getUuid()->toString() === $context->getUser()->getUuid()->toString()) {
                        return;
                    }
                }
            }

            if (
                PermissionsAdmin::checkOwners($context->getUser(), 'SHOP', 'DELETE')
                && PermissionsAdmin::checkActions($context->getUser(), 'SHOP', 'DELETE')
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
