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
        return __('admin.accommodations.single');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.accommodations.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.accommodations.basic_section'))
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('user_id')
                                ->label(__('admin.accommodations.owner'))
                                ->relationship('user', 'name')
                                ->searchable()
                                ->preload()
                                ->visible(fn (): bool => auth()->user()?->isSuperAdmin() ?? false)
                                ->required()
                                ->columnSpan(1),
                            Select::make('type')
                                ->label(__('admin.accommodations.type'))
                                ->options(AccommodationType::options())
                                ->required()
                                ->native(false)
                                ->columnSpan(1),
                            TextInput::make('title')
                                ->label(__('admin.accommodations.title'))
                                ->required()
                                ->maxLength(255)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Set $set, ?string $state): void {
                                    $set('slug', Str::slug($state ?? ''));
                                })
                                ->columnSpan(1),
                            TextInput::make('title_en')
                                ->label(__('admin.accommodations.title_en'))
                                ->maxLength(255)
                                ->columnSpan(1),
                            TextInput::make('slug')
                                ->label(__('admin.accommodations.slug'))
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->columnSpan(1),
                            Select::make('status')
                                ->label(__('admin.accommodations.status'))
                                ->options(AccommodationStatus::options())
                                ->default(AccommodationStatus::Draft->value)
                                ->required()
                                ->native(false)
                                ->visible(fn (): bool => static::isAdminPanel())
                                ->columnSpan(1),
                            TextInput::make('currency')
                                ->label(__('admin.accommodations.currency'))
                                ->default('EUR')
                                ->maxLength(3)
                                ->required()
                                ->columnSpan(1),
                        ]),
                    ]),
                Section::make(__('admin.accommodations.content_section'))
                    ->schema([
                        Grid::make(2)->schema([
                            Textarea::make('short_description')->label(__('admin.accommodations.short_description'))->rows(4),
                            Textarea::make('short_description_en')->label(__('admin.accommodations.short_description_en'))->rows(4),
                        ]),
                        Grid::make(2)->schema([
                            Textarea::make('description')->label(__('admin.accommodations.description'))->rows(8),
                            Textarea::make('description_en')->label(__('admin.accommodations.description_en'))->rows(8),
                        ]),
                        Grid::make(5)->schema([
                            TextInput::make('max_guests')->label(__('admin.accommodations.max_guests'))->numeric(),
                            TextInput::make('bedrooms')->label(__('admin.accommodations.bedrooms'))->numeric(),
                            TextInput::make('bathrooms')->label(__('admin.accommodations.bathrooms'))->numeric(),
                            TextInput::make('beds')->label(__('admin.accommodations.beds'))->numeric(),
                            TextInput::make('size_m2')->label(__('admin.accommodations.size_m2'))->numeric(),
                        ]),
                        TextInput::make('price_from')->label(__('admin.accommodations.price_from'))->numeric()->prefix('EUR'),
                    ]),
                Section::make(__('admin.accommodations.location_section'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('location_name')->label(__('admin.accommodations.location_name')),
                            TextInput::make('location_name_en')->label(__('admin.accommodations.location_name_en')),
                            TextInput::make('address')->label(__('admin.accommodations.address')),
                            TextInput::make('address_en')->label(__('admin.accommodations.address_en')),
                            TextInput::make('city')->label(__('admin.accommodations.city')),
                            TextInput::make('city_en')->label(__('admin.accommodations.city_en')),
                            TextInput::make('region')->label(__('admin.accommodations.region')),
                            TextInput::make('region_en')->label(__('admin.accommodations.region_en')),
                            TextInput::make('country')->label(__('admin.accommodations.country')),
                            TextInput::make('country_en')->label(__('admin.accommodations.country_en')),
                            TextInput::make('google_maps_url')->label(__('admin.accommodations.google_maps_url'))->url(),
                            TextInput::make('latitude')->label(__('admin.accommodations.latitude'))->numeric(),
                            TextInput::make('longitude')->label(__('admin.accommodations.longitude'))->numeric(),
                        ]),
                    ]),
                Section::make(__('admin.accommodations.amenities_section'))
                    ->schema([
                        Select::make('amenities')
                            ->label(__('admin.accommodations.amenities'))
                            ->relationship('amenities', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                    ]),
                Section::make(__('admin.accommodations.media_section'))
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('hero_media')->label(__('admin.accommodations.hero_image'))->collection('hero')->image()->disk('public')->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('gallery_media')->label(__('admin.accommodations.gallery'))->collection('gallery')->multiple()->reorderable()->image()->disk('public')->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('logo_media')->label(__('admin.accommodations.logo'))->collection('logo')->image()->disk('public')->columnSpanFull(),
                    ]),
                Section::make(__('admin.accommodations.contact_section'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('contact_name')->label(__('admin.accommodations.contact_name')),
                            TextInput::make('contact_phone')->label(__('admin.accommodations.contact_phone')),
                            TextInput::make('contact_email')->label(__('admin.accommodations.contact_email'))->email(),
                            TextInput::make('whatsapp_number')->label(__('admin.accommodations.whatsapp_number')),
                            TextInput::make('viber_number')->label(__('admin.accommodations.viber_number')),
                            TextInput::make('instagram_url')->label(__('admin.accommodations.instagram_url'))->url(),
                            TextInput::make('facebook_url')->label(__('admin.accommodations.facebook_url'))->url(),
                            TextInput::make('booking_url')->label(__('admin.accommodations.booking_url'))->url(),
                            TextInput::make('airbnb_url')->label(__('admin.accommodations.airbnb_url'))->url(),
                            TextInput::make('website_url')->label(__('admin.accommodations.website_url'))->url(),
                        ]),
                    ]),
                Section::make(__('admin.accommodations.appearance_section'))
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('theme_key')
                                ->label(__('admin.accommodations.theme'))
                                ->options(fn (): array => ThemePreset::query()->where('is_active', true)->pluck('name', 'key')->all() ?: ['default' => 'Default'])
                                ->default('default')
                                ->native(false),
                            TextInput::make('primary_color')->label(__('admin.accommodations.primary_color'))->type('color'),
                            TextInput::make('secondary_color')->label(__('admin.accommodations.secondary_color'))->type('color'),
                        ]),
                    ]),
                Section::make(__('admin.accommodations.seo_section'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('meta_title')->label(__('admin.accommodations.meta_title'))->maxLength(255),
                            TextInput::make('meta_title_en')->label(__('admin.accommodations.meta_title_en'))->maxLength(255),
                        ]),
                        Grid::make(2)->schema([
                            Textarea::make('meta_description')->label(__('admin.accommodations.meta_description'))->rows(4),
                            Textarea::make('meta_description_en')->label(__('admin.accommodations.meta_description_en'))->rows(4),
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
                    ->label(__('admin.accommodations.title'))
                    ->searchable()
                    ->searchable(['title', 'title_en', 'slug'])
                    ->sortable()
                    ->formatStateUsing(fn (string $state, Accommodation $record): string => $record->isDemoAccommodation() ? $state.' [Demo]' : $state),
                TextColumn::make('type')->label(__('admin.accommodations.type'))->formatStateUsing(fn ($state) => $state?->label() ?? $state)->badge(),
                TextColumn::make('status')->label(__('admin.accommodations.status'))->formatStateUsing(fn ($state) => $state?->label() ?? $state)->badge(),
                TextColumn::make('user.name')->label(__('admin.accommodations.owner'))->visible(fn (): bool => static::isAdminPanel()),
                TextColumn::make('city')->label(__('admin.accommodations.city'))->searchable(),
                TextColumn::make('theme_key')->label(__('admin.accommodations.theme'))->badge(),
                TextColumn::make('published_at')->label(__('admin.accommodations.published_at'))->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('created_at')->label(__('admin.accommodations.created_at'))->dateTime('d.m.Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('admin.accommodations.status'))->options(AccommodationStatus::options()),
                SelectFilter::make('type')->label(__('admin.accommodations.type'))->options(AccommodationType::options()),
                SelectFilter::make('user_id')
                    ->label(__('admin.accommodations.owner'))
                    ->options(fn (): array => User::query()->role('owner')->orderBy('name')->pluck('name', 'id')->all())
                    ->visible(fn (): bool => static::isAdminPanel()),
                SelectFilter::make('city')
                    ->label(__('admin.accommodations.city'))
                    ->options(fn (): array => Accommodation::query()
                        ->whereNotNull('city')
                        ->orderBy('city')
                        ->pluck('city', 'city')
                        ->all()),
                SelectFilter::make('theme_key')
                    ->label(__('admin.accommodations.theme'))
                    ->options(fn (): array => ThemePreset::query()->orderBy('name')->pluck('name', 'key')->all()),
                Filter::make('demo_only')
                    ->label(__('admin.accommodations.demo_only'))
                    ->query(fn (Builder $query): Builder => $query->where('slug', 'villa-lavanda-tara'))
                    ->visible(fn (): bool => static::isAdminPanel()),
            ])
            ->recordActions([
                ViewAction::make()->label(__('admin.accommodations.view')),
                EditAction::make()->label(__('admin.accommodations.edit')),
                Action::make('preview')->label(__('admin.accommodations.preview_site'))->icon(Heroicon::OutlinedEye)->url(fn (Accommodation $record): string => $record->previewUrl(), shouldOpenInNewTab: true),
                Action::make('build_site')
                    ->label(fn (): string => static::isOwnerPanel() ? __('admin.accommodations.build_site') : __('admin.accommodations.publish'))
                    ->icon(Heroicon::OutlinedArrowUpCircle)
                    ->color('success')
                    ->visible(fn (Accommodation $record): bool => $record->status !== AccommodationStatus::Published)
                    ->disabled(fn (): bool => static::isOwnerPanel() && ! (auth()->user()?->canPublishSites() ?? false))
                    ->tooltip(fn (): ?string => static::isOwnerPanel() && ! (auth()->user()?->canPublishSites() ?? false)
                        ? __('admin.accommodations.publish_locked_tooltip')
                        : null)
                    ->action(function (Accommodation $record): void {
                        if (static::isOwnerPanel() && ! (auth()->user()?->canPublishSites() ?? false)) {
                            Notification::make()
                                ->title(__('admin.accommodations.publish_locked_title'))
                                ->body(__('admin.accommodations.publish_locked_body'))
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
                    ->label(fn (): string => static::isOwnerPanel() ? __('admin.accommodations.hide_site') : __('admin.accommodations.unpublish'))
                    ->icon(Heroicon::OutlinedArrowDownCircle)
                    ->color('gray')
                    ->visible(fn (Accommodation $record): bool => $record->status === AccommodationStatus::Published)
                    ->action(fn (Accommodation $record) => $record->update([
                        'status' => AccommodationStatus::Draft,
                        'published_at' => null,
                    ])),
                DeleteAction::make()->label(__('admin.accommodations.delete')),
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
