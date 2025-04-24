<?php

namespace App\Filament\Resources\NoteResource\Pages;

use App\Filament\Resources\noteResource;
use App\Models\Customer;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateNote extends CreateRecord
{
    protected static string $resource = NoteResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['notable_type'] = Customer::class;

        return $data;
    }
}
