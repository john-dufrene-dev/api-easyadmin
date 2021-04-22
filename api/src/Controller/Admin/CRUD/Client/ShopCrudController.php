<?php

namespace App\Controller\Admin\CRUD\Client;

use App\Entity\Client\Shop;
use Doctrine\ORM\QueryBuilder;
use App\Form\Type\Client\ShopFileType;
use App\Form\Type\Client\ShopHourType;
use App\Service\Admin\Builder\ExportBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Admin\Actions\CustomizeActions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use App\Service\Admin\Permissions\PermissionsAdmin;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Factory\FilterFactory;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Factory\AdminContextFactory;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ShopCrudController extends AbstractCrudController
{
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
        return Shop::class;
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $assets->addWebpackEncoreEntry('admin/crud/shop');
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
        $this->actions->all($actions);

        // Action new export csv
        if (
            PermissionsAdmin::checkAdmin($this->getUser())
            || PermissionsAdmin::checkActions($this->getUser(), 'SHOP', 'EXPORT')
        ) {
            $export = $this->actions->export('exportCsv', 'csv');
            $actions->add(Crud::PAGE_INDEX, $export);
        }

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

            yield TextField::new('reference')->setLabel('admin.field.reference');
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
                yield TextField::new('displayuuid')->setLabel('admin.field.displayuuid');
            }

            yield TextField::new('reference')->setLabel('admin.field.reference');
            yield TextField::new('name')->setLabel('admin.shop.field.name');
            yield EmailField::new('email')->setLabel('admin.shop.field.email');

            if (PermissionsAdmin::checkAdmin($this->getUser())) {
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

            yield BooleanField::new('shop_info.shipping_click')
                ->setCustomOption('renderAsSwitch', false)
                ->setLabel('admin.shop.field.shipping_click');
            yield BooleanField::new('shop_info.shipping_delivery')
                ->setCustomOption('renderAsSwitch', false)
                ->setLabel('admin.shop.field.shipping_delivery');

            yield CollectionField::new('shop_info.shop_hour')
                ->setTemplatePath('admin/fields/clients/collection_shop_hour.html.twig')
                ->setLabel('admin.shop.field.shop_hour');

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
        }

        // EDIT
        if (Crud::PAGE_EDIT === $pageName) {
            yield FormField::addPanel('admin.shop.panel_shop')->renderCollapsed(false);

            yield TextField::new('reference')->setFormTypeOptions([
                'disabled' => true,
            ])->setLabel('admin.field.reference');
            yield TextField::new('name')->setLabel('admin.shop.field.name');
            yield EmailField::new('email')->setLabel('admin.shop.field.email');

            if (PermissionsAdmin::checkAdmin($this->getUser())) {
                yield BooleanField::new('is_active')->setLabel('admin.shop.field.is_active');
            }

            yield FormField::addPanel('admin.shop.panel_shop_info')->renderCollapsed();

            yield CountryField::new('shop_info.country')
                ->setFormType(CountryType::class)
                ->setLabel('admin.shop.field.country');
            yield TextField::new('shop_info.city')->setLabel('admin.shop.field.city');
            yield TextField::new('shop_info.postal_code')->setLabel('admin.shop.field.postal_code');
            yield TextField::new('shop_info.address')->setLabel('admin.shop.field.address');
            yield NumberField::new('shop_info.latitude')->setLabel('admin.shop.field.latitude')
                ->setNumDecimals(8);
            yield NumberField::new('shop_info.longitude')->setLabel('admin.shop.field.longitude')
                ->setNumDecimals(8);
            yield TelephoneField::new('shop_info.phone')->setLabel('admin.shop.field.phone');

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
            ->setParameter('uuid', $this->getUser()->getUuid()->toBinary()) // put your user id connected here
        ;
    }

    /*************** -- Custom Actions -- ***************/
    /***************************************************/
    /**************************************************/
    /*************************************************/

    /**
     * exportCsv
     *
     * @param  mixed $request
     * @return Response
     */
    public function exportCsv(Request $request): Response
    {
        if (
            !PermissionsAdmin::checkAdmin($this->getUser())
            && !PermissionsAdmin::checkActions($this->getUser(), 'SHOP', 'EXPORT')
        ) {
            throw $this->createAccessDeniedException();
        }

        $context = $request->attributes->get(EA::CONTEXT_REQUEST_ATTRIBUTE);
        $fields = FieldCollection::new($this->configureFields(Crud::PAGE_INDEX));
        $filters = $this->get(FilterFactory::class)->create($context->getCrud()->getFiltersConfig(), $fields, $context->getEntity());

        \parse_str(\parse_url($request->query->get(EA::REFERRER))[EA::QUERY], $referrerQuery);
        $query = isset($referrerQuery[EA::QUERY]) ? $referrerQuery[EA::QUERY] : null;
        $request->query->set(EA::QUERY, $query);
        // recreate searchDto so that it takes into account the querystring 'query'
        $searchDto = $this->adminContextFactory->getSearchDto($request, $context->getCrud());

        $shops = $this->createIndexQueryBuilder($searchDto, $context->getEntity(), $fields, $filters)
            ->getQuery()
            ->getResult();

        $data = [];
        foreach ($shops as $shop) {
            $data[] = $shop->getExportData();
        }

        // @todo : translation
        if (empty($data)) {
            $data[] = ['error' => 'empty file'];
        }

        return $this->export->exportCsv(
            $data,
            'export_shop_' . date_create()->format('dmyhis') . '.' . $this->export->format('csv')
        );
    }
}
