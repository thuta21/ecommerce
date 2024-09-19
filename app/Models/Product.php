<?php

namespace App\Models;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'description',
        'image',
        'price',
        'stock',
        'is_active',
        'is_featured',
        'in_stock',
        'is_sale'
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function orderItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function getForm()
    {
        return [
            Group::make()->schema([
                Section::make('Product Information')->schema([
                    TextInput::make('name')->maxLength(255)
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
                        ->unique(Product::class, 'slug', ignoreRecord: true),
                    MarkdownEditor::make('description')->columnSpanFull()->fileAttachmentsDirectory('products')->required(),
                ])->columns(2),

                Section::make('Images')->schema([
                    FileUpload::make('images')
                        ->image()
                        ->multiple()
                        ->maxFiles(5)
                        ->directory('products')
                        ->reorderable()
                        ->required(),
                ])
            ])->columns(2),
            Group::make()->schema([
                Section::make('price')->schema([
                    TextInput::make('price')
                        ->numeric()
                        ->prefix('USD')
                        ->required()
                ]),

                Section::make('Associations')->schema([
                    Select::make('category_id')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->relationship('category', 'name'),

                    Select::make('brand_id')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->relationship('brand', 'name'),
                ]),

                Section::make('Status')->schema([
                    Toggle::make('in_stock')->required()->default(true),
                    Toggle::make('is_active')->required()->default(true),
                    Toggle::make('is_featured')->required(),
                    Toggle::make('is_sale')->required()
                ])
            ])->columnSpan(1)
        ];
    }
}
