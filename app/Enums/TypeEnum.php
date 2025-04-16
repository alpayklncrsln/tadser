<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TypeEnum :string implements HasLabel
{

    case EDT ="edt";
    case PANORAMA="panorama";
//    case LOGO = "logo";

    public function getLabel(): ?string
    {
       return  $this->name;
    }
}
