<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\InteractsWithPanelContext;
use App\Filament\Resources\ThemePresetResource\Pages;
use App\Models\ThemePreset;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ThemePresetResource extends Resource
{
    use InteractsWithPanelContext;

    protected static ?string $model = ThemePreset::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canAccess(): bool
    {
        return static::isAdminPanel() && (auth()->user()?->isSuperAdmin() ?? false);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('admin.nav.catalog');
    }

    public static function getModelLabel(): string
    {
        return __('admin.themes.single');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.themes.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.themes.section'))
                    ->schema([
                        TextInput::make('key')->label(__('admin.themes.key'))->required()->unique(ignoreRecord: true),
                        TextInput::make('name')->label(__('admin.themes.name'))->required(),
                        Textarea::make('description')->label(__('admin.themes.description'))->rows(4)->columnSpanFull(),
                        TextInput::make('preview_image')->label(__('admin.themes.preview_image'))->url(),
                        Toggle::make('is_active')->label(__('admin.themes.active'))->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('admin.themes.name'))->searchable(),
                TextColumn::make('key')->label(__('admin.themes.key'))->badge(),
                TextColumn::make('description')->label(__('admin.themes.description'))->limit(70)->wrap(),
                IconColumn::make('is_active')->label(__('admin.themes.active'))->boolean(),
                TextColumn::make('updated_at')->label(__('admin.themes.updated_at'))->dateTime('d.m.Y H:i'),
            ])
            ->recordActions([
                Action::make('preview')
                    ->label(__('admin.themes.preview'))
                    ->url(fn (ThemePreset $record): string => route('storefront.demo-theme', $record->key), shouldOpenInNewTab: true),
                EditAction::make()->label(__('admin.themes.edit')),
                DeleteAction::make()->label(__('admin.themes.delete')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListThemePresets::route('/'),
            'create' => Pages\CreateThemePreset::route('/create'),
            'edit' => Pages\EditThemePreset::route('/{record}/edit'),
        ];
    }
}
