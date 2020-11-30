<?php

namespace App\Service\Admin\Actions;

use App\Entity\Security\Admin;
use Symfony\Component\Security\Core\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

/**
 * CustomizeActions
 */
final class CustomizeActions
{
    public const IMPERSONATE = 'impersonate';

    /**
     * security
     *
     * @var mixed
     */
    protected $security;

    /**
     * __construct
     *
     * @param  mixed $security
     * @return void
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
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
            });

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
     * @return void
     */
    public function impersonate()
    {
        return Action::new(self::IMPERSONATE, 'Impersonate')
            ->setIcon('fa fa-fw fa-user-lock')
            ->setLabel(false)
            ->linkToRoute('admin_dashboard', function (Admin $e) {
                return [
                    'id' => $e->getId(),
                    '_switch_user' => $e->getEmail()
                ];
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
}
