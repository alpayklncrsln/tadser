<?php

namespace App\Filament\Resources;

use App\Enums\DayEnum;
use App\Enums\TypeEnum;
use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers\OrdersRelationManager;
use App\Models\Customer;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationLabel="Müşteriler";

    protected static ?string $modelLabel ="Müşteri";
    public static function getPluralModelLabel(): string
    {
        return Self::$navigationLabel;
    }


    protected static ?string $slug = 'customers';

    protected static ?string $navigationIcon = 'fas-store';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->content(fn(?Customer $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Düzenleme Tarihi')
                    ->content(fn(?Customer $record): string => $record?->updated_at?->diffForHumans() ?? '-'),

                TextInput::make('code')->label('Müşteri Kodu')
                    ->maxLength(12)
                    ->required(),

                TextInput::make('name')
                    ->label('Müşteri Adı')
                    ->maxLength(255)
                    ->required(),


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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kodu'),
                TextColumn::make('name')
                    ->label('Adı')
                    ->searchable()
                    ->sortable()->limit(40)->toggleable(),

                TextColumn::make('owner')->label("Sahibi")
                    ->default('-')->toggleable(isToggledHiddenByDefault:true),

                TextColumn::make('phone')->label('Telefon')
                    ->url(fn($state)=>'tel:'.$state)->toggleable(),

                ToggleColumn::make('is_active')
                    ->label('Aktif')->toggleable(),

                ToggleColumn::make('is_phone')
                    ->label("Telefon Muşterisi")->toggleable(),

                TextColumn::make('work_type')->label('Çalışma Tipi')->badge()->toggleable(isToggledHiddenByDefault:true),
//                TextColumn::make('payment_day')->label('Ödeme Günü'),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('work_type')->label('Çalışma Tipi')
                    ->options(TypeEnum::class),

            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            //->select(['id','code','name','is_active','is_phone','phone'])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [
            OrdersRelationManager::class
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
