<?php

namespace App\Filament\Resources;

use App\Enums\AccommodationStatus;
use App\Enums\AccommodationType;
use App\Filament\Concerns\InteractsWithPanelContext;
use App\Filament\Resources\AccommodationResource\Pages;
use App\Models\Accommodation;
use App\Models\ThemePreset;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class AccommodationResource extends Resource
{
    use InteractsWithPanelContext;

    protected static ?string $model = Accommodation::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHomeModern;

    protected static ?string $recordTitleAttribute = 'title';

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('admin.nav.site_builder');
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Accommodation' : 'Smestaj';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() === 'en' ? 'Accommodations' : 'Smestaji';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Osnovni podaci')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('user_id')
                                ->label('Vlasnik')
                                ->relationship('user', 'name')
                                ->searchable()
                                ->preload()
                                ->visible(fn (): bool => auth()->user()?->isSuperAdmin() ?? false)
                                ->required()
                                ->columnSpan(1),
                            Select::make('type')
                                ->label('Tip smestaja')
                                ->options(AccommodationType::options())
                                ->required()
                                ->native(false)
                                ->columnSpan(1),
                            TextInput::make('title')
                                ->label('Naziv')
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Set $set, ?string $state): void {
                                    $set('slug', Str::slug($state ?? ''));
                                })
                                ->columnSpan(1),
                            TextInput::make('title_en')
                                ->label('Naziv na engleskom')
                                ->maxLength(255)
                                ->columnSpan(1),
                            TextInput::make('slug')
                                ->label('Slug')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->columnSpan(1),
                            Select::make('status')
                                ->label('Status')
                                ->options(AccommodationStatus::options())
                                ->default(AccommodationStatus::Draft->value)
                                ->required()
                                ->native(false)
                                ->visible(fn (): bool => static::isAdminPanel())
                                ->columnSpan(1),
                            TextInput::make('currency')
                                ->label('Valuta')
                                ->default('EUR')
                                ->maxLength(3)
                                ->required()
                                ->columnSpan(1),
                        ]),
                    ]),
                Section::make('Opis i kapacitet')
                    ->schema([
                        Grid::make(2)->schema([
                            Textarea::make('short_description')->label('Kratak opis (SR)')->rows(4),
                            Textarea::make('short_description_en')->label('Short description (EN)')->rows(4),
                        ]),
                        Grid::make(2)->schema([
                            Textarea::make('description')->label('Opis (SR)')->rows(8),
                            Textarea::make('description_en')->label('Description (EN)')->rows(8),
                        ]),
                        Grid::make(5)->schema([
                            TextInput::make('max_guests')->label('Maks. gostiju')->numeric(),
                            TextInput::make('bedrooms')->label('Sobe')->numeric(),
                            TextInput::make('bathrooms')->label('Kupatila')->numeric(),
                            TextInput::make('beds')->label('Kreveti')->numeric(),
                            TextInput::make('size_m2')->label('Kvadratura m2')->numeric(),
                        ]),
                        TextInput::make('price_from')->label('Cena od')->numeric()->prefix('EUR'),
                    ]),
                Section::make('Lokacija')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('location_name')->label('Lokacija (SR)'),
                            TextInput::make('location_name_en')->label('Location name (EN)'),
                            TextInput::make('address')->label('Adresa (SR)'),
                            TextInput::make('address_en')->label('Address (EN)'),
                            TextInput::make('city')->label('Grad (SR)'),
                            TextInput::make('city_en')->label('City (EN)'),
                            TextInput::make('region')->label('Region (SR)'),
                            TextInput::make('region_en')->label('Region (EN)'),
                            TextInput::make('country')->label('Drzava (SR)'),
                            TextInput::make('country_en')->label('Country (EN)'),
                            TextInput::make('google_maps_url')->label('Google Maps URL')->url(),
                            TextInput::make('latitude')->label('Latitude')->numeric(),
                            TextInput::make('longitude')->label('Longitude')->numeric(),
                        ]),
                    ]),
                Section::make('Sadrzaji')
                    ->schema([
                        Select::make('amenities')
                            ->label('Sadrzaji')
                            ->relationship('amenities', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                    ]),
                Section::make('Slike')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('hero_media')->label('Hero slika')->collection('hero')->image()->disk('public')->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('gallery_media')->label('Galerija')->collection('gallery')->multiple()->reorderable()->image()->disk('public')->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('logo_media')->label('Logo')->collection('logo')->image()->disk('public')->columnSpanFull(),
                    ]),
                Section::make('Kontakt i linkovi')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('contact_name')->label('Kontakt osoba'),
                            TextInput::make('contact_phone')->label('Telefon'),
                            TextInput::make('contact_email')->label('Email')->email(),
                            TextInput::make('whatsapp_number')->label('WhatsApp broj'),
                            TextInput::make('viber_number')->label('Viber broj'),
                            TextInput::make('instagram_url')->label('Instagram')->url(),
                            TextInput::make('facebook_url')->label('Facebook')->url(),
                            TextInput::make('booking_url')->label('Booking URL')->url(),
                            TextInput::make('airbnb_url')->label('Airbnb URL')->url(),
                            TextInput::make('website_url')->label('Website URL')->url(),
                        ]),
                    ]),
                Section::make('Izgled sajta')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('theme_key')
                                ->label('Tema')
                                ->options(fn (): array => ThemePreset::query()->where('is_active', true)->pluck('name', 'key')->all() ?: ['default' => 'Default'])
                                ->default('default')
                                ->native(false),
                            TextInput::make('primary_color')->label('Primarna boja')->type('color'),
                            TextInput::make('secondary_color')->label('Sekundarna boja')->type('color'),
                        ]),
                    ]),
                Section::make('SEO')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('meta_title')->label('Meta naslov (SR)')->maxLength(255),
                            TextInput::make('meta_title_en')->label('Meta title (EN)')->maxLength(255),
                        ]),
                        Grid::make(2)->schema([
                            Textarea::make('meta_description')->label('Meta opis (SR)')->rows(4),
                            Textarea::make('meta_description_en')->label('Meta description (EN)')->rows(4),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('user'))
            ->columns([
                TextColumn::make('title')
                    ->label('Naziv')
                    ->searchable()
                    ->searchable(['title', 'title_en', 'slug'])
                    ->sortable()
                    ->formatStateUsing(fn (string $state, Accommodation $record): string => $record->isDemoAccommodation() ? $state.' [Demo]' : $state),
                TextColumn::make('type')->label('Tip')->formatStateUsing(fn ($state) => $state?->label() ?? $state)->badge(),
                TextColumn::make('status')->label('Status')->formatStateUsing(fn ($state) => $state?->label() ?? $state)->badge(),
                TextColumn::make('user.name')->label('Vlasnik')->visible(fn (): bool => static::isAdminPanel()),
                TextColumn::make('city')->label('Grad')->searchable(),
                TextColumn::make('theme_key')->label('Tema')->badge(),
                TextColumn::make('published_at')->label('Objavljen')->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('created_at')->label('Kreiran')->dateTime('d.m.Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->label('Status')->options(AccommodationStatus::options()),
                SelectFilter::make('type')->label('Tip')->options(AccommodationType::options()),
                SelectFilter::make('user_id')
                    ->label('Vlasnik')
                    ->options(fn (): array => User::query()->role('owner')->orderBy('name')->pluck('name', 'id')->all())
                    ->visible(fn (): bool => static::isAdminPanel()),
                SelectFilter::make('city')
                    ->label('Grad')
                    ->options(fn (): array => Accommodation::query()
                        ->whereNotNull('city')
                        ->orderBy('city')
                        ->pluck('city', 'city')
                        ->all()),
                SelectFilter::make('theme_key')
                    ->label('Tema')
                    ->options(fn (): array => ThemePreset::query()->orderBy('name')->pluck('name', 'key')->all()),
                Filter::make('demo_only')
                    ->label('Samo demo')
                    ->query(fn (Builder $query): Builder => $query->where('slug', 'villa-lavanda-tara'))
                    ->visible(fn (): bool => static::isAdminPanel()),
            ])
            ->recordActions([
                ViewAction::make()->label('Pregled'),
                EditAction::make()->label('Izmeni'),
                Action::make('preview')->label('Preview sajta')->icon(Heroicon::OutlinedEye)->url(fn (Accommodation $record): string => $record->previewUrl(), shouldOpenInNewTab: true),
                Action::make('build_site')
                    ->label(fn (): string => static::isOwnerPanel() ? 'Build my site' : 'Objavi')
                    ->icon(Heroicon::OutlinedArrowUpCircle)
                    ->color('success')
                    ->visible(fn (Accommodation $record): bool => $record->status !== AccommodationStatus::Published)
                    ->disabled(fn (): bool => static::isOwnerPanel() && ! (auth()->user()?->canPublishSites() ?? false))
                    ->tooltip(fn (): ?string => static::isOwnerPanel() && ! (auth()->user()?->canPublishSites() ?? false)
                        ? 'Objava sajta je dostupna nakon odobrenja super admina.'
                        : null)
                    ->action(function (Accommodation $record): void {
                        if (static::isOwnerPanel() && ! (auth()->user()?->canPublishSites() ?? false)) {
                            Notification::make()
                                ->title('Objava jos nije odobrena')
                                ->body('Super admin mora da aktivira dozvolu za objavu sajta nakon uplate ili odobrenja.')
                                ->warning()
                                ->send();

                            return;
                        }

                        $record->update([
                            'status' => AccommodationStatus::Published,
                            'published_at' => now(),
                        ]);
                    }),
                Action::make('unpublish')
                    ->label(fn (): string => static::isOwnerPanel() ? 'Sakrij sajt' : 'Povuci objavu')
                    ->icon(Heroicon::OutlinedArrowDownCircle)
                    ->color('gray')
                    ->visible(fn (Accommodation $record): bool => $record->status === AccommodationStatus::Published)
                    ->action(fn (Accommodation $record) => $record->update([
                        'status' => AccommodationStatus::Draft,
                        'published_at' => null,
                    ])),
                DeleteAction::make()->label('Obrisi'),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user && static::isOwnerPanel() && ! $user->isSuperAdmin()) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public static function getGlobalSearchResultTitle(\Illuminate\Database\Eloquent\Model $record): string
    {
        return $record->title;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccommodations::route('/'),
            'create' => Pages\CreateAccommodation::route('/create'),
            'view' => Pages\ViewAccommodation::route('/{record}'),
            'edit' => Pages\EditAccommodation::route('/{record}/edit'),
        ];
    }
}
