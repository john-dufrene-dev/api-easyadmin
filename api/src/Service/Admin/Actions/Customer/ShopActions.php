<?php

namespace App\Service\Admin\Actions\Customer;

use App\Entity\Customer\User;
use Symfony\Component\Security\Core\Security;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

/**
 * ShopActions
 */
final class ShopActions
{
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

    /**
     * relatedShop
     *
     * @param  mixed $actions
     * @return Actions
     */
    public function relatedShop(Actions $actions): Actions
    {
        $actions->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $a) {
            return $a->setIcon('fa fa-edit')->setLabel(false)->displayIf(function ($e) {
                if ($e instanceof User) {
                    if (0 !== $this->security->getUser()->getShops()) {
                        $user_uuid = $e->getUuid()->toRfc4122();
                        foreach ($this->security->getUser()->getShops() as $shop) {
                            foreach ($shop->getUsers() as $user) {
                                if ($user->getUuid()->toRfc4122() === $user_uuid) {
                                    return true;
                                }
                            }
                        }
                    }
                }
                return false;
            });
        });

        $actions->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $a) {
            return $a->setIcon('fa fa-trash')->setLabel(false)->displayIf(function ($e) {
                if ($e instanceof User) {
                    if (0 !== $this->security->getUser()->getShops()) {
                        $user_uuid = $e->getUuid()->toRfc4122();
                        foreach ($this->security->getUser()->getShops() as $shop) {
                            foreach ($shop->getUsers() as $user) {
                                if ($user->getUuid()->toRfc4122() === $user_uuid) {
                                    return true;
                                }
                            }
                        }
                    }
                }
                return false;
            });
        });

        $actions->update(Crud::PAGE_DETAIL, Action::EDIT, function (Action $a) {
            return $a->setIcon('fa fa-edit')->setLabel(false)
                ->displayIf(function ($e) {
                    if ($e instanceof User) {
                        if (0 !== $this->security->getUser()->getShops()) {
                            $user_uuid = $e->getUuid()->toRfc4122();
                            foreach ($this->security->getUser()->getShops() as $shop) {
                                foreach ($shop->getUsers() as $user) {
                                    if ($user->getUuid()->toRfc4122() === $user_uuid) {
                                        return true;
                                    }
                                }
                            }
                        }
                    }
                    return false;
                });
        });

        $actions->update(Crud::PAGE_DETAIL, Action::DELETE, function (Action $a) {
            return $a->setIcon('fa fa-trash')->setLabel(false)->setCssClass('action-delete btn btn-danger')
                ->displayIf(function ($e) {
                    if ($e instanceof User) {
                        if (0 !== $this->security->getUser()->getShops()) {
                            $user_uuid = $e->getUuid()->toRfc4122();
                            foreach ($this->security->getUser()->getShops() as $shop) {
                                foreach ($shop->getUsers() as $user) {
                                    if ($user->getUuid()->toRfc4122() === $user_uuid) {
                                        return true;
                                    }
                                }
                            }
                        }
                    }
                    return false;
                });
        });



        return $actions;
    }
}
