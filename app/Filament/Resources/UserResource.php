<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\InteractsWithPanelContext;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Support\SiteBillingCatalog;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Actions as SchemaActions;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Laravel\Paddle\Subscription;
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
            Tabs::make('user_edit_tabs')
                ->persistTabInQueryString()
                ->tabs([
                    Tab::make(__('admin.users.account_tab'))
                        ->schema([
                            static::accountSection(),
                        ]),
                    Tab::make(__('admin.users.billing_tab'))
                        ->visible(fn (?User $record): bool => $record !== null)
                        ->schema([
                            static::billingManagementSection(),
                            static::billingSummarySection(),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(static::tableColumns())
            ->filters([
                SelectFilter::make('is_active')
                    ->label(__('admin.users.status'))
                    ->options([
                        '1' => __('admin.users.active_yes'),
                        '0' => __('admin.users.active_no'),
                    ]),
                SelectFilter::make('publishing_access')
                    ->label(__('admin.users.billing_status'))
                    ->options([
                        User::PUBLISHING_PLAN_SOURCE_SUBSCRIPTION => __('admin.users.billing_paid'),
                        User::PUBLISHING_PLAN_SOURCE_MANUAL_PLAN => __('admin.users.billing_manual_plan'),
                        User::PUBLISHING_PLAN_SOURCE_LOCKED => __('admin.users.billing_locked'),
                        User::PUBLISHING_PLAN_SOURCE_ADMIN => __('admin.users.billing_admin'),
                    ])
                    ->query(function ($query, array $data) {
                        return match ($data['value'] ?? null) {
                            User::PUBLISHING_PLAN_SOURCE_SUBSCRIPTION => $query->whereHas('subscriptions', static::publishingSubscriptionScope()),
                            User::PUBLISHING_PLAN_SOURCE_MANUAL_PLAN => $query
                                ->whereNotNull('manual_billing_plan_key')
                                ->whereDoesntHave('subscriptions', static::publishingSubscriptionScope())
                                ->whereDoesntHave('roles', fn ($roleQuery) => $roleQuery->where('name', 'super_admin')),
                            User::PUBLISHING_PLAN_SOURCE_LOCKED => $query
                                ->whereNull('manual_billing_plan_key')
                                ->whereDoesntHave('subscriptions', static::publishingSubscriptionScope())
                                ->whereDoesntHave('roles', fn ($roleQuery) => $roleQuery->where('name', 'super_admin')),
                            User::PUBLISHING_PLAN_SOURCE_ADMIN => $query->whereHas('roles', fn ($roleQuery) => $roleQuery->where('name', 'super_admin')),
                            default => $query,
                        };
                    }),
                SelectFilter::make('manual_billing_plan_key')
                    ->label(__('admin.users.current_package'))
                    ->options(fn (): array => app(SiteBillingCatalog::class)->options()),
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
                static::packageActionGroup(),
                static::toggleActiveAction(),
                static::editRecordAction(),
                static::deleteRecordAction(),
            ]);
    }

    public static function packageActions(): array
    {
        $actions = collect(app(SiteBillingCatalog::class)->all())
            ->map(function (array $plan, string $key): Action {
                return Action::make("assign_package_{$key}")
                    ->label((string) ($plan['name'] ?? $key))
                    ->icon(Heroicon::OutlinedCreditCard)
                    ->action(function (User $record) use ($key): void {
                        $record->update([
                            'is_active' => true,
                            'manual_billing_plan_key' => $key,
                            'manual_billing_activated_at' => now(),
                        ]);

                        Notification::make()
                            ->title(__('admin.users.package_assigned', ['package' => $record->fresh()->currentPublishingPlanLabel()]))
                            ->success()
                            ->send();
                    });
            })
            ->values()
            ->all();

        $actions[] = Action::make('clear_manual_package')
            ->label(__('admin.users.clear_manual_package'))
            ->icon(Heroicon::OutlinedXMark)
            ->color('gray')
            ->requiresConfirmation()
            ->action(function (User $record): void {
                $record->update([
                    'manual_billing_plan_key' => null,
                    'manual_billing_activated_at' => null,
                ]);

                Notification::make()
                    ->title(__('admin.users.manual_package_cleared'))
                    ->success()
                    ->send();
            });

        return $actions;
    }

    public static function packageActionGroup(): ActionGroup
    {
        return ActionGroup::make(static::packageActions())
            ->label(__('admin.users.manage_package'))
            ->icon(Heroicon::OutlinedRocketLaunch)
            ->color('info')
            ->button();
    }

    public static function toggleActiveAction(): Action
    {
        return Action::make('toggle_active')
            ->label(fn (User $record): string => $record->is_active ? __('admin.users.deactivate') : __('admin.users.activate'))
            ->icon(fn (User $record) => $record->is_active ? Heroicon::OutlinedNoSymbol : Heroicon::OutlinedCheckCircle)
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
            });
    }

    public static function editRecordAction(): EditAction
    {
        return EditAction::make()->label(__('admin.users.edit'));
    }

    public static function deleteRecordAction(): DeleteAction
    {
        return DeleteAction::make()->label(__('admin.users.delete'));
    }

    protected static function accountSection(): Section
    {
        return Section::make(__('admin.users.account_section'))
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
                ]),
            ]);
    }

    protected static function billingManagementSection(): Section
    {
        return Section::make(__('admin.users.billing_management_section'))
            ->schema([
                Placeholder::make('billing_management_hint')
                    ->hiddenLabel()
                    ->content(__('admin.users.billing_management_hint')),
                SchemaActions::make([
                    static::packageActionGroup(),
                ]),
            ]);
    }

    protected static function billingSummarySection(): Section
    {
        return Section::make(__('admin.users.billing_summary_section'))
            ->schema([
                Placeholder::make('billing_summary_manage_hint')
                    ->hiddenLabel()
                    ->content(__('admin.users.billing_summary_manage_hint')),
                Grid::make(3)->schema([
                    static::billingSummaryPlaceholder('billing_summary_plan', __('admin.users.current_package'), fn (User $record): string => $record->currentPublishingPlanLabel()),
                    static::billingSummaryPlaceholder('billing_summary_source', __('admin.users.package_source'), fn (User $record): string => $record->publishingPlanSourceLabel()),
                    static::billingSummaryPlaceholder('billing_summary_access', __('admin.users.billing_status'), fn (User $record): string => $record->publishingAccessLabel()),
                    static::billingSummaryPlaceholder('billing_summary_slots', __('admin.users.package_slots'), fn (User $record): string => $record->publishingUsageLabel(), '0 / 0'),
                    static::billingSummaryPlaceholder('billing_summary_setup_fee', __('admin.users.setup_fee_status'), fn (User $record): string => $record->publishingSetupFeeStatusLabel()),
                    static::billingSummaryPlaceholder('billing_summary_latest_payment', __('admin.users.latest_payment'), fn (User $record): string => $record->latestPublishingTransactionLabel()),
                ]),
            ]);
    }

    protected static function billingSummaryPlaceholder(string $name, string $label, \Closure $resolver, ?string $fallback = null): Placeholder
    {
        return Placeholder::make($name)
            ->label($label)
            ->content(fn (?User $record): string => $record ? $resolver($record) : ($fallback ?? __('admin.users.billing_summary_empty')));
    }

    protected static function tableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label(__('admin.users.name_short'))
                ->searchable()
                ->sortable()
                ->formatStateUsing(fn (string $state, User $record): string => $record->isDemoAccount() ? $state.' [Demo ACC]' : $state),
            TextColumn::make('email')->label(__('admin.users.email'))->searchable(),
            TextColumn::make('roles.name')->label(__('admin.users.roles'))->badge(),
            IconColumn::make('is_active')->label(__('admin.users.active_yes'))->boolean(),
            TextColumn::make('current_package')
                ->label(__('admin.users.current_package'))
                ->state(fn (User $record): string => $record->currentPublishingPlanLabel())
                ->badge()
                ->color(fn (User $record): string => $record->publishingAccessColor()),
            TextColumn::make('package_source')
                ->label(__('admin.users.package_source'))
                ->state(fn (User $record): string => $record->publishingPlanSourceLabel())
                ->badge(),
            TextColumn::make('publishing_slots')
                ->label(__('admin.users.package_slots'))
                ->state(fn (User $record): string => $record->publishingUsageLabel()),
            TextColumn::make('setup_fee_status')
                ->label(__('admin.users.setup_fee_status'))
                ->state(fn (User $record): string => $record->publishingSetupFeeStatusLabel())
                ->badge()
                ->color(fn (User $record): string => $record->publishingSetupFeeStatusColor()),
            TextColumn::make('latest_payment')
                ->label(__('admin.users.latest_payment'))
                ->state(fn (User $record): string => $record->latestPublishingTransactionLabel())
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('publishing_access')
                ->label(__('admin.users.billing_status'))
                ->state(fn (User $record): string => $record->publishingAccessLabel())
                ->badge()
                ->color(fn (User $record): string => $record->publishingAccessColor()),
            TextColumn::make('created_at')->label(__('admin.users.created_at'))->dateTime('d.m.Y H:i')->sortable(),
        ];
    }

    protected static function publishingSubscriptionScope(): \Closure
    {
        return fn ($subscriptionQuery) => $subscriptionQuery
            ->where('type', User::PUBLISHING_SUBSCRIPTION_TYPE)
            ->where(function ($statusQuery): void {
                $statusQuery
                    ->where('status', Subscription::STATUS_ACTIVE)
                    ->orWhere('status', Subscription::STATUS_TRIALING);
            });
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
