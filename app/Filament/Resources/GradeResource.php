<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GradeResource\Pages;
use App\Models\Grade;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GradeResource extends Resource
{
    protected static ?string $model = Grade::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Configuración Académica';

    protected static ?string $modelLabel = 'Grado';

    protected static ?string $pluralModelLabel = 'Grados';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Grado')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre del Grado')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Primero de Primaria'),

                        Forms\Components\TextInput::make('code')
                            ->label('Código')
                            ->maxLength(20)
                            ->placeholder('Ej: 1P')
                            ->unique(ignoreRecord: true),

                        Forms\Components\Select::make('type')
                            ->label('Tipo')
                            ->options([
                                'single' => 'Grado Único',
                                'multi' => 'Multigrado',
                            ])
                            ->required()
                            ->default('single')
                            ->live()
                            ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                if ($state === 'single') {
                                    $set('min_grade', null);
                                    $set('max_grade', null);
                                }
                            }),

                        Forms\Components\TextInput::make('level')
                            ->label('Nivel')
                            ->numeric()
                            ->placeholder('Ej: 1, 2, 3...')
                            ->helperText('Número que representa el orden del grado'),

                        Forms\Components\TextInput::make('min_grade')
                            ->label('Grado Mínimo')
                            ->numeric()
                            ->visible(fn (Get $get): bool => $get('type') === 'multi')
                            ->required(fn (Get $get): bool => $get('type') === 'multi')
                            ->placeholder('Ej: 1'),

                        Forms\Components\TextInput::make('max_grade')
                            ->label('Grado Máximo')
                            ->numeric()
                            ->visible(fn (Get $get): bool => $get('type') === 'multi')
                            ->required(fn (Get $get): bool => $get('type') === 'multi')
                            ->gte('min_grade')
                            ->placeholder('Ej: 3'),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->maxLength(500)
                            ->rows(2)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Grado Activo')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Director de Grupo')
                    ->description('Asigna un profesor como director de este grupo/grado.')
                    ->icon('heroicon-o-user-circle')
                    ->schema([
                        Forms\Components\Select::make('director_id')
                            ->label('Profesor Director de Grupo')
                            ->options(
                                User::where('role', 'teacher')
                                    ->where('is_active', true)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->placeholder('Seleccionar profesor...')
                            ->helperText('El director de grupo es responsable del seguimiento académico y disciplinario de los estudiantes.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Sin código'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'single' => 'Único',
                        'multi' => 'Multigrado',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'single' => 'info',
                        'multi' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('level')
                    ->label('Nivel')
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('grade_range')
                    ->label('Rango')
                    ->getStateUsing(function (Grade $record): string {
                        if ($record->type === 'multi' && $record->min_grade && $record->max_grade) {
                            return "{$record->min_grade} - {$record->max_grade}";
                        }
                        return '-';
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('director.name')
                    ->label('Director de Grupo')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Sin asignar')
                    ->icon('heroicon-o-user-circle')
                    ->iconColor('primary'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'single' => 'Único',
                        'multi' => 'Multigrado',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->placeholder('Todos')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('level');
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
            'index' => Pages\ListGrades::route('/'),
            'create' => Pages\CreateGrade::route('/create'),
            'edit' => Pages\EditGrade::route('/{record}/edit'),
        ];
    }
}
