<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

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
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            Tab::make('Bugün')->modifyQueryUsing(function(Builder $query){
                $startOfDay = now()->startOfDay();
                $endOfDay = now()->endOfDay();
              return  $query->withoutTrashed()->whereBetween('created_at', [$startOfDay, $endOfDay])
                    ->orWhereBetween('updated_at', [$startOfDay, $endOfDay]);
            }),
            Tab::make('Tümü')->modifyQueryUsing(fn($query)=>$query),
        ];
    }
}
