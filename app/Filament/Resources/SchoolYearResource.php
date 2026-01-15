<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SchoolYearResource\Pages;
use App\Filament\Resources\SchoolYearResource\RelationManagers;
use App\Models\SchoolYear;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class SchoolYearResource extends Resource
{
    protected static ?string $model = SchoolYear::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Configuración Académica';

    protected static ?string $modelLabel = 'Año Escolar';

    protected static ?string $pluralModelLabel = 'Años Escolares';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Año Escolar')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Año')
                            ->placeholder('Ej: 2025')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Año Activo')
                            ->helperText('Solo puede haber un año escolar activo a la vez.')
                            ->default(false),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Año Escolar')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('periods_count')
                    ->label('Periodos')
                    ->counts('periods')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->placeholder('Todos')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),
            ])
            ->actions([
                Tables\Actions\Action::make('activate')
                    ->label('Activar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Activar Año Escolar')
                    ->modalDescription('¿Está seguro de activar este año escolar? Los demás años serán desactivados automáticamente.')
                    ->modalSubmitActionLabel('Sí, activar')
                    ->visible(fn (SchoolYear $record): bool => !$record->is_active)
                    ->action(function (SchoolYear $record): void {
                        // Desactivar todos los años escolares
                        SchoolYear::where('is_active', true)->update(['is_active' => false]);
                        
                        // Activar el año seleccionado
                        $record->update(['is_active' => true]);

                        Notification::make()
                            ->title('Año escolar activado')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('createPeriods')
                    ->label('Crear 4 Periodos')
                    ->icon('heroicon-o-plus-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Crear Periodos')
                    ->modalDescription('Se crearán los 4 periodos para este año escolar. ¿Desea continuar?')
                    ->modalSubmitActionLabel('Sí, crear periodos')
                    ->visible(fn (SchoolYear $record): bool => $record->periods()->count() < 4)
                    ->action(function (SchoolYear $record): void {
                        $existingNumbers = $record->periods()->pluck('number')->toArray();
                        
                        for ($i = 1; $i <= 4; $i++) {
                            if (!in_array($i, $existingNumbers)) {
                                $record->periods()->create([
                                    'number' => $i,
                                    'name' => "Periodo {$i}",
                                    'is_active' => false,
                                ]);
                            }
                        }

                        Notification::make()
                            ->title('Periodos creados exitosamente')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PeriodsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchoolYears::route('/'),
            'create' => Pages\CreateSchoolYear::route('/create'),
            'edit' => Pages\EditSchoolYear::route('/{record}/edit'),
        ];
    }
}
