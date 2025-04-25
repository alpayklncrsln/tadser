<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\NoteResource;
use App\Models\Customer;
use App\Models\Note;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;


class CalendarWidget extends FullCalendarWidget
{
    public function fetchEvents(array $info): array
    {
        return Note::query()->get()->map(
                fn(Note $note) => [
                    'id' => $note->id,
                    'title' => $note->description,
                    'start' => $note->start_at,
                    'end' => $note->end_at,
                'url' => NoteResource::getUrl(name: 'edit', parameters: ['record' => $note])
                ])
            ->all();
    }

    public function getFormSchema(): array
    {
        return [
            Select::make('notable_id')->label('Müşteri')->options(fn()=> Customer::get()->pluck('name', 'id')->toArray())
                ->searchable()->columnSpanFull(),
                Textarea::make('description')->ColumnSpanFull()->label('Açıklama'),
                DateTimePicker::make('start_at') ->required()->label('Başlangıç Tarihi'),
                DateTimePicker::make('end_at')->required()->label('Bitis Tarihi'),
        ];
    }


    protected function headerActions(): array
    {
        return [
            CreateAction::make()->mountUsing(
                function (Form $form, array $arguments) {
                    $form->fill([
                        'start_at' => $arguments['start'] ?? null,
                        'end_at' => $arguments['end'] ?? null
                    ]);
                })
        ];
    }
}
