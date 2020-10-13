<?php

namespace App\Controller\Admin;

use App\Entity\Client\Shop;
use App\Entity\Security\Admin;
use App\Entity\Security\AdminGroup;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Admin\Builder\ConfigurationBuilder;
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
    protected $config;

    public function __construct(ConfigurationBuilder $config)
    {
        $this->config = $config;
    }

    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTranslationDomain('admin')
            // @Todo : Dynamic Title in admin
            ->setTitle($this->config->get('CONF_DASHBOARD_TITLE') ?? '')
            // ->setTitle('<img src="..."> Easy <span class="text-small">Admin.</span>')
            ->setFaviconPath('favicon.ico');
    }

    public function configureMenuItems(): iterable
    {
        /*************** -- DEFAULT LINK -- ***************/
        yield MenuItem::linktoDashboard('admin.dashboard.home', 'fa fa-home');
        yield MenuItem::linkToLogout('admin.dashboard.logout', 'fa fa-sign-out');

        /*************** -- SHOP LINK -- ***************/
        if (PermissionsAdmin::checkAdmin($this->getUser()) || PermissionsAdmin::checkActions($this->getUser(), 'SHOP', 'INDEX')) {
            yield MenuItem::section('admin.dashboard.menu.shop');
        }

        if (
            PermissionsAdmin::checkAdmin($this->getUser())
            || PermissionsAdmin::checkActions($this->getUser(), 'SHOP', 'INDEX')
        ) {
            yield MenuItem::linkToCrud('admin.dashboard.menu.shops', 'fas fa-cart-plus', Shop::class);
        }

        /*************** -- ADMIN LINK -- ***************/
        if (
            PermissionsAdmin::checkAdmin($this->getUser())
            || PermissionsAdmin::checkActions($this->getUser(), 'ADMIN', 'INDEX')
            || PermissionsAdmin::checkActions($this->getUser(), 'ADMIN_GROUP', 'INDEX')
        ) {
            yield MenuItem::section('admin.dashboard.menu.admin');
        }

        if (
            PermissionsAdmin::checkAdmin($this->getUser())
            || PermissionsAdmin::checkActions($this->getUser(), 'ADMIN', 'INDEX')
        ) {
            yield MenuItem::linkToCrud('admin.dashboard.menu.admins', 'fas fa-users-cog', Admin::class);
        }

        if (
            PermissionsAdmin::checkAdmin($this->getUser())
            || PermissionsAdmin::checkActions($this->getUser(), 'ADMIN_GROUP', 'INDEX')
        ) {
            yield MenuItem::linkToCrud('admin.dashboard.menu.groups', 'fas fa-users', AdminGroup::class);
        }
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->setName($user->getUsername())
            ->displayUserName(true)
            ->addMenuItems([
                MenuItem::linkToCrud('admin.dashboard.my_profile', 'fa fa-id-card', Admin::class)
                    ->setAction('edit')
                    ->setEntityId($this->getUser()->getUuid()->toString()),
            ]);
    }
}
