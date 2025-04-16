<?php

namespace App\Filament\Resources;

use App\Enums\TypeEnum;
use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $slug = 'products';

    protected static ?string $navigationLabel="Ürünler";

    protected static ?string $modelLabel ="Ürün";
    public static function getPluralModelLabel(): string
    {
        return Self::$navigationLabel;
    }


    protected static ?string $navigationIcon = 'fas-shopping-basket';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?Product $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?Product $record): string => $record?->updated_at?->diffForHumans() ?? '-'),

                Select::make('brand_id')->searchable()->relationship('brand','name')
                    ->label('Marka')
                    ->preload()->columnSpanFull()->required()
                ->createOptionForm([
                    TextInput::make('name')->maxLength(255)->required()->label('Adı'),
                    Textarea::make('description')->maxLength(15555)->label('Açıklama'),
                ])
                    ->editOptionForm([
                        TextInput::make('name')->maxLength(255)->required()->label('Adı'),
                        Textarea::make('description')->maxLength(15555)->label('Açıklama'),
                    ])
                ,
                TextInput::make('name')->label('Adı')
                    ->required()->maxLength(255),
                TextInput::make('code')->maxLength(255)->label('Kodu'),
                Select::make('type')->options(TypeEnum::class)->label('Tipi')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('Kodus')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')->label('Adı')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')->label('Tipi')->badge()
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('brand_id')->searchable()
                    ->relationship('brand','name')->label('Marka')->multiple()
                ->searchable()->preload()
            ])
            ->actions([
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
            'index' => Pages\ListProducts::route('/'),
//            'create' => Pages\CreateProduct::route('/create'),
//            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['brand:id,name'])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
