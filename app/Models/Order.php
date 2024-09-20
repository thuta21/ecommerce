<?php

namespace App\Models;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Group;
use Illuminate\Support\Number;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total',
        'payment_method',
        'payment_status',
        'status',
        'shipping_address',
        'currency',
        'shipping_fee',
        'shipping_method',
        'note'
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function address(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Address::class);
    }

    public static function getForm(): array
    {
        return [
            Group::make()->schema([
                Section::make('Order Information')->schema([
                    Select::make('user_id')
                        ->label('Customer')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Select::make('payment_method')
                        ->options([
                            'stripe' => 'Stripe',
                            'cod' => 'Cash on Delivery',
                        ])->required(),

                    Select::make('payment_status')
                        ->options([
                            'pending' => 'Pending',
                            'paid' => 'Paid',
                            'failed' => 'Failed',
                        ])
                        ->default('pending')
                        ->required(),

                    ToggleButtons::make('status')
                        ->options([
                            'pending' => 'Pending',
                            'processing' => 'Processing',
                            'shipped' => 'Shipped',
                            'delivered' => 'Delivered',
                            'canceled' => 'Canceled'
                        ])
                        ->inline()
                        ->colors([
                            'pending' => 'info',
                            'processing' => 'warning',
                            'shipped' => 'success',
                            'delivered' => 'success',
                            'canceled' => 'danger'
                        ])
                        ->icons([
                            'pending' => 'heroicon-m-clock',
                            'processing' => 'heroicon-m-cog',
                            'shipped' => 'heroicon-m-truck',
                            'delivered' => 'heroicon-m-check',
                            'canceled' => 'heroicon-o-x-circle'
                        ])
                        ->default('pending'),

                    Select::make('currency')
                        ->options([
                            'usd' => 'USD',
                            'eur' => 'EUR',
                            'MMK' => 'MMK',
                        ])
                        ->default('usd')
                        ->required(),

                    Select::make('shipping_method')
                        ->options([
                            'standard' => 'Standard',
                            'express' => 'Express',
                        ]),

                    Textarea::make('notes')
                        ->columnSpanFull()
                ])->columns(2),
                Section::make('Order Items')->schema([
                    Repeater::make('orderItems')
                        ->relationship()
                        ->schema([
                            Select::make('product_id')
                                ->relationship('product', 'name')
                                ->searchable()
                                ->preload()
                                ->distinct()
                                ->reactive()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->afterStateUpdated(fn($state, Set $set) => $set('unit_amount',
                                    Product::find($state)?->price ?? 0))
                                ->afterStateUpdated(fn($state, Set $set) => $set('total_amount',
                                    Product::find($state)?->price ?? 0))
                                ->required()
                                ->columnSpan(4),

                            TextInput::make('quantity')
                                ->numeric()
                                ->minValue(1)
                                ->default(1)
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(fn($state, Set $set, Get $get) => $set('total_amount', $state * $get('unit_amount')))
                                ->columnSpan(2),

                            TextInput::make('unit_amount')
                                ->numeric()
                                ->disabled()
                                ->required()
                                ->dehydrated()
                                ->columnSpan(3),

                            TextInput::make('total_amount')
                                ->numeric()
                                ->required()
                                ->dehydrated()
                                ->columnSpan(3)
                        ])->columns(12),

                    Placeholder::make('grand_total_placeholder')
                    ->label('Grand Total')
                    ->content(function (Get $get, Set $set) {
                        $total = 0;
                        if (!$repeaters = $get('orderItems')) {
                            return $total;
                        }

                        foreach ($repeaters as $key => $repeater) {
                            $total += $get("orderItems.{$key}.total_amount");
                        }

                        $set('total', $total);
                        return Number::currency($total, $get('currency'));
                    }),

                    Hidden::make('total')->default(0)
                ])
            ])->columnSpanFull(),
        ];
    }
}
