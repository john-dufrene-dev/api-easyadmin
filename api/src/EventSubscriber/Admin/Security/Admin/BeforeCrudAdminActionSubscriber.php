<?php

namespace App\EventSubscriber\Admin\Security\Admin;

use App\Entity\Security\Admin;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use App\Service\Admin\Permissions\PermissionsAdmin;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;

class BeforeCrudAdminActionSubscriber implements EventSubscriberInterface
{
    public function onBeforeGetOrEditOrDeleteEntity(BeforeCrudActionEvent $event)
    {
        $context = $event->getAdminContext();

        if (!($context->getEntity()->getInstance() instanceof Admin)) {
            return;
        }

        //DETAIL
        if ($context->getCrud()->getCurrentPage() === Crud::PAGE_DETAIL) {

            if ($context->getEntity()->getInstance()->getIsAdmin()) {
                if ($context->getUser()->getId() !== $context->getEntity()->getInstance()->getId()) {
                    throw new ForbiddenActionException($context);
                }
            }

            if (PermissionsAdmin::checkAdmin($context->getUser())) {
                return;
            }

            if (
                PermissionsAdmin::checkActions($context->getUser(), 'ADMIN', 'DETAIL')
                && $context->getEntity()->getInstance()->getUuid()->toString() === $context->getUser()->getUuid()->toString()
            ) {
                return;
            }

            if (
                PermissionsAdmin::checkOwners($context->getUser(), 'ADMIN', 'DETAIL')
                && PermissionsAdmin::checkActions($context->getUser(), 'ADMIN', 'DETAIL')
            ) {
                return;
            }

            throw new ForbiddenActionException($context);
        }

        //EDIT
        if ($context->getCrud()->getCurrentPage() === Crud::PAGE_EDIT) {

            if ($context->getEntity()->getInstance()->getIsAdmin()) {
                if ($context->getUser()->getId() !== $context->getEntity()->getInstance()->getId()) {
                    throw new ForbiddenActionException($context);
                }
            }

            if (PermissionsAdmin::checkAdmin($context->getUser())) {
                return;
            }

            if (
                PermissionsAdmin::checkActions($context->getUser(), 'ADMIN', 'EDIT')
                && $context->getEntity()->getInstance()->getUuid()->toString() === $context->getUser()->getUuid()->toString()
            ) {
                return;
            }

            if (
                PermissionsAdmin::checkOwners($context->getUser(), 'ADMIN', 'EDIT')
                && PermissionsAdmin::checkActions($context->getUser(), 'ADMIN', 'EDIT')
            ) {
                return;
            }

            throw new ForbiddenActionException($context);
        }

        //DELETE
        if ($context->getCrud()->getCurrentAction() === Action::DELETE) {

            if ($context->getEntity()->getInstance()->getIsAdmin()) {
                if ($context->getUser()->getId() !== $context->getEntity()->getInstance()->getId()) {
                    throw new ForbiddenActionException($context);
                }
            }

            if (PermissionsAdmin::checkAdmin($context->getUser())) {
                return;
            }

            if (
                PermissionsAdmin::checkActions($context->getUser(), 'ADMIN', 'DELETE')
                && $context->getEntity()->getInstance()->getUuid()->toString() === $context->getUser()->getUuid()->toString()
            ) {
                return;
            }

            if (
                PermissionsAdmin::checkOwners($context->getUser(), 'ADMIN', 'DELETE')
                && PermissionsAdmin::checkActions($context->getUser(), 'ADMIN', 'EDIT')
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
