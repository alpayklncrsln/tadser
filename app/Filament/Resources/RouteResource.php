<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RouteResource\Pages;
use App\Filament\Resources\RouteResource\RelationManagers\CustomersRelationManager;
use App\Models\Route;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RouteResource extends Resource
{
    protected static ?string $model = Route::class;

    protected static ?string $slug = 'routes';

    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string$modelLabel ="Rut";
    public static function getPluralModelLabel(): string
    {
        return "Rutlar";
    }

    protected static ?string $navigationIcon = 'heroicon-o-map';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('created_at')->hiddenOn('create')
                ->label('Oluşturulma Tarihi')
                ->content(fn(?Route $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Güncellme Tarihi')->hiddenOn('create')
                    ->content(fn(?Route $record): string => $record?->updated_at?->diffForHumans() ?? '-'),

                TextInput::make('name')->label('Rota Adı')
                    ->required()->columnSpanFull(),

                Textarea::make('description')->columnSpanFull()->label('Açıklama'),

//                Section::make('Müşteriler')->description('Rotanızdaki muşterileri seçebilirsiniz.')
//                    ->collapsible()
//                    ->collapsed()
//                    ->schema([
//                    CheckboxList::make('customers')->relationship('customers', 'name')->searchable()->columns(2)
//                        ->label('')
//                ])


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Rota Adı')
                    ->description(fn(Route $record): string => $record->description??'-')
                    ->searchable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('customers')->label('Müşteriler')
                    ->fillForm(function (Route $record): array {
                        return [
                            'customers' => $record->customers->pluck('id')->toArray(),
                        ];
                    })->form([
                    CheckboxList::make('customers')->relationship('customers', 'name')->searchable()->columns(2)
                        ->label('')
                ])->modalHeading('Müşteriler'),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoutes::route('/'),
            'create' => Pages\CreateRoute::route('/create'),
            'edit' => Pages\EditRoute::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            CustomersRelationManager::class
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'user.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $details = [];

        if ($record->user) {
            $details['User'] = $record->user->name;
        }

        return $details;
    }
}
