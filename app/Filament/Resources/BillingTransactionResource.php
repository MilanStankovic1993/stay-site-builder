<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\InteractsWithPanelContext;
use App\Filament\Resources\BillingTransactionResource\Pages;
use App\Models\User;
use App\Support\AdminBillingResourceSupport;
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
                    ->placeholder('-'),
                TextColumn::make('resolved_package')
                    ->label(__('admin.billing_resources.package'))
                    ->state(fn (Transaction $record): string => $record->billable?->currentPublishingPlanLabel() ?? '-')
                    ->badge(),
                TextColumn::make('status')
                    ->label(__('admin.billing_resources.status'))
                    ->badge()
                    ->color(fn (string $state): string => AdminBillingResourceSupport::transactionStatusColor($state)),
                TextColumn::make('total')
                    ->label(__('admin.billing_resources.total'))
                    ->state(fn (Transaction $record): string => $record->total()),
                TextColumn::make('tax')
                    ->label(__('admin.billing_resources.tax'))
                    ->state(fn (Transaction $record): string => $record->tax()),
                TextColumn::make('subscription.status')
                    ->label(__('admin.billing_resources.subscription_status'))
                    ->badge()
                    ->placeholder('-'),
                TextColumn::make('billed_at')
                    ->label(__('admin.billing_resources.billed_at'))
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('admin.billing_resources.status'))
                    ->options(AdminBillingResourceSupport::transactionStatusOptions()),
                SelectFilter::make('billable_id')
                    ->label(__('admin.billing_resources.owner'))
                    ->options(fn (): array => AdminBillingResourceSupport::ownerOptions())
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
                    ->url(fn (Transaction $record): string => AdminBillingResourceSupport::openUserUrl($record->billable_id)),
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
