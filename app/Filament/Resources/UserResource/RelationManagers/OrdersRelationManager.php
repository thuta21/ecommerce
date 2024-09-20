<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Order Id'),
                Tables\Columns\TextColumn::make('total')->numeric()->money('USD'),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn (string $state): string => match($state) {
                    'pending' => 'info',
                    'processing' => 'warning',
                    'shipped' => 'success',
                    'deliver' => 'success',
                    'canceled' => 'danger',
                })
                ->icon(fn(string $state): string => match ($state) {
                    'pending' => 'heroicon-o-sparkles',
                    'processing' => 'heroicon-m-arrow-path',
                    'shipped' => 'heroicon-o-truck',
                    'deliver' => 'heroicon-m-check-badge',
                    'canceled' => 'heroicon-m-x-circle',
                })->sortable(),
                Tables\Columns\TextColumn::make('payment_method')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('payment_status')->sortable()->badge()->searchable(),
                Tables\Columns\TextColumn::make('created_at')->label('Order Date')->dateTime()

            ])
            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->actions([
                Tables\Actions\Action::make('View Order')
                ->url(fn (Order $record):string => OrderResource::getUrl('view', ['record' => $record]))
                ->color('info')
                ->icon('heroicon-o-eye'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
