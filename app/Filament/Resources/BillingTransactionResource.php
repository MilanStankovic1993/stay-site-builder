<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\InteractsWithPanelContext;
use App\Filament\Resources\BillingTransactionResource\Pages;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Paddle\Transaction;

class BillingTransactionResource extends Resource
{
    use InteractsWithPanelContext;

    protected static ?string $model = Transaction::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

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
        return 3;
    }

    public static function getModelLabel(): string
    {
        return __('admin.billing_resources.transaction_single');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin.billing_resources.transaction_plural');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['billable', 'subscription']))
            ->columns([
                TextColumn::make('billable.name')
                    ->label(__('admin.billing_resources.owner'))
                    ->searchable(),
                TextColumn::make('billable.email')
                    ->label(__('admin.billing_resources.email'))
                    ->searchable(),
                TextColumn::make('invoice_number')
                    ->label(__('admin.billing_resources.invoice'))
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->label(__('admin.billing_resources.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Transaction::STATUS_PAID, Transaction::STATUS_COMPLETED, Transaction::STATUS_BILLED => 'success',
                        Transaction::STATUS_READY, Transaction::STATUS_DRAFT => 'gray',
                        Transaction::STATUS_PAST_DUE => 'warning',
                        Transaction::STATUS_CANCELED => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('total')
                    ->label(__('admin.billing_resources.total'))
                    ->state(fn (Transaction $record): string => $record->total()),
                TextColumn::make('tax')
                    ->label(__('admin.billing_resources.tax'))
                    ->state(fn (Transaction $record): string => $record->tax()),
                TextColumn::make('subscription.status')
                    ->label(__('admin.billing_resources.subscription_status'))
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('billed_at')
                    ->label(__('admin.billing_resources.billed_at'))
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('admin.billing_resources.status'))
                    ->options([
                        Transaction::STATUS_COMPLETED => __('admin.billing_resources.transaction_completed'),
                        Transaction::STATUS_PAID => __('admin.billing_resources.transaction_paid'),
                        Transaction::STATUS_BILLED => __('admin.billing_resources.transaction_billed'),
                        Transaction::STATUS_PAST_DUE => __('admin.billing_resources.status_past_due'),
                        Transaction::STATUS_CANCELED => __('admin.billing_resources.status_canceled'),
                    ]),
                SelectFilter::make('billable_id')
                    ->label(__('admin.billing_resources.owner'))
                    ->options(fn (): array => User::query()->role('owner')->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload(),
                SelectFilter::make('currency')
                    ->label(__('admin.billing_resources.currency'))
                    ->options(fn (): array => Transaction::query()
                        ->where('billable_type', User::class)
                        ->distinct()
                        ->orderBy('currency')
                        ->pluck('currency', 'currency')
                        ->all()),
            ])
            ->recordActions([
                Action::make('open_user')
                    ->label(__('admin.billing_resources.open_user'))
                    ->icon(Heroicon::OutlinedUser)
                    ->url(fn (Transaction $record): string => UserResource::getUrl('edit', ['record' => $record->billable_id], panel: 'admin')),
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
            'index' => Pages\ListBillingTransactions::route('/'),
        ];
    }
}
