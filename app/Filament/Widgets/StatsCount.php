<?php

namespace App\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsCount extends BaseWidget
{
    use HasWidgetShield;
    protected function getStats(): array
    {
        return [
            Stat::make('Users', \App\Models\User::count())
                ->color('primary')
                ->icon('heroicon-o-users'),
            Stat::make('Roles', \Spatie\Permission\Models\Role::count())
                ->color('success')
                ->icon('heroicon-o-shield-check'),
            Stat::make('Permissions', \Spatie\Permission\Models\Permission::count())
                ->color('warning')
                ->icon('heroicon-o-key'),
        ];
    }
}
