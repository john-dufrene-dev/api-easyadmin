<?php

namespace App\Controller\Admin\CRUD\Customer;

use App\Entity\Customer\User;
use Doctrine\ORM\QueryBuilder;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    protected $actions;

    protected $export;

    protected $adminContextFactory;

    public function __construct(CustomizeActions $actions)
    {
        $this->actions = $actions;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setDefaultSort(['id' => 'ASC'])
            ->setDateFormat('full')
            ->setTimeFormat('full');
    }

    public function configureFilters(Filters $filters): Filters
    {
        if (
            PermissionsAdmin::checkAdmin($this->getUser())
            || PermissionsAdmin::checkOwners($this->getUser(), 'USER', 'INDEX')
        ) {
            $filters->add('id');
            $filters->add('uuid');
        }

        $filters->add('reference');
        $filters->add('email');
        // @todo : try to add association filter
        // $filters->add(EntityFilter::new('shop')); // Not working for now !

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

        if ($this->isGranted(PermissionsAdmin::ROLE_USER_ACTION_ALL)) {
            return $actions;
        }

        $actions->setPermission(Action::NEW, PermissionsAdmin::ROLE_USER_ACTION_NEW);
        $actions->setPermission(Action::SAVE_AND_ADD_ANOTHER, PermissionsAdmin::ROLE_USER_ACTION_NEW);
        $actions->setPermission(Action::EDIT, PermissionsAdmin::ROLE_USER_ACTION_EDIT);
        $actions->setPermission(Action::SAVE_AND_RETURN, PermissionsAdmin::ROLE_USER_ACTION_EDIT);
        $actions->setPermission(Action::SAVE_AND_CONTINUE, PermissionsAdmin::ROLE_USER_ACTION_EDIT);
        $actions->setPermission(Action::DELETE, PermissionsAdmin::ROLE_USER_ACTION_DELETE);
        $actions->setPermission(Action::DETAIL, PermissionsAdmin::ROLE_USER_ACTION_DETAIL);
        $actions->setPermission(Action::INDEX, PermissionsAdmin::ROLE_USER_ACTION_INDEX);

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        // @todo : Finish all the model User
        // INDEX
        if (Crud::PAGE_INDEX === $pageName) {
            if (PermissionsAdmin::checkAdmin($this->getUser())) {
                yield IdField::new('id')->setLabel('admin.field.id');
            }

            yield TextField::new('reference')->setLabel('admin.field.reference');
            yield EmailField::new('email')->setLabel('admin.user.field.email');
            yield DateField::new('created_at')->setLabel('admin.field.created_at');
            yield DateField::new('updated_at')->setLabel('admin.field.updated_at');
            yield BooleanField::new('is_active')->setLabel('admin.user.field.is_active');
            yield BooleanField::new('is_verified')->setLabel('admin.user.field.is_verified');
        }

        // DETAIL
        if (Crud::PAGE_DETAIL === $pageName) {
            yield FormField::addPanel('admin.user.panel_user')->renderCollapsed(false);
            if (
                PermissionsAdmin::checkAdmin($this->getUser())
                || PermissionsAdmin::checkOwners($this->getUser(), 'USER', 'DETAIL')
            ) {
                yield IdField::new('id')->setLabel('admin.field.id');
                yield TextField::new('displayuuid')->setLabel('admin.field.displayuuid');
            }

            yield TextField::new('reference')->setLabel('admin.field.reference');
            yield EmailField::new('email')->setLabel('admin.user.field.email');
            yield BooleanField::new('is_active')->setLabel('admin.user.field.is_active');
            yield BooleanField::new('is_verified')->setLabel('admin.user.field.is_verified');

            yield DateField::new('created_at')->setLabel('admin.field.created_at');
            yield DateField::new('updated_at')->setLabel('admin.field.updated_at');

            if (
                (PermissionsAdmin::checkAdmin($this->getUser()))
                || (PermissionsAdmin::checkActions($this->getUser(), 'SHOP', 'DETAIL'))
            ) {
                yield FormField::addPanel('admin.user.panel_user')->renderCollapsed();
                yield TextField::new('shop')->setLabel('admin.user.field.shop');
            }
        }

        // EDIT
        if (Crud::PAGE_EDIT === $pageName) {
            yield FormField::addPanel('admin.user.panel_user')->renderCollapsed(false);

            yield TextField::new('reference')->setFormTypeOptions([
                'disabled' => true,
            ])->setLabel('admin.field.reference');
            yield EmailField::new('email')->setLabel('admin.user.field.email');
            yield PasswordField::new('plainPassword')->setLabel('admin.user.field.plain_password');

            yield BooleanField::new('is_active')->setLabel('admin.user.field.is_active');
            yield BooleanField::new('is_verified')->setLabel('admin.user.field.is_verified');

            if (
                (PermissionsAdmin::checkAdmin($this->getUser()))
                || (PermissionsAdmin::checkActions($this->getUser(), 'USER', 'EDIT'))
            ) {
                yield FormField::addPanel('admin.user.panel_shop_id')->renderCollapsed();
                yield AssociationField::new('shop')
                    // ->autocomplete(true)
                    ->setLabel('admin.user.field.shop');
            }
        }

        // NEW
        if (Crud::PAGE_NEW === $pageName) {
            yield FormField::addPanel('admin.user.panel_user')->renderCollapsed(false);
            yield EmailField::new('email')->setLabel('admin.user.field.email');
            yield PasswordField::new('password')->setLabel('admin.user.field.password');

            yield BooleanField::new('is_active')->setLabel('admin.user.field.is_active');
            yield BooleanField::new('is_verified')->setLabel('admin.user.field.is_verified');

            if (
                (PermissionsAdmin::checkAdmin($this->getUser()))
                || (PermissionsAdmin::checkActions($this->getUser(), 'USER', 'NEW'))
            ) {
                yield FormField::addPanel('admin.user.panel_shop_id')->renderCollapsed();
                yield AssociationField::new('shop')->setLabel('admin.user.field.shop');
            }
        }
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        if ($this->isGranted(PermissionsAdmin::IS_ADMIN)) {
            return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        }

        if (PermissionsAdmin::checkOwners($this->getUser(), 'USER', 'INDEX')) {
            return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        }

        $uuid = null;
        $shops = $this->getUser()->getShops();

        if (count($shops) !== 0) {
            $uuids = [];
            foreach ($this->getUser()->getShops() as $shop) {
                \array_push($uuids, $shop->getUuid()->toBinary());
            }
            $uuid = $uuids;
        }

        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->leftJoin('entity.shop', 's')
            ->addSelect('s')
            ->andWhere('entity.shop IN (:shops)')
            ->setParameter('shops', $uuid) // put your user id connected here
        ;
    }
}
