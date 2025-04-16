<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum DayEnum: string implements HasLabel
{
    case MONDAY = "monday";
    case TUESDAY = "tuesday";
    case WEDNESDAY = "wednesday";
    case THURSDAY = "thursday";
    case FRIDAY = "friday";
    case SATURDAY = "saturday";
    case SUNDAY = "sunday";

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MONDAY => 'Pazartesi',
            self::TUESDAY => 'Salı',
            self::WEDNESDAY => 'Çarşamba',
            self::THURSDAY => 'Perşembe',
            self::FRIDAY => 'Cuma',
            self::SATURDAY => 'Cumartesi',
            self::SUNDAY => 'Pazar',
        };
    }
}
