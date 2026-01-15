<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeriodResource\Pages;
use App\Models\Period;
use App\Models\SchoolYear;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PeriodResource extends Resource
{
    protected static ?string $model = Period::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Configuración Académica';

    protected static ?string $modelLabel = 'Periodo';

    protected static ?string $pluralModelLabel = 'Periodos';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Periodo')
                    ->schema([
                        Forms\Components\Select::make('school_year_id')
                            ->label('Año Escolar')
                            ->relationship('schoolYear', 'name')
                            ->required()
                            ->default(fn () => SchoolYear::where('is_active', true)->first()?->id)
                            ->preload()
                            ->searchable(),

                        Forms\Components\Select::make('number')
                            ->label('Número de Periodo')
                            ->options([
                                1 => 'Periodo 1',
                                2 => 'Periodo 2',
                                3 => 'Periodo 3',
                                4 => 'Periodo 4',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Primer Periodo'),

                        Forms\Components\DatePicker::make('start_date')
                            ->label('Fecha de Inicio'),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('Fecha de Fin')
                            ->afterOrEqual('start_date'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Periodo Activo')
                            ->default(false),

                        Forms\Components\Toggle::make('is_finalized')
                            ->label('Periodo Finalizado')
                            ->helperText('Si está finalizado, no se pueden modificar las notas.')
                            ->default(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('schoolYear.name')
                    ->label('Año Escolar')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('number')
                    ->label('N°')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('Sin definir'),

                Tables\Columns\TextColumn::make('end_date')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('Sin definir'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\IconColumn::make('is_finalized')
                    ->label('Finalizado')
                    ->boolean()
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('danger')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('school_year_id')
                    ->label('Año Escolar')
                    ->relationship('schoolYear', 'name')
                    ->preload()
                    ->default(fn () => SchoolYear::where('is_active', true)->first()?->id),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->placeholder('Todos')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),
            ])
            ->actions([
                Tables\Actions\Action::make('finalize')
                    ->label('Finalizar')
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Finalizar Periodo')
                    ->modalDescription('¿Está seguro de finalizar este periodo? Una vez finalizado, los profesores NO podrán modificar las calificaciones.')
                    ->modalSubmitActionLabel('Sí, finalizar')
                    ->visible(fn ($record): bool => !$record->is_finalized)
                    ->action(function ($record): void {
                        $record->update(['is_finalized' => true, 'is_active' => false]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Periodo finalizado')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('reopen')
                    ->label('Reabrir')
                    ->icon('heroicon-o-lock-open')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Reabrir Periodo')
                    ->modalDescription('¿Está seguro de reabrir este periodo? Los profesores podrán modificar las calificaciones nuevamente.')
                    ->modalSubmitActionLabel('Sí, reabrir')
                    ->visible(fn ($record): bool => $record->is_finalized)
                    ->action(function ($record): void {
                        $record->update(['is_finalized' => false]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Periodo reabierto')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('generateReports')
                    ->label('Generar Boletines')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->url(fn ($record) => route('filament.admin.pages.generate-report-cards', [
                        'school_year_id' => $record->school_year_id,
                        'period_id' => $record->id,
                    ])),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('school_year_id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeriods::route('/'),
            'create' => Pages\CreatePeriod::route('/create'),
            'edit' => Pages\EditPeriod::route('/{record}/edit'),
        ];
    }
}
