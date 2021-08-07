<?php

namespace App\EventSubscriber\Admin\Security\Admin;

use App\Entity\Security\Admin;
use Symfony\Component\Routing\RouterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use App\Service\Admin\Permissions\PermissionsAdmin;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;

class BeforeCrudAdminActionSubscriber implements EventSubscriberInterface
{
    public const REDIRECT_ADMIN_NO_INDEX = 'admin_dashboard';

    protected $router;

    protected $pms;

    public function __construct(RouterInterface $router, PermissionsAdmin $pms)
    {
        $this->router = $router;
        $this->pms = $pms;
    }

    public function onBeforeGetOrEditOrDeleteEntity(BeforeCrudActionEvent $event)
    {
        $context = $event->getAdminContext();

        if (!($context->getEntity()->getInstance() instanceof Admin)) {
            return;
        }

        //INDEX
        if ($context->getCrud()->getCurrentPage() === Crud::PAGE_INDEX) {
            if (!$this->pms->canUseActions($context->getUser(), 'ADMIN', 'INDEX')) {
                return $event->setResponse(new RedirectResponse($this->router->generate(self::REDIRECT_ADMIN_NO_INDEX)));
            }
        }

        //DETAIL
        if ($context->getCrud()->getCurrentPage() === Crud::PAGE_DETAIL) {

            if ($context->getEntity()->getInstance()->getIsAdmin()) {
                if ($context->getUser()->getId() !== $context->getEntity()->getInstance()->getId()) {
                    throw new ForbiddenActionException($context);
                }
            }

            if ($this->pms->isAdmin($context->getUser())) {
                return;
            }

            if (
                $this->pms->canUseActions($context->getUser(), 'ADMIN', 'DETAIL')
                && $context->getEntity()->getInstance()->getUuid()->toRfc4122() === $context->getUser()->getUuid()->toRfc4122()
            ) {
                return;
            }

            if (
                $this->pms->canUseOwners($context->getUser(), 'ADMIN', 'DETAIL')
                && $this->pms->canUseActions($context->getUser(), 'ADMIN', 'DETAIL')
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

            if ($this->pms->isAdmin($context->getUser())) {
                return;
            }

            if (
                $this->pms->canUseActions($context->getUser(), 'ADMIN', 'EDIT')
                && $context->getEntity()->getInstance()->getUuid()->toRfc4122() === $context->getUser()->getUuid()->toRfc4122()
            ) {
                return;
            }

            if (
                $this->pms->canUseOwners($context->getUser(), 'ADMIN', 'EDIT')
                && $this->pms->canUseActions($context->getUser(), 'ADMIN', 'EDIT')
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

            if ($this->pms->isAdmin($context->getUser())) {
                return;
            }

            if (
                $this->pms->canUseActions($context->getUser(), 'ADMIN', 'DELETE')
                && $context->getEntity()->getInstance()->getUuid()->toRfc4122() === $context->getUser()->getUuid()->toRfc4122()
            ) {
                return;
            }

            if (
                $this->pms->canUseOwners($context->getUser(), 'ADMIN', 'DELETE')
                && $this->pms->canUseActions($context->getUser(), 'ADMIN', 'DELETE')
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
