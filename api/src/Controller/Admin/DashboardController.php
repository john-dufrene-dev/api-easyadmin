<?php

namespace App\Controller\Admin;

use App\Entity\Client\Shop;
use App\Entity\Security\Admin;
use App\Entity\Security\AdminGroup;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Admin\Permissions\PermissionsAdmin;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/", name="admin_dashboard")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Easy-admin-api')
            // ->setTitle('<img src="..."> Easy <span class="text-small">Admin.</span>')
            ->setFaviconPath('favicon.ico')
            ->setTranslationDomain('admin');
    }

    public function configureMenuItems(): iterable
    {
        /*************** -- DEFAULT LINK -- ***************/
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToLogout('Logout', 'fa fa-sign-out');

        /*************** -- SHOP LINK -- ***************/
        if (PermissionsAdmin::checkAdmin($this->getUser()) || PermissionsAdmin::checkActions($this->getUser(), 'SHOP', 'INDEX')) {
            yield MenuItem::section('Shop Management');
        }

        if (
            PermissionsAdmin::checkAdmin($this->getUser())
            || PermissionsAdmin::checkActions($this->getUser(), 'SHOP', 'INDEX')
        ) {
            yield MenuItem::linkToCrud('Shops', 'fas fa-cart-plus', Shop::class);
        }

        /*************** -- ADMIN LINK -- ***************/
        if (
            PermissionsAdmin::checkAdmin($this->getUser())
            || PermissionsAdmin::checkActions($this->getUser(), 'ADMIN', 'INDEX')
            || PermissionsAdmin::checkActions($this->getUser(), 'ADMIN_GROUP', 'INDEX')
        ) {
            yield MenuItem::section('Admin Management');
        }

        if (
            PermissionsAdmin::checkAdmin($this->getUser())
            || PermissionsAdmin::checkActions($this->getUser(), 'ADMIN', 'INDEX')
        ) {
            yield MenuItem::linkToCrud('Admins', 'fas fa-users-cog', Admin::class);
        }

        if (
            PermissionsAdmin::checkAdmin($this->getUser())
            || PermissionsAdmin::checkActions($this->getUser(), 'ADMIN_GROUP', 'INDEX')
        ) {
            yield MenuItem::linkToCrud('Admin Groups', 'fas fa-users', AdminGroup::class);
        }
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->setName($user->getUsername())
            ->displayUserName(true)
            ->addMenuItems([
                MenuItem::linkToCrud('My Profile', 'fa fa-id-card', Admin::class)
                    ->setAction('edit')
                    ->setEntityId($this->getUser()->getUuid()->toString()),
            ]);
    }
}
