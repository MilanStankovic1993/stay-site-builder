<?php

namespace App\Enums;

enum InquirySource: string
{
    case Website = 'website';

    public function label(): string
    {
        return match ($this) {
            self::Website => 'Website',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case): array => [$case->value => $case->label()])
            ->all();
    }
}
