<?php

namespace App\Controller\Admin\CRUD\Configuration;

use Doctrine\ORM\QueryBuilder;
use App\Entity\Configuration\Config;
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
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;

class ConfigGeneralCrudController extends AbstractBaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return Config::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('edit', function (?Config $config) {
                return $config ? $config->getName() : null;
            })
            ->setPageTitle('detail', function (?Config $config) {
                return $config ? $config->getName() : null;
            })
            ->showEntityActionsInlined()
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
        $this->adminCustomizeActions()->limitedToEdit($actions);
        $this->adminCustomizeActions()->limitedToEditCustomize($actions);

        // // Default reorder actions
        $this->adminCustomizeActions()->reorderForEdit($actions);

        if ($this->isGranted($this->pms()->isAdmin)) {
            return $actions;
        }

        $actions->setPermission(Action::EDIT, $this->pms()->isAdmin);
        $actions->setPermission(Action::SAVE_AND_RETURN, $this->pms()->isAdmin);
        $actions->setPermission(Action::SAVE_AND_CONTINUE, $this->pms()->isAdmin);
        $actions->setPermission(Action::DETAIL, $this->pms()->isAdmin);
        $actions->setPermission(Action::INDEX, $this->pms()->isAdmin);

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        $c = $this->currentAdminContext()->getEntity();

        // INDEX
        if (Crud::PAGE_INDEX === $pageName) {
            if ($this->pms()->isAdmin($this->getUser())) {
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

            if ($this->adminConfig()->getType($c->getInstance()->getTyping()) === $this->adminConfig()->textType) {
                yield TextEditorField::new('value')->setLabel('admin.config.field.value');
            }

            if ($this->adminConfig()->getType($c->getInstance()->getTyping()) === $this->adminConfig()->textEditorType) {
                yield TextEditorField::new('value')->setLabel('admin.config.field.value');
            }

            if ($this->adminConfig()->getType($c->getInstance()->getTyping()) === $this->adminConfig()->textareaType) {
                yield TextAreaField::new('value')->setLabel('admin.config.field.value');
            }

            if ($this->adminConfig()->getType($c->getInstance()->getTyping()) === $this->adminConfig()->integerType) {
                yield IntegerField::new('value')->setLabel('admin.config.field.value');
            }

            if ($this->adminConfig()->getType($c->getInstance()->getTyping()) === $this->adminConfig()->booleanType) {
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
                    ]);
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

            if ($this->adminConfig()->getType($c->getInstance()->getTyping()) === $this->adminConfig()->textType) {
                yield TextField::new('value')->setLabel('admin.config.field.value');
            }

            if ($this->adminConfig()->getType($c->getInstance()->getTyping()) === $this->adminConfig()->textEditorType) {
                yield TextEditorField::new('value')->setLabel('admin.config.field.value');
            }

            if ($this->adminConfig()->getType($c->getInstance()->getTyping()) === $this->adminConfig()->textareaType) {
                yield TextAreaField::new('value')->setLabel('admin.config.field.value');
            }

            if ($this->adminConfig()->getType($c->getInstance()->getTyping()) === $this->adminConfig()->integerType) {
                yield IntegerField::new('value')->setLabel('admin.config.field.value');
            }

            if ($this->adminConfig()->getType($c->getInstance()->getTyping()) === $this->adminConfig()->booleanType) {
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
            ->setParameter('names', $this->adminConfig()->getGeneralConfigValues());
    }
}
