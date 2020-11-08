<?php

namespace App\Controller\Admin\CRUD\Monitoring;

use App\Entity\Monitoring\Log;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class LogCrudController extends AbstractCrudController
{
    public const DISPLAY_ERROR = 'ERROR';
    public const DISPLAY_NOTICE = 'NOTICE';

    protected $actions;

    public function __construct(CustomizeActions $actions)
    {
        $this->actions = $actions;
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
            return $actions;
        }

        $actions->setPermission(Action::DETAIL, PermissionsAdmin::IS_ADMIN);
        $actions->setPermission(Action::INDEX, PermissionsAdmin::IS_ADMIN);

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
}
