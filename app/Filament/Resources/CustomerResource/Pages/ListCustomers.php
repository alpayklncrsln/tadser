<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Imports\CustomerImporter;
use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('end of day')->label('Gün Sonu')
                ->requiresConfirmation()
                ->modalIcon('heroicon-o-clock')
                ->modalHeading('Gün Sonu')
                ->modalDescription('Gün sonu oluşturmaya emin misiniz ?')
                ->modalSubmitActionLabel('Evet')
                ->action(function (){
                    $startOfDay = now()->startOfDay();
                    $endOfDay = now()->endOfDay();

                    $orders = Order::query()
                        ->where('user_id',Auth::id())
                        ->where(function($query) use ($startOfDay, $endOfDay) {
                            $query->whereBetween('created_at', [$startOfDay, $endOfDay])
                                ->orWhereBetween('updated_at', [$startOfDay, $endOfDay]);
                        })
                        ->with([
                            'customer:id,name,work_type,code,owner',
                            'user:id,name',
                            'orderProducts.product:id,name,code',
                        ])->get();
                    $pdfContent = Pdf::loadView('pdf.end-of-day', ['orders' => $orders])
                        ->output();

                    return response()->streamDownload(function () use ($pdfContent) {
                        echo $pdfContent;
                    }, 'gün sonu '.now()->format('d-m-Y').' '.Auth::user()->name.'.pdf');
            }),
            //ImportAction::make()->importer(CustomerImporter::class),
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'aktif'=> Tab::make('Aktif')->modifyQueryUsing(fn($query)=>$query->where('is_active',true)),
            'tumu'=> Tab::make('Tümü')->modifyQueryUsing(fn($query)=>$query),
            'pasif'=> Tab::make('Pasif')->modifyQueryUsing(fn($query)=>$query->where('is_active',false)),
        ];
    }
}
