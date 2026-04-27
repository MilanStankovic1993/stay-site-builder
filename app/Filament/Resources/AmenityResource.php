<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\InteractsWithPanelContext;
use App\Filament\Resources\AmenityResource\Pages;
use App\Models\Amenity;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AmenityResource extends Resource
{
    use InteractsWithPanelContext;

    protected static ?string $model = Amenity::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

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
        return __('admin.amenities.single');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.amenities.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.amenities.section'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')->label(__('admin.amenities.name'))->required()->maxLength(255),
                            TextInput::make('category')->label(__('admin.amenities.category'))->maxLength(255),
                            TextInput::make('icon')->label(__('admin.amenities.icon'))->maxLength(255)->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('admin.amenities.name'))->searchable()->sortable(),
                TextColumn::make('category')->label(__('admin.amenities.category'))->searchable(),
                TextColumn::make('icon')->label(__('admin.amenities.icon'))->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make()->label(__('admin.amenities.edit')),
                DeleteAction::make()->label(__('admin.amenities.delete')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAmenities::route('/'),
            'create' => Pages\CreateAmenity::route('/create'),
            'edit' => Pages\EditAmenity::route('/{record}/edit'),
        ];
    }
}
