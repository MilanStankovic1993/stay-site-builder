<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\InteractsWithPanelContext;
use App\Filament\Resources\BillingSubscriptionResource\Pages;
use App\Models\User;
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
                    ->color(fn (string $state): string => match ($state) {
                        Subscription::STATUS_ACTIVE, Subscription::STATUS_TRIALING => 'success',
                        Subscription::STATUS_PAST_DUE => 'warning',
                        Subscription::STATUS_CANCELED => 'danger',
                        Subscription::STATUS_PAUSED => 'gray',
                        default => 'gray',
                    }),
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
                    ->placeholder('—')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('admin.billing_resources.status'))
                    ->options([
                        Subscription::STATUS_ACTIVE => __('admin.billing_resources.status_active'),
                        Subscription::STATUS_TRIALING => __('admin.billing_resources.status_trialing'),
                        Subscription::STATUS_PAST_DUE => __('admin.billing_resources.status_past_due'),
                        Subscription::STATUS_PAUSED => __('admin.billing_resources.status_paused'),
                        Subscription::STATUS_CANCELED => __('admin.billing_resources.status_canceled'),
                    ]),
                SelectFilter::make('type')
                    ->label(__('admin.billing_resources.subscription_type'))
                    ->options([
                        User::PUBLISHING_SUBSCRIPTION_TYPE => __('admin.billing_resources.publishing_type'),
                    ]),
                SelectFilter::make('billable_id')
                    ->label(__('admin.billing_resources.owner'))
                    ->options(fn (): array => User::query()->role('owner')->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                Action::make('open_user')
                    ->label(__('admin.billing_resources.open_user'))
                    ->icon(Heroicon::OutlinedUser)
                    ->url(fn (Subscription $record): string => UserResource::getUrl('edit', ['record' => $record->billable_id], panel: 'admin')),
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
