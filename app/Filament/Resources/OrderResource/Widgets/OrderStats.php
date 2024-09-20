<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class OrderStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('New Orders', Order::query()->where('status', 'pending')->count())
                ->icon('heroicon-o-shopping-cart'),

            Stat::make('Order Processing', Order::query()->where('status', 'processing')->count())
                ->icon('heroicon-o-clipboard-document-check'),

            Stat::make('Orders Shipped', Order::query()->where('status', 'shipped')->count())
                ->icon('heroicon-o-truck'),

            Stat::make('Average Price', Number::currency(Order::query()->avg('total'), 'USD')),
        ];
    }
}
