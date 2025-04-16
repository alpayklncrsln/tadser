<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Enums\QuantityType;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrderProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderProducts';

    protected static ?string $title = 'Ürünler';

    /**
     * @return string|null
     */
    public static function getModelLabel(): ?string
    {
        return 'Siparişe Ürün';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')->label('Ürün')
                    ->relationship('product', 'name')->searchable()
                    ->required()->preload()->columnSpanFull(),

                Forms\Components\Select::make('quantity_type')->label('Miktar Tipi')
                  ->options(QuantityType::class)
                    ->required()->default(QuantityType::BOX),

                Forms\Components\TextInput::make('quantity')->integer()->required()
                    ->label('Miktar')->minValue(1)->default(1)->maxValue(100000),
                Forms\Components\TextInput::make('discount')->integer()->default(0)
                    ->label('İskonto')->numeric()->maxValue(75)->minValue(0),


                Forms\Components\TextInput::make('price')->label('Fiyat') ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->numeric()

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('quantity')
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->searchable()->label('Ürün Adı'),
                Tables\Columns\TextColumn::make('quantity')->label('Miktar'),
                Tables\Columns\TextColumn::make('discount')->label('İskonto')->prefix('%'),
                Tables\Columns\TextColumn::make('price')->label('Fiyat')->default('-')->prefix('₺'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Siparişe Ürün Ekle')
                ->modalHeading('Siparişe Ürün Ekle')
                ->modalSubmitActionLabel('Siparişe Ürün Ekle')

                ,
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])


            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
