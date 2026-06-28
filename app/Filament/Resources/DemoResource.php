<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DemoResource\Pages;
use App\Models\Demo;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DemoResource extends Resource
{
    protected static ?string $model = Demo::class;

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';

    protected static ?string $navigationLabel = 'Demos';

    protected static ?string $modelLabel = 'demo';

    protected static ?string $pluralModelLabel = 'demos';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Demo')
                    ->schema([
                        Forms\Components\Select::make('lead_id')
                            ->label('Cliente')
                            ->relationship('lead', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->placeholder('Demo web estética, demo fisio...')
                            ->maxLength(255),

                        Forms\Components\DateTimePicker::make('due_at')
                            ->label('Fecha límite')
                            ->required()
                            ->seconds(false)
                            ->default(now()->addDay()->setTime(12, 0)),

                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options(Demo::statusOptions())
                            ->default(Demo::STATUS_PENDING)
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('priority')
                            ->label('Prioridad')
                            ->options(Demo::priorityOptions())
                            ->default('medium')
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('demo_url')
                            ->label('URL demo')
                            ->url()
                            ->maxLength(255),

                        Forms\Components\Toggle::make('video_sent')
                            ->label('Vídeo enviado')
                            ->inline(false),

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
            ->defaultSort('due_at')
            ->columns([
                Tables\Columns\TextColumn::make('lead.name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->placeholder('Sin título'),

                Tables\Columns\TextColumn::make('due_at')
                    ->label('Fecha límite')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->color(fn ($state): string => $state && $state->isPast() ? 'danger' : 'gray'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Demo::statusOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        Demo::STATUS_PENDING => 'warning',
                        Demo::STATUS_IN_PROGRESS => 'info',
                        Demo::STATUS_DONE => 'primary',
                        Demo::STATUS_SENT => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridad')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Demo::priorityOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'high' => 'danger',
                        'medium' => 'warning',
                        'low' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\IconColumn::make('video_sent')
                    ->label('Vídeo')
                    ->boolean(),

                Tables\Columns\TextColumn::make('demo_url')
                    ->label('URL')
                    ->url(fn (?string $state): ?string => $state)
                    ->openUrlInNewTab()
                    ->limit(30)
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(Demo::statusOptions()),

                Tables\Filters\SelectFilter::make('priority')
                    ->label('Prioridad')
                    ->options(Demo::priorityOptions()),

                Tables\Filters\Filter::make('pending_due')
                    ->label('Pendientes vencidas')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereIn('status', [Demo::STATUS_PENDING, Demo::STATUS_IN_PROGRESS])
                        ->where('due_at', '<=', now())),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_sent')
                    ->label('Marcar enviada')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn (Demo $record): bool => $record->status !== Demo::STATUS_SENT)
                    ->action(fn (Demo $record) => $record->update(['status' => Demo::STATUS_SENT])),

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
            'index' => Pages\ListDemos::route('/'),
            'create' => Pages\CreateDemo::route('/create'),
            'edit' => Pages\EditDemo::route('/{record}/edit'),
        ];
    }
}
