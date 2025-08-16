<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait FilamentPermissionAwareNavigation
{
    public static function canAccessMenu(): bool
    {
        $user = auth()->user();

        if (!$user) return false;

        // Jika super admin, bisa akses semuanya
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Cek permission khusus kalau bukan super_admin
        return $user->can(static::$requiredPermission ?? '');
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();

        if (!$user) return false;

        // Super admin selalu bisa lihat navigasi
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Permission berdasarkan nama slug menu
        return $user->can(static::$requiredPermission ?? 'view_' . static::getSlug());
    }
}
