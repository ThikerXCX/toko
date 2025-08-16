<?php

namespace App\Traits;

trait FilamentPermissionAwareWidget
{
    public static function canView(): bool
    {
        $user = auth()->user();

        if (!$user) return false;

        // Jika super admin, selalu bisa lihat widget
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Cek permission berdasarkan property `$requiredPermission`
        return $user->can(static::$requiredPermission ?? '');
    }
}
