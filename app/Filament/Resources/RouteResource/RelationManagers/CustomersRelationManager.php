<?php

namespace App\Filament\Resources\RouteResource\RelationManagers;

use App\Enums\DayEnum;
use App\Enums\TypeEnum;
use App\Models\Customer;
use App\Models\Route;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomersRelationManager extends RelationManager
{
    protected static string $relationship = 'customers';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->id('tabs')
                    ->tabs([
                        Tabs\Tab::make('Müşteri Seç')
                            ->schema([
                                CheckboxList::make('customers')->label('Müşteriler')
                                    ->columnSpanFull()
                                    ->options(fn(): array => Customer::select(['id', 'name'])->pluck('name', 'id')->toArray())
                                    ->searchable()->columns(2)
                            ]),
                        Tabs\Tab::make('Müşteri Oluştur')
                            ->schema([
                                Placeholder::make('created_at')
                                    ->label('Oluşturulma Tarihi')
                                    ->content(fn(?Customer $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                                Placeholder::make('updated_at')
                                    ->label('Düzenleme Tarihi')
                                    ->content(fn(?Customer $record): string => $record?->updated_at?->diffForHumans() ?? '-'),

                                TextInput::make('code')->label('Müşteri Kodu')
                                    ->maxLength(12)
                                    ->required(fn(Forms\Get $get): bool => $get('tabs')== 2),

                                TextInput::make('name')
                                    ->label('Müşteri Adı')
                                    ->maxLength(255)
                                    ->required(fn(Forms\Get $get): bool => $get('tabs')== 2),


                                TextInput::make('owner')
                                    ->label('Sahibi'),

                                TextInput::make('phone')
                                    ->label('Telefon Numarası'),

                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true),

                                Toggle::make('is_phone')->label('Telefon Müşterisi'),

                                Select::make('work_type')->label('Çalışma Tipi')
                                    ->options(TypeEnum::class),

                                Select::make('payment_day')->name('payment_day')->id('payment_day')->label('Ödeme Günü')->options(DayEnum::class)->default(DayEnum::WEDNESDAY),
                            ])->columns(2)
                    ])->columnSpanFull()->contained(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Müşteriler')
            ->columns([
                Tables\Columns\TextColumn::make('code')->label('Müşteri Kodu'),
                Tables\Columns\TextColumn::make('name')->label('Müşteri Adı'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
//                Tables\Actions\AttachAction::make(),
               Tables\Actions\CreateAction::make()->label('Müşteri Ekle')->slideOver()
                ->fillForm( function (): array {
                    return [
                        'customers' => $this->getOwnerRecord()->customers->pluck('id')->toArray(),
                    ];
                })->action(function (array $data): void {
                    if (!empty($data['customers']) && $data['name'] == null) {
                        $this->getOwnerRecord()->customers()->sync($data['customers']);
                    }
                    else {
                        unset($data['customers']);
                        $this->getOwnerRecord()->customers()->create($data);
                    }
                })
            ])
            ->actions([
                Tables\Actions\EditAction::make()->url(fn($record) => route('filament.admin.resources.customers.edit', $record)),
                Tables\Actions\DetachAction::make(),
            ]);
    }
}
