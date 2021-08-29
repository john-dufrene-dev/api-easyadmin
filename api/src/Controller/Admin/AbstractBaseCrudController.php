<?php

namespace App\Controller\Admin;

use RuntimeException;
use App\Service\Utils\PaginatorFactory;
use Doctrine\ORM\EntityManagerInterface;
use function Symfony\Component\String\u;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\Admin\Builder\ExportBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Admin\Actions\CustomizeActions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use App\Service\Admin\Builder\ConfigurationBuilder;
use App\Service\Admin\Permissions\PermissionsAdmin;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use Symfony\Contracts\Translation\TranslatorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Factory\FilterFactory;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Factory\AdminContextFactory;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

abstract class AbstractBaseCrudController extends AbstractCrudController
{
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
     * adminCustomizeActions
     *
     * @return CustomizeActions
     */
    protected function adminCustomizeActions(): CustomizeActions
    {
        return $this->get(CustomizeActions::class);
    }

    /**
     * adminExportBuilder
     *
     * @return ExportBuilder
     */
    protected function adminExportBuilder(): ExportBuilder
    {
        return $this->get(ExportBuilder::class);
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
     * adminpaginatorFactory
     *
     * @return PaginatorFactory
     */
    protected function adminpaginatorFactory(): PaginatorFactory
    {
        return $this->get(PaginatorFactory::class);
    }

    /**
     * adminManagerRegistry
     *
     * @return ManagerRegistry
     */
    protected function adminManagerRegistry(): ManagerRegistry
    {
        return $this->get(ManagerRegistry::class);
    }

    /**
     * adminEntityManager
     *
     * @return EntityManagerInterface
     */
    protected function adminEntityManager(): EntityManagerInterface
    {
        return $this->get(EntityManagerInterface::class);
    }

    /**
     * adminEm
     *
     * @return EntityManagerInterface
     */
    protected function adminEm(): EntityManagerInterface
    {
        return $this->adminEntityManager();
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
                CustomizeActions::class => '?' . CustomizeActions::class,
                ExportBuilder::class => '?' . ExportBuilder::class,
                PermissionsAdmin::class => '?' . PermissionsAdmin::class,
                ConfigurationBuilder::class => '?' . ConfigurationBuilder::class,
                PaginatorFactory::class => '?' . PaginatorFactory::class,
                EntityManagerInterface::class => '?' . EntityManagerInterface::class,
                ManagerRegistry::class => '?' . ManagerRegistry::class,
            ]
        );
    }

    /*************** -- Custom Actions -- ***************/
    /***************************************************/
    /**************************************************/
    /*************************************************/

    /**
     * exportCsv
     *
     * @param  mixed $request
     * @return Response
     */
    public function exportCsv(Request $request): Response
    {
        if (
            !$this->pms()->isAdmin($this->getUser())
            && !$this->pms()->canUseActions($this->getUser(), $this->entityName(), 'EXPORT')
        ) {
            throw $this->createAccessDeniedException();
        }

        $context = $request->attributes->get(EA::CONTEXT_REQUEST_ATTRIBUTE);
        $fields = FieldCollection::new($this->configureFields(Crud::PAGE_INDEX));
        $filters = $this->get(FilterFactory::class)->create(
            $context->getCrud()->getFiltersConfig(),
            $fields,
            $context->getEntity()
        );

        \parse_str(\parse_url($request->query->get(EA::REFERRER))[EA::QUERY], $referrerQuery);
        $query = isset($referrerQuery[EA::QUERY]) ? $referrerQuery[EA::QUERY] : null;
        $request->query->set(EA::QUERY, $query);
        // recreate searchDto so that it takes into account the querystring 'query'
        $searchDto = $this->adminContextFactory()->getSearchDto($request, $context->getCrud());

        $entities = $this->createIndexQueryBuilder($searchDto, $context->getEntity(), $fields, $filters)
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($entities as $entity) {
            $data[] = $entity->getExportData();
        }

        // @todo : translation
        if (empty($data)) {
            $data[] = ['error' => 'empty file'];
        }

        return $this->adminExportBuilder()->exportCsv(
            $data,
            'export_' . u($this->entityName())->lower() . '_' . date_create()->format('dmyhis') . '.' .
                $this->adminExportBuilder()->format('csv')
        );
    }
}
