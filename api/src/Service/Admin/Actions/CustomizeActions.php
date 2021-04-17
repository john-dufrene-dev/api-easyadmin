<?php

namespace App\Service\Admin\Actions;

use App\Entity\Security\Admin;
use Symfony\Component\Security\Core\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * CustomizeActions
 */
final class CustomizeActions
{
    public const IMPERSONATE = 'impersonate';
    public const EXPORT_CSV = 'export_to_csv';
    public const BATCH_ACTIVE = 'batch_active';

    /**
     * security
     *
     * @var mixed
     */
    protected $security;

    /**
     * params
     *
     * @var mixed
     */
    protected $params;

    /**
     * __construct
     *
     * @param  mixed $security
     * @return void
     */
    public function __construct(Security $security, ParameterBagInterface $params)
    {
        $this->security = $security;
        $this->params = $params;
    }

    /*********** CUSTOM USING ACTIONS ***********/

    /**
     * all
     *
     * @param  mixed $actions
     * @return Actions
     */
    public function all(Actions $actions): Actions
    {
        $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
        $actions->add(Crud::PAGE_EDIT, Action::INDEX);
        $actions->add(Crud::PAGE_EDIT, Action::DELETE);
        $actions->add(Crud::PAGE_EDIT, Action::DETAIL);
        $actions->add(Crud::PAGE_NEW, Action::INDEX);
        $actions->add(Crud::PAGE_NEW, Action::SAVE_AND_CONTINUE);

        return $actions;
    }

    /**
     * limitedToShow
     *
     * @param  mixed $actions
     * @return Actions
     */
    public function limitedToShow(Actions $actions): Actions
    {
        $actions->add(Crud::PAGE_INDEX, Action::DETAIL);

        $actions->remove(Action::INDEX, Action::DELETE);
        $actions->remove(Action::INDEX, Action::NEW);
        $actions->remove(Action::INDEX, Action::EDIT);
        $actions->remove(Action::EDIT, Action::SAVE_AND_RETURN);
        $actions->remove(Action::EDIT, Action::SAVE_AND_CONTINUE);
        $actions->remove(Action::DETAIL, Action::DELETE);
        $actions->remove(Action::DETAIL, Action::EDIT);
        $actions->remove(Action::NEW, Action::SAVE_AND_RETURN);
        $actions->remove(Action::NEW, Action::SAVE_AND_ADD_ANOTHER);

        $actions->disable(
            Action::EDIT,
            Action::NEW,
            Action::DELETE,
            Action::SAVE_AND_RETURN,
            Action::SAVE_AND_ADD_ANOTHER,
            Action::SAVE_AND_CONTINUE
        );

        return $actions;
    }

    /**
     * limitedToEdit
     *
     * @param  mixed $actions
     * @return Actions
     */
    public function limitedToEdit(Actions $actions): Actions
    {
        $actions->add(Crud::PAGE_INDEX, Action::DETAIL);
        $actions->add(Crud::PAGE_EDIT, Action::INDEX);
        $actions->add(Crud::PAGE_EDIT, Action::DETAIL);

        $actions->remove(Action::INDEX, Action::DELETE);
        $actions->remove(Action::INDEX, Action::NEW);
        $actions->remove(Action::NEW, Action::SAVE_AND_RETURN);
        $actions->remove(Action::NEW, Action::SAVE_AND_ADD_ANOTHER);

        $actions->disable(
            Action::NEW,
            Action::DELETE,
            Action::SAVE_AND_ADD_ANOTHER
        );

        return $actions;
    }

    /**
     * reorder
     *
     * @param  mixed $actions
     * @return Actions
     */
    public function reorder(Actions $actions): Actions
    {
        $actions->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT, Action::DELETE]);
        $actions->reorder(Crud::PAGE_DETAIL, [Action::INDEX, Action::EDIT, Action::DELETE]);
        $actions->reorder(Crud::PAGE_EDIT, [
            Action::INDEX, Action::SAVE_AND_CONTINUE, Action::SAVE_AND_RETURN,
            Action::DETAIL, Action::DELETE
        ]);
        $actions->reorder(Crud::PAGE_NEW, [
            Action::INDEX, Action::SAVE_AND_CONTINUE, Action::SAVE_AND_ADD_ANOTHER,
            Action::SAVE_AND_RETURN
        ]);

        return $actions;
    }

    /**
     * reorderForEdit
     *
     * @param  mixed $actions
     * @return Actions
     */
    public function reorderForEdit(Actions $actions): Actions
    {
        $actions->reorder(Crud::PAGE_INDEX, [Action::DETAIL, Action::EDIT]);
        $actions->reorder(Crud::PAGE_DETAIL, [Action::INDEX, Action::EDIT]);
        $actions->reorder(Crud::PAGE_EDIT, [
            Action::INDEX, Action::SAVE_AND_CONTINUE, Action::SAVE_AND_RETURN, Action::DETAIL
        ]);

        return $actions;
    }

    /*********** CUSTOM CUSTOMIZE ACTIONS ***********/

    /**
     * customize
     *
     * @param  mixed $actions
     * @return Actions
     */
    public function customize(Actions $actions): Actions
    {
        $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $a) {
                return $a->setIcon('fa fa-plus')->setLabel(false);
            })
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $a) {
                return $a->setIcon('fa fa-edit')->setLabel(false)->displayIf(function ($e) {
                    if ($e instanceof Admin) {
                        if ($this->security->getUser()->getId() === $e->getId()) {
                            return true;
                        }
                        return $e->getIsAdmin() !== true;
                    }
                    return true;
                });
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $a) {
                return $a->setIcon('fa fa-trash')->setLabel(false)->displayIf(function ($e) {
                    if ($e instanceof Admin) {
                        if ($this->security->getUser()->getId() === $e->getId()) {
                            return false;
                        }
                        return $e->getIsAdmin() !== true;
                    }
                    return true;
                });
            })
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $a) {
                return $a->setIcon('fa fa-eye')->setLabel(false)->displayIf(function ($e) {
                    if ($e instanceof Admin) {
                        if ($this->security->getUser()->getId() === $e->getId()) {
                            return true;
                        }
                        return $e->getIsAdmin() !== true;
                    }
                    return true;
                });
            })
            // @todo : waiting new release for batch action
            // ->update(Crud::PAGE_INDEX, Action::BATCH_DELETE, function (Action $a) {
            //     return $a->setIcon('fa fa-trash')->setLabel(false)->displayIf(function ($e) {
            //         return true;
            //     });
            // })
            ;

        $actions
            ->update(Crud::PAGE_DETAIL, Action::EDIT, function (Action $a) {
                return $a->setIcon('fa fa-edit')->setLabel(false)->displayIf(function ($e) {
                    if ($e instanceof Admin) {
                        if ($this->security->getUser()->getId() === $e->getId()) {
                            return true;
                        }
                        return $e->getIsAdmin() !== true;
                    }
                    return true;
                });
            })
            ->update(Crud::PAGE_DETAIL, Action::DELETE, function (Action $a) {
                return $a->setIcon('fa fa-trash')->setLabel(false)->setCssClass('action-delete btn btn-danger')
                    ->displayIf(function ($e) {
                        if ($e instanceof Admin) {
                            if ($this->security->getUser()->getId() === $e->getId()) {
                                return false;
                            }
                            return $e->getIsAdmin() !== true;
                        }
                        return true;
                    });
            });

        $actions
            ->update(Crud::PAGE_EDIT, Action::INDEX, function (Action $a) {
                return $a->setIcon('fa fa-list-alt')->setCssClass('btn btn-secondary');
            })
            ->update(Crud::PAGE_EDIT, Action::DETAIL, function (Action $a) {
                return $a->setIcon('fa fa-eye')->setLabel(false)->setCssClass('btn btn-info')->displayIf(function ($e) {
                    if ($e instanceof Admin) {
                        if ($this->security->getUser()->getId() === $e->getId()) {
                            return true;
                        }
                        return $e->getIsAdmin() !== true;
                    }
                    return true;
                });
            })

            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE, function (Action $a) {
                return $a->setIcon('fa fa-edit')->setCssClass('btn btn-success')->displayIf(function ($e) {
                    if ($e instanceof Admin) {
                        if ($this->security->getUser()->getId() === $e->getId()) {
                            return true;
                        }
                        return $e->getIsAdmin() !== true;
                    }
                    return true;
                });
            })
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, function (Action $a) {
                return $a->setIcon('fa fa-edit')->setLabel(false)->setCssClass('btn btn-success')->displayIf(function ($e) {
                    if ($e instanceof Admin) {
                        if ($this->security->getUser()->getId() === $e->getId()) {
                            return true;
                        }
                        return $e->getIsAdmin() !== true;
                    }
                    return true;
                });
            })
            ->update(Crud::PAGE_EDIT, Action::DELETE, function (Action $a) {
                return $a->setIcon('fa fa-trash')->setLabel(false)->setCssClass('action-delete btn btn-danger')
                    ->displayIf(function ($e) {
                        if ($e instanceof Admin) {
                            if ($this->security->getUser()->getId() === $e->getId()) {
                                return false;
                            }
                            return $e->getIsAdmin() !== true;
                        }
                        return true;
                    });
            });

        $actions
            ->update(Crud::PAGE_NEW, Action::INDEX, function (Action $a) {
                return $a->setIcon('fa fa-list-alt')->setCssClass('btn btn-secondary');
            })
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_CONTINUE, function (Action $a) {
                return $a->setIcon('fa fa-edit')->setCssClass('btn btn-success');
            })
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER, function (Action $a) {
                return $a->setIcon('fa fa-edit')->setCssClass('btn btn-success');
            })
            ->update(Crud::PAGE_NEW, Action::SAVE_AND_RETURN, function (Action $a) {
                return $a->setIcon(('fa fa-plus-circle'))->setLabel(false)->setCssClass('btn btn-info');
            });

        return $actions;
    }

    /**
     * limitedToShowCustomize
     *
     * @param  mixed $actions
     * @return Actions
     */
    public function limitedToShowCustomize(Actions $actions): Actions
    {
        $actions
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $a) {
                return $a->setIcon('fa fa-eye')->setLabel(false)->displayIf(function ($e) {
                    if ($e instanceof Admin) {
                        if ($this->security->getUser()->getId() === $e->getId()) {
                            return true;
                        }
                        return $e->getIsAdmin() !== true;
                    }
                    return true;
                });
            });

        return $actions;
    }

    /**
     * limitedToEditCustomize
     *
     * @param  mixed $actions
     * @return Actions
     */
    public function limitedToEditCustomize(Actions $actions): Actions
    {
        $actions
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $a) {
                return $a->setIcon('fa fa-edit')->setLabel(false);
            })
            ->update(Crud::PAGE_INDEX, Action::DETAIL, function (Action $a) {
                return $a->setIcon('fa fa-eye')->setLabel(false);
            });

        $actions
            ->update(Crud::PAGE_DETAIL, Action::EDIT, function (Action $a) {
                return $a->setIcon('fa fa-edit')->setLabel(false);
            });

        $actions
            ->update(Crud::PAGE_EDIT, Action::INDEX, function (Action $a) {
                return $a->setIcon('fa fa-list-alt')->setCssClass('btn btn-secondary');
            })
            ->update(Crud::PAGE_EDIT, Action::DETAIL, function (Action $a) {
                return $a->setIcon('fa fa-eye')->setLabel(false)->setCssClass('btn btn-info');
            })

            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_CONTINUE, function (Action $a) {
                return $a->setIcon('fa fa-edit')->setCssClass('btn btn-success');
            })
            ->update(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN, function (Action $a) {
                return $a->setIcon('fa fa-edit')->setLabel(false)->setCssClass('btn btn-success');
            });

        return $actions;
    }

    /*********** CUSTOM NEW ACTIONS ***********/

    /**
     * impersonate
     *
     * @return Action
     */
    public function impersonate(): Action
    {
        return Action::new(self::IMPERSONATE, 'Impersonate')
            ->setIcon('fa fa-fw fa-user-lock')
            ->setLabel(false)
            ->linkToUrl(function (Admin $e) {
                return '?' . $this->params->get('route_for_switch_user') . '=' . $e->getUsername();
            })
            ->displayIf(function ($e) {
                if ($e instanceof Admin) {
                    if ($this->security->getUser()->getId() === $e->getId()) {
                        return false;
                    }
                    return $e->getIsAdmin() !== true;
                }
                return true;
            });
    }

    /**
     * export
     *
     * @param  mixed $action
     * @param  mixed $format
     * @return Action
     */
    public function export($action, $format): Action
    {
        switch ($format) {
            case 'csv':
                $using_format = self::EXPORT_CSV;
                break;
            default:
                $using_format = self::EXPORT_CSV;
                break;
        }

        return Action::new($using_format, $action)
            ->setIcon('fa fa-fw fa-download')
            ->setLabel(false)
            ->linkToCrudAction($action)
            ->setCssClass('btn')
            ->createAsGlobalAction();
    }

    // @todo : Add active action - verified action
}
