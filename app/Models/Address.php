<?php

namespace App\Models;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'name',
        'phone',
        'street',
        'city',
        'province',
        'country',
        'postal_code'
    ];

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                ->label('Name')
                ->placeholder('Enter name')
                ->maxLength(255)
                ->required(),

            TextInput::make('phone')
                ->tel()
                ->label('Phone')
                ->placeholder('Enter phone')
                ->maxLength(20)
                ->required(),

            TextInput::make('city')
                ->label('City')
                ->placeholder('Enter city')
                ->maxLength(255)
                ->required(),

            TextInput::make('province')
                ->label('Province')
                ->placeholder('Enter province')
                ->maxLength(255)
                ->required(),

            TextInput::make('country')
                ->label('Country')
                ->placeholder('Enter country')
                ->maxLength(255)
                ->required(),

            TextInput::make('postal_code')
                ->label('Postal Code')
                ->placeholder('Enter postal code')
                ->maxLength(10)
                ->numeric()
                ->required(),

            Textarea::make('street')
                ->label('Street')
                ->placeholder('Enter street')
                ->maxLength(255)
                ->required()
                ->columnSpanFull(),
        ];
    }
}
