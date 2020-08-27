<?php

namespace App\Controller\Admin\CRUD;

use Doctrine\ORM\QueryBuilder;
use App\Entity\Security\AdminGroup;
use Symfony\Component\Form\FormInterface;
use App\Service\Admin\Actions\CustomizeActions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use App\Service\Admin\Permissions\PermissionsAdmin;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;

class AdminGroupCrudController extends AbstractCrudController
{
    protected $actions;

    public function __construct(CustomizeActions $actions)
    {
        $this->actions = $actions;
    }

    public static function getEntityFqcn(): string
    {
        return AdminGroup::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        if (
            PermissionsAdmin::checkAdmin($this->getUser())
            || PermissionsAdmin::checkOwners($this->getUser(), 'ADMIN_GROUP', 'INDEX')
        ) {
            $filters->add('id');
        }

        $filters->add('name');

        return $filters;
    }

    public function configureActions(Actions $actions): Actions
    {
        // Actions adding by default
        $this->actions->all($actions);

        // Default customize actions
        $this->actions->customize($actions);

        // Default reorder actions
        $this->actions->reorder($actions);

        if ($this->isGranted(PermissionsAdmin::IS_ADMIN)) {
            return $actions;
        }

        if ($this->isGranted(PermissionsAdmin::ROLE_ADMIN_GROUP_ACTION_ALL)) {
            return $actions;
        }

        $actions->setPermission(Action::NEW, PermissionsAdmin::ROLE_ADMIN_GROUP_ACTION_NEW);
        $actions->setPermission(Action::SAVE_AND_ADD_ANOTHER, PermissionsAdmin::ROLE_ADMIN_GROUP_ACTION_NEW);
        $actions->setPermission(Action::EDIT, PermissionsAdmin::ROLE_ADMIN_GROUP_ACTION_EDIT);
        $actions->setPermission(Action::SAVE_AND_RETURN, PermissionsAdmin::ROLE_ADMIN_GROUP_ACTION_EDIT);
        $actions->setPermission(Action::SAVE_AND_CONTINUE, PermissionsAdmin::ROLE_ADMIN_GROUP_ACTION_EDIT);
        $actions->setPermission(Action::DELETE, PermissionsAdmin::ROLE_ADMIN_GROUP_ACTION_DELETE);
        $actions->setPermission(Action::DETAIL, PermissionsAdmin::ROLE_ADMIN_GROUP_ACTION_DETAIL);
        $actions->setPermission(Action::INDEX, PermissionsAdmin::ROLE_ADMIN_GROUP_ACTION_INDEX);

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('Admin Group infos');

        // INDEX
        if (Crud::PAGE_INDEX === $pageName) {
            if (
                PermissionsAdmin::checkAdmin($this->getUser())
                || PermissionsAdmin::checkOwners($this->getUser(), 'ADMIN_GROUP', 'INDEX')
            ) {
                yield IdField::new('id');
            }

            yield TextField::new('name');
        }

        // DETAIL
        if (Crud::PAGE_DETAIL === $pageName) {
            if (
                PermissionsAdmin::checkAdmin($this->getUser())
                || PermissionsAdmin::checkOwners($this->getUser(), 'ADMIN_GROUP', 'DETAIL')
            ) {
                yield IdField::new('id');
            }

            yield TextField::new('name');

            if (
                (PermissionsAdmin::checkAdmin($this->getUser()))
                || (PermissionsAdmin::checkActions($this->getUser(), 'ADMIN_GROUP', 'DETAIL'))
                && ($this->isGranted(PermissionsAdmin::ROLE_ALLOWED_TO_EDIT_GROUPS))
            ) {
                yield ArrayField::new('roles');
            }

            if (
                (PermissionsAdmin::checkAdmin($this->getUser()))
                || (PermissionsAdmin::checkActions($this->getUser(), 'ADMIN_GROUP', 'DETAIL'))
                && ($this->isGranted(PermissionsAdmin::ROLE_ALLOWED_TO_EDIT_GROUPS))
            ) {
                yield ArrayField::new('admins');
            }
        }

        // EDIT
        if (Crud::PAGE_EDIT === $pageName) {
            yield IdField::new('id')->hideOnForm();
            yield TextField::new('name');

            if (
                (PermissionsAdmin::checkAdmin($this->getUser()))
                || (PermissionsAdmin::checkActions($this->getUser(), 'ADMIN_GROUP', 'EDIT'))
                && ($this->isGranted(PermissionsAdmin::ROLE_ALLOWED_TO_EDIT_GROUPS))
            ) {
                yield ChoiceField::new('roles')->setChoices(PermissionsAdmin::getAllRoles())
                    ->allowMultipleChoices(true)
                    ->autocomplete(true)
                    ->setRequired(false);
            }
        }

        // NEW
        if (Crud::PAGE_NEW === $pageName) {
            yield IdField::new('id')->hideOnForm();
            yield TextField::new('name');

            if (
                (PermissionsAdmin::checkAdmin($this->getUser()))
                || (PermissionsAdmin::checkActions($this->getUser(), 'ADMIN_GROUP', 'NEW'))
                && ($this->isGranted(PermissionsAdmin::ROLE_ALLOWED_TO_EDIT_GROUPS))
            ) {
                yield ChoiceField::new('roles')->setChoices(PermissionsAdmin::getAllRoles())
                    ->allowMultipleChoices(true)
                    ->autocomplete(true)
                    ->setRequired(false);
            }
        }
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        if ($this->isGranted(PermissionsAdmin::IS_ADMIN)) {
            return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        }

        if (PermissionsAdmin::checkOwners($this->getUser(), 'ADMIN_GROUP', 'INDEX')) {
            return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        }

        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->leftJoin('entity.admins', 'd')
            ->addSelect('d')
            ->andWhere('d.uuid = :uuid')
            ->setParameter('uuid', $this->getUser()->getUuid()) // put your user id connected here
        ;
    }

    public function createEditForm(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormInterface
    {
        if (PermissionsAdmin::checkAdmin($this->getUser())) {
            return parent::createEditFormBuilder($entityDto, $formOptions, $context)->getForm();
        }

        if (PermissionsAdmin::checkActions($this->getUser(), 'ADMIN_GROUP', 'EDIT')) {
            foreach ($entityDto->getInstance()->getAdmins() as $admin) {
                if ($admin->getId() === $this->getUser()->getId()) {
                    return parent::createEditFormBuilder($entityDto, $formOptions, $context)->getForm();
                }
            }
        }

        if (
            PermissionsAdmin::checkOwners($this->getUser(), 'ADMIN_GROUP', 'EDIT')
            && PermissionsAdmin::checkActions($this->getUser(), 'ADMIN_GROUP', 'EDIT')
        ) {
            return parent::createEditFormBuilder($entityDto, $formOptions, $context)->getForm();
        }

        throw new ForbiddenActionException($context);
    }
}
