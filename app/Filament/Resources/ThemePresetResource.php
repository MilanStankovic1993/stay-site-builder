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

    protected static string|\UnitEnum|null $navigationGroup = 'Katalog';

    protected static ?string $modelLabel = 'Tema';

    protected static ?string $pluralModelLabel = 'Teme';

    public static function canAccess(): bool
    {
        return static::isAdminPanel() && (auth()->user()?->isSuperAdmin() ?? false);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tema')
                    ->schema([
                        TextInput::make('key')->label('Key')->required()->unique(ignoreRecord: true),
                        TextInput::make('name')->label('Naziv')->required(),
                        Textarea::make('description')->label('Opis')->rows(4)->columnSpanFull(),
                        TextInput::make('preview_image')->label('Preview image URL')->url(),
                        Toggle::make('is_active')->label('Aktivna')->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Naziv')->searchable(),
                TextColumn::make('key')->label('Key')->badge(),
                TextColumn::make('description')->label('Opis')->limit(70)->wrap(),
                IconColumn::make('is_active')->label('Aktivna')->boolean(),
                TextColumn::make('updated_at')->label('Azurirano')->dateTime('d.m.Y H:i'),
            ])
            ->recordActions([
                Action::make('preview')
                    ->label('Preview teme')
                    ->url(fn (ThemePreset $record): string => route('storefront.demo-theme', $record->key), shouldOpenInNewTab: true),
                EditAction::make()->label('Izmeni'),
                DeleteAction::make()->label('Obrisi'),
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
