<?php

namespace App\Enums;

enum AccommodationStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Disabled = 'disabled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Skica',
            self::Published => 'Objavljen',
            self::Disabled => 'Onemogućen',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case): array => [$case->value => $case->label()])
            ->all();
    }
}
