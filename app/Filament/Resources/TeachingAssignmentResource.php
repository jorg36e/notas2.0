<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeachingAssignmentResource\Pages;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Sede;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TeachingAssignmentResource extends Resource
{
    protected static ?string $model = TeachingAssignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Gestión Académica';

    protected static ?string $modelLabel = 'Asignación Docente';

    protected static ?string $pluralModelLabel = 'Asignaciones Docentes';

    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Asignación')
                    ->schema([
                        Forms\Components\Select::make('school_year_id')
                            ->label('Año Escolar')
                            ->relationship('schoolYear', 'name')
                            ->required()
                            ->default(fn () => SchoolYear::where('is_active', true)->first()?->id)
                            ->preload()
                            ->searchable()
                            ->live(),

                        Forms\Components\Select::make('teacher_id')
                            ->label('Profesor')
                            ->options(function () {
                                return User::where('role', 'teacher')
                                    ->where('is_active', true)
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->getSearchResultsUsing(fn (string $search): array => 
                                User::where('role', 'teacher')
                                    ->where('is_active', true)
                                    ->where(function ($query) use ($search) {
                                        $query->where('name', 'like', "%{$search}%")
                                            ->orWhere('identification', 'like', "%{$search}%");
                                    })
                                    ->limit(50)
                                    ->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->getOptionLabelUsing(fn ($value): ?string => 
                                User::find($value)?->name
                            ),

                        Forms\Components\Select::make('sede_id')
                            ->label('Sede')
                            ->relationship('sede', 'name')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('grade_id', null);
                                $set('subject_id', null);
                            }),

                        Forms\Components\Select::make('grade_id')
                            ->label('Grado')
                            ->options(function (Get $get) {
                                return Grade::where('is_active', true)
                                    ->orderBy('level')
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->preload()
                            ->searchable()
                            ->live(),

                        Forms\Components\Select::make('subject_id')
                            ->label('Asignatura')
                            ->options(function (Get $get) {
                                $sedeId = $get('sede_id');
                                
                                if ($sedeId) {
                                    // Si hay sede seleccionada, filtrar por las asignaturas habilitadas
                                    $sede = Sede::find($sedeId);
                                    if ($sede && $sede->subjects()->exists()) {
                                        return $sede->subjects()
                                            ->where('is_active', true)
                                            ->orderBy('name')
                                            ->pluck('subjects.name', 'subjects.id');
                                    }
                                }
                                
                                // Si no hay sede o no hay asignaturas habilitadas, mostrar todas las activas
                                return Subject::where('is_active', true)
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->preload()
                            ->searchable(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Información Adicional')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Asignación Activa')
                            ->default(true),

                        Forms\Components\Textarea::make('notes')
                            ->label('Observaciones')
                            ->maxLength(500)
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),
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

                Tables\Columns\ImageColumn::make('teacher.profile_photo')
                    ->label('')
                    ->circular()
                    ->size(40)
                    ->getStateUsing(fn ($record) => $record->teacher?->profile_photo ? asset('storage/' . $record->teacher->profile_photo) : null)
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->teacher->name ?? 'P') . '&background=10b981&color=fff'),

                Tables\Columns\TextColumn::make('teacher.identification')
                    ->label('Identificación')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('Profesor')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sede.name')
                    ->label('Sede')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('grade.name')
                    ->label('Grado')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Asignatura')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activa')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

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

                Tables\Filters\SelectFilter::make('teacher_id')
                    ->label('Profesor')
                    ->options(fn () => User::where('role', 'teacher')->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('sede_id')
                    ->label('Sede')
                    ->relationship('sede', 'name')
                    ->preload()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('grade_id')
                    ->label('Grado')
                    ->relationship('grade', 'name')
                    ->preload()
                    ->searchable(),

                Tables\Filters\SelectFilter::make('subject_id')
                    ->label('Asignatura')
                    ->relationship('subject', 'name')
                    ->preload()
                    ->searchable(),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado')
                    ->placeholder('Todas')
                    ->trueLabel('Solo activas')
                    ->falseLabel('Solo inactivas'),
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
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListTeachingAssignments::route('/'),
            'create' => Pages\CreateTeachingAssignment::route('/create'),
            'edit' => Pages\EditTeachingAssignment::route('/{record}/edit'),
        ];
    }
}
