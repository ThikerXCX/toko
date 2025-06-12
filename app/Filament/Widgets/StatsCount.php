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
            Stat::make('Products kurang dari minimal stock', \App\Models\Product::whereColumn('stok', '<', 'stok_minimal')->count())
                ->color('danger')
                ->icon('heroicon-o-cube'),
        ];
    }
}
