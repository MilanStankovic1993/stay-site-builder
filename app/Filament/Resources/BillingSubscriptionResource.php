<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\InteractsWithPanelContext;
use App\Filament\Resources\BillingSubscriptionResource\Pages;
use App\Models\User;
use App\Support\AdminBillingResourceSupport;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Paddle\Subscription;

class BillingSubscriptionResource extends Resource
{
    use InteractsWithPanelContext;

    protected static ?string $model = Subscription::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

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
        return __('admin.nav.platform');
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function getModelLabel(): string
    {
        return __('admin.billing_resources.subscription_single');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.billing_resources.subscription_plural');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['billable', 'items']))
            ->columns([
                TextColumn::make('billable.name')
                    ->label(__('admin.billing_resources.owner'))
                    ->searchable(),
                TextColumn::make('billable.email')
                    ->label(__('admin.billing_resources.email'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(__('admin.billing_resources.subscription_type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === User::PUBLISHING_SUBSCRIPTION_TYPE
                        ? __('admin.billing_resources.publishing_type')
                        : $state),
                TextColumn::make('status')
                    ->label(__('admin.billing_resources.status'))
                    ->badge()
                    ->color(fn (string $state): string => AdminBillingResourceSupport::subscriptionStatusColor($state)),
                TextColumn::make('resolved_package')
                    ->label(__('admin.billing_resources.package'))
                    ->state(fn (Subscription $record): string => $record->billable?->currentPublishingPlanLabel() ?? '-')
                    ->badge(),
                TextColumn::make('slot_usage')
                    ->label(__('admin.billing_resources.package_slots'))
                    ->state(fn (Subscription $record): string => $record->billable?->publishingUsageLabel() ?? '0 / 0'),
                TextColumn::make('items.price_id')
                    ->label(__('admin.billing_resources.price_id'))
                    ->listWithLineBreaks()
                    ->limitList(2),
                TextColumn::make('created_at')
                    ->label(__('admin.billing_resources.started_at'))
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label(__('admin.billing_resources.ends_at'))
                    ->placeholder('-')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('admin.billing_resources.status'))
                    ->options(AdminBillingResourceSupport::subscriptionStatusOptions()),
                SelectFilter::make('type')
                    ->label(__('admin.billing_resources.subscription_type'))
                    ->options([
                        User::PUBLISHING_SUBSCRIPTION_TYPE => __('admin.billing_resources.publishing_type'),
                    ]),
                SelectFilter::make('billable_id')
                    ->label(__('admin.billing_resources.owner'))
                    ->options(fn (): array => AdminBillingResourceSupport::ownerOptions())
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                Action::make('open_user')
                    ->label(__('admin.billing_resources.open_user'))
                    ->icon(Heroicon::OutlinedUser)
                    ->url(fn (Subscription $record): string => AdminBillingResourceSupport::openUserUrl($record->billable_id)),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('billable_type', User::class);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBillingSubscriptions::route('/'),
        ];
    }
}
