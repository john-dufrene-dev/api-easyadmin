<?php

namespace App\Controller\Admin\CRUD;

use App\Entity\Security\Admin;
use App\Service\Admin\Field\PasswordField;
use App\Service\Admin\Actions\CustomizeActions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use App\Service\Admin\Permissions\PermissionsAdmin;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AdminCrudController extends AbstractCrudController
{
    protected $actions;

    public function __construct(CustomizeActions $actions)
    {
        $this->actions = $actions;
    }

    public static function getEntityFqcn(): string
    {
        return Admin::class;
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
        yield FormField::addPanel('User infos');
        yield IdField::new('id')->hideOnForm();

        if (Crud::PAGE_INDEX === $pageName) {
            yield TextField::new('uuid');
            yield EmailField::new('email');
        }

        if (Crud::PAGE_DETAIL === $pageName) {
            yield TextField::new('uuid');
            yield EmailField::new('email');
        }

        if (Crud::PAGE_EDIT === $pageName) {
            yield TextField::new('uuid')->setFormTypeOptions([
                'disabled' => true,
            ]);
            yield EmailField::new('email');
            yield PasswordField::new('plainPassword');
        }

        if (Crud::PAGE_NEW === $pageName) {
            yield HiddenField::new('uuid');
            yield EmailField::new('email');
            yield PasswordField::new('password');
        }
    }
}
