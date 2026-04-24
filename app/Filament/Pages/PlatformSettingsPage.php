<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\InteractsWithPanelContext;
use App\Models\ThemePreset;
use App\Settings\PlatformSettings;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PlatformSettingsPage extends SettingsPage
{
    use InteractsWithPanelContext;

    protected static string $settings = PlatformSettings::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Globalna podesavanja';

    protected static string|\UnitEnum|null $navigationGroup = 'Platforma';

    protected static ?string $title = 'Globalna podesavanja';

    public static function canAccess(): bool
    {
        return static::isAdminPanel() && (auth()->user()?->isSuperAdmin() ?? false);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Platforma')
                    ->schema([
                        TextInput::make('platform_name')->label('Naziv platforme')->required(),
                        TextInput::make('platform_contact_email')->label('Kontakt email')->email()->required(),
                        Select::make('default_theme')
                            ->label('Podrazumevana tema za nove sajtove')
                            ->options(fn (): array => ThemePreset::query()->where('is_active', true)->pluck('name', 'key')->all())
                            ->required()
                            ->native(false)
                            ->helperText('Koristi se kao fallback kada korisnik ne izabere temu.'),
                        TextInput::make('default_meta_title')->label('Podrazumevani SEO naslov')->required(),
                        Textarea::make('default_meta_description')
                            ->label('Podrazumevani SEO opis')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
