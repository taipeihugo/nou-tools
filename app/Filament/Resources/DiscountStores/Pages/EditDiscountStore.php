<?php

namespace App\Filament\Resources\DiscountStores\Pages;

use App\Filament\Resources\DiscountStores\DiscountStoreResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDiscountStore extends EditRecord
{
    protected static string $resource = DiscountStoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
