<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }

    public static function getForm()
    {
        return [
            Section::make('Users Information')
                ->columns(2)
                ->schema([
                    TextInput::make('name')->required(),
                    TextInput::make('email')
                        ->label('Email Address')
                        ->email()
                        ->maxLength(255)
                        ->unique()
                        ->required(),
                    DateTimePicker::make('email_verified_at')
                        ->label('Email Verified At')
                        ->default(now()),
                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->dehydrated(fn($state) => filled($state))
                        ->required(fn(Page $livewire) => $livewire instanceof CreateRecord),
                ]),
        ];
    }
}
