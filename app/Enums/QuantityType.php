<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum QuantityType:string implements HasLabel
{
    case BOX ="box";
    case QUANTITY ="quantity";
    public function getLabel(): ?string
    {
        return match ($this) {
            self::BOX => 'Koli',
            self::QUANTITY => 'Adet',
        };
    }
}
