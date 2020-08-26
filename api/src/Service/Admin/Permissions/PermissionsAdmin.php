<?php

namespace App\Service\Admin\Permissions;

final class PermissionsAdmin
{
    public const IS_ADMIN = 'ROLE_SUPER_ADMIN';

    // Role actions for Admin 
    public const ROLE_ADMIN_ACTION_ALL      = 'ROLE_ADMIN_ACTION_ALL';
    public const ROLE_ADMIN_ACTION_INDEX    = 'ROLE_ADMIN_ACTION_INDEX';
    public const ROLE_ADMIN_ACTION_EDIT     = 'ROLE_ADMIN_ACTION_EDIT';
    public const ROLE_ADMIN_ACTION_DELETE   = 'ROLE_ADMIN_ACTION_DELETE';
    public const ROLE_ADMIN_ACTION_NEW      = 'ROLE_ADMIN_ACTION_NEW';
    public const ROLE_ADMIN_ACTION_DETAIL   = 'ROLE_ADMIN_ACTION_DETAIL';

    public static function exists(?string $permissionName): bool
    {
        if (null === $permissionName) {
            return false;
        }

        return \defined('self::' . $permissionName);
    }

    public function getActionsAdminEntity()
    {
        return [
            'ROLE_ADMIN_ACTION_ALL' => self::ROLE_ADMIN_ACTION_ALL,
            'ROLE_ADMIN_ACTION_INDEX' => self::ROLE_ADMIN_ACTION_INDEX,
            'ROLE_ADMIN_ACTION_EDIT' => self::ROLE_ADMIN_ACTION_EDIT,
            'ROLE_ADMIN_ACTION_DELETE' => self::ROLE_ADMIN_ACTION_DELETE,
            'ROLE_ADMIN_ACTION_NEW' => self::ROLE_ADMIN_ACTION_NEW,
            'ROLE_ADMIN_ACTION_DETAIL' => self::ROLE_ADMIN_ACTION_DETAIL,
        ];
    }
}
