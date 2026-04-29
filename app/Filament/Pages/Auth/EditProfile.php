<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;

class EditProfile extends BaseEditProfile
{
    public static function getLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Edit profile' : 'Izmeni profil';
    }

    public function getTitle(): string
    {
        return static::getLabel();
    }

    public function getFormContentComponent(): Component
    {
        return Section::make(static::getLabel())
            ->description(app()->getLocale() === 'en'
                ? 'Update your account details and password from one place.'
                : 'Azurirajte podatke naloga i lozinku sa jednog mesta.')
            ->schema([
                parent::getFormContentComponent(),
            ])
            ->compact();
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }
}
