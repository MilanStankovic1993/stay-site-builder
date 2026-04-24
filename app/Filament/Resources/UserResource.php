<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\InteractsWithPanelContext;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    use InteractsWithPanelContext;

    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\UnitEnum|null $navigationGroup = 'Administracija';

    protected static ?string $modelLabel = 'Korisnik';

    protected static ?string $pluralModelLabel = 'Korisnici';

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
        return $schema->components([
            Section::make('Korisnicki nalog')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')->label('Ime i prezime')->required()->maxLength(255),
                        TextInput::make('email')->label('Email')->email()->required()->unique(ignoreRecord: true)->maxLength(255),
                        TextInput::make('password')
                            ->label('Lozinka')
                            ->password()
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->columnSpanFull(),
                        Select::make('roles')
                            ->label('Uloge')
                            ->relationship('roles', 'name')
                            ->options(fn (): array => Role::query()->pluck('name', 'name')->all())
                            ->multiple()
                            ->preload()
                            ->required(),
                        Toggle::make('is_active')->label('Aktivan nalog')->default(true)->inline(false),
                        Toggle::make('can_publish_sites')
                            ->label('Moze da objavi sajt')
                            ->helperText('Ukljuciti tek nakon odobrenja ili uplate.')
                            ->default(false)
                            ->inline(false),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Ime')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn (string $state, User $record): string => $record->isDemoAccount() ? $state.' [Demo ACC]' : $state),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('roles.name')->label('Uloge')->badge(),
                IconColumn::make('is_active')->label('Aktivan')->boolean(),
                IconColumn::make('can_publish_sites')->label('Objava sajta')->boolean(),
                TextColumn::make('created_at')->label('Kreiran')->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->recordActions([
                EditAction::make()->label('Izmeni'),
                DeleteAction::make()->label('Obrisi'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
