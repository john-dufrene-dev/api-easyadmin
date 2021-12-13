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

    protected $pms;

    public function __construct(EntityManagerInterface $em, AdminUrlGenerator $adminUrlGenerator, PermissionsAdmin $pms)
    {
        $this->em = $em;
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->pms = $pms;
    }
    
    /**
     * onBeforeGetOrEditOrDeleteEntity
     *
     * @param  mixed $event
     * @return mixed
     */
    public function onBeforeGetOrEditOrDeleteEntity(BeforeCrudActionEvent $event): mixed
    {
        $context = $event->getAdminContext();

        if (!($context->getEntity()->getInstance() instanceof User)) {
            return null;
        }

        //DETAIL
        if ($context->getCrud()->getCurrentPage() === Crud::PAGE_DETAIL) {

            if ($this->pms->isAdmin($context->getUser())) {
                return null;
            }

            if ($this->pms->canUseActions($context->getUser(), 'USER', 'DETAIL')) {
                //  verify if admin of the Shop is default Shop User
                if (null !== $context->getEntity()->getInstance()->getShop()) {
                    foreach ($context->getEntity()->getInstance()->getShop()->getAdmins() as $admin) {
                        if ($admin->getUuid()->toRfc4122() === $context->getUser()->getUuid()->toRfc4122()) {
                            return null;
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
                                    return null;
                                }
                            }
                        }
                    }
                }
            }

            if (
                $this->pms->canUseOwners($context->getUser(), 'USER', 'DETAIL')
                && $this->pms->canUseActions($context->getUser(), 'USER', 'DETAIL')
            ) {
                return null;
            }

            throw new ForbiddenActionException($context);
        }

        //EDIT
        if ($context->getCrud()->getCurrentPage() === Crud::PAGE_EDIT) {

            if ($this->pms->isAdmin($context->getUser())) {
                return null;
            }

            if ($this->pms->canUseActions($context->getUser(), 'USER', 'EDIT')) {
                // Verify if admin of the Shop is default Shop User
                if (null !== $context->getEntity()->getInstance()->getShop()) {
                    foreach ($context->getEntity()->getInstance()->getShop()->getAdmins() as $admin) {
                        if ($admin->getUuid()->toRfc4122() === $context->getUser()->getUuid()->toRfc4122()) {
                            return null;
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
                $this->pms->canUseOwners($context->getUser(), 'USER', 'EDIT')
                && $this->pms->canUseActions($context->getUser(), 'USER', 'EDIT')
            ) {
                return null;
            }

            throw new ForbiddenActionException($context);
        }

        //DELETE
        if ($context->getCrud()->getCurrentAction() === Action::DELETE) {

            if ($this->pms->isAdmin($context->getUser())) {
                return null;
            }

            if ($this->pms->canUseActions($context->getUser(), 'USER', 'DELETE')) {
                if (null !== $context->getEntity()->getInstance()->getShop()) {
                    foreach ($context->getEntity()->getInstance()->getShop()->getAdmins() as $admin) {
                        if ($admin->getUuid()->toRfc4122() === $context->getUser()->getUuid()->toRfc4122()) {
                            return null;
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
                $this->pms->canUseOwners($context->getUser(), 'USER', 'DELETE')
                && $this->pms->canUseActions($context->getUser(), 'USER', 'DELETE')
            ) {
                return null;
            }

            throw new ForbiddenActionException($context);
        }
    }
    
    /**
     * getSubscribedEvents
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeCrudActionEvent::class => 'onBeforeGetOrEditOrDeleteEntity',
        ];
    }
}
