<?php

namespace App\Filament\Resources;

use App\Enums\InquiryStatus;
use App\Filament\Concerns\InteractsWithPanelContext;
use App\Filament\Resources\AccommodationInquiryResource\Pages;
use App\Models\Accommodation;
use App\Models\AccommodationInquiry;
use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AccommodationInquiryResource extends Resource
{
    use InteractsWithPanelContext;

    protected static ?string $model = AccommodationInquiry::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxStack;

    protected static ?string $recordTitleAttribute = 'guest_name';

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('admin.nav.communication');
    }

    public static function getModelLabel(): string
    {
        return __('admin.inquiries.single');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.inquiries.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.inquiries.details'))
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('guest_name')->label(__('admin.inquiries.guest'))->disabled(),
                            TextInput::make('guest_email')->label(__('admin.inquiries.email'))->disabled(),
                            TextInput::make('guest_phone')->label(__('admin.inquiries.phone'))->disabled(),
                            TextInput::make('guests_count')->label(__('admin.inquiries.guests_count'))->disabled(),
                            DatePicker::make('check_in')->label(__('admin.inquiries.check_in'))->disabled(),
                            DatePicker::make('check_out')->label(__('admin.inquiries.check_out'))->disabled(),
                            TextInput::make('source')->label(__('admin.inquiries.source'))->disabled()->columnSpanFull(),
                            Textarea::make('message')->label(__('admin.inquiries.message'))->rows(7)->disabled()->columnSpanFull(),
                            Select::make('status')->label(__('admin.inquiries.status'))->options(InquiryStatus::options())->required()->native(false)->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['accommodation.user', 'user']))
            ->columns([
                TextColumn::make('accommodation.title')->label(__('admin.inquiries.accommodation'))->searchable(),
                TextColumn::make('accommodation.user.name')
                    ->label(__('admin.inquiries.owner'))
                    ->searchable()
                    ->visible(fn (): bool => static::isAdminPanel()),
                TextColumn::make('guest_name')->label(__('admin.inquiries.guest'))->searchable(),
                TextColumn::make('guest_phone')->label(__('admin.inquiries.phone')),
                TextColumn::make('guest_email')->label(__('admin.inquiries.email'))->searchable(),
                TextColumn::make('status')->label(__('admin.inquiries.status'))->formatStateUsing(fn ($state) => $state?->label() ?? $state)->badge(),
                TextColumn::make('source')->label(__('admin.inquiries.source'))->badge(),
                TextColumn::make('created_at')->label(__('admin.inquiries.sent'))->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label(__('admin.inquiries.status'))->options(InquiryStatus::options()),
                SelectFilter::make('user_id')
                    ->label(__('admin.inquiries.owner'))
                    ->options(fn (): array => User::query()->role('owner')->orderBy('name')->pluck('name', 'id')->all())
                    ->visible(fn (): bool => static::isAdminPanel()),
                SelectFilter::make('accommodation_id')
                    ->label(__('admin.inquiries.accommodation'))
                    ->options(fn (): array => Accommodation::query()->orderBy('title')->pluck('title', 'id')->all())
                    ->searchable()
                    ->preload(),
                SelectFilter::make('source')
                    ->label(__('admin.inquiries.source'))
                    ->options([
                        'website' => 'Website',
                    ]),
            ])
            ->recordActions([
                EditAction::make()->label(__('admin.inquiries.review_status')),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('accommodation');
        $user = auth()->user();

        if ($user && static::isOwnerPanel() && ! $user->isSuperAdmin()) {
            $query->whereHas('accommodation', fn (Builder $builder) => $builder->where('user_id', $user->id));
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccommodationInquiries::route('/'),
            'edit' => Pages\EditAccommodationInquiry::route('/{record}/edit'),
        ];
    }
}
