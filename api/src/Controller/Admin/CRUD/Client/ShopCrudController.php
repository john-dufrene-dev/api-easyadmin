<?php

namespace App\Controller\Admin\CRUD\Client;

use App\Entity\Client\Shop;
use Doctrine\ORM\QueryBuilder;
use App\Form\Type\Client\ShopFileType;
use App\Form\Type\Client\ShopHourType;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ShopCrudController extends AbstractCrudController
{
    protected $actions;

    public function __construct(CustomizeActions $actions)
    {
        $this->actions = $actions;
    }

    public static function getEntityFqcn(): string
    {
        return Shop::class;
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
            || PermissionsAdmin::checkOwners($this->getUser(), 'SHOP', 'INDEX')
        ) {
            $filters->add('id');
            $filters->add('uuid');
        }

        $filters->add('name');
        $filters->add('email');

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

        if ($this->isGranted(PermissionsAdmin::ROLE_SHOP_ACTION_ALL)) {
            return $actions;
        }

        $actions->setPermission(Action::NEW, PermissionsAdmin::ROLE_SHOP_ACTION_NEW);
        $actions->setPermission(Action::SAVE_AND_ADD_ANOTHER, PermissionsAdmin::ROLE_SHOP_ACTION_NEW);
        $actions->setPermission(Action::EDIT, PermissionsAdmin::ROLE_SHOP_ACTION_EDIT);
        $actions->setPermission(Action::SAVE_AND_RETURN, PermissionsAdmin::ROLE_SHOP_ACTION_EDIT);
        $actions->setPermission(Action::SAVE_AND_CONTINUE, PermissionsAdmin::ROLE_SHOP_ACTION_EDIT);
        $actions->setPermission(Action::DELETE, PermissionsAdmin::ROLE_SHOP_ACTION_DELETE);
        $actions->setPermission(Action::DETAIL, PermissionsAdmin::ROLE_SHOP_ACTION_DETAIL);
        $actions->setPermission(Action::INDEX, PermissionsAdmin::ROLE_SHOP_ACTION_INDEX);

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        // INDEX
        if (Crud::PAGE_INDEX === $pageName) {
            if (PermissionsAdmin::checkAdmin($this->getUser())) {
                yield IdField::new('id')->setLabel('admin.field.id');
            }

            yield TextField::new('displayuuid')->setLabel('admin.field.displayuuid');
            yield TextField::new('name')->setLabel('admin.shop.field.name');
            yield EmailField::new('email')->setLabel('admin.shop.field.email');
            yield DateField::new('created_at')->setLabel('admin.field.created_at');
            yield DateField::new('updated_at')->setLabel('admin.field.updated_at');

            if (PermissionsAdmin::checkAdmin($this->getUser())) {
                yield BooleanField::new('is_active')->setLabel('admin.shop.field.is_active');
            }
        }

        // DETAIL
        if (Crud::PAGE_DETAIL === $pageName) {
            yield FormField::addPanel('admin.shop.panel_shop')->renderCollapsed(false);
            if (
                PermissionsAdmin::checkAdmin($this->getUser())
                || PermissionsAdmin::checkOwners($this->getUser(), 'SHOP', 'DETAIL')
            ) {
                yield IdField::new('id')->setLabel('admin.field.id');
            }

            yield TextField::new('displayuuid')->setLabel('admin.field.displayuuid');
            yield TextField::new('name')->setLabel('admin.shop.field.name');
            yield EmailField::new('email')->setLabel('admin.shop.field.email');

            if (PermissionsAdmin::checkAdmin($this->getUser())) {
                yield BooleanField::new('is_active')->setLabel('admin.shop.field.is_active');
            }

            yield FormField::addPanel('admin.shop.panel_shop_info')->renderCollapsed();
            yield CountryField::new('shop_info.country')->setLabel('admin.shop.field.country');

            yield BooleanField::new('shop_info.shipping_click')
                ->setCustomOption('renderAsSwitch', false)
                ->setLabel('admin.shop.field.shipping_click');
            yield BooleanField::new('shop_info.shipping_delivery')
                ->setCustomOption('renderAsSwitch', false)
                ->setLabel('admin.shop.field.shipping_delivery');

            // @Todo : transform in array to show shop hour
            // yield ArrayField::new('shop_info.shop_hour');

            yield FormField::addPanel('admin.shop.panel_shop_files')->renderCollapsed();
            yield CollectionField::new('shop_files')
                ->setTemplatePath('admin/fields/clients/collection_shop_images.html.twig');

            if (
                (PermissionsAdmin::checkAdmin($this->getUser()))
                || (PermissionsAdmin::checkActions($this->getUser(), 'SHOP', 'DETAIL'))
                && ($this->isGranted(PermissionsAdmin::ROLE_ALLOWED_TO_EDIT_ADMINS_SHOPS))
            ) {
                yield FormField::addPanel('admin.shop.panel_shop_admin')->renderCollapsed();
                yield ArrayField::new('admins')->setLabel('admin.shop.field.admins');
            }

            yield DateField::new('created_at')->setLabel('admin.field.created_at');
            yield DateField::new('updated_at')->setLabel('admin.field.updated_at');
        }

        // EDIT
        if (Crud::PAGE_EDIT === $pageName) {
            yield FormField::addPanel('admin.shop.panel_shop')->renderCollapsed(false);

            yield TextField::new('displayuuid')->setFormTypeOptions([
                'disabled' => true,
            ])->setLabel('admin.field.displayuuid');
            yield TextField::new('name')->setLabel('admin.shop.field.name');
            yield EmailField::new('email')->setLabel('admin.shop.field.email');

            if (PermissionsAdmin::checkAdmin($this->getUser())) {
                yield BooleanField::new('is_active')->setLabel('admin.shop.field.is_active');
            }

            yield FormField::addPanel('admin.shop.panel_shop_info')->renderCollapsed();

            yield CountryField::new('shop_info.country')->setLabel('admin.shop.field.country');

            yield BooleanField::new('shop_info.shipping_click')
                ->setCustomOption('renderAsSwitch', false)
                ->setLabel('admin.shop.field.shipping_click');
            yield BooleanField::new('shop_info.shipping_delivery')
                ->setCustomOption('renderAsSwitch', false)
                ->setLabel('admin.shop.field.shipping_delivery');

            // @Todo : Reorder AddPanel()
            yield CollectionField::new('shop_info.shop_hour')->setLabel('admin.shop.field.shop_hour')
                ->setCustomOption('allowAdd', false) //disable add field
                ->setCustomOption('allowDelete', false) //disable remove field
                ->setFormTypeOptions([
                    'entry_type' => ShopHourType::class,
                    'entry_options' => [
                        'attr' => [
                            'class' => 'd-inline-flex p-2',
                        ],
                    ],
                ]);

            yield FormField::addPanel('admin.shop.panel_shop_files')->renderCollapsed();
            yield CollectionField::new('shop_files')->setEntryType(ShopFileType::class)
                ->setFormTypeOption('by_reference', false);

            if (
                (PermissionsAdmin::checkAdmin($this->getUser()))
                || (PermissionsAdmin::checkActions($this->getUser(), 'SHOP', 'EDIT'))
                && ($this->isGranted(PermissionsAdmin::ROLE_ALLOWED_TO_EDIT_ADMINS_SHOPS))
            ) {
                yield FormField::addPanel('admin.shop.panel_shop_admin')->renderCollapsed();
                yield AssociationField::new('admins')->setLabel('admin.shop.field.admins');
            }
        }

        // NEW
        if (Crud::PAGE_NEW === $pageName) {
            yield FormField::addPanel('admin.shop.panel_shop')->renderCollapsed(false);
            yield TextField::new('name')->setLabel('admin.shop.field.name');
            yield EmailField::new('email')->setLabel('admin.shop.field.email');

            if (PermissionsAdmin::checkAdmin($this->getUser())) {
                yield BooleanField::new('is_active')->setLabel('admin.shop.field.is_active');
            }

            if (
                (PermissionsAdmin::checkAdmin($this->getUser()))
                || (PermissionsAdmin::checkActions($this->getUser(), 'SHOP', 'NEW'))
                && ($this->isGranted(PermissionsAdmin::ROLE_ALLOWED_TO_EDIT_ADMINS_SHOPS))
            ) {
                yield FormField::addPanel('admin.shop.panel_shop_admin')->renderCollapsed();
                yield AssociationField::new('admins')->setLabel('admin.shop.field.admins');
            }
        }
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        if ($this->isGranted(PermissionsAdmin::IS_ADMIN)) {
            return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        }

        if (PermissionsAdmin::checkOwners($this->getUser(), 'SHOP', 'INDEX')) {
            return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        }

        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->leftJoin('entity.admins', 'd')
            ->addSelect('d')
            ->andWhere('d.uuid = :uuid')
            ->setParameter('uuid', $this->getUser()->getUuid()) // put your user id connected here
        ;
    }
}
