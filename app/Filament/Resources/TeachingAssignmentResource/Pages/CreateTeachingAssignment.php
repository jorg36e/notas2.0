<?php

namespace App\Filament\Resources\TeachingAssignmentResource\Pages;

use App\Filament\Resources\TeachingAssignmentResource;
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
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateTeachingAssignment extends CreateRecord
{
    protected static string $resource = TeachingAssignmentResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Header con información del año escolar
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('school_year_id')
                            ->label('📅 Año Escolar')
                            ->relationship('schoolYear', 'name')
                            ->required()
                            ->default(fn () => SchoolYear::where('is_active', true)->first()?->id)
                            ->preload()
                            ->searchable()
                            ->live()
                            ->native(false)
                            ->columnSpanFull(),
                    ])
                    ->columnSpan(1),

                // Sección del profesor
                Forms\Components\Section::make('👨‍🏫 Docente')
                    ->description('Selecciona el profesor para esta asignación')
                    ->icon('heroicon-o-user')
                    ->schema([
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
                            ->native(false)
                            ->placeholder('Buscar por nombre o identificación...')
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
                    ])
                    ->collapsible()
                    ->columnSpan(1),

                // Sección de sede
                Forms\Components\Section::make('🏫 Sede')
                    ->description('Selecciona la sede educativa')
                    ->icon('heroicon-o-building-office')
                    ->schema([
                        Forms\Components\Select::make('sede_id')
                            ->label('Sede')
                            ->relationship('sede', 'name')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('grade_ids', []);
                                $set('subject_ids', []);
                            }),
                    ])
                    ->collapsible()
                    ->columnSpan(1),

                // Sección de grados - Ancho completo
                Forms\Components\Section::make('📚 Grados')
                    ->description('Selecciona uno o más grados. Usa "Seleccionar todo" para elegir todos.')
                    ->icon('heroicon-o-academic-cap')
                    ->schema([
                        Forms\Components\CheckboxList::make('grade_ids')
                            ->label('')
                            ->options(function (Get $get) {
                                return Grade::where('is_active', true)
                                    ->orderBy('level')
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->columns([
                                'default' => 2,
                                'sm' => 3,
                                'md' => 4,
                                'lg' => 6,
                            ])
                            ->gridDirection('row')
                            ->bulkToggleable()
                            ->searchable(),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),

                // Sección de asignaturas - Ancho completo
                Forms\Components\Section::make('📖 Asignaturas')
                    ->description('Selecciona una o más asignaturas. Las opciones dependen de la sede seleccionada.')
                    ->icon('heroicon-o-book-open')
                    ->schema([
                        Forms\Components\CheckboxList::make('subject_ids')
                            ->label('')
                            ->options(function (Get $get) {
                                $sedeId = $get('sede_id');
                                
                                if ($sedeId) {
                                    $sede = Sede::find($sedeId);
                                    if ($sede && $sede->subjects()->exists()) {
                                        return $sede->subjects()
                                            ->where('is_active', true)
                                            ->orderBy('name')
                                            ->pluck('subjects.name', 'subjects.id');
                                    }
                                }
                                
                                return Subject::where('is_active', true)
                                    ->orderBy('name')
                                    ->pluck('name', 'id');
                            })
                            ->required()
                            ->columns([
                                'default' => 2,
                                'sm' => 3,
                                'md' => 4,
                                'lg' => 5,
                            ])
                            ->gridDirection('row')
                            ->bulkToggleable()
                            ->searchable(),
                    ])
                    ->collapsible()
                    ->columnSpanFull(),

                // Opciones adicionales
                Forms\Components\Section::make('⚙️ Opciones')
                    ->description('Configuración adicional de la asignación')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Asignación Activa')
                            ->helperText('Desactiva para ocultar temporalmente esta asignación')
                            ->default(true)
                            ->inline(false),

                        Forms\Components\Textarea::make('notes')
                            ->label('Observaciones')
                            ->placeholder('Escribe aquí cualquier nota o comentario sobre esta asignación...')
                            ->maxLength(500)
                            ->rows(2)
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    protected function handleRecordCreation(array $data): TeachingAssignment
    {
        $gradeIds = $data['grade_ids'] ?? [];
        $subjectIds = $data['subject_ids'] ?? [];
        
        $created = 0;
        $skipped = 0;
        $firstRecord = null;

        foreach ($gradeIds as $gradeId) {
            foreach ($subjectIds as $subjectId) {
                // Verificar si ya existe
                $exists = TeachingAssignment::where('school_year_id', $data['school_year_id'])
                    ->where('teacher_id', $data['teacher_id'])
                    ->where('sede_id', $data['sede_id'])
                    ->where('grade_id', $gradeId)
                    ->where('subject_id', $subjectId)
                    ->exists();

                if (!$exists) {
                    $record = TeachingAssignment::create([
                        'school_year_id' => $data['school_year_id'],
                        'teacher_id' => $data['teacher_id'],
                        'sede_id' => $data['sede_id'],
                        'grade_id' => $gradeId,
                        'subject_id' => $subjectId,
                        'is_active' => $data['is_active'] ?? true,
                        'notes' => $data['notes'] ?? null,
                    ]);
                    
                    if (!$firstRecord) {
                        $firstRecord = $record;
                    }
                    $created++;
                } else {
                    $skipped++;
                }
            }
        }

        if ($created > 0) {
            Notification::make()
                ->title('Asignaciones creadas')
                ->body("Se crearon {$created} asignaciones correctamente." . ($skipped > 0 ? " Se omitieron {$skipped} que ya existían." : ""))
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Sin cambios')
                ->body("Todas las asignaciones seleccionadas ya existían.")
                ->warning()
                ->send();
        }

        // Retornar el primer registro creado o crear uno temporal para evitar errores
        return $firstRecord ?? TeachingAssignment::first();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null; // Ya manejamos la notificación en handleRecordCreation
    }
}
