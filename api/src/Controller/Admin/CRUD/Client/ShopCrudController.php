<?php

namespace App\Controller\Admin\CRUD\Client;

use App\Entity\Client\Shop;
use Doctrine\ORM\QueryBuilder;
use App\Form\Type\Client\ShopFileType;
use App\Form\Type\Client\ShopHourType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;

class ShopCrudController extends AbstractBaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return Shop::class;
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $assets->addWebpackEncoreEntry('admin/crud/shop');
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('edit', function (?Shop $shop) {
                return $shop ? $shop->getName() : null;
            })
            ->setPageTitle('detail', function (?Shop $shop) {
                return $shop ? $shop->getName() : null;
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
            || $this->pms()->canUseOwners($this->getUser(), 'SHOP', 'INDEX')
        ) {
            $filters->add('id');
            $filters->add('uuid');
        }

        $filters->add('reference');
        $filters->add('name');
        $filters->add('email');
        // @todo : try to add association filter
        // $filters->add(EntityFilter::new('shop_info')); // Not working for now !

        return $filters;
    }

    public function configureActions(Actions $actions): Actions
    {
        // Actions adding by default
        $this->adminCustomizeActions()->all($actions);

        // Action new export csv
        if (
            $this->pms()->isAdmin($this->getUser())
            || $this->pms()->canUseActions($this->getUser(), 'SHOP', 'EXPORT')
        ) {
            $export = $this->adminCustomizeActions()->export('exportCsv', 'csv');
            $actions->add(Crud::PAGE_INDEX, $export);
        }

        // Action Enable/disable
        $enable = $this->adminCustomizeActions()->batchToggleActive('enableShops', true);
        $disable = $this->adminCustomizeActions()->batchToggleActive('disableShops', false);
        $actions->addBatchAction($enable);
        $actions->addBatchAction($disable);

        // Default customize actions
        $this->adminCustomizeActions()->customize($actions);

        // Default reorder actions
        $this->adminCustomizeActions()->reorder($actions);

        if ($this->isGranted($this->pms()->isAdmin)) {
            return $actions;
        }

        if ($this->isGranted($this->pms()->getAction('SHOP'))) {
            return $actions;
        }

        $actions->setPermission(Action::NEW, $this->pms()->getAction('SHOP', 'NEW'));
        $actions->setPermission(Action::SAVE_AND_ADD_ANOTHER, $this->pms()->getAction('SHOP', 'NEW'));
        $actions->setPermission(Action::EDIT, $this->pms()->getAction('SHOP', 'EDIT'));
        $actions->setPermission(Action::SAVE_AND_RETURN, $this->pms()->getAction('SHOP', 'EDIT'));
        $actions->setPermission(Action::SAVE_AND_CONTINUE, $this->pms()->getAction('SHOP', 'EDIT'));
        $actions->setPermission(Action::DELETE, $this->pms()->getAction('SHOP', 'DELETE'));
        $actions->setPermission(Action::DETAIL, $this->pms()->getAction('SHOP', 'DETAIL'));
        $actions->setPermission(Action::INDEX, $this->pms()->getAction('SHOP', 'INDEX'));

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        // INDEX
        if (Crud::PAGE_INDEX === $pageName) {
            if ($this->pms()->isAdmin($this->getUser())) {
                yield IdField::new('id')->setLabel('admin.field.id');
            }

            yield TextField::new('reference')->setLabel('admin.field.reference');
            yield TextField::new('name')->setLabel('admin.shop.field.name');
            yield EmailField::new('email')->setLabel('admin.shop.field.email');
            yield DateField::new('created_at')->setLabel('admin.field.created_at');
            yield DateField::new('updated_at')->setLabel('admin.field.updated_at');

            if ($this->pms()->isAdmin($this->getUser())) {
                yield BooleanField::new('is_active')->setLabel('admin.shop.field.is_active');
            }
        }

        // DETAIL
        if (Crud::PAGE_DETAIL === $pageName) {
            yield FormField::addPanel('admin.shop.panel_shop')->renderCollapsed(false);
            if (
                $this->pms()->isAdmin($this->getUser())
                || $this->pms()->canUseOwners($this->getUser(), 'SHOP', 'DETAIL')
            ) {
                yield IdField::new('id')->setLabel('admin.field.id');
                yield TextField::new('displayuuid')->setLabel('admin.field.displayuuid');
            }

            yield TextField::new('reference')->setLabel('admin.field.reference');
            yield TextField::new('name')->setLabel('admin.shop.field.name');
            yield EmailField::new('email')->setLabel('admin.shop.field.email');

            if ($this->pms()->isAdmin($this->getUser())) {
                yield BooleanField::new('is_active')->setLabel('admin.shop.field.is_active');
            }

            yield DateField::new('created_at')->setLabel('admin.field.created_at');
            yield DateField::new('updated_at')->setLabel('admin.field.updated_at');

            yield FormField::addPanel('admin.shop.panel_shop_info')->renderCollapsed();
            yield CountryField::new('shop_info.country')->setLabel('admin.shop.field.country');
            yield TextField::new('shop_info.city')->setLabel('admin.shop.field.city');
            yield TextField::new('shop_info.postal_code')->setLabel('admin.shop.field.postal_code');
            yield TextField::new('shop_info.address')->setLabel('admin.shop.field.address');
            yield NumberField::new('shop_info.latitude')->setLabel('admin.shop.field.latitude')
                ->setNumDecimals(8);
            yield NumberField::new('shop_info.longitude')->setLabel('admin.shop.field.longitude')
                ->setNumDecimals(8);
            yield TelephoneField::new('shop_info.phone')->setLabel('admin.shop.field.phone');

            yield CollectionField::new('shop_info.shop_hour')
                ->setTemplatePath('admin/fields/clients/collection_shop_hour.html.twig')
                ->setLabel('admin.shop.field.shop_hour');

            yield FormField::addPanel('admin.shop.panel_shop_files')->renderCollapsed();
            yield CollectionField::new('shop_files')
                ->setTemplatePath('admin/fields/clients/collection_shop_images.html.twig');

            yield FormField::addPanel('admin.shop.panel_shop_services')->renderCollapsed();
            yield BooleanField::new('shop_info.shipping_click')
                ->setCustomOption('renderAsSwitch', false)
                ->setLabel('admin.shop.field.shipping_click');
            yield BooleanField::new('shop_info.shipping_delivery')
                ->setCustomOption('renderAsSwitch', false)
                ->setLabel('admin.shop.field.shipping_delivery');

            if (
                ($this->pms()->isAdmin($this->getUser()))
                || ($this->pms()->canUseActions($this->getUser(), 'SHOP', 'DETAIL'))
                && ($this->isGranted($this->pms()->roleAllowedToEditAdminShops))
            ) {
                yield FormField::addPanel('admin.shop.panel_shop_admin')->renderCollapsed();
                yield ArrayField::new('admins')->setLabel('admin.shop.field.admins');
            }
        }

        // EDIT
        if (Crud::PAGE_EDIT === $pageName) {
            yield FormField::addPanel('admin.shop.panel_shop')->renderCollapsed(false);

            yield TextField::new('reference')->setFormTypeOptions([
                'disabled' => true,
            ])->setLabel('admin.field.reference')->setColumns(4);
            yield TextField::new('name')->setLabel('admin.shop.field.name')->setColumns(4);
            yield EmailField::new('email')->setLabel('admin.shop.field.email')->setColumns(4);

            if ($this->pms()->isAdmin($this->getUser())) {
                yield BooleanField::new('is_active')->setLabel('admin.shop.field.is_active');
            }

            yield FormField::addPanel('admin.shop.panel_shop_info')->renderCollapsed();

            yield CountryField::new('shop_info.country')
                ->setFormTypeOptions(['choice_translation_domain' => false])
                ->setLabel('admin.shop.field.country')->setColumns(4);
            yield TextField::new('shop_info.city')->setLabel('admin.shop.field.city')->setColumns(4);
            yield TextField::new('shop_info.postal_code')->setLabel('admin.shop.field.postal_code')->setColumns(4);
            yield TextField::new('shop_info.address')->setLabel('admin.shop.field.address')->setColumns(12);
            yield NumberField::new('shop_info.latitude')->setLabel('admin.shop.field.latitude')
                ->setNumDecimals(8)->setColumns(6);
            yield NumberField::new('shop_info.longitude')->setLabel('admin.shop.field.longitude')
                ->setNumDecimals(8)->setColumns(6);
            yield TelephoneField::new('shop_info.phone')->setLabel('admin.shop.field.phone');

            // @Todo : Reorder AddPanel()
            yield CollectionField::new('shop_info.shop_hour')->setLabel('admin.shop.field.shop_hour')
                ->setColumns(12)
                ->renderExpanded(true)
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
            ->showEntryLabel(false)
                ->setFormTypeOption('by_reference', false)->setColumns(12);

            yield FormField::addPanel('admin.shop.panel_shop_services')->renderCollapsed();
            yield BooleanField::new('shop_info.shipping_click')
                ->setCustomOption('renderAsSwitch', false)
                ->setLabel('admin.shop.field.shipping_click');
            yield BooleanField::new('shop_info.shipping_delivery')
                ->setCustomOption('renderAsSwitch', false)
                ->setLabel('admin.shop.field.shipping_delivery');

            if (
                ($this->pms()->isAdmin($this->getUser()))
                || ($this->pms()->canUseActions($this->getUser(), 'SHOP', 'EDIT'))
                && ($this->isGranted($this->pms()->roleAllowedToEditAdminShops))
            ) {
                yield FormField::addPanel('admin.shop.panel_shop_admin')->renderCollapsed();
                yield AssociationField::new('admins')->setLabel('admin.shop.field.admins')->setColumns(12);
            }
        }

        // NEW
        if (Crud::PAGE_NEW === $pageName) {
            yield FormField::addPanel('admin.shop.panel_shop')->renderCollapsed(false);
            yield TextField::new('name')->setLabel('admin.shop.field.name');
            yield EmailField::new('email')->setLabel('admin.shop.field.email');

            if ($this->pms()->isAdmin($this->getUser())) {
                yield BooleanField::new('is_active')->setLabel('admin.shop.field.is_active');
            }

            if (
                ($this->pms()->isAdmin($this->getUser()))
                || ($this->pms()->canUseActions($this->getUser(), 'SHOP', 'NEW'))
                && ($this->isGranted($this->pms()->roleAllowedToEditAdminShops))
            ) {
                yield FormField::addPanel('admin.shop.panel_shop_admin')->renderCollapsed();
                yield AssociationField::new('admins')->setLabel('admin.shop.field.admins');
            }
        }
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        if ($this->isGranted($this->pms()->isAdmin)) {
            return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        }

        if ($this->pms()->canUseOwners($this->getUser(), 'SHOP', 'INDEX')) {
            return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        }

        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->leftJoin('entity.admins', 'd')
            ->addSelect('d')
            ->andWhere('d.uuid = :uuid')
            ->setParameter('uuid', $this->getUser()->getUuid()->toBinary()) // put your user id connected here
        ;
    }

    /*************** -- Custom Actions -- ***************/
    /***************************************************/
    /**************************************************/
    /*************************************************/

    /**
     * enableShops
     *
     * @param  mixed $batchActionDto
     * @return void
     */
    public function enableShops(BatchActionDto $batchActionDto)
    {
        if (
            !$this->pms()->isAdmin($this->getUser())
            && !$this->pms()->canUseActions($this->getUser(), 'SHOP', 'EDIT')
        ) {
            throw $this->createAccessDeniedException();
        }

        $entityManager = $this->adminManagerRegistry()->getManagerForClass($batchActionDto->getEntityFqcn());

        foreach ($batchActionDto->getEntityIds() as $uuid) {
            $shop = $entityManager->find($batchActionDto->getEntityFqcn(), $uuid);
            $shop->setIsActive(true);
            $shop->setUpdatedAt(new \DateTime());

            $this->get('event_dispatcher')->dispatch(new AfterEntityUpdatedEvent($shop));
        }

        $entityManager->flush();

        return $this->redirect($batchActionDto->getReferrerUrl());
    }

    /**
     * disableShops
     *
     * @param  mixed $batchActionDto
     * @return void
     */
    public function disableShops(BatchActionDto $batchActionDto)
    {
        if (
            !$this->pms()->isAdmin($this->getUser())
            && !$this->pms()->canUseActions($this->getUser(), 'SHOP', 'EDIT')
        ) {
            throw $this->createAccessDeniedException();
        }

        $entityManager = $this->adminManagerRegistry()->getManagerForClass($batchActionDto->getEntityFqcn());

        foreach ($batchActionDto->getEntityIds() as $uuid) {
            $shop = $entityManager->find($batchActionDto->getEntityFqcn(), $uuid);
            $shop->setIsActive(false);
            $shop->setUpdatedAt(new \DateTime());

            $this->get('event_dispatcher')->dispatch(new AfterEntityUpdatedEvent($shop));
        }

        $entityManager->flush();

        return $this->redirect($batchActionDto->getReferrerUrl());
    }
}
