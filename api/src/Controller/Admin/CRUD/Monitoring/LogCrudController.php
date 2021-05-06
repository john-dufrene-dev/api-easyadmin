<?php

namespace App\Controller\Admin\CRUD\Monitoring;

use App\Entity\Monitoring\Log;
use App\Service\Admin\Builder\ExportBuilder;
use Symfony\Component\HttpFoundation\Request;
use App\Service\Admin\Actions\CustomizeActions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use App\Service\Admin\Permissions\PermissionsAdmin;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Factory\FilterFactory;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Factory\AdminContextFactory;

class LogCrudController extends AbstractCrudController
{
    public const DISPLAY_ERROR = 'ERROR';
    public const DISPLAY_NOTICE = 'NOTICE';

    protected $actions;

    protected $export;

    protected $adminContextFactory;

    public function __construct(
        CustomizeActions $actions,
        ExportBuilder $export,
        AdminContextFactory $adminContextFactory
    ) {
        $this->actions = $actions;
        $this->export = $export;
        $this->adminContextFactory = $adminContextFactory;
    }

    public static function getEntityFqcn(): string
    {
        return Log::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'DESC'])
            ->setDateFormat('full')
            ->setTimeFormat('full');
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('id')
            ->add('uuid')
            ->add('message')
            ->add('user')
            ->add('context')
            ->add(ChoiceFilter::new('level')
                ->setChoices([
                    -1 => -1,
                    0 => 0,
                    1 => 1,
                    2 => 2,
                    3 => 3
                ]))
            ->add(ChoiceFilter::new('level_name')
                ->setChoices([
                    'NOTICE' => self::DISPLAY_NOTICE,
                    'ERROR' => self::DISPLAY_ERROR
                ]))
            ->add(DateTimeFilter::new('created_at'));
    }

    public function configureActions(Actions $actions): Actions
    {
        // Actions adding by just for show index and detail (limited)
        $this->actions->limitedToShow($actions);
        $this->actions->limitedToShowCustomize($actions);

        if ($this->isGranted(PermissionsAdmin::IS_ADMIN)) {
            $export = $this->actions->export('exportCsv', 'csv');
            $actions->add(Crud::PAGE_INDEX, $export);

            return $actions;
        }

        $actions->setPermission(Action::DETAIL, PermissionsAdmin::IS_ADMIN);
        $actions->setPermission(Action::INDEX, PermissionsAdmin::IS_ADMIN);
        $actions->setPermission($this->actions::EXPORT_CSV, PermissionsAdmin::IS_ADMIN);

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        // INDEX
        if (Crud::PAGE_INDEX === $pageName) {
            yield IdField::new('id')->setLabel('admin.field.id');
            yield TextField::new('message')->setLabel('admin.log.field.message');
            yield TextField::new('user')->setLabel('admin.log.field.user');
            yield ArrayField::new('context')->setLabel('admin.log.field.context');
            yield ChoiceField::new('level_name')
                ->setLabel('admin.log.field.level_name')
                ->setChoices([
                    'admin.log.badge_notice' => self::DISPLAY_NOTICE,
                    'admin.log.badge_error' => self::DISPLAY_ERROR
                ])
                ->renderAsBadges([
                    self::DISPLAY_ERROR => 'danger',
                    self::DISPLAY_NOTICE => 'info',
                ]);
            yield ChoiceField::new('level')
                ->setLabel('admin.log.field.level')
                ->setChoices([
                    'admin.log.badge__1' => -1,
                    0 => 0,
                    'admin.log.badge_1' => 1,
                    2 => 2,
                    3 => 3
                ])
                ->renderAsBadges([
                    -1 => 'secondary',
                    1 => 'secondary',
                ]);
            yield DateField::new('created_at')->setLabel('admin.field.created_at');
        }

        // DETAIL
        if (Crud::PAGE_DETAIL === $pageName) {
            yield FormField::addPanel('admin.log.panel_info');
            yield IdField::new('id')->setLabel('admin.field.id');
            yield TextField::new('displayuuid')->setLabel('admin.field.displayuuid');
            yield TextField::new('message')->setLabel('admin.log.field.message');
            yield TextField::new('user')->setLabel('admin.log.field.user');
            yield ArrayField::new('context')->setLabel('admin.log.field.context');
            yield ChoiceField::new('level_name')
                ->setLabel('admin.log.field.level_name')
                ->setChoices([
                    'admin.log.badge_notice' => self::DISPLAY_NOTICE,
                    'admin.log.badge_error' => self::DISPLAY_ERROR
                ])
                ->renderAsBadges([
                    self::DISPLAY_ERROR => 'danger',
                    self::DISPLAY_NOTICE => 'info',
                ]);
            yield ChoiceField::new('level')
                ->setLabel('admin.log.field.level')
                ->setChoices([
                    'admin.log.badge__1' => -1,
                    0 => 0,
                    'admin.log.badge_1' => 1,
                    2 => 2,
                    3 => 3
                ])
                ->renderAsBadges([
                    -1 => 'secondary',
                    1 => 'secondary',
                ]);
            // @Todo : transform in array to show extra
            // yield ArrayField::new('extra')->setLabel('admin.log.field.extra');
            yield DateField::new('created_at')->setLabel('admin.field.created_at');
        }
    }

    /*************** -- Custom Actions -- ***************/
    /***************************************************/
    /**************************************************/
    /*************************************************/

    /**
     * exportCsv
     *
     * @param  mixed $request
     * @return void
     */
    public function exportCsv(Request $request)
    {
        if (!PermissionsAdmin::checkAdmin($this->getUser())) {
            throw $this->createAccessDeniedException();
        }

        // retrieve referrer's querystring 'filters'
        \parse_str(\parse_url($request->query->get(EA::REFERRER))[EA::QUERY], $referrerQuery);

        if (isset($referrerQuery[EA::FILTERS])) {
            $request->query->set(EA::FILTERS, $referrerQuery[EA::FILTERS]);
        }

        $context = $request->attributes->get(EA::CONTEXT_REQUEST_ATTRIBUTE);
        $fields = FieldCollection::new($this->configureFields(Crud::PAGE_INDEX));
        $filters = $this->get(FilterFactory::class)->create($context->getCrud()->getFiltersConfig(), $fields, $context->getEntity());

        \parse_str(\parse_url($request->query->get(EA::REFERRER))[EA::QUERY], $referrerQuery);
        $query = isset($referrerQuery[EA::QUERY]) ? $referrerQuery[EA::QUERY] : null;
        $request->query->set(EA::QUERY, $query);
        // recreate searchDto so that it takes into account the querystring 'query'
        $searchDto = $this->adminContextFactory->getSearchDto($request, $context->getCrud());
        
        $logs = $this->createIndexQueryBuilder($searchDto, $context->getEntity(), $fields, $filters)
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($logs as $log) {
            $data[] = $log->getExportData();
        }

        // @todo : translation
        if (empty($data)) {
            $data[] = ['error' => 'empty file'];
        }

        return $this->export->exportCsv(
            $data,
            'export_log_' . date_create()->format('dmyhis') . '.' . $this->export->format('csv')
        );
    }
}
