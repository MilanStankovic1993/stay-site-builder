<?php

namespace App\Enums;

enum InquiryStatus: string
{
    case New = 'new';
    case Contacted = 'contacted';
    case Closed = 'closed';
    case Spam = 'spam';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Novo',
            self::Contacted => 'Kontaktiran',
            self::Closed => 'Zatvoren',
            self::Spam => 'Spam',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case): array => [$case->value => $case->label()])
            ->all();
    }
}
