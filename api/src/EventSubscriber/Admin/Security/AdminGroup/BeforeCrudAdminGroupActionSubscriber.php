<?php

namespace App\EventSubscriber\Admin\Security\AdminGroup;

use App\Entity\Security\AdminGroup;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use App\Service\Admin\Permissions\PermissionsAdmin;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;

class BeforeCrudAdminGroupActionSubscriber implements EventSubscriberInterface
{
    protected $pms;

    public function __construct(PermissionsAdmin $pms)
    {
        $this->pms = $pms;
    }

    public function onBeforeGetOrEditOrDeleteEntity(BeforeCrudActionEvent $event)
    {
        $context = $event->getAdminContext();

        if (!($context->getEntity()->getInstance() instanceof AdminGroup)) {
            return;
        }

        //DETAIL
        if ($context->getCrud()->getCurrentPage() === Crud::PAGE_DETAIL) {

            if ($this->pms->isAdmin($context->getUser())) {
                return;
            }

            if ($this->pms->canUseActions($context->getUser(), 'ADMIN_GROUP', 'DETAIL')) {
                foreach ($context->getEntity()->getInstance()->getAdmins() as $admin) {
                    if ($admin->getUuid()->toRfc4122() === $context->getUser()->getUuid()->toRfc4122()) {
                        return;
                    }
                }
            }

            if (
                $this->pms->canUseOwners($context->getUser(), 'ADMIN_GROUP', 'DETAIL')
                && $this->pms->canUseActions($context->getUser(), 'ADMIN_GROUP', 'DETAIL')
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

            if ($this->pms->canUseActions($context->getUser(), 'ADMIN_GROUP', 'EDIT')) {
                foreach ($context->getEntity()->getInstance()->getAdmins() as $admin) {
                    if ($admin->getUuid()->toRfc4122() === $context->getUser()->getUuid()->toRfc4122()) {
                        return;
                    }
                }
            }

            if (
                $this->pms->canUseOwners($context->getUser(), 'ADMIN_GROUP', 'EDIT')
                && $this->pms->canUseActions($context->getUser(), 'ADMIN_GROUP', 'EDIT')
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

            if ($this->pms->canUseActions($context->getUser(), 'ADMIN_GROUP', 'DELETE')) {
                foreach ($context->getEntity()->getInstance()->getAdmins() as $admin) {
                    if ($admin->getUuid()->toRfc4122() === $context->getUser()->getUuid()->toRfc4122()) {
                        return;
                    }
                }
            }

            if (
                $this->pms->canUseOwners($context->getUser(), 'ADMIN_GROUP', 'DELETE')
                && $this->pms->canUseActions($context->getUser(), 'ADMIN_GROUP', 'DELETE')
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
