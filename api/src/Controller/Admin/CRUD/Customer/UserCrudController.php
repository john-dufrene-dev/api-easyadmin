<?php

namespace App\Controller\Admin\CRUD\Customer;

use App\Entity\Client\Shop;
use App\Entity\Customer\User;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Customer\UserShopHistory;
use App\Service\Admin\Field\PasswordField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use App\Service\Admin\Actions\Customer\ShopActions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use App\Controller\Admin\AbstractBaseCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use App\Service\Admin\Field\Customer\IsLinkedShopField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;

class UserCrudController extends AbstractBaseCrudController
{
    protected $shopActions;

    public function __construct(ShopActions $shopActions)
    {
        $this->shopActions = $shopActions;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('edit', function (?User $user) {
                return $user ? $user->getUserIdentifier() : null;
            })
            ->setPageTitle('detail', function (?User $user) {
                return $user ? $user->getUserIdentifier() : null;
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
            || $this->pms()->canUseOwners($this->getUser(), 'USER', 'INDEX')
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
        $this->adminCustomizeActions()->all($actions);

        // Default customize actions
        $this->adminCustomizeActions()->customize($actions);

        // Default reorder actions
        $this->adminCustomizeActions()->reorder($actions);

        if ($this->isGranted($this->pms()->isAdmin)) {
            return $actions;
        }

        // Disable edit User when is not Shop Linked
        $this->shopActions->relatedShop($actions);

        if ($this->isGranted($this->pms()->getAction('USER'))) {
            return $actions;
        }

        $actions->setPermission(Action::NEW, $this->pms()->getAction('USER', 'NEW'));
        $actions->setPermission(Action::SAVE_AND_ADD_ANOTHER, $this->pms()->getAction('USER', 'NEW'));
        $actions->setPermission(Action::EDIT, $this->pms()->getAction('USER', 'EDIT'));
        $actions->setPermission(Action::SAVE_AND_RETURN, $this->pms()->getAction('USER', 'EDIT'));
        $actions->setPermission(Action::SAVE_AND_CONTINUE, $this->pms()->getAction('USER', 'EDIT'));
        $actions->setPermission(Action::DELETE, $this->pms()->getAction('USER', 'DELETE'));
        $actions->setPermission(Action::DETAIL, $this->pms()->getAction('USER', 'DETAIL'));
        $actions->setPermission(Action::INDEX, $this->pms()->getAction('USER', 'INDEX'));

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        // @todo : Finish all the model User
        // INDEX
        if (Crud::PAGE_INDEX === $pageName) {
            if (
                $this->pms()->isAdmin($this->getUser())
                || $this->pms()->canUseOwners($this->getUser(), 'USER', 'INDEX')
            ) {
                yield IdField::new('id')->setLabel('admin.field.id');
            }

            yield TextField::new('reference')->setLabel('admin.field.reference');
            yield EmailField::new('email')->setLabel('admin.user.field.email');
            yield DateField::new('created_at')->setLabel('admin.field.created_at');
            yield DateField::new('updated_at')->setLabel('admin.field.updated_at');
            yield BooleanField::new('is_active')->setLabel('admin.user.field.is_active');
            yield BooleanField::new('is_verified')->setLabel('admin.user.field.is_verified');
            yield IsLinkedShopField::new('shop.name')
                ->setLabel('admin.user.field.linked_shop')
                ->formatValue(function ($value, $entity) {

                    // Verify if Shop exist
                    if (null === $value) {
                        return $this->translator()->trans('admin.user.field.is_linked_shop', [], 'admin');
                    }

                    // Verify if is Granted to show Shop
                    if (
                        $this->pms()->isAdmin($this->getUser())
                        || $this->pms()->canUseOwners($this->getUser(), 'USER', 'INDEX')
                    ) {
                        return $value;
                    }

                    // verify if Shop linked to the good User
                    if (0 !== $this->getUser()->getShops()) {
                        $user_uuid = $entity->getUuid()->toRfc4122();
                        foreach ($this->getUser()->getShops() as $shop) {
                            foreach ($shop->getUsers() as $user) {
                                if ($user->getUuid()->toRfc4122() === $user_uuid) {
                                    return $value;
                                }
                            }
                        }
                    }

                    // If Shop is not linked to this User - @todo history system
                    return $this->translator()->trans('admin.user.field.change_linked_shop', [], 'admin');
                });
        }

        // DETAIL
        if (Crud::PAGE_DETAIL === $pageName) {
            yield FormField::addPanel('admin.user.panel_user')->renderCollapsed(false);
            if (
                $this->pms()->isAdmin($this->getUser())
                || $this->pms()->canUseOwners($this->getUser(), 'USER', 'DETAIL')
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

            yield FormField::addPanel('admin.user.panel_user_info')->renderCollapsed();

            yield TextField::new('user_info.firstname')->setLabel('admin.user.field.firstname');
            yield TextField::new('user_info.lastname')->setLabel('admin.user.field.lastname');
            yield DateField::new('user_info.birthday')
                ->setFormat('full', 'none')
                ->setLabel('admin.user.field.birthday');
            yield ChoiceField::new('user_info.gender')
                ->setLabel('admin.user.field.gender')
                ->setFormTypeOption('empty_data', null)
                ->setChoices([
                    'admin.user.field.gender_male' => 'M',
                    'admin.user.field.gender_female' => 'F',
                    'admin.user.field.gender_other' => 'O',
                ]);
            yield TelephoneField::new('user_info.phone')->setLabel('admin.user.field.phone');

            if (
                $this->pms()->isAdmin($this->getUser())
                || $this->pms()->canUseActions($this->getUser(), 'SHOP', 'DETAIL')
            ) {
                yield FormField::addPanel('admin.user.panel_shop_id')->renderCollapsed();
                yield IsLinkedShopField::new('shop.name')
                    ->setLabel('admin.user.field.linked_shop')
                    ->formatValue(function ($value, $entity) {

                        // Verify if Shop exist
                        if (null === $value) {
                            return $this->translator()->trans('admin.user.field.is_linked_shop', [], 'admin');
                        }

                        // Verify if is Granted to show Shop
                        if (
                            $this->pms()->isAdmin($this->getUser())
                            || $this->pms()->canUseOwners($this->getUser(), 'USER', 'DETAIL')
                        ) {
                            return $value;
                        }

                        // verify if Shop linked to the good User
                        if (0 !== $this->getUser()->getShops()) {
                            $user_uuid = $entity->getUuid()->toRfc4122();
                            foreach ($this->getUser()->getShops() as $shop) {
                                foreach ($shop->getUsers() as $user) {
                                    if ($user->getUuid()->toRfc4122() === $user_uuid) {
                                        return $value;
                                    }
                                }
                            }
                        }

                        // If Shop is not linked to this User - @todo history system
                        return $this->translator()->trans('admin.user.field.change_linked_shop', [], 'admin');
                    });
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

            yield FormField::addPanel('admin.user.panel_user_info')->renderCollapsed();

            yield TextField::new('user_info.firstname')->setLabel('admin.user.field.firstname');
            yield TextField::new('user_info.lastname')->setLabel('admin.user.field.lastname');
            yield DateField::new('user_info.birthday')
                ->setFormat('short', 'none')
                ->setLabel('admin.user.field.birthday');
            yield ChoiceField::new('user_info.gender')
                ->setLabel('admin.user.field.gender')
                ->setFormTypeOption('empty_data', null)
                ->setChoices([
                    'admin.user.field.gender_male' => 'M',
                    'admin.user.field.gender_female' => 'F',
                    'admin.user.field.gender_other' => 'O',
                ]);
            yield TelephoneField::new('user_info.phone')->setLabel('admin.user.field.phone');

            if (
                $this->pms()->isAdmin($this->getUser())
                || $this->pms()->canUseActions($this->getUser(), 'USER', 'EDIT')
            ) {
                // Configuration variables
                $shops = $this->getDoctrine()->getRepository(Shop::class);
                $choices = (count($shops->findByAdmin($this->getUser()->getUuid()->toBinary())) !== 0
                    && !$this->pms()->isAdmin($this->getUser())
                    && !$this->pms()->canUseOwners($this->getUser(), 'USER', 'EDIT'))
                    ? $shops->findByAdmin($this->getUser()->getUuid()->toBinary())
                    : $shops->findAll();

                yield FormField::addPanel('admin.user.panel_shop_id')->renderCollapsed();
                yield ChoiceField::new('shop')
                    ->autocomplete(true)
                    ->setChoices($choices)
                    ->setLabel('admin.user.field.shop')
                    ->setFormTypeOptions([
                        'choice_label' => 'getName',
                        // 'disabled' => true, @todo : disable if another Shop but in history of the Shop
                        'choice_translation_domain' => false
                    ]);
            }

            // @Todo : Add roles system ROLE__USER by default
        }

        // NEW
        if (Crud::PAGE_NEW === $pageName) {
            yield FormField::addPanel('admin.user.panel_user')->renderCollapsed(false);
            yield EmailField::new('email')->setLabel('admin.user.field.email');
            yield PasswordField::new('password')->setLabel('admin.user.field.password');

            yield BooleanField::new('is_active')->setLabel('admin.user.field.is_active');
            yield BooleanField::new('is_verified')->setLabel('admin.user.field.is_verified');

            if (
                ($this->pms()->isAdmin($this->getUser()))
                || ($this->pms()->canUseActions($this->getUser(), 'USER', 'NEW'))
            ) {
                // Configuration variables
                $required = true;
                $shops = $this->getDoctrine()->getRepository(Shop::class);
                $choices = (count($shops->findByAdmin($this->getUser()->getUuid()->toBinary())) !== 0
                    && !$this->pms()->isAdmin($this->getUser())
                    && !$this->pms()->canUseOwners($this->getUser(), 'USER', 'NEW'))
                    ? $shops->findByAdmin($this->getUser()->getUuid()->toBinary())
                    : $shops->findAll();

                if (
                    ($this->pms()->isAdmin($this->getUser()))
                    || ($this->pms()->canUseOwners($this->getUser(), 'USER', 'NEW'))
                ) {
                    $required = false;
                }

                yield FormField::addPanel('admin.user.panel_shop_id')->renderCollapsed();
                yield ChoiceField::new('shop')
                    ->autocomplete(true)
                    ->setChoices($choices)
                    ->setLabel('admin.user.field.shop')
                    ->setFormTypeOptions([
                        'choice_label' => 'getName',
                        'choice_translation_domain' => false,
                        'required' => $required,
                    ]);
            }
        }
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        if ($this->isGranted($this->pms()->isAdmin)) {
            return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        }

        if ($this->pms()->canUseOwners($this->getUser(), 'USER', 'INDEX')) {
            return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        }

        $uuid_shop = null;
        $uuid_user = null;
        $shops = $this->getUser()->getShops();

        if (count($shops) !== 0) {
            $uuids_shop = [];
            $uuids_users = [];

            foreach ($shops as $shop) {
                // verify all shops
                \array_push($uuids_shop, $shop->getUuid()->toBinary());

                $shops_history_manager = $this->adminEm()->getRepository(UserShopHistory::class);
                $users_histories = $shops_history_manager->findBy(['shop_reference' => $shop->getReference()]);

                // Verify all histories shops
                if (count($users_histories) !== 0) {
                    foreach ($users_histories as $user) {
                        \array_push($uuids_users, $user->getUserReference());
                    }
                }
            }

            $uuid_shop = $uuids_shop;
            $uuid_user = $uuids_users;
        }

        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->leftJoin('entity.shop', 's')
            ->addSelect('s')
            ->andwhere('entity.shop IN (:shops) OR entity.reference IN (:users)')
            ->setParameter('shops', $uuid_shop)
            ->setParameter('users', $uuid_user);
    }
}
