<?php

namespace App\Service\Admin\Permissions;

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
     * Role allowed to edit roles
     * You can change roles of all Admin with this permissions
     *
     */
    public const ROLE_ALLOWED_TO_EDIT_ROLES     = 'ROLE_ALLOWED_TO_EDIT_ROLES';

    /**
     * 
     * Role allowed to edit groups
     * You can change groups of all Admin with this permissions
     *
     */
    public const ROLE_ALLOWED_TO_EDIT_GROUPS     = 'ROLE_ALLOWED_TO_EDIT_GROUPS';

    /**
     * 
     * Role allowed to edit admins in shop
     * You can change admins of all Shop with this permissions
     *
     */
    public const ROLE_ALLOWED_TO_EDIT_ADMINS_SHOPS     = 'ROLE_ALLOWED_TO_EDIT_ADMINS_SHOPS';

    /**
     * 
     * Role allowed to switch
     * You can switch and test other Admin
     *
     */
    public const ROLE_ALLOWED_TO_SWITCH     = 'ROLE_ALLOWED_TO_SWITCH';

    /**
     * 
     * Role for api documentation
     * You can try and use the swagger UI documentation
     *
     */
    public const ROLE_API_DOCUMENTATION     = 'ROLE_API_DOCUMENTATION';

    /**
     * 
     * Roles actions
     * You can access actions depends of your roles
     *
     */

    /*************** -- ROLES ACTIONS FOR ADMIN ENTITY -- ***************/
    public const ROLE_ADMIN_ACTION_ALL          = 'ROLE_ADMIN_ACTION_ALL';
    public const ROLE_ADMIN_ACTION_INDEX        = 'ROLE_ADMIN_ACTION_INDEX';
    public const ROLE_ADMIN_ACTION_EDIT         = 'ROLE_ADMIN_ACTION_EDIT';
    public const ROLE_ADMIN_ACTION_DELETE       = 'ROLE_ADMIN_ACTION_DELETE';
    public const ROLE_ADMIN_ACTION_NEW          = 'ROLE_ADMIN_ACTION_NEW';
    public const ROLE_ADMIN_ACTION_DETAIL       = 'ROLE_ADMIN_ACTION_DETAIL';
    public const ROLE_ADMIN_ACTION_EXPORT       = 'ROLE_ADMIN_ACTION_EXPORT';

    /*************** -- ROLES ACTIONS FOR ADMIN_GROUP ENTITY -- ***************/
    public const ROLE_ADMIN_GROUP_ACTION_ALL          = 'ROLE_ADMIN_GROUP_ACTION_ALL';
    public const ROLE_ADMIN_GROUP_ACTION_INDEX        = 'ROLE_ADMIN_GROUP_ACTION_INDEX';
    public const ROLE_ADMIN_GROUP_ACTION_EDIT         = 'ROLE_ADMIN_GROUP_ACTION_EDIT';
    public const ROLE_ADMIN_GROUP_ACTION_DELETE       = 'ROLE_ADMIN_GROUP_ACTION_DELETE';
    public const ROLE_ADMIN_GROUP_ACTION_NEW          = 'ROLE_ADMIN_GROUP_ACTION_NEW';
    public const ROLE_ADMIN_GROUP_ACTION_DETAIL       = 'ROLE_ADMIN_GROUP_ACTION_DETAIL';
    public const ROLE_ADMIN_GROUP_ACTION_EXPORT       = 'ROLE_ADMIN_GROUP_ACTION_EXPORT';

    /*************** -- ROLES ACTIONS FOR SHOP ENTITY -- ***************/
    public const ROLE_SHOP_ACTION_ALL          = 'ROLE_SHOP_ACTION_ALL';
    public const ROLE_SHOP_ACTION_INDEX        = 'ROLE_SHOP_ACTION_INDEX';
    public const ROLE_SHOP_ACTION_EDIT         = 'ROLE_SHOP_ACTION_EDIT';
    public const ROLE_SHOP_ACTION_DELETE       = 'ROLE_SHOP_ACTION_DELETE';
    public const ROLE_SHOP_ACTION_NEW          = 'ROLE_SHOP_ACTION_NEW';
    public const ROLE_SHOP_ACTION_DETAIL       = 'ROLE_SHOP_ACTION_DETAIL';
    public const ROLE_SHOP_ACTION_EXPORT       = 'ROLE_SHOP_ACTION_EXPORT';

    /*************** -- ROLES ACTIONS FOR USER ENTITY -- ***************/
    public const ROLE_USER_ACTION_ALL          = 'ROLE_USER_ACTION_ALL';
    public const ROLE_USER_ACTION_INDEX        = 'ROLE_USER_ACTION_INDEX';
    public const ROLE_USER_ACTION_EDIT         = 'ROLE_USER_ACTION_EDIT';
    public const ROLE_USER_ACTION_DELETE       = 'ROLE_USER_ACTION_DELETE';
    public const ROLE_USER_ACTION_NEW          = 'ROLE_USER_ACTION_NEW';
    public const ROLE_USER_ACTION_DETAIL       = 'ROLE_USER_ACTION_DETAIL';
    public const ROLE_USER_ACTION_EXPORT       = 'ROLE_USER_ACTION_EXPORT';

    /**
     * 
     * Roles owners
     * You can just access your own entity informations if you have not this roles
     *
     */

    /*************** -- ROLES OWNERS FOR ADMIN ENTITY -- ***************/
    public const ROLE_ADMIN_OWNER_ALL           = 'ROLE_ADMIN_OWNER_ALL';
    public const ROLE_ADMIN_OWNER_INDEX         = 'ROLE_ADMIN_OWNER_INDEX';
    public const ROLE_ADMIN_OWNER_EDIT          = 'ROLE_ADMIN_OWNER_EDIT';
    public const ROLE_ADMIN_OWNER_DELETE        = 'ROLE_ADMIN_OWNER_DELETE';
    public const ROLE_ADMIN_OWNER_NEW           = 'ROLE_ADMIN_OWNER_NEW';
    public const ROLE_ADMIN_OWNER_DETAIL        = 'ROLE_ADMIN_OWNER_DETAIL';
    public const ROLE_ADMIN_OWNER_EXPORT        = 'ROLE_ADMIN_OWNER_EXPORT';

    /*************** -- ROLES OWNERS FOR ADMIN_GROUP ENTITY -- ***************/
    public const ROLE_ADMIN_GROUP_OWNER_ALL           = 'ROLE_ADMIN_GROUP_OWNER_ALL';
    public const ROLE_ADMIN_GROUP_OWNER_INDEX         = 'ROLE_ADMIN_GROUP_OWNER_INDEX';
    public const ROLE_ADMIN_GROUP_OWNER_EDIT          = 'ROLE_ADMIN_GROUP_OWNER_EDIT';
    public const ROLE_ADMIN_GROUP_OWNER_DELETE        = 'ROLE_ADMIN_GROUP_OWNER_DELETE';
    public const ROLE_ADMIN_GROUP_OWNER_NEW           = 'ROLE_ADMIN_GROUP_OWNER_NEW';
    public const ROLE_ADMIN_GROUP_OWNER_DETAIL        = 'ROLE_ADMIN_GROUP_OWNER_DETAIL';
    public const ROLE_ADMIN_GROUP_OWNER_EXPORT        = 'ROLE_ADMIN_GROUP_OWNER_EXPORT';

    /*************** -- ROLES OWNERS FOR SHOP ENTITY -- ***************/
    public const ROLE_SHOP_OWNER_ALL           = 'ROLE_SHOP_OWNER_ALL';
    public const ROLE_SHOP_OWNER_INDEX         = 'ROLE_SHOP_OWNER_INDEX';
    public const ROLE_SHOP_OWNER_EDIT          = 'ROLE_SHOP_OWNER_EDIT';
    public const ROLE_SHOP_OWNER_DELETE        = 'ROLE_SHOP_OWNER_DELETE';
    public const ROLE_SHOP_OWNER_NEW           = 'ROLE_SHOP_OWNER_NEW';
    public const ROLE_SHOP_OWNER_DETAIL        = 'ROLE_SHOP_OWNER_DETAIL';
    public const ROLE_SHOP_OWNER_EXPORT        = 'ROLE_SHOP_OWNER_EXPORT';

    /*************** -- ROLES OWNERS FOR USER ENTITY -- ***************/
    public const ROLE_USER_OWNER_ALL           = 'ROLE_USER_OWNER_ALL';
    public const ROLE_USER_OWNER_INDEX         = 'ROLE_USER_OWNER_INDEX';
    public const ROLE_USER_OWNER_EDIT          = 'ROLE_USER_OWNER_EDIT';
    public const ROLE_USER_OWNER_DELETE        = 'ROLE_USER_OWNER_DELETE';
    public const ROLE_USER_OWNER_NEW           = 'ROLE_USER_OWNER_NEW';
    public const ROLE_USER_OWNER_DETAIL        = 'ROLE_USER_OWNER_DETAIL';
    public const ROLE_USER_OWNER_EXPORT        = 'ROLE_USER_OWNER_EXPORT';

    public static function exists(?string $permissionName): bool
    {
        if (null === $permissionName) {
            return false;
        }

        return \defined('self::' . $permissionName);
    }

    public static function getAllRoles()
    {
        return [
            'permissions.super_admin'                           => self::getSuperAdmin(),
            'permissions.roles_allowed_to_edit_roles'           => self::getRoleAllowedToEditRoles(),
            'permissions.roles_allowed_to_edit_groups'          => self::getRoleAllowedToEditGroups(),
            'permissions.roles_allowed_to_edit_admins_shop'     => self::getRoleAllowedToEditAdminsinShop(),
            'permissions.roles_allowed_to_switch'               => self::getRoleAllowedToSwitch(),
            'permissions.roles_allowed_to_read_api_doc'         => self::getRoleAllowedReadApiDocumentation(),
            'permissions.actions.admin'                         => self::getActionsAdminEntity(),
            'permissions.owners.admin'                          => self::getOwnersAdminEntity(),
            'permissions.actions.admin_group'                   => self::getActionsAdminGroupEntity(),
            'permissions.owners.admin_group'                    => self::getOwnersAdminGroupEntity(),
            'permissions.actions.shop'                          => self::getActionsShopEntity(),
            'permissions.owners.shop'                           => self::getOwnersShopEntity(),
            'permissions.actions.user'                          => self::getActionsUserEntity(),
            'permissions.owners.user'                           => self::getOwnersUserEntity(),
        ];
    }

    /*************** -- Functions to pass to getAllRoles() -- ***************/
    /***********************************************************************/
    /**********************************************************************/
    /*********************************************************************/

    public static function getSuperAdmin()
    {
        return [
            'ROLE_SUPER_ADMIN' => self::IS_ADMIN,
        ];
    }

    public static function getRoleAllowedToEditRoles()
    {
        return [
            'ROLE_ALLOWED_TO_EDIT_ROLES' => self::ROLE_ALLOWED_TO_EDIT_ROLES,
        ];
    }

    public static function getRoleAllowedToEditGroups()
    {
        return [
            'ROLE_ALLOWED_TO_EDIT_GROUPS' => self::ROLE_ALLOWED_TO_EDIT_GROUPS,
        ];
    }

    public static function getRoleAllowedToSwitch()
    {
        return [
            'ROLE_ALLOWED_TO_SWITCH' => self::ROLE_ALLOWED_TO_SWITCH,
        ];
    }

    public static function getRoleAllowedReadApiDocumentation()
    {
        return [
            'ROLE_API_DOCUMENTATION' => self::ROLE_API_DOCUMENTATION,
        ];
    }

    public static function getRoleAllowedToEditAdminsinShop()
    {
        return [
            'ROLE_ALLOWED_TO_EDIT_ADMINS_SHOPS' => self::ROLE_ALLOWED_TO_EDIT_ADMINS_SHOPS,
        ];
    }

    /// ACTIONS ///

    public static function getActionsAdminEntity()
    {
        return [
            'ROLE_ADMIN_ACTION_ALL' => self::ROLE_ADMIN_ACTION_ALL,
            'ROLE_ADMIN_ACTION_INDEX' => self::ROLE_ADMIN_ACTION_INDEX,
            'ROLE_ADMIN_ACTION_EDIT' => self::ROLE_ADMIN_ACTION_EDIT,
            'ROLE_ADMIN_ACTION_DELETE' => self::ROLE_ADMIN_ACTION_DELETE,
            'ROLE_ADMIN_ACTION_NEW' => self::ROLE_ADMIN_ACTION_NEW,
            'ROLE_ADMIN_ACTION_DETAIL' => self::ROLE_ADMIN_ACTION_DETAIL,
            'ROLE_ADMIN_ACTION_EXPORT' => self::ROLE_ADMIN_ACTION_EXPORT,
        ];
    }

    public static function getActionsAdminGroupEntity()
    {
        return [
            'ROLE_ADMIN_GROUP_ACTION_ALL' => self::ROLE_ADMIN_GROUP_ACTION_ALL,
            'ROLE_ADMIN_GROUP_ACTION_INDEX' => self::ROLE_ADMIN_GROUP_ACTION_INDEX,
            'ROLE_ADMIN_GROUP_ACTION_EDIT' => self::ROLE_ADMIN_GROUP_ACTION_EDIT,
            'ROLE_ADMIN_GROUP_ACTION_DELETE' => self::ROLE_ADMIN_GROUP_ACTION_DELETE,
            'ROLE_ADMIN_GROUP_ACTION_NEW' => self::ROLE_ADMIN_GROUP_ACTION_NEW,
            'ROLE_ADMIN_GROUP_ACTION_DETAIL' => self::ROLE_ADMIN_GROUP_ACTION_DETAIL,
            'ROLE_ADMIN_GROUP_ACTION_EXPORT' => self::ROLE_ADMIN_GROUP_ACTION_EXPORT,
        ];
    }

    public static function getActionsShopEntity()
    {
        return [
            'ROLE_SHOP_ACTION_ALL' => self::ROLE_SHOP_ACTION_ALL,
            'ROLE_SHOP_ACTION_INDEX' => self::ROLE_SHOP_ACTION_INDEX,
            'ROLE_SHOP_ACTION_EDIT' => self::ROLE_SHOP_ACTION_EDIT,
            'ROLE_SHOP_ACTION_DELETE' => self::ROLE_SHOP_ACTION_DELETE,
            'ROLE_SHOP_ACTION_NEW' => self::ROLE_SHOP_ACTION_NEW,
            'ROLE_SHOP_ACTION_DETAIL' => self::ROLE_SHOP_ACTION_DETAIL,
            'ROLE_SHOP_ACTION_EXPORT' => self::ROLE_SHOP_ACTION_EXPORT,
        ];
    }

    public static function getActionsUserEntity()
    {
        return [
            'ROLE_USER_ACTION_ALL' => self::ROLE_USER_ACTION_ALL,
            'ROLE_USER_ACTION_INDEX' => self::ROLE_USER_ACTION_INDEX,
            'ROLE_USER_ACTION_EDIT' => self::ROLE_USER_ACTION_EDIT,
            'ROLE_USER_ACTION_DELETE' => self::ROLE_USER_ACTION_DELETE,
            'ROLE_USER_ACTION_NEW' => self::ROLE_USER_ACTION_NEW,
            'ROLE_USER_ACTION_DETAIL' => self::ROLE_USER_ACTION_DETAIL,
            'ROLE_USER_ACTION_EXPORT' => self::ROLE_USER_ACTION_EXPORT,
        ];
    }

    /// OWNERS ///

    public static function getOwnersAdminEntity()
    {
        return [
            'ROLE_ADMIN_OWNER_ALL' => self::ROLE_ADMIN_OWNER_ALL,
            'ROLE_ADMIN_OWNER_INDEX' => self::ROLE_ADMIN_OWNER_INDEX,
            'ROLE_ADMIN_OWNER_EDIT' => self::ROLE_ADMIN_OWNER_EDIT,
            'ROLE_ADMIN_OWNER_DELETE' => self::ROLE_ADMIN_OWNER_DELETE,
            'ROLE_ADMIN_OWNER_NEW' => self::ROLE_ADMIN_OWNER_NEW,
            'ROLE_ADMIN_OWNER_DETAIL' => self::ROLE_ADMIN_OWNER_DETAIL,
            'ROLE_ADMIN_OWNER_EXPORT' => self::ROLE_ADMIN_OWNER_EXPORT,
        ];
    }

    public static function getOwnersAdminGroupEntity()
    {
        return [
            'ROLE_ADMIN_GROUP_OWNER_ALL' => self::ROLE_ADMIN_GROUP_OWNER_ALL,
            'ROLE_ADMIN_GROUP_OWNER_INDEX' => self::ROLE_ADMIN_GROUP_OWNER_INDEX,
            'ROLE_ADMIN_GROUP_OWNER_EDIT' => self::ROLE_ADMIN_GROUP_OWNER_EDIT,
            'ROLE_ADMIN_GROUP_OWNER_DELETE' => self::ROLE_ADMIN_GROUP_OWNER_DELETE,
            'ROLE_ADMIN_GROUP_OWNER_NEW' => self::ROLE_ADMIN_GROUP_OWNER_NEW,
            'ROLE_ADMIN_GROUP_OWNER_DETAIL' => self::ROLE_ADMIN_GROUP_OWNER_DETAIL,
            'ROLE_ADMIN_GROUP_OWNER_EXPORT' => self::ROLE_ADMIN_GROUP_OWNER_EXPORT,
        ];
    }

    public static function getOwnersShopEntity()
    {
        return [
            'ROLE_SHOP_OWNER_ALL' => self::ROLE_SHOP_OWNER_ALL,
            'ROLE_SHOP_OWNER_INDEX' => self::ROLE_SHOP_OWNER_INDEX,
            'ROLE_SHOP_OWNER_EDIT' => self::ROLE_SHOP_OWNER_EDIT,
            'ROLE_SHOP_OWNER_DELETE' => self::ROLE_SHOP_OWNER_DELETE,
            'ROLE_SHOP_OWNER_NEW' => self::ROLE_SHOP_OWNER_NEW,
            'ROLE_SHOP_OWNER_DETAIL' => self::ROLE_SHOP_OWNER_DETAIL,
            'ROLE_SHOP_OWNER_EXPORT' => self::ROLE_SHOP_OWNER_EXPORT,
        ];
    }

    public static function getOwnersUserEntity()
    {
        return [
            'ROLE_USER_OWNER_ALL' => self::ROLE_USER_OWNER_ALL,
            'ROLE_USER_OWNER_INDEX' => self::ROLE_USER_OWNER_INDEX,
            'ROLE_USER_OWNER_EDIT' => self::ROLE_USER_OWNER_EDIT,
            'ROLE_USER_OWNER_DELETE' => self::ROLE_USER_OWNER_DELETE,
            'ROLE_USER_OWNER_NEW' => self::ROLE_USER_OWNER_NEW,
            'ROLE_USER_OWNER_DETAIL' => self::ROLE_USER_OWNER_DETAIL,
            'ROLE_USER_OWNER_EXPORT' => self::ROLE_USER_OWNER_EXPORT,
        ];
    }

    /*************** -- Functions to have Authenticator checker -- ***************/
    /****************************************************************************/
    /***************************************************************************/
    /**************************************************************************/

    public static function checkAdmin($user)
    {
        if ($user->hasRole(self::IS_ADMIN)) {
            return true;
        }

        return false;
    }

    public static function checkActions($user, $entity, $page)
    {
        $all = 'ROLE_' . $entity . '_ACTION_ALL';
        $page = 'ROLE_' . $entity . '_ACTION_' . $page;

        if ($user->hasRole($all) || $user->hasRole($page)) {
            return true;
        }

        return false;
    }

    public static function checkOwners($user, $entity, $page)
    {
        $all = 'ROLE_' . $entity . '_OWNER_ALL';
        $page = 'ROLE_' . $entity . '_OWNER_' . $page;

        if ($user->hasRole($all) || $user->hasRole($page)) {
            return true;
        }

        return false;
    }
}
