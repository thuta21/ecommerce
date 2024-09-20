<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrderResource\Widgets\OrderStats::class
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('All'),
            'pending' => Tab::make()->query(fn($query) => $query->where('status', 'pending')),
            'processing' => Tab::make()->query(fn($query) => $query->where('status', 'processing')),
            'shipped' => Tab::make()->query(fn($query) => $query->where('status', 'shipped')),
            'deliver' => Tab::make()->query(fn($query) => $query->where('status', 'deliver')),
            'canceled' => Tab::make()->query(fn($query) => $query->where('status', 'canceled')),
        ];
    }
}
