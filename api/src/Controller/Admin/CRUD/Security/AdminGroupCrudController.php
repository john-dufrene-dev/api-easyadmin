<?php

namespace App\Controller\Admin\CRUD\Security;

use Doctrine\ORM\QueryBuilder;
use App\Entity\Security\AdminGroup;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;

class AdminGroupCrudController extends AbstractBaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return AdminGroup::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        if (
            $this->pms()->isAdmin($this->getUser())
            || $this->pms()->canUseOwners($this->getUser(), 'ADMIN_GROUP', 'INDEX')
        ) {
            $filters->add('id');
            $filters->add('uuid');
        }

        $filters->add('reference');
        $filters->add('name');

        return $filters;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('edit', function (?AdminGroup $group) {
                return $group ? $group->getName() : null;
            })
            ->setPageTitle('detail', function (?AdminGroup $group) {
                return $group ? $group->getName() : null;
            })
            ->showEntityActionsInlined()
            ->setDefaultSort(['id' => 'ASC'])
            ->setDateFormat('full')
            ->setTimeFormat('full');
    }

    public function configureActions(Actions $actions): Actions
    {
        // Actions adding by default
        $this->adminCustomizeActions()->all($actions);

        // Default customize actions
        $this->adminCustomizeActions()->customize($actions);

        // Default reorder actions
        $this->adminCustomizeActions()->reorder($actions);

        if ($this->isGranted($this->pms()->isAdmin)) {
            return $actions;
        }

        if ($this->isGranted($this->pms()->getAction('ADMIN_GROUP'))) {
            return $actions;
        }

        $actions->setPermission(Action::NEW, $this->pms()->getAction('ADMIN_GROUP', 'NEW'));
        $actions->setPermission(Action::SAVE_AND_ADD_ANOTHER, $this->pms()->getAction('ADMIN_GROUP', 'NEW'));
        $actions->setPermission(Action::EDIT, $this->pms()->getAction('ADMIN_GROUP', 'EDIT'));
        $actions->setPermission(Action::SAVE_AND_RETURN, $this->pms()->getAction('ADMIN_GROUP', 'EDIT'));
        $actions->setPermission(Action::SAVE_AND_CONTINUE, $this->pms()->getAction('ADMIN_GROUP', 'EDIT'));
        $actions->setPermission(Action::DELETE, $this->pms()->getAction('ADMIN_GROUP', 'DELETE'));
        $actions->setPermission(Action::DETAIL, $this->pms()->getAction('ADMIN_GROUP', 'DETAIL'));
        $actions->setPermission(Action::INDEX, $this->pms()->getAction('ADMIN_GROUP', 'INDEX'));

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('admin.group.title');

        // INDEX
        if (Crud::PAGE_INDEX === $pageName) {
            if (
                $this->pms()->isAdmin($this->getUser())
                || $this->pms()->canUseOwners($this->getUser(), 'ADMIN_GROUP', 'INDEX')
            ) {
                yield IdField::new('id')->setLabel('admin.field.id');
            }

            yield TextField::new('reference')->setLabel('admin.field.reference');
            yield TextField::new('name')->setLabel('admin.group.field.name');
            yield DateField::new('created_at')->setLabel('admin.field.created_at');
            yield DateField::new('updated_at')->setLabel('admin.field.updated_at');
        }

        // DETAIL
        if (Crud::PAGE_DETAIL === $pageName) {
            if (
                $this->pms()->isAdmin($this->getUser())
                || $this->pms()->canUseOwners($this->getUser(), 'ADMIN_GROUP', 'DETAIL')
            ) {
                yield IdField::new('id')->setLabel('admin.field.id');
                yield TextField::new('displayuuid')->setLabel('admin.field.displayuuid');
            }

            yield TextField::new('reference')->setLabel('admin.field.reference');
            yield TextField::new('name')->setLabel('admin.group.field.name');

            if (
                ($this->pms()->isAdmin($this->getUser()))
                || ($this->pms()->canUseActions($this->getUser(), 'ADMIN_GROUP', 'DETAIL'))
                && ($this->isGranted($this->pms()->roleAllowedToEditGroups))
            ) {
                yield ArrayField::new('roles')->setLabel('admin.group.field.roles');
            }

            if (
                ($this->pms()->isAdmin($this->getUser()))
                || ($this->pms()->canUseActions($this->getUser(), 'ADMIN_GROUP', 'DETAIL'))
                && ($this->isGranted($this->pms()->roleAllowedToEditGroups))
            ) {
                yield ArrayField::new('admins')->setLabel('admin.group.field.admins');
            }

            yield DateField::new('created_at')->setLabel('admin.field.created_at');
            yield DateField::new('updated_at')->setLabel('admin.field.updated_at');
        }

        // EDIT
        if (Crud::PAGE_EDIT === $pageName) {
            yield TextField::new('reference')->setFormTypeOptions([
                'disabled' => true,
            ])->setLabel('admin.field.reference');
            yield TextField::new('name')->setLabel('admin.group.field.name');

            if (
                ($this->pms()->isAdmin($this->getUser()))
                || ($this->pms()->canUseActions($this->getUser(), 'ADMIN_GROUP', 'EDIT'))
                && ($this->isGranted($this->pms()->roleAllowedToEditGroups))
            ) {
                yield ChoiceField::new('roles')->setChoices($this->pms()->getRoles())
                    ->allowMultipleChoices(true)
                    ->setFormTypeOptions(['choice_translation_domain' => false])
                    ->autocomplete(true)
                    ->setRequired(false)
                    ->setLabel('admin.group.field.roles');
            }
        }

        // NEW
        if (Crud::PAGE_NEW === $pageName) {
            yield TextField::new('name')->setLabel('admin.group.field.name');

            if (
                ($this->pms()->isAdmin($this->getUser()))
                || ($this->pms()->canUseActions($this->getUser(), 'ADMIN_GROUP', 'EDIT'))
                && ($this->isGranted($this->pms()->roleAllowedToEditGroups))
            ) {
                yield ChoiceField::new('roles')->setChoices($this->pms()->getRoles())
                    ->allowMultipleChoices(true)
                    ->autocomplete(true)
                    ->setRequired(false)
                    ->setLabel('admin.group.field.roles');
            }
        }
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        if ($this->isGranted($this->pms()->isAdmin)) {
            return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        }

        if ($this->pms()->canUseOwners($this->getUser(), 'ADMIN_GROUP', 'INDEX')) {
            return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        }

        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->leftJoin('entity.admins', 'd')
            ->addSelect('d')
            ->andWhere('d.uuid = :uuid')
            ->setParameter('uuid', $this->getUser()->getUuid()->toBinary()) // put your user id connected here
        ;
    }
}
