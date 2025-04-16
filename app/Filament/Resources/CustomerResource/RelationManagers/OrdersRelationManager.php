<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Enums\QuantityType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';
    protected static ?string $title="Siparişler";
    protected static ?string $modelLabel='Sipariş';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
               Forms\Components\Repeater::make('')
                   ->relationship('orderProducts')
                   ->addActionLabel('Ürün Ekle')->cloneable()->label('Ürünler')
                   ->schema([
                       Forms\Components\Select::make('product_id')->label('Ürün')
                           ->relationship('product', 'name')->searchable()
                           ->required()->preload()->columnSpanFull(),

                       Forms\Components\Select::make('quantity_type')->label('Miktar Tipi')
                           ->options(QuantityType::class)
                           ->required()->default(QuantityType::BOX),

                       Forms\Components\TextInput::make('quantity')->integer()->required()
                           ->label('Miktar')->minValue(1)->default(1)->maxValue(100000),
                       Forms\Components\TextInput::make('discount')->integer()->default(0)->required()->minValue(0)
                           ->label('İskonto')->numeric()->maxValue(75)->minValue(0),


                       Forms\Components\TextInput::make('price')->label('Fiyat') ->mask(RawJs::make('$money($input)'))
                           ->stripCharacters(',')
                           ->numeric()
               ])->columnSpanFull()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('code')
            ->columns([
//                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('updated_at')->date('d M Y')->label('İşlem'),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->modifyQueryUsing(fn($query)=>$query->orderByDesc('id'));
    }
}

