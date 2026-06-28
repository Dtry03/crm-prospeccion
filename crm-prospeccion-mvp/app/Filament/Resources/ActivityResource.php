<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use App\Models\Activity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationLabel = 'Actividades';

    protected static ?string $modelLabel = 'actividad';

    protected static ?string $pluralModelLabel = 'actividades';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Actividad comercial')
                    ->schema([
                        Forms\Components\Select::make('lead_id')
                            ->label('Cliente')
                            ->relationship('lead', 'name')
                            ->searchable()
                            ->preload()
                            ->native(false),

                        Forms\Components\Select::make('type')
                            ->label('Tipo')
                            ->options(Activity::typeOptions())
                            ->required()
                            ->native(false),

                        Forms\Components\DateTimePicker::make('occurred_at')
                            ->label('Fecha')
                            ->required()
                            ->seconds(false)
                            ->default(now()),

                        Forms\Components\Textarea::make('notes')
                            ->label('Notas')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('occurred_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('occurred_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Activity::typeOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        Activity::TYPE_CONTACT => 'gray',
                        Activity::TYPE_RESPONSE => 'info',
                        Activity::TYPE_DEMO_REQUESTED, Activity::TYPE_DEMO_SENT => 'warning',
                        Activity::TYPE_BUDGET_SENT => 'primary',
                        Activity::TYPE_WON => 'success',
                        Activity::TYPE_LOST => 'danger',
                        Activity::TYPE_FOLLOW_UP => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('lead.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Notas')
                    ->limit(50)
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options(Activity::typeOptions()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
            'create' => Pages\CreateActivity::route('/create'),
            'edit' => Pages\EditActivity::route('/{record}/edit'),
        ];
    }
}
