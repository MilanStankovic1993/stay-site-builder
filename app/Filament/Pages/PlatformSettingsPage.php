<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\InteractsWithPanelContext;
use App\Models\ThemePreset;
use App\Settings\PlatformSettings;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Support\Enums\Width;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class PlatformSettingsPage extends SettingsPage
{
    use InteractsWithPanelContext;

    protected static string $settings = PlatformSettings::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected Width|string|null $maxContentWidth = Width::FiveExtraLarge;

    public static function canAccess(): bool
    {
        return static::isAdminPanel() && (auth()->user()?->isSuperAdmin() ?? false);
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.platform.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('admin.nav.platform');
    }

    public function getTitle(): string
    {
        return __('admin.platform.title');
    }

    public function getSubheading(): ?string
    {
        return __('admin.platform.intro');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.platform.branding_section'))
                    ->description(__('admin.platform.branding_description'))
                    ->schema([
                        TextInput::make('platform_name')
                            ->label(__('admin.platform.name'))
                            ->placeholder('StaySite Builder')
                            ->required(),
                        TextInput::make('platform_contact_email')
                            ->label(__('admin.platform.contact_email'))
                            ->email()
                            ->placeholder('hello@example.com')
                            ->helperText(__('admin.platform.contact_email_help'))
                            ->required(),
                    ])
                    ->columns(2),
                Section::make(__('admin.platform.defaults_section'))
                    ->description(__('admin.platform.defaults_description'))
                    ->schema([
                        Select::make('default_theme')
                            ->label(__('admin.platform.default_theme'))
                            ->options(fn (): array => ThemePreset::query()->where('is_active', true)->pluck('name', 'key')->all())
                            ->required()
                            ->native(false)
                            ->helperText(__('admin.platform.default_theme_help')),
                        TextInput::make('default_meta_title')
                            ->label(__('admin.platform.default_meta_title'))
                            ->placeholder('StaySite Builder')
                            ->helperText(__('admin.platform.default_meta_title_help'))
                            ->required(),
                        Textarea::make('default_meta_description')
                            ->label(__('admin.platform.default_meta_description'))
                            ->helperText(__('admin.platform.default_meta_description_help'))
                            ->placeholder(__('admin.platform.default_meta_description_placeholder'))
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
