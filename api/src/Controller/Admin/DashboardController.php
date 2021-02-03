<?php

namespace App\Controller\Admin;

use App\Entity\Client\Shop;
use App\Entity\Monitoring\Log;
use App\Entity\Security\Admin;
use Symfony\UX\Chartjs\Model\Chart;
use App\Entity\Security\AdminGroup;
use App\Entity\Configuration\Config;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use App\Service\Admin\Builder\ConfigurationBuilder;
use App\Service\Admin\Permissions\PermissionsAdmin;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Controller\Admin\CRUD\Configuration\ConfigGeneralCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class DashboardController extends AbstractDashboardController
{
    /**
     * config
     *
     * @var mixed
     */
    protected $config;

    /**
     * chartBuilder
     *
     * @var mixed
     */
    protected $chartBuilder;

    public const SET_PAGINATOR_PAGE_SIZE = 15; // Default pagination
    public const SET_DEFAULT_FOLDER_EASYADMIN = 'admin/_easyadmin/'; // Default folder to override template EasyAdminBundle
    public const SET_DEFAULT_ROUTE_API_DOC = '/api/docs'; // Default route API documentation

    public function __construct(
        ConfigurationBuilder $config,
        ChartBuilderInterface $chartBuilder
    ) {
        $this->config = $config;
        $this->chartBuilder = $chartBuilder;
    }

    /**
     * @Route("%url_for_admin%", name="admin_dashboard")
     */
    public function index(): Response
    {
        // @todo add chartJS system
        // $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
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

        // return $this->render(self::SET_DEFAULT_FOLDER_EASYADMIN . 'welcome.html.twig', [
        //     'chart' => $chart,
        // ]);

        return parent::index();
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            // this defines the pagination size for all CRUD controllers
            // (each CRUD controller can override this value if needed)
            ->setPaginatorPageSize($this->config->get('CONF_DEFAULT_PAGINATOR') ?? self::SET_PAGINATOR_PAGE_SIZE)
            // the first argument is the "template name", which is the same as the
            // Twig path but without the `@EasyAdmin/` prefix
            ->overrideTemplates([
                'layout' => self::SET_DEFAULT_FOLDER_EASYADMIN . 'layout.html.twig',
                // @Todo change to default paginator when new version is merge
                'crud/paginator' => self::SET_DEFAULT_FOLDER_EASYADMIN . 'crud/paginator.html.twig',
                'crud/detail' => self::SET_DEFAULT_FOLDER_EASYADMIN . 'crud/detail.html.twig',
                'crud/edit' => self::SET_DEFAULT_FOLDER_EASYADMIN . 'crud/edit.html.twig',
            ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTranslationDomain('admin')
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

        /*************** -- SETTINGS CORE LINK -- ***************/
        // @todo : Add some configurations system
        if (PermissionsAdmin::checkAdmin($this->getUser())) {
            yield MenuItem::section('admin.dashboard.menu.settings');
            yield MenuItem::linkToCrud('admin.dashboard.menu.settings_general', 'fas fa-receipt', Config::class)
                ->setController(ConfigGeneralCrudController::class);
        }

        /*************** -- MONITORING LINK -- ***************/
        if (PermissionsAdmin::checkAdmin($this->getUser())) {
            yield MenuItem::section('admin.dashboard.menu.monitoring');
            yield MenuItem::linkToCrud('admin.dashboard.menu.logs', 'fas fa-book-reader', Log::class);
        }

        /*************** -- DOCUMENTATION LINK -- ***************/
        if (PermissionsAdmin::checkAdmin($this->getUser()) || $this->isGranted(PermissionsAdmin::ROLE_API_DOCUMENTATION)) {
            yield MenuItem::section('admin.dashboard.menu.documentation');
        }

        if (PermissionsAdmin::checkAdmin($this->getUser()) || $this->isGranted(PermissionsAdmin::ROLE_API_DOCUMENTATION)) {
            yield MenuItem::linkToUrl('admin.dashboard.menu.api_doc', 'fas fa-spider', self::SET_DEFAULT_ROUTE_API_DOC)
                ->setLinkTarget('_blank');
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
                    ->setEntityId($this->getUser()->getUuid()->toRfc4122()),
            ]);
    }
}
