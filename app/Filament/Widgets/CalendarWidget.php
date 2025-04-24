<?php

namespace App\Filament\Widgets;

use App\Models\Note;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;


class CalendarWidget extends FullCalendarWidget
{
    public function fetchEvents(array $info): array
    {


        return Note::query()
            ->where('start_at', '>=', $info['started_at'])
            ->where('end_at', '<=', $info['ended_at'])
            ->get()
            ->map(
                fn(Note $note) => [
                    'id' => $note->id,
                    'title' => $note->description,
                    'start' => $note->start_at,
                    'end' => $note->end_at,
//                'url' => NoteResource::getUrl(name: 'edit', parameters: ['record' => $note])
                ])
            ->all();
    }

    public function config(): array
    {
        return [
            'firstDay' => 1,
            'headerToolbar' => [
                'left' => 'dayGridWeek,dayGridDay',
                'center' => 'title',
                'right' => 'prev,next today',
            ],
        ];
    }


}
