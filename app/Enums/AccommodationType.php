<?php

namespace App\Enums;

enum AccommodationType: string
{
    case Apartment = 'apartment';
    case Villa = 'villa';
    case House = 'house';
    case Cabin = 'cabin';
    case Studio = 'studio';
    case Room = 'room';

    public function label(): string
    {
        return match ($this) {
            self::Apartment => 'Apartman',
            self::Villa => 'Vila',
            self::House => 'Kuća',
            self::Cabin => 'Brvnara',
            self::Studio => 'Studio',
            self::Room => 'Soba',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case): array => [$case->value => $case->label()])
            ->all();
    }
}
