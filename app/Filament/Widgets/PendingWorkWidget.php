<?php

namespace App\Filament\Widgets;

use App\Models\Demo;
use App\Models\Lead;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class PendingWorkWidget extends BaseWidget
{
    protected static ?string $heading = 'Demos pendientes más urgentes';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Demo::query()
                    ->with('lead')
                    ->whereIn('status', [Demo::STATUS_PENDING, Demo::STATUS_IN_PROGRESS])
                    ->orderBy('due_at')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('lead.name')
                    ->label('Cliente')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('title')
                    ->label('Demo')
                    ->placeholder('Sin título'),

                Tables\Columns\TextColumn::make('due_at')
                    ->label('Fecha límite')
                    ->dateTime('d/m/Y H:i')
                    ->color(fn ($state): string => $state && $state->isPast() ? 'danger' : 'gray'),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridad')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'high' => 'danger',
                        'medium' => 'warning',
                        'low' => 'gray',
                        default => 'gray',
                    }),
            ]);
    }
}
