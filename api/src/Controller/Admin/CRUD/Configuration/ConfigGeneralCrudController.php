<?php

namespace App\Controller\Admin\CRUD\Configuration;

use Doctrine\ORM\QueryBuilder;
use App\Entity\Configuration\Config;
use App\Service\Admin\Actions\CustomizeActions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use App\Service\Admin\Builder\ConfigurationBuilder;
use App\Service\Admin\Permissions\PermissionsAdmin;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Provider\AdminContextProvider;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ConfigGeneralCrudController extends AbstractCrudController
{
    // @todo : Add eventSubscriber (block update if not this specific Controller)

    protected $actions;

    protected $conf;

    public function __construct(CustomizeActions $actions, ConfigurationBuilder $conf)
    {
        $this->actions = $actions;
        $this->conf = $conf;
    }

    public static function getEntityFqcn(): string
    {
        return Config::class;
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
        $filters->add('id');
        $filters->add('uuid');
        $filters->add('name');
        $filters->add('description');
        $filters->add('value');

        return $filters;
    }

    public function configureActions(Actions $actions): Actions
    {
        // Actions adding by just for show and edit (limited)
        $this->actions->limitedToEdit($actions);
        $this->actions->limitedToEditCustomize($actions);

        // // Default reorder actions
        $this->actions->reorderForEdit($actions);

        if ($this->isGranted(PermissionsAdmin::IS_ADMIN)) {
            return $actions;
        }

        $actions->setPermission(Action::EDIT, PermissionsAdmin::IS_ADMIN);
        $actions->setPermission(Action::SAVE_AND_RETURN, PermissionsAdmin::IS_ADMIN);
        $actions->setPermission(Action::SAVE_AND_CONTINUE, PermissionsAdmin::IS_ADMIN);
        $actions->setPermission(Action::DETAIL, PermissionsAdmin::IS_ADMIN);
        $actions->setPermission(Action::INDEX, PermissionsAdmin::IS_ADMIN);

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        $c = $this->getContext()->getEntity();

        // INDEX
        if (Crud::PAGE_INDEX === $pageName) {
            if (PermissionsAdmin::checkAdmin($this->getUser())) {
                yield IdField::new('id')->setLabel('admin.field.id');
            }

            yield TextField::new('name')->setLabel('admin.config.field.name');
            yield TextField::new('description')->setLabel('admin.config.field.description');
            yield TextEditorField::new('value')->setLabel('admin.config.field.value');
            yield DateField::new('updated_at')->setLabel('admin.field.updated_at');
            yield BooleanField::new('is_active')->setLabel('admin.config.field.is_active');
        }

        // DETAIL
        if (Crud::PAGE_DETAIL === $pageName) {
            yield FormField::addPanel('admin.config.panel_general')->renderCollapsed(false);
            yield TextField::new('name')->setLabel('admin.config.field.name');
            yield TextareaField::new('description')->setLabel('admin.config.field.description');
            yield BooleanField::new('is_active')->setLabel('admin.config.field.is_active');

            if ($this->conf->getType($c->getInstance()->getTyping()) === ConfigurationBuilder::TEXT_TYPE) {
                yield TextEditorField::new('value')->setLabel('admin.config.field.value');
            }

            if ($this->conf->getType($c->getInstance()->getTyping()) === ConfigurationBuilder::TEXT_EDITOR_TYPE) {
                yield TextEditorField::new('value')->setLabel('admin.config.field.value');
            }

            if ($this->conf->getType($c->getInstance()->getTyping()) === ConfigurationBuilder::TEXTAREA_TYPE) {
                yield TextAreaField::new('value')->setLabel('admin.config.field.value');
            }

            if ($this->conf->getType($c->getInstance()->getTyping()) === ConfigurationBuilder::INTEGER_TYPE) {
                yield IntegerField::new('value')->setLabel('admin.config.field.value');
            }

            if ($this->conf->getType($c->getInstance()->getTyping()) === ConfigurationBuilder::BOOLEAN_TYPE) {
                yield ChoiceField::new('value')
                    ->setLabel('admin.config.field.value')
                    ->setFormTypeOption('empty_data', 0)
                    ->setChoices([
                        'false' => 0,
                        'true' => 1
                    ])
                    ->renderAsBadges([
                        0 => 'primary',
                        1 => 'primary',
                    ]);;
            }
            yield DateField::new('created_at')->setLabel('admin.field.created_at');
            yield DateField::new('updated_at')->setLabel('admin.field.updated_at');
        }

        // EDIT
        if (Crud::PAGE_EDIT === $pageName) {
            yield FormField::addPanel('admin.config.panel_general')->renderCollapsed(false);
            yield TextField::new('name')->setFormTypeOptions([
                'disabled' => true,
            ])->setLabel('admin.config.field.name');
            yield TextareaField::new('description')->setFormTypeOptions([
                'disabled' => true,
            ])->setNumOfRows(2)->setLabel('admin.config.field.description');
            yield BooleanField::new('is_active')->setLabel('admin.config.field.is_active');

            if ($this->conf->getType($c->getInstance()->getTyping()) === ConfigurationBuilder::TEXT_TYPE) {
                yield TextField::new('value')->setLabel('admin.config.field.value');
            }

            if ($this->conf->getType($c->getInstance()->getTyping()) === ConfigurationBuilder::TEXT_EDITOR_TYPE) {
                yield TextEditorField::new('value')->setLabel('admin.config.field.value');
            }

            if ($this->conf->getType($c->getInstance()->getTyping()) === ConfigurationBuilder::TEXTAREA_TYPE) {
                yield TextAreaField::new('value')->setLabel('admin.config.field.value');
            }

            if ($this->conf->getType($c->getInstance()->getTyping()) === ConfigurationBuilder::INTEGER_TYPE) {
                yield IntegerField::new('value')->setLabel('admin.config.field.value');
            }

            if ($this->conf->getType($c->getInstance()->getTyping()) === ConfigurationBuilder::BOOLEAN_TYPE) {
                yield ChoiceField::new('value')
                    ->setLabel('admin.config.field.value')
                    ->setFormTypeOption('empty_data', 0)
                    ->setChoices([
                        'false' => 0,
                        'true' => 1
                    ]);
            }
        }
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.name IN (:names)')
            ->setParameter('names', $this->getConfigValues());
    }

    /**
     * getContext
     *
     * @return AdminContext
     */
    public function getContext(): ?AdminContext
    {
        return $this->get(AdminContextProvider::class)->getContext();
    }

    /**
     * getConfigValues
     *
     * @return array
     */
    public function getConfigValues(): array
    {
        return [
            'CONF_DASHBOARD_TITLE',
            'CONF_DEFAULT_PAGINATOR'
        ];
    }
}
