<?php

namespace App\Controller\Admin;

use App\Entity\Client\Shop;
use App\Entity\Customer\User;
use App\Entity\Monitoring\Log;
use App\Entity\Security\Admin;
use App\Entity\Security\AdminGroup;
use Symfony\UX\Chartjs\Model\Chart;
use App\Entity\Configuration\Config;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Controller\Admin\AbstractBaseDashboardController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Controller\Admin\CRUD\Configuration\ConfigGeneralCrudController;

#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractBaseDashboardController
{
    #[Route("%url_for_admin%", name: 'admin_dashboard')]
    public function index(): Response
    {
        // @todo add chartJS system
        // $chart = $this->adminChartBuilder()->createChart(Chart::TYPE_BAR);
        // $chart->setData([
        //     'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        //     'datasets' => [
        //         [
        //             'label' => 'My First dataset',
        //             'backgroundColor' => 'rgb(255, 99, 132)',
        //             'borderColor' => 'rgb(255, 99, 132)',
        //             'data' => [0, 10, 5, 2, 20, 30, 45],
        //         ],
        //         [
        //             'label' => 'My second dataset',
        //             'backgroundColor' => 'rgb(120, 99, 132)',
        //             'borderColor' => 'rgb(255, 99, 132)',
        //             'data' => [100, 10, 5, 2, 55, 30, 45],
        //         ],
        //         [
        //             'label' => 'My third dataset',
        //             'backgroundColor' => 'rgb(210, 99, 132)',
        //             'borderColor' => 'rgb(255, 99, 132)',
        //             'data' => [50, 10, 5, 2, 20, 30, 45],
        //         ],
        //     ],
        // ]);

        // $chart->setOptions([
        //     'scales' => [
        //         'yAxes' => [
        //             ['ticks' => ['min' => 0, 'max' => 100]],
        //         ],
        //     ],
        // ]);

        // return $this->render($this->defaultFolderEasyAdmin . 'welcome.html.twig', [
        //     'chart' => $chart,
        // ]);

        return parent::index();
    }

    public function configureAssets(): Assets
    {
        return Assets::new()->addWebpackEncoreEntry('admin');
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            // this defines the pagination size for all CRUD controllers
            // (each CRUD controller can override this value if needed)
            ->setPaginatorPageSize($this->getPaginator())
            // the first argument is the "template name", which is the same as the
            // Twig path but without the `@EasyAdmin/` prefix
            ->overrideTemplates([
                'layout' => $this->defaultFolderEasyAdmin . 'layout.html.twig',
                'crud/detail' => $this->defaultFolderEasyAdmin . 'crud/detail.html.twig',
                'crud/edit' => $this->defaultFolderEasyAdmin . 'crud/edit.html.twig',
            ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTranslationDomain('admin')
            ->setTitle($this->getDashboardTitle())
            // ->setTitle('<img src="..."> Easy <span class="text-small">Admin.</span>')
            ->setFaviconPath('favicon.ico');
    }

    public function configureMenuItems(): iterable
    {
        /*************** -- DEFAULT LINK -- ***************/
        yield MenuItem::linktoDashboard('admin.dashboard.home', 'fa fa-home');
        yield MenuItem::linkToLogout('admin.dashboard.logout', 'fa fa-sign-out');

        /*************** -- SHOP LINK -- ***************/
        if ($this->pms()->isAdmin($this->getUser()) || $this->pms()->canUseActions($this->getUser(), 'SHOP', 'INDEX')) {
            yield MenuItem::section('admin.dashboard.menu.shop');
        }

        if (
            $this->pms()->isAdmin($this->getUser())
            || $this->pms()->canUseActions($this->getUser(), 'SHOP', 'INDEX')
        ) {
            yield MenuItem::linkToCrud('admin.dashboard.menu.shops', 'fas fa-cart-plus', Shop::class);
        }

        /*************** -- USER LINK -- ***************/
        if ($this->pms()->isAdmin($this->getUser()) || $this->pms()->canUseActions($this->getUser(), 'USER', 'INDEX')) {
            yield MenuItem::section('admin.dashboard.menu.user');
        }

        if (
            $this->pms()->isAdmin($this->getUser())
            || $this->pms()->canUseActions($this->getUser(), 'USER', 'INDEX')
        ) {
            yield MenuItem::linkToCrud('admin.dashboard.menu.users', 'fas fa-user', User::class);
        }

        /*************** -- ADMIN LINK -- ***************/
        if (
            $this->pms()->isAdmin($this->getUser())
            || $this->pms()->canUseActions($this->getUser(), 'ADMIN', 'INDEX')
            || $this->pms()->canUseActions($this->getUser(), 'ADMIN_GROUP', 'INDEX')
        ) {
            yield MenuItem::section('admin.dashboard.menu.admin');
        }

        if (
            $this->pms()->isAdmin($this->getUser())
            || $this->pms()->canUseActions($this->getUser(), 'ADMIN', 'INDEX')
        ) {
            yield MenuItem::linkToCrud('admin.dashboard.menu.admins', 'fas fa-users-cog', Admin::class);
        }

        if (
            $this->pms()->isAdmin($this->getUser())
            || $this->pms()->canUseActions($this->getUser(), 'ADMIN_GROUP', 'INDEX')
        ) {
            yield MenuItem::linkToCrud('admin.dashboard.menu.groups', 'fas fa-users', AdminGroup::class);
        }

        /*************** -- SETTINGS CORE LINK -- ***************/
        // @todo : Add some configurations system
        if ($this->pms()->isAdmin($this->getUser())) {
            yield MenuItem::section('admin.dashboard.menu.settings');
            yield MenuItem::linkToCrud('admin.dashboard.menu.settings_general', 'fas fa-receipt', Config::class)
                ->setController(ConfigGeneralCrudController::class);
        }

        /*************** -- MONITORING LINK -- ***************/
        if ($this->pms()->isAdmin($this->getUser())) {
            yield MenuItem::section('admin.dashboard.menu.monitoring');
            yield MenuItem::linkToCrud('admin.dashboard.menu.logs', 'fas fa-book-reader', Log::class);
        }

        /*************** -- DOCUMENTATION LINK -- ***************/
        if ($this->pms()->isAdmin($this->getUser()) || $this->isGranted($this->pms()->roleApiDocumentation)) {
            yield MenuItem::section('admin.dashboard.menu.documentation');
        }

        if ($this->pms()->isAdmin($this->getUser()) || $this->isGranted($this->pms()->roleApiDocumentation)) {
            yield MenuItem::linkToUrl('admin.dashboard.menu.api_doc', 'fas fa-spider', $this->defaultRouteApiDoc)
                ->setLinkTarget('_blank');
        }
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->setName($user->getUserIdentifier())
            ->displayUserName(true)
            ->addMenuItems([
                MenuItem::linkToCrud('admin.dashboard.my_profile', 'fa fa-id-card', Admin::class)
                    ->setAction('edit')
                    ->setEntityId($this->getUser()->getUuid()->toRfc4122()),
            ]);
    }
}
