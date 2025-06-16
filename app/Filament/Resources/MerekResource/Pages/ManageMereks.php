<?php

namespace App\Filament\Resources\MerekResource\Pages;

use App\Filament\Resources\MerekResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMereks extends ManageRecords
{
    protected static string $resource = MerekResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->createAnother(false),
        ];
    }
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
