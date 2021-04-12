<?php

namespace App\EventSubscriber\Admin\Customer;

use App\Entity\Customer\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use App\Service\Admin\Permissions\PermissionsAdmin;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;

class BeforeCrudUserActionSubscriber implements EventSubscriberInterface
{
    public function onBeforeGetOrEditOrDeleteEntity(BeforeCrudActionEvent $event)
    {
        $context = $event->getAdminContext();

        if (!($context->getEntity()->getInstance() instanceof User)) {
            return;
        }

        //DETAIL
        if ($context->getCrud()->getCurrentPage() === Crud::PAGE_DETAIL) {

            if (PermissionsAdmin::checkAdmin($context->getUser())) {
                return;
            }

            if (PermissionsAdmin::checkActions($context->getUser(), 'USER', 'DETAIL')) {
                return;
            }

            throw new ForbiddenActionException($context);
        }

        //EDIT
        if ($context->getCrud()->getCurrentPage() === Crud::PAGE_EDIT) {

            if (PermissionsAdmin::checkAdmin($context->getUser())) {
                return;
            }

            if (PermissionsAdmin::checkActions($context->getUser(), 'USER', 'EDIT')) {
                return;
            }

            throw new ForbiddenActionException($context);
        }

        //DELETE
        if ($context->getCrud()->getCurrentAction() === Action::DELETE) {

            if (PermissionsAdmin::checkAdmin($context->getUser())) {
                return;
            }

            if (PermissionsAdmin::checkActions($context->getUser(), 'USER', 'DELETE')) {
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
