<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Models\Activity;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Clientes';

    protected static ?string $modelLabel = 'cliente';

    protected static ?string $pluralModelLabel = 'clientes';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Datos del cliente')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre / contacto')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('business_name')
                            ->label('Negocio')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('sector')
                            ->label('Sector')
                            ->placeholder('fisio, estética, restaurante...')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('city')
                            ->label('Ciudad')
                            ->maxLength(255),

                        Forms\Components\Select::make('source')
                            ->label('Origen')
                            ->options(Lead::sourceOptions())
                            ->default('instagram')
                            ->required()
                            ->native(false),

                        Forms\Components\Select::make('potential')
                            ->label('Potencial')
                            ->options(Lead::potentialOptions())
                            ->default('medium')
                            ->required()
                            ->native(false),

                        Forms\Components\TextInput::make('contact_url')
                            ->label('Instagram / web')
                            ->url()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Estado comercial')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options(Lead::statusOptions())
                            ->default(Lead::STATUS_CONTACTED)
                            ->required()
                            ->native(false),

                        Forms\Components\DatePicker::make('contacted_at')
                            ->label('Fecha de contacto')
                            ->default(now()),

                        Forms\Components\DateTimePicker::make('next_follow_up_at')
                            ->label('Próximo seguimiento')
                            ->seconds(false),

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
            ->defaultSort('updated_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Contacto')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('business_name')
                    ->label('Negocio')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sector')
                    ->label('Sector')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Lead::statusOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        Lead::STATUS_CONTACTED => 'gray',
                        Lead::STATUS_RESPONDED => 'info',
                        Lead::STATUS_DEMO_REQUESTED, Lead::STATUS_DEMO_PENDING => 'warning',
                        Lead::STATUS_DEMO_SENT, Lead::STATUS_BUDGET_SENT => 'primary',
                        Lead::STATUS_FOLLOW_UP => 'warning',
                        Lead::STATUS_WON => 'success',
                        Lead::STATUS_LOST, Lead::STATUS_NO_RESPONSE => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('potential')
                    ->label('Potencial')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Lead::potentialOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'high' => 'success',
                        'medium' => 'warning',
                        'low' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('contacted_at')
                    ->label('Contactado')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('next_follow_up_at')
                    ->label('Seguimiento')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->color(fn ($state): string => $state && $state->isPast() ? 'danger' : 'gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options(Lead::statusOptions()),

                Tables\Filters\SelectFilter::make('potential')
                    ->label('Potencial')
                    ->options(Lead::potentialOptions()),

                Tables\Filters\Filter::make('follow_up_due')
                    ->label('Seguimiento vencido')
                    ->query(fn (Builder $query): Builder => $query
                        ->whereNotNull('next_follow_up_at')
                        ->where('next_follow_up_at', '<=', now())),
            ])
            ->actions([
                Tables\Actions\Action::make('quick_response')
                    ->label('Respondió')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('info')
                    ->action(function (Lead $record): void {
                        $record->update(['status' => Lead::STATUS_RESPONDED]);

                        Notification::make()
                            ->title('Respuesta registrada')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('quick_demo')
                    ->label('Pidió demo')
                    ->icon('heroicon-o-computer-desktop')
                    ->color('warning')
                    ->action(function (Lead $record): void {
                        $record->update(['status' => Lead::STATUS_DEMO_REQUESTED]);

                        Notification::make()
                            ->title('Demo solicitada registrada')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('quick_budget')
                    ->label('Presupuesto')
                    ->icon('heroicon-o-banknotes')
                    ->color('primary')
                    ->action(function (Lead $record): void {
                        $record->update(['status' => Lead::STATUS_BUDGET_SENT]);

                        Notification::make()
                            ->title('Presupuesto registrado')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('quick_won')
                    ->label('Ganado')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Lead $record): void {
                        $record->update(['status' => Lead::STATUS_WON]);

                        Notification::make()
                            ->title('Venta cerrada')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }
}
