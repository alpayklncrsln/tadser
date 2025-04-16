<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Enums\TypeEnum;
use App\Filament\Imports\ProductImporter;
use App\Filament\Resources\ProductResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()->importer(ProductImporter::class),
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            //Tab::make('All')->modifyQueryUsing(fn($query)=>$query),
            Tab::make('LOGO')->modifyQueryUsing(fn($query)=>$query),
            Tab::make('EDT')->modifyQueryUsing(fn($query)=>$query->where('type',TypeEnum::EDT)),
            Tab::make('Panorama')->modifyQueryUsing(fn($query)=>$query->where('type','!=',TypeEnum::EDT)),

        ];
    }
}
