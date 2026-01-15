<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnrollmentResource\Pages;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\SchoolYear;
use App\Models\Sede;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Gestión Académica';

    protected static ?string $modelLabel = 'Matrícula';

    protected static ?string $pluralModelLabel = 'Matrículas';

    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de Matrícula')
                    ->schema([
                        Forms\Components\Select::make('school_year_id')
                            ->label('Año Escolar')
                            ->relationship('schoolYear', 'name')
                            ->required()
                            ->default(fn () => SchoolYear::where('is_active', true)->first()?->id)
                            ->preload()
                            ->searchable()
                            ->live(),

                        Forms\Components\Select::make('student_id')
                            ->label('Estudiante')
                            ->options(function () {
                                return User::where('role', 'student')
                                    ->where('is_active', true)
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->getSearchResultsUsing(fn (string $search): array => 
                                User::where('role', 'student')
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
                            ->afterStateUpdated(fn (Set $set) => $set('grade_id', null)),

                        Forms\Components\Select::make('grade_id')
                            ->label('Grado')
                            ->options(function (Get $get) {
                                // Obtener todos los grados activos
                                return Grade::where('is_active', true)
                                    ->orderBy('level')
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->preload()
                            ->searchable(),

                        Forms\Components\DatePicker::make('enrollment_date')
                            ->label('Fecha de Matrícula')
                            ->default(now()),

                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'active' => 'Activa',
                                'inactive' => 'Inactiva',
                                'transferred' => 'Trasladado',
                                'withdrawn' => 'Retirado',
                            ])
                            ->default('active')
                            ->required(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Observaciones')
                            ->maxLength(500)
                            ->rows(2)
                            ->columnSpanFull(),
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

                Tables\Columns\ImageColumn::make('student.profile_photo')
                    ->label('')
                    ->circular()
                    ->size(40)
                    ->getStateUsing(fn ($record) => $record->student?->profile_photo ? asset('storage/' . $record->student->profile_photo) : null)
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->student->name ?? 'E') . '&background=8b5cf6&color=fff'),

                Tables\Columns\TextColumn::make('student.identification')
                    ->label('Identificación')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('student.name')
                    ->label('Estudiante')
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

                Tables\Columns\TextColumn::make('enrollment_date')
                    ->label('Fecha Matrícula')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Activa',
                        'inactive' => 'Inactiva',
                        'transferred' => 'Trasladado',
                        'withdrawn' => 'Retirado',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        'transferred' => 'warning',
                        'withdrawn' => 'danger',
                        default => 'gray',
                    }),

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

                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Activa',
                        'inactive' => 'Inactiva',
                        'transferred' => 'Trasladado',
                        'withdrawn' => 'Retirado',
                    ]),
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
            'index' => Pages\ListEnrollments::route('/'),
            'create' => Pages\CreateEnrollment::route('/create'),
            'edit' => Pages\EditEnrollment::route('/{record}/edit'),
        ];
    }
}
