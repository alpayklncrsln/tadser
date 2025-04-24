<?php

namespace App\Filament\Resources\NoteResource\Pages;

use App\Filament\Resources\noteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNote extends ListRecords
{
    protected static string $resource = noteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
