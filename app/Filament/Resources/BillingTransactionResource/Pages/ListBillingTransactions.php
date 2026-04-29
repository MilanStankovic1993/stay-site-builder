<?php

namespace App\Filament\Resources\BillingTransactionResource\Pages;

use App\Filament\Resources\BillingTransactionResource;
use Filament\Resources\Pages\ListRecords;

class ListBillingTransactions extends ListRecords
{
    protected static string $resource = BillingTransactionResource::class;
}
