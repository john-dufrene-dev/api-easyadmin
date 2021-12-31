<?php

namespace App\Controller\Admin\CRUD\Security;

use App\Entity\Security\Admin;
use Doctrine\ORM\QueryBuilder;
use App\Service\Admin\Field\PasswordField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use App\Controller\Admin\AbstractBaseCrudController;
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

class AdminCrudController extends AbstractBaseCrudController
{
    public const ACTIVE_CUSTOM_ROLES = false; // Enable or disable custom roles

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
            ->showEntityActionsInlined()
            ->setDefaultSort(['id' => 'ASC'])
            ->setDateFormat('full')
            ->setTimeFormat('full');
    }

    public function configureFilters(Filters $filters): Filters
    {
        if (
            $this->pms()->isAdmin($this->getUser())
            || $this->pms()->canUseOwners($this->getUser(), 'ADMIN', 'INDEX')
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
        $this->adminCustomizeActions()->all($actions);

        // Action new impersonation route
        if ($this->isGranted($this->pms()->roleAllowedToSwitch)) {
            $switch_user = $this->adminCustomizeActions()->impersonate($actions);
            $actions->add(Crud::PAGE_INDEX, $switch_user);
            $actions->setPermission($this->adminCustomizeActions()::IMPERSONATE, $this->pms()->roleAllowedToSwitch);
        }

        // Default customize actions
        $this->adminCustomizeActions()->customize($actions);

        // Default reorder actions
        $this->adminCustomizeActions()->reorder($actions);

        if ($this->isGranted($this->pms()->isAdmin)) {
            return $actions;
        }

        if ($this->isGranted($this->pms()->getAction('ADMIN'))) {
            return $actions;
        }

        $actions->setPermission(Action::NEW, $this->pms()->getAction('ADMIN', 'NEW'));
        $actions->setPermission(Action::SAVE_AND_ADD_ANOTHER, $this->pms()->getAction('ADMIN', 'NEW'));
        $actions->setPermission(Action::EDIT, $this->pms()->getAction('ADMIN', 'EDIT'));
        $actions->setPermission(Action::SAVE_AND_RETURN, $this->pms()->getAction('ADMIN', 'EDIT'));
        $actions->setPermission(Action::SAVE_AND_CONTINUE, $this->pms()->getAction('ADMIN', 'EDIT'));
        $actions->setPermission(Action::DELETE, $this->pms()->getAction('ADMIN', 'DELETE'));
        $actions->setPermission(Action::BATCH_DELETE, $this->pms()->getAction('ADMIN', 'DELETE'));
        $actions->setPermission(Action::DETAIL, $this->pms()->getAction('ADMIN', 'DETAIL'));
        $actions->setPermission(Action::INDEX, $this->pms()->getAction('ADMIN', 'INDEX'));

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        // INDEX
        if (Crud::PAGE_INDEX === $pageName) {
            if (
                $this->pms()->isAdmin($this->getUser())
                || $this->pms()->canUseOwners($this->getUser(), 'ADMIN', 'INDEX')
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
                $this->pms()->isAdmin($this->getUser())
                || $this->pms()->canUseOwners($this->getUser(), 'ADMIN', 'DETAIL')
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
                ($this->pms()->isAdmin($this->getUser()))
                || ($this->pms()->canUseActions($this->getUser(), 'ADMIN', 'DETAIL'))
                && ($this->isGranted($this->pms()->roleAllowedToEditRoles))
            ) {
                yield FormField::addPanel('admin.admin.panel_roles')->renderCollapsed();
                yield ArrayField::new('roles')->setLabel('admin.admin.field.roles');
            }

            if (
                ($this->pms()->isAdmin($this->getUser()))
                || ($this->pms()->canUseActions($this->getUser(), 'ADMIN', 'DETAIL'))
                && ($this->isGranted($this->pms()->roleAllowedToEditGroups))
            ) {
                yield FormField::addPanel('admin.admin.panel_groups')->renderCollapsed();
                yield ArrayField::new('groups')->setLabel('admin.admin.field.groups');
            }

            if (
                ($this->pms()->isAdmin($this->getUser()))
                || ($this->pms()->canUseActions($this->getUser(), 'ADMIN', 'DETAIL'))
                && ($this->isGranted($this->pms()->roleAllowedToEditAdminShops))
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
                ->setChoices($this->adminpaginatorFactory()->choicePaginator())
                ->setFormTypeOptions(['choice_translation_domain' => false]);

            // You can use it if you want to customize roles without using AdminGroup
            // By default this field is disabled
            if (self::ACTIVE_CUSTOM_ROLES) {
                if (
                    ($this->pms()->isAdmin($this->getUser()))
                    || ($this->pms()->canUseActions($this->getUser(), 'ADMIN', 'EDIT'))
                    && ($this->isGranted($this->pms()->roleAllowedToEditRoles))
                ) {
                    yield FormField::addPanel('admin.admin.panel_roles')->renderCollapsed();
                    yield ChoiceField::new('roles')->setChoices($this->pms()->getRoles())
                        ->allowMultipleChoices(true)
                        ->autocomplete(true)
                        ->setRequired(false)
                        ->setFormTypeOptions(['choice_translation_domain' => false])
                        ->setLabel('admin.admin.field.roles');
                }
            }

            if (
                ($this->pms()->isAdmin($this->getUser()))
                || ($this->pms()->canUseActions($this->getUser(), 'ADMIN', 'EDIT'))
                && ($this->isGranted($this->pms()->roleAllowedToEditGroups))
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
                    ($this->pms()->isAdmin($this->getUser()))
                    || ($this->pms()->canUseActions($this->getUser(), 'ADMIN', 'NEW'))
                    && ($this->isGranted($this->pms()->roleAllowedToEditRoles))
                ) {
                    yield FormField::addPanel('admin.admin.panel_roles')->renderCollapsed();
                    yield ChoiceField::new('roles')->setChoices($this->pms()->getRoles())
                        ->allowMultipleChoices(true)
                        ->autocomplete(true)
                        ->setRequired(false)
                        ->setFormTypeOptions(['choice_translation_domain' => false])
                        ->setLabel('admin.admin.field.roles');
                }
            }

            if (
                ($this->pms()->isAdmin($this->getUser()))
                || ($this->pms()->canUseActions($this->getUser(), 'ADMIN', 'NEW'))
                && ($this->isGranted($this->pms()->roleAllowedToEditGroups))
            ) {
                yield FormField::addPanel('admin.admin.panel_groups')->renderCollapsed();
                yield AssociationField::new('groups')->setLabel('admin.admin.field.groups');
            }
        }
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        if ($this->isGranted($this->pms()->isAdmin)) {
            return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        }

        if ($this->pms()->canUseOwners($this->getUser(), 'ADMIN', 'INDEX')) {
            return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        }

        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.uuid = :uuid')
            ->setParameter('uuid', $this->getUser()->getUuid()->toBinary()) // put your user id connected here
        ;
    }
}
