<?php

namespace App\Controller\Admin\CRUD\Security;

use App\Entity\Security\Admin;
use Doctrine\ORM\QueryBuilder;
use App\Service\Utils\PaginatorFactory;
use App\Service\Admin\Field\PasswordField;
use App\Service\Admin\Actions\CustomizeActions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use App\Service\Admin\Permissions\PermissionsAdmin;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AdminCrudController extends AbstractCrudController
{
    protected $actions;

    protected $paginator;

    public const ACTIVE_CUSTOM_ROLES = false; // Enable or disable custom roles

    public function __construct(CustomizeActions $actions, PaginatorFactory $paginator)
    {
        $this->actions = $actions;
        $this->paginator = $paginator;
    }

    public static function getEntityFqcn(): string
    {
        return Admin::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('edit', function (?Admin $admin) {
                return $admin ? $admin->getUserIdentifier() : null;
            })
            ->setPageTitle('detail', function (?Admin $admin) {
                return $admin ? $admin->getUserIdentifier() : null;
            })
            ->setDefaultSort(['id' => 'ASC'])
            ->setDateFormat('full')
            ->setTimeFormat('full');
    }

    public function configureFilters(Filters $filters): Filters
    {
        if (
            PermissionsAdmin::checkAdmin($this->getUser())
            || PermissionsAdmin::checkOwners($this->getUser(), 'ADMIN', 'INDEX')
        ) {
            $filters->add('id');
            $filters->add('uuid');
        }

        $filters->add('reference');
        $filters->add('email');

        return $filters;
    }

    public function configureActions(Actions $actions): Actions
    {
        // Actions adding by default
        $this->actions->all($actions);

        // Action new impersonation route
        if ($this->isGranted(PermissionsAdmin::ROLE_ALLOWED_TO_SWITCH)) {
            $switch_user = $this->actions->impersonate($actions);
            $actions->add(Crud::PAGE_INDEX, $switch_user);
            $actions->setPermission($this->actions::IMPERSONATE, PermissionsAdmin::ROLE_ALLOWED_TO_SWITCH);
        }

        // Default customize actions
        $this->actions->customize($actions);

        // Default reorder actions
        $this->actions->reorder($actions);

        if ($this->isGranted(PermissionsAdmin::IS_ADMIN)) {
            return $actions;
        }

        if ($this->isGranted(PermissionsAdmin::ROLE_ADMIN_ACTION_ALL)) {
            return $actions;
        }

        $actions->setPermission(Action::NEW, PermissionsAdmin::ROLE_ADMIN_ACTION_NEW);
        $actions->setPermission(Action::SAVE_AND_ADD_ANOTHER, PermissionsAdmin::ROLE_ADMIN_ACTION_NEW);
        $actions->setPermission(Action::EDIT, PermissionsAdmin::ROLE_ADMIN_ACTION_EDIT);
        $actions->setPermission(Action::SAVE_AND_RETURN, PermissionsAdmin::ROLE_ADMIN_ACTION_EDIT);
        $actions->setPermission(Action::SAVE_AND_CONTINUE, PermissionsAdmin::ROLE_ADMIN_ACTION_EDIT);
        $actions->setPermission(Action::DELETE, PermissionsAdmin::ROLE_ADMIN_ACTION_DELETE);
        $actions->setPermission(Action::DETAIL, PermissionsAdmin::ROLE_ADMIN_ACTION_DETAIL);
        $actions->setPermission(Action::INDEX, PermissionsAdmin::ROLE_ADMIN_ACTION_INDEX);

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        // INDEX
        if (Crud::PAGE_INDEX === $pageName) {
            if (
                PermissionsAdmin::checkAdmin($this->getUser())
                || PermissionsAdmin::checkOwners($this->getUser(), 'ADMIN', 'INDEX')
            ) {
                yield IdField::new('id')->setLabel('admin.field.id');
            }

            yield TextField::new('reference')->setLabel('admin.field.reference');
            yield EmailField::new('email')->setLabel('admin.admin.field.email');
            yield DateField::new('created_at')->setLabel('admin.field.created_at');
            yield DateField::new('updated_at')->setLabel('admin.field.updated_at');
        }

        // DETAIL
        if (Crud::PAGE_DETAIL === $pageName) {
            yield FormField::addPanel('admin.admin.panel_informations')->renderCollapsed(false);
            if (
                PermissionsAdmin::checkAdmin($this->getUser())
                || PermissionsAdmin::checkOwners($this->getUser(), 'ADMIN', 'DETAIL')
            ) {
                yield IdField::new('id')->setLabel('admin.field.id');
                yield TextField::new('displayuuid')->setLabel('admin.field.displayuuid');
            }

            yield TextField::new('reference')->setLabel('admin.field.reference');
            yield EmailField::new('email')->setLabel('admin.admin.field.email');
            yield DateField::new('created_at')->setLabel('admin.field.created_at');
            yield DateField::new('updated_at')->setLabel('admin.field.updated_at');

            yield FormField::addPanel('admin.admin.panel_config')->renderCollapsed();
            yield TextField::new('admin_config.dashboard_title')->setLabel('admin.admin.field.dashboard_title');
            yield IntegerField::new('admin_config.crud_paginator')
                ->setLabel('admin.admin.field.crud_paginator')
                ->setFormTypeOptions(['choice_translation_domain' => false]);

            if (
                (PermissionsAdmin::checkAdmin($this->getUser()))
                || (PermissionsAdmin::checkActions($this->getUser(), 'ADMIN', 'DETAIL'))
                && ($this->isGranted(PermissionsAdmin::ROLE_ALLOWED_TO_EDIT_ROLES))
            ) {
                yield FormField::addPanel('admin.admin.panel_roles')->renderCollapsed();
                yield ArrayField::new('roles')->setLabel('admin.admin.field.roles');
            }

            if (
                (PermissionsAdmin::checkAdmin($this->getUser()))
                || (PermissionsAdmin::checkActions($this->getUser(), 'ADMIN', 'DETAIL'))
                && ($this->isGranted(PermissionsAdmin::ROLE_ALLOWED_TO_EDIT_GROUPS))
            ) {
                yield FormField::addPanel('admin.admin.panel_groups')->renderCollapsed();
                yield ArrayField::new('groups')->setLabel('admin.admin.field.groups');
            }

            if (
                (PermissionsAdmin::checkAdmin($this->getUser()))
                || (PermissionsAdmin::checkActions($this->getUser(), 'ADMIN', 'DETAIL'))
                && ($this->isGranted(PermissionsAdmin::ROLE_ALLOWED_TO_EDIT_ADMINS_SHOPS))
            ) {
                yield FormField::addPanel('admin.admin.panel_shop')->renderCollapsed();
                yield ArrayField::new('shops')->setLabel('admin.admin.field.shops');
            }
        }

        // EDIT
        if (Crud::PAGE_EDIT === $pageName) {
            yield FormField::addPanel('admin.admin.panel_informations')->renderCollapsed(false);
            yield TextField::new('reference')->setFormTypeOptions([
                'disabled' => true,
            ])->setLabel('admin.field.reference');
            yield EmailField::new('email')->setLabel('admin.admin.field.email');
            yield PasswordField::new('plainPassword')->setLabel('admin.admin.field.plain_password');

            yield FormField::addPanel('admin.admin.panel_config')->renderCollapsed();
            yield TextField::new('admin_config.dashboard_title')->setLabel('admin.admin.field.dashboard_title');
            yield ChoiceField::new('admin_config.crud_paginator')
                ->setLabel('admin.admin.field.crud_paginator')
                ->setChoices($this->paginator->choicePaginator())
                ->setFormTypeOptions(['choice_translation_domain' => false]);

            // You can use it if you want to customize roles without using AdminGroup
            // By default this field is disabled
            if (self::ACTIVE_CUSTOM_ROLES) {
                if (
                    (PermissionsAdmin::checkAdmin($this->getUser()))
                    || (PermissionsAdmin::checkActions($this->getUser(), 'ADMIN', 'EDIT'))
                    && ($this->isGranted(PermissionsAdmin::ROLE_ALLOWED_TO_EDIT_ROLES))
                ) {
                    yield FormField::addPanel('admin.admin.panel_roles')->renderCollapsed();
                    yield ChoiceField::new('roles')->setChoices(PermissionsAdmin::getAllRoles())
                        ->allowMultipleChoices(true)
                        ->autocomplete(true)
                        ->setRequired(false)
                        ->setFormTypeOptions(['choice_translation_domain' => false])
                        ->setLabel('admin.admin.field.roles');
                }
            }

            if (
                (PermissionsAdmin::checkAdmin($this->getUser()))
                || (PermissionsAdmin::checkActions($this->getUser(), 'ADMIN', 'EDIT'))
                && ($this->isGranted(PermissionsAdmin::ROLE_ALLOWED_TO_EDIT_GROUPS))
            ) {
                yield FormField::addPanel('admin.admin.panel_groups')->renderCollapsed();
                yield AssociationField::new('groups')->setLabel('admin.admin.field.groups');
            }
        }

        // NEW
        if (Crud::PAGE_NEW === $pageName) {
            yield FormField::addPanel('admin.admin.panel_informations')->renderCollapsed(false);
            yield EmailField::new('email')->setLabel('admin.admin.field.email');
            yield PasswordField::new('password')->setLabel('admin.admin.field.password');

            // You can use it if you want to customize roles without using AdminGroup
            // By default this field is disabled
            if (self::ACTIVE_CUSTOM_ROLES) {
                if (
                    (PermissionsAdmin::checkAdmin($this->getUser()))
                    || (PermissionsAdmin::checkActions($this->getUser(), 'ADMIN', 'NEW'))
                    && ($this->isGranted(PermissionsAdmin::ROLE_ALLOWED_TO_EDIT_ROLES))
                ) {
                    yield FormField::addPanel('admin.admin.panel_roles')->renderCollapsed();
                    yield ChoiceField::new('roles')->setChoices(PermissionsAdmin::getAllRoles())
                        ->allowMultipleChoices(true)
                        ->autocomplete(true)
                        ->setRequired(false)
                        ->setFormTypeOptions(['choice_translation_domain' => false])
                        ->setLabel('admin.admin.field.roles');
                }
            }

            if (
                (PermissionsAdmin::checkAdmin($this->getUser()))
                || (PermissionsAdmin::checkActions($this->getUser(), 'ADMIN', 'NEW'))
                && ($this->isGranted(PermissionsAdmin::ROLE_ALLOWED_TO_EDIT_GROUPS))
            ) {
                yield FormField::addPanel('admin.admin.panel_groups')->renderCollapsed();
                yield AssociationField::new('groups')->setLabel('admin.admin.field.groups');
            }
        }
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        if ($this->isGranted(PermissionsAdmin::IS_ADMIN)) {
            return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        }

        if (PermissionsAdmin::checkOwners($this->getUser(), 'ADMIN', 'INDEX')) {
            return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        }

        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.uuid = :uuid')
            ->setParameter('uuid', $this->getUser()->getUuid()->toBinary()) // put your user id connected here
        ;
    }
}
