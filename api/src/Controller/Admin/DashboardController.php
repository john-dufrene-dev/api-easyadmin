<?php

namespace App\Controller\Admin;

use App\Entity\Security\Admin;
use App\Entity\Security\AdminGroup;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Admin\Permissions\PermissionsAdmin;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
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
            // ->setTitle('<img src="..."> ACME <span class="text-small">Corp.</span>')
            // ->setFaviconPath('favicon.svg')
            ->setTranslationDomain('admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToLogout('Logout', 'fa fa-sign-out');

        if (
            PermissionsAdmin::checkAdmin($this->getUser())
            || PermissionsAdmin::checkActions($this->getUser(), 'ADMIN', 'INDEX')
            || PermissionsAdmin::checkActions($this->getUser(), 'ADMIN_GROUP', 'INDEX')
        ) {
            yield MenuItem::section('Administration');
        }

        if (
            PermissionsAdmin::checkAdmin($this->getUser())
            || PermissionsAdmin::checkActions($this->getUser(), 'ADMIN', 'INDEX')
        ) {
            yield MenuItem::linkToCrud('Admins', 'fa fa-users', Admin::class);
        }

        if (
            PermissionsAdmin::checkAdmin($this->getUser())
            || PermissionsAdmin::checkActions($this->getUser(), 'ADMIN_GROUP', 'INDEX')
        ) {
            yield MenuItem::linkToCrud('Admin Groups', 'fa fa-users', AdminGroup::class);
        }
    }
}
