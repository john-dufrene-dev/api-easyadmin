<?php

namespace App\Controller\Admin;

use RuntimeException;
use function Symfony\Component\String\u;
use App\Service\Admin\Builder\ConfigurationBuilder;
use App\Service\Admin\Permissions\PermissionsAdmin;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Factory\AdminContextFactory;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

abstract class AbstractBaseDashboardController extends AbstractDashboardController
{
    public $paginationPageSize = 15; // Default pagination
    public $defaultFolderEasyAdmin = 'admin/_easyadmin/'; // Default folder to override template EasyAdminBundle
    public $defaultRouteApiDoc = '/api/docs'; // Default route API documentation

    /**
     * currentAdminContext
     *
     * @return AdminContext
     */
    protected function currentAdminContext(): AdminContext
    {
        $currentAdminContext = $this->get(AdminContextProvider::class)->getContext();
        if ($currentAdminContext === null) {
            throw new RuntimeException('Current request is not in an EasyAdmin context.');
        }

        return $currentAdminContext;
    }

    /**
     * translator
     *
     * @return TranslatorInterface
     */
    protected function translator(): TranslatorInterface
    {
        return $this->get(TranslatorInterface::class);
    }

    /**
     * adminUrlGenerator
     *
     * @return AdminUrlGenerator
     */
    protected function adminUrlGenerator(): AdminUrlGenerator
    {
        return $this->get(AdminUrlGenerator::class);
    }

    /**
     * adminContextFactory
     *
     * @return AdminContextFactory
     */
    protected function adminContextFactory(): AdminContextFactory
    {
        return $this->get(AdminContextFactory::class);
    }

    /**
     * adminPermission
     *
     * @return PermissionsAdmin
     */
    protected function adminPermission(): PermissionsAdmin
    {
        return $this->get(PermissionsAdmin::class);
    }

    /**
     * pms
     *
     * @return PermissionsAdmin
     */
    protected function pms(): PermissionsAdmin
    {
        return $this->adminPermission();
    }

    /**
     * adminConfig
     *
     * @return ConfigurationBuilder
     */
    protected function adminConfig(): ConfigurationBuilder
    {
        return $this->get(ConfigurationBuilder::class);
    }

    /**
     * adminChartBuilder
     *
     * @return ChartBuilderInterface
     */
    protected function adminChartBuilder(): ChartBuilderInterface
    {
        return $this->get(ChartBuilderInterface::class);
    }

    /**
     * entityName
     *
     * @return string
     */
    protected function entityName(): ?string
    {
        return u($this->currentAdminContext()->getEntity()->getFqcn())->afterLast('\\')->toString();
    }

    /**
     * @return array<array-key, string>
     */
    public static function getSubscribedServices(): array
    {
        return \array_merge(
            parent::getSubscribedServices(),
            [
                AdminContextFactory::class => '?' . AdminContextFactory::class,
                TranslatorInterface::class => '?' . TranslatorInterface::class,
                PermissionsAdmin::class => '?' . PermissionsAdmin::class,
                ConfigurationBuilder::class => '?' . ConfigurationBuilder::class,
                ChartBuilderInterface::class => '?' . ChartBuilderInterface::class,
            ]
        );
    }
}
