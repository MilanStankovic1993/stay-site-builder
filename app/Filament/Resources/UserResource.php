<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\InteractsWithPanelContext;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    use InteractsWithPanelContext;

    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

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
        return __('admin.nav.administration');
    }

    public static function getModelLabel(): string
    {
        return __('admin.users.single');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.users.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('admin.users.account_section'))
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')->label(__('admin.users.name'))->required()->maxLength(255),
                        TextInput::make('email')->label(__('admin.users.email'))->email()->required()->unique(ignoreRecord: true)->maxLength(255),
                        TextInput::make('password')
                            ->label(__('admin.users.password'))
                            ->password()
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->columnSpanFull(),
                        Select::make('roles')
                            ->label(__('admin.users.roles'))
                            ->relationship('roles', 'name')
                            ->options(fn (): array => Role::query()->pluck('name', 'name')->all())
                            ->multiple()
                            ->preload()
                            ->required(),
                        Toggle::make('is_active')
                            ->label(__('admin.users.active'))
                            ->default(false)
                            ->helperText(__('admin.users.active_help'))
                            ->inline(false),
                        Toggle::make('can_publish_sites')
                            ->label(__('admin.users.publish'))
                            ->helperText(__('admin.users.publish_help'))
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
                    ->label(__('admin.users.name_short'))
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn (string $state, User $record): string => $record->isDemoAccount() ? $state.' [Demo ACC]' : $state),
                TextColumn::make('email')->label(__('admin.users.email'))->searchable(),
                TextColumn::make('roles.name')->label(__('admin.users.roles'))->badge(),
                IconColumn::make('is_active')->label(__('admin.users.active_yes'))->boolean(),
                IconColumn::make('can_publish_sites')->label(__('admin.users.publish'))->boolean(),
                TextColumn::make('created_at')->label(__('admin.users.created_at'))->dateTime('d.m.Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label(__('admin.users.status'))
                    ->options([
                        '1' => __('admin.users.active_yes'),
                        '0' => __('admin.users.active_no'),
                    ]),
                SelectFilter::make('can_publish_sites')
                    ->label(__('admin.users.publish'))
                    ->options([
                        '1' => __('admin.users.approved'),
                        '0' => __('admin.users.not_approved'),
                    ]),
                SelectFilter::make('roles')
                    ->label(__('admin.users.role'))
                    ->relationship('roles', 'name')
                    ->options(fn (): array => Role::query()->orderBy('name')->pluck('name', 'name')->all())
                    ->multiple()
                    ->preload(),
                SelectFilter::make('demo_account')
                    ->label(__('admin.users.demo'))
                    ->options([
                        '1' => __('admin.users.demo_only'),
                        '0' => __('admin.users.demo_without'),
                    ])
                    ->query(function ($query, array $data) {
                        return match ($data['value'] ?? null) {
                            '1' => $query->where('email', 'owner@example.com'),
                            '0' => $query->where('email', '!=', 'owner@example.com'),
                            default => $query,
                        };
                    }),
            ])
            ->recordActions([
                Action::make('toggle_active')
                    ->label(fn (User $record): string => $record->is_active ? __('admin.users.deactivate') : __('admin.users.activate'))
                    ->icon(fn (User $record): string => $record->is_active ? Heroicon::OutlinedNoSymbol : Heroicon::OutlinedCheckCircle)
                    ->color(fn (User $record): string => $record->is_active ? 'gray' : 'success')
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        $record->update([
                            'is_active' => ! $record->is_active,
                        ]);

                        Notification::make()
                            ->title($record->is_active ? __('admin.users.activation_on') : __('admin.users.activation_off'))
                            ->success()
                            ->send();
                    }),
                Action::make('toggle_publish_access')
                    ->label(fn (User $record): string => $record->can_publish_sites ? __('admin.users.revoke_publish') : __('admin.users.approve_publish'))
                    ->icon(fn (User $record): string => $record->can_publish_sites ? Heroicon::OutlinedLockClosed : Heroicon::OutlinedGlobeAlt)
                    ->color(fn (User $record): string => $record->can_publish_sites ? 'gray' : 'success')
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        $record->update([
                            'can_publish_sites' => ! $record->can_publish_sites,
                        ]);

                        Notification::make()
                            ->title($record->can_publish_sites ? __('admin.users.publish_on') : __('admin.users.publish_off'))
                            ->success()
                            ->send();
                    }),
                EditAction::make()->label(__('admin.users.edit')),
                DeleteAction::make()->label(__('admin.users.delete')),
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
