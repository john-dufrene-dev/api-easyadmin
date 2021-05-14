<?php

namespace App\EventSubscriber\Admin\Customer;

use App\Entity\Customer\User;
use App\Entity\Customer\UserShopHistory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use App\Service\Admin\Permissions\PermissionsAdmin;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Controller\Admin\CRUD\Customer\UserCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;

class BeforeCrudUserActionSubscriber implements EventSubscriberInterface
{
    public const REDIRECT_ADMIN_NO_INDEX = 'admin_dashboard';

    protected $em;

    protected $adminUrlGenerator;

    public function __construct(EntityManagerInterface $em, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->em = $em;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

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
                //  verify if admin of the Shop is default Shop User
                if (null !== $context->getEntity()->getInstance()->getShop()) {
                    foreach ($context->getEntity()->getInstance()->getShop()->getAdmins() as $admin) {
                        if ($admin->getUuid()->toRfc4122() === $context->getUser()->getUuid()->toRfc4122()) {
                            return;
                        }
                    }
                }

                // Verify histories shops
                $shops = $context->getUser()->getShops();
                if (count($shops) !== 0) {
                    foreach ($shops as $shop) {
                        $shops_history_manager = $this->em->getRepository(UserShopHistory::class);
                        $users_histories = $shops_history_manager->findBy(['shop_reference' => $shop->getReference()]);
                        if (count($users_histories) !== 0) {
                            foreach ($users_histories as $user) {
                                if ($user->getUserReference() === $context->getEntity()->getInstance()->getReference()) {
                                    return;
                                }
                            }
                        }
                    }
                }
            }

            if (
                PermissionsAdmin::checkOwners($context->getUser(), 'USER', 'DETAIL')
                && PermissionsAdmin::checkActions($context->getUser(), 'USER', 'DETAIL')
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

            if (PermissionsAdmin::checkActions($context->getUser(), 'USER', 'EDIT')) {
                // Verify if admin of the Shop is default Shop User
                if (null !== $context->getEntity()->getInstance()->getShop()) {
                    foreach ($context->getEntity()->getInstance()->getShop()->getAdmins() as $admin) {
                        if ($admin->getUuid()->toRfc4122() === $context->getUser()->getUuid()->toRfc4122()) {
                            return;
                        }
                    }
                }

                // Verify histories shops
                $shops = $context->getUser()->getShops();
                if (count($shops) !== 0) {
                    foreach ($shops as $shop) {
                        $shops_history_manager = $this->em->getRepository(UserShopHistory::class);
                        $users_histories = $shops_history_manager->findBy(['shop_reference' => $shop->getReference()]);
                        if (count($users_histories) !== 0) {
                            foreach ($users_histories as $user) {
                                if ($user->getUserReference() === $context->getEntity()->getInstance()->getReference()) {
                                    // redirect to detail user if not User Shop
                                    $url = $this->adminUrlGenerator
                                        ->setController(UserCrudController::class)
                                        ->setAction(Action::DETAIL)
                                        ->setEntityId($context->getEntity()->getInstance()->getUuid()->toRfc4122())
                                        ->generateUrl();
                                    return $event->setResponse(new RedirectResponse($url));
                                }
                            }
                        }
                    }
                }
            }

            if (
                PermissionsAdmin::checkOwners($context->getUser(), 'USER', 'EDIT')
                && PermissionsAdmin::checkActions($context->getUser(), 'USER', 'EDIT')
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

            if (PermissionsAdmin::checkActions($context->getUser(), 'USER', 'DELETE')) {
                if (null !== $context->getEntity()->getInstance()->getShop()) {
                    foreach ($context->getEntity()->getInstance()->getShop()->getAdmins() as $admin) {
                        if ($admin->getUuid()->toRfc4122() === $context->getUser()->getUuid()->toRfc4122()) {
                            return;
                        }
                    }
                }
            }

            // Verify histories shops
            $shops = $context->getUser()->getShops();
            if (count($shops) !== 0) {
                foreach ($shops as $shop) {
                    $shops_history_manager = $this->em->getRepository(UserShopHistory::class);
                    $users_histories = $shops_history_manager->findBy(['shop_reference' => $shop->getReference()]);
                    if (count($users_histories) !== 0) {
                        foreach ($users_histories as $user) {
                            if ($user->getUserReference() === $context->getEntity()->getInstance()->getReference()) {
                                // redirect to detail user if not User Shop
                                $url = $this->adminUrlGenerator
                                    ->setController(UserCrudController::class)
                                    ->setAction(Action::DETAIL)
                                    ->setEntityId($context->getEntity()->getInstance()->getUuid()->toRfc4122())
                                    ->generateUrl();
                                return $event->setResponse(new RedirectResponse($url));
                            }
                        }
                    }
                }
            }

            if (
                PermissionsAdmin::checkOwners($context->getUser(), 'USER', 'DELETE')
                && PermissionsAdmin::checkActions($context->getUser(), 'USER', 'DELETE')
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
