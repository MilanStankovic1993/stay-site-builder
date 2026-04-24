<?php

namespace App\Filament\Resources;

use App\Enums\InquiryStatus;
use App\Filament\Concerns\InteractsWithPanelContext;
use App\Filament\Resources\AccommodationInquiryResource\Pages;
use App\Models\AccommodationInquiry;
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

    protected static string|\UnitEnum|null $navigationGroup = 'Komunikacija';

    protected static ?string $modelLabel = 'Upit';

    protected static ?string $pluralModelLabel = 'Upiti';

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalji upita')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('guest_name')->label('Gost')->disabled(),
                            TextInput::make('guest_email')->label('Email')->disabled(),
                            TextInput::make('guest_phone')->label('Telefon')->disabled(),
                            TextInput::make('guests_count')->label('Broj gostiju')->disabled(),
                            DatePicker::make('check_in')->label('Dolazak')->disabled(),
                            DatePicker::make('check_out')->label('Odlazak')->disabled(),
                            TextInput::make('source')->label('Izvor')->disabled()->columnSpanFull(),
                            Textarea::make('message')->label('Poruka')->rows(7)->disabled()->columnSpanFull(),
                            Select::make('status')->label('Status')->options(InquiryStatus::options())->required()->native(false)->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('accommodation'))
            ->columns([
                TextColumn::make('accommodation.title')->label('Smestaj')->searchable(),
                TextColumn::make('guest_name')->label('Gost')->searchable(),
                TextColumn::make('guest_phone')->label('Telefon'),
                TextColumn::make('guest_email')->label('Email')->searchable(),
                TextColumn::make('status')->label('Status')->formatStateUsing(fn ($state) => $state?->label() ?? $state)->badge(),
                TextColumn::make('created_at')->label('Poslato')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->label('Status')->options(InquiryStatus::options()),
            ])
            ->recordActions([
                EditAction::make()->label('Pregled / status'),
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
