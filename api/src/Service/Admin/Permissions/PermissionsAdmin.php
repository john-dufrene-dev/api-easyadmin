<?php

namespace App\Service\Admin\Permissions;

use function Symfony\Component\String\u;

final class PermissionsAdmin
{
    /**
     * 
     * Role super admin
     * You can do everythinks you want with this role
     *
     */
    public const DEFAULT = 'ROLE_ADMIN';

    /**
     * 
     * Role super admin
     * You can do everythinks you want with this role
     *
     */
    public const IS_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * 
     * Role for api documentation
     * You can try and use the swagger UI documentation
     *
     */
    public const ROLE_API_DOCUMENTATION     = 'ROLE_API_DOCUMENTATION';

    public $isAdmin = 'ROLE_SUPER_ADMIN';
    public $roleAllowedToEditRoles = 'ROLE_ALLOWED_TO_EDIT_ROLES';
    public $roleAllowedToEditGroups = 'ROLE_ALLOWED_TO_EDIT_GROUPS';
    public $roleAllowedToEditAdminShops = 'ROLE_ALLOWED_TO_EDIT_ADMINS_SHOPS';
    public $roleAllowedToSwitch = 'ROLE_ALLOWED_TO_SWITCH';
    public $roleApiDocumentation = 'ROLE_API_DOCUMENTATION';

    public $roles = [
        'ADMIN',
        'ADMIN_GROUP',
        'SHOP',
        'USER'
    ];

    /**
     * get
     *
     * @param  mixed $entity
     * @param  mixed $role
     * @param  mixed $action
     * @return string
     */
    public function get($entity, $role = 'ALL', $action = 'ACTION'): string
    {
        return 'ROLE_' . $entity . '_' . $action . '_' . $role;
    }

    /**
     * getAction
     *
     * @param  mixed $entity
     * @param  mixed $role
     * @return string
     */
    public function getAction($entity, $role = 'ALL'): string
    {
        return 'ROLE_' . $entity . '_ACTION_' . $role;
    }

    /**
     * getOwner
     *
     * @param  mixed $entity
     * @param  mixed $role
     * @return string
     */
    public function getOwner($entity, $role = 'ALL'): string
    {
        return 'ROLE_' . $entity . '_OWNER_' . $role;
    }

    /**
     * getRoles
     *
     * @return array
     */
    public function getRoles(): array
    {
        $roles = [];

        $roles['pms.super_admin'] = [$this->isAdmin => $this->isAdmin];
        $roles['pms.roles_allowed_to_edit_roles'] = [$this->roleAllowedToEditRoles => $this->roleAllowedToEditRoles];
        $roles['pms.roles_allowed_to_edit_groups'] = [$this->roleAllowedToEditGroups => $this->roleAllowedToEditGroups];
        $roles['pms.roles_allowed_to_edit_admins_shop'] = [$this->roleAllowedToEditAdminShops => $this->roleAllowedToEditAdminShops];
        $roles['pms.roles_allowed_to_switch'] = [$this->roleAllowedToSwitch => $this->roleAllowedToSwitch];
        $roles['pms.roles_allowed_to_read_api_doc'] = [$this->roleApiDocumentation => $this->roleApiDocumentation];

        foreach ($this->roles as $role) {
            $roles['pms.actions.' . u($role)->lower()] = $this->getActionsEntity($role);
            $roles['pms.owners.' . u($role)->lower()] = $this->getOwnersEntity($role);
        }

        return $roles;
    }

    /*************** -- Functions to pass to getAllRoles() -- ***************/
    /***********************************************************************/
    /**********************************************************************/
    /*********************************************************************/

    /// ACTIONS ///

    /**
     * getActionsEntity
     *
     * @param  mixed $entity
     * @return array
     */
    public function getActionsEntity($entity): array
    {
        return [
            'ROLE_' . $entity . '_ACTION_ALL'       => 'ROLE_' . $entity . '_ACTION_ALL',
            'ROLE_' . $entity . '_ACTION_INDEX'     => 'ROLE_' . $entity . '_ACTION_INDEX',
            'ROLE_' . $entity . '_ACTION_EDIT'      => 'ROLE_' . $entity . '_ACTION_EDIT',
            'ROLE_' . $entity . '_ACTION_DELETE'    => 'ROLE_' . $entity . '_ACTION_DELETE',
            'ROLE_' . $entity . '_ACTION_NEW'       => 'ROLE_' . $entity . '_ACTION_NEW',
            'ROLE_' . $entity . '_ACTION_DETAIL'    => 'ROLE_' . $entity . '_ACTION_DETAIL',
            'ROLE_' . $entity . '_ACTION_EXPORT'    => 'ROLE_' . $entity . '_ACTION_EXPORT',
        ];
    }

    /**
     * getOwnersEntity
     *
     * @param  mixed $entity
     * @return array
     */
    public function getOwnersEntity($entity): array
    {
        return [
            'ROLE_' . $entity . '_OWNER_ALL'       => 'ROLE_' . $entity . '_OWNER_ALL',
            'ROLE_' . $entity . '_OWNER_INDEX'     => 'ROLE_' . $entity . '_OWNER_INDEX',
            'ROLE_' . $entity . '_OWNER_EDIT'      => 'ROLE_' . $entity . '_OWNER_EDIT',
            'ROLE_' . $entity . '_OWNER_DELETE'    => 'ROLE_' . $entity . '_OWNER_DELETE',
            'ROLE_' . $entity . '_OWNER_NEW'       => 'ROLE_' . $entity . '_OWNER_NEW',
            'ROLE_' . $entity . '_OWNER_DETAIL'    => 'ROLE_' . $entity . '_OWNER_DETAIL',
            'ROLE_' . $entity . '_OWNER_EXPORT'    => 'ROLE_' . $entity . '_OWNER_EXPORT',
        ];
    }

    /*************** -- Functions to have Authenticator checker -- ***************/
    /****************************************************************************/
    /***************************************************************************/
    /**************************************************************************/

    /**
     * isAdmin
     *
     * @param  mixed $user
     * @return bool
     */
    public function isAdmin($user): bool
    {
        if ($user->hasRole($this->isAdmin)) {
            return true;
        }

        return false;
    }

    /**
     * canUseActions
     *
     * @param  mixed $user
     * @param  mixed $entity
     * @param  mixed $page
     * @return bool
     */
    public function canUseActions($user, $entity, $page): bool
    {
        $all = 'ROLE_' . $entity . '_ACTION_ALL';
        $page = 'ROLE_' . $entity . '_ACTION_' . $page;

        if ($user->hasRole($all) || $user->hasRole($page)) {
            return true;
        }

        return false;
    }

    /**
     * canUseOwners
     *
     * @param  mixed $user
     * @param  mixed $entity
     * @param  mixed $page
     * @return bool
     */
    public function canUseOwners($user, $entity, $page): bool
    {
        $all = 'ROLE_' . $entity . '_OWNER_ALL';
        $page = 'ROLE_' . $entity . '_OWNER_' . $page;

        if ($user->hasRole($all) || $user->hasRole($page)) {
            return true;
        }

        return false;
    }
}
