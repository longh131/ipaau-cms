<?php

namespace App\Filament\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

trait AuthorizesAdminAccess
{
    public static function canViewAny(): bool
    {
        return static::userHasAdminPermission(static::adminPermissionFor('view'));
    }

    public static function canCreate(): bool
    {
        return static::userHasAdminPermission(static::adminPermissionFor('create'));
    }

    public static function canEdit(Model $record): bool
    {
        return static::userHasAdminPermission(static::adminPermissionFor('edit'));
    }

    public static function canDelete(Model $record): bool
    {
        return static::userHasAdminPermission(static::adminPermissionFor('delete'));
    }

    public static function canDeleteAny(): bool
    {
        return static::userHasAdminPermission(static::adminPermissionFor('delete'));
    }

    protected static function adminPermissionFor(string $ability): ?string
    {
        if (isset(static::$adminPermission) && is_string(static::$adminPermission) && static::$adminPermission !== '') {
            return static::$adminPermission;
        }

        if (isset(static::$adminPermissionPrefix) && is_string(static::$adminPermissionPrefix) && static::$adminPermissionPrefix !== '') {
            return match ($ability) {
                'create' => static::$adminPermissionPrefix.'.create',
                'edit' => static::$adminPermissionPrefix.'.edit',
                'delete' => static::$adminPermissionPrefix.'.delete',
                default => static::$adminPermissionPrefix.'.view',
            };
        }

        return null;
    }

    protected static function userHasAdminPermission(?string $permission): bool
    {
        if ($permission === null) {
            return false;
        }

        $user = auth()->user();

        return $user instanceof User && $user->hasPermission($permission);
    }
}
