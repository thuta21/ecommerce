<?php

namespace App\Models;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'image', 'is_active'];

    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Product::class);
    }

    public static function getForm(): array
    {
        return [
            Section::make('Brand Information')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn(
                            string $operation,
                            $state,
                            Set $set
                        ) => $operation === 'create' ? $set('slug', Str::slug($state)) : null)
                        ->required(),

                    TextInput::make('slug')
                        ->maxLength(255)
                        ->disabled()
                        ->required()
                        ->dehydrated()
                        ->unique(Category::class, 'slug', ignoreRecord: true),

                    FileUpload::make('image')
                        ->image()
                        ->directory('categories')
                        ->required(),

                    Toggle::make('is_active')
                        ->default(true)
                        ->required(),
                ]),
        ];
    }

}
