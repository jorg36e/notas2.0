<x-filament-panels::page>
    @php
        $user = auth()->user();
        $activeYear = \App\Models\SchoolYear::where('is_active', true)->first();
        $activePeriod = $activeYear 
            ? \App\Models\Period::where('school_year_id', $activeYear->id)->where('is_active', true)->first()
            : null;

        $assignments = collect();
        $assignmentsCount = 0;
        $subjectsCount = 0;
        $studentsCount = 0;
        $gradedCount = 0;
        $pendingCount = 0;

        if ($activeYear) {
            $assignments = \App\Models\TeachingAssignment::where('teacher_id', $user->id)
                ->where('school_year_id', $activeYear->id)
                ->where('is_active', true)
                ->with(['subject', 'grade', 'sede'])
                ->get();

            $assignmentsCount = $assignments->count();
            $subjectsCount = $assignments->pluck('subject_id')->unique()->count();

            if ($assignments->count() > 0) {
                $studentIds = collect();
                foreach ($assignments as $assignment) {
                    $assignedGrade = $assignment->grade;
                    
                    if ($assignedGrade->type === 'multi' && $assignedGrade->min_grade && $assignedGrade->max_grade) {
                        $gradesInRange = \App\Models\Grade::where('is_active', true)
                            ->whereBetween('level', [$assignedGrade->min_grade, $assignedGrade->max_grade])
                            ->pluck('id')
                            ->toArray();
                        
                        $ids = \App\Models\Enrollment::where('school_year_id', $activeYear->id)
                            ->where('sede_id', $assignment->sede_id)
                            ->whereIn('grade_id', $gradesInRange)
                            ->where('status', 'active')
                            ->pluck('student_id');
                    } else {
                        $ids = \App\Models\Enrollment::where('school_year_id', $activeYear->id)
                            ->where('sede_id', $assignment->sede_id)
                            ->where('grade_id', $assignment->grade_id)
                            ->where('status', 'active')
                            ->pluck('student_id');
                    }
                    $studentIds = $studentIds->merge($ids);
                }
                $studentsCount = $studentIds->unique()->count();

                if ($activePeriod) {
                    foreach ($assignments as $assignment) {
                        $gradedCount += \App\Models\StudentGrade::where('teaching_assignment_id', $assignment->id)
                            ->where('period_id', $activePeriod->id)
                            ->whereNotNull('final_score')
                            ->count();
                    }
                    $totalPossible = $studentsCount * $assignmentsCount;
                    $pendingCount = max(0, $totalPossible - $gradedCount);
                }
            }
        }
    @endphp

    {{-- Sección de Acciones Rápidas --}}
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-bolt class="h-5 w-5 text-warning-500" />
                Acciones Rápidas
            </div>
        </x-slot>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="{{ \App\Filament\Teacher\Pages\GradesPage::getUrl() }}" 
               class="group flex flex-col items-center justify-center p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all duration-200">
                <div class="p-3 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 mb-2 group-hover:scale-110 transition-transform">
                    <x-heroicon-o-pencil-square class="h-6 w-6" />
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 text-center">Calificar</span>
                <span class="text-xs text-gray-500 dark:text-gray-400 text-center mt-0.5">Ingresar notas</span>
            </a>
            
            <a href="{{ \App\Filament\Teacher\Pages\GradesPage::getUrl() }}" 
               class="group flex flex-col items-center justify-center p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-success-500 hover:bg-success-50 dark:hover:bg-success-900/20 transition-all duration-200">
                <div class="p-3 rounded-full bg-success-100 dark:bg-success-900/30 text-success-600 dark:text-success-400 mb-2 group-hover:scale-110 transition-transform">
                    <x-heroicon-o-academic-cap class="h-6 w-6" />
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 text-center">Mis Asignaturas</span>
                <span class="text-xs text-gray-500 dark:text-gray-400 text-center mt-0.5">Ver cursos</span>
            </a>
            
            <a href="{{ \App\Filament\Teacher\Pages\MyProfile::getUrl() }}" 
               class="group flex flex-col items-center justify-center p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-info-500 hover:bg-info-50 dark:hover:bg-info-900/20 transition-all duration-200">
                <div class="p-3 rounded-full bg-info-100 dark:bg-info-900/30 text-info-600 dark:text-info-400 mb-2 group-hover:scale-110 transition-transform">
                    <x-heroicon-o-user-circle class="h-6 w-6" />
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 text-center">Mi Perfil</span>
                <span class="text-xs text-gray-500 dark:text-gray-400 text-center mt-0.5">Ver información</span>
            </a>
            
            @if(\App\Filament\Teacher\Pages\ReportCards::shouldRegisterNavigation())
            <a href="{{ \App\Filament\Teacher\Pages\ReportCards::getUrl() }}" 
               class="group flex flex-col items-center justify-center p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-warning-500 hover:bg-warning-50 dark:hover:bg-warning-900/20 transition-all duration-200">
                <div class="p-3 rounded-full bg-warning-100 dark:bg-warning-900/30 text-warning-600 dark:text-warning-400 mb-2 group-hover:scale-110 transition-transform">
                    <x-heroicon-o-document-text class="h-6 w-6" />
                </div>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 text-center">Boletines</span>
                <span class="text-xs text-gray-500 dark:text-gray-400 text-center mt-0.5">Descargar reportes</span>
            </a>
            @else
            <div class="flex flex-col items-center justify-center p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                <div class="p-3 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-400 mb-2">
                    <x-heroicon-o-calendar class="h-6 w-6" />
                </div>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 text-center">{{ $activePeriod?->name ?? 'N/A' }}</span>
                <span class="text-xs text-gray-400 dark:text-gray-500 text-center mt-0.5">Periodo actual</span>
            </div>
            @endif
        </div>
    </x-filament::section>

    {{-- Grid de Estadísticas usando componentes Filament --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
        {{-- Stat: Asignaciones --}}
        <x-filament::section class="!p-0">
            <div class="flex items-center gap-4 p-4">
                <div class="p-3 rounded-xl bg-primary-100 dark:bg-primary-900/30">
                    <x-heroicon-o-clipboard-document-list class="h-8 w-8 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $assignmentsCount }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Asignaciones</p>
                </div>
            </div>
        </x-filament::section>

        {{-- Stat: Asignaturas --}}
        <x-filament::section class="!p-0">
            <div class="flex items-center gap-4 p-4">
                <div class="p-3 rounded-xl bg-info-100 dark:bg-info-900/30">
                    <x-heroicon-o-book-open class="h-8 w-8 text-info-600 dark:text-info-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $subjectsCount }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Asignaturas</p>
                </div>
            </div>
        </x-filament::section>

        {{-- Stat: Estudiantes --}}
        <x-filament::section class="!p-0">
            <div class="flex items-center gap-4 p-4">
                <div class="p-3 rounded-xl bg-warning-100 dark:bg-warning-900/30">
                    <x-heroicon-o-user-group class="h-8 w-8 text-warning-600 dark:text-warning-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $studentsCount }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Estudiantes</p>
                </div>
            </div>
        </x-filament::section>

        {{-- Stat: Calificados --}}
        <x-filament::section class="!p-0">
            <div class="flex items-center gap-4 p-4">
                <div class="p-3 rounded-xl bg-success-100 dark:bg-success-900/30">
                    <x-heroicon-o-check-circle class="h-8 w-8 text-success-600 dark:text-success-400" />
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $gradedCount }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Calificados</p>
                </div>
            </div>
        </x-filament::section>
    </div>

    {{-- Contenido en dos columnas --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        {{-- Mis Asignaciones --}}
        <div class="lg:col-span-2">
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-academic-cap class="h-5 w-5 text-primary-500" />
                        Mis Asignaciones
                    </div>
                </x-slot>
                <x-slot name="headerEnd">
                    <a href="{{ \App\Filament\Teacher\Pages\GradesPage::getUrl() }}" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">
                        Ver todas →
                    </a>
                </x-slot>
                
                @if($assignments->isEmpty())
                    <div class="text-center py-8">
                        <x-heroicon-o-academic-cap class="h-12 w-12 mx-auto text-gray-400 mb-3" />
                        <p class="text-gray-500 dark:text-gray-400">No tienes asignaciones activas</p>
                    </div>
                @else
                    <div class="overflow-hidden">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="text-left py-3 px-2 font-medium text-gray-500 dark:text-gray-400">Asignatura</th>
                                    <th class="text-left py-3 px-2 font-medium text-gray-500 dark:text-gray-400">Grado</th>
                                    <th class="text-left py-3 px-2 font-medium text-gray-500 dark:text-gray-400">Sede</th>
                                    <th class="text-right py-3 px-2 font-medium text-gray-500 dark:text-gray-400">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach($assignments->take(6) as $assignment)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                        <td class="py-3 px-2">
                                            <div class="flex items-center gap-2">
                                                <span class="text-lg">📚</span>
                                                <span class="font-medium text-gray-900 dark:text-white">{{ $assignment->subject->name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-2 text-gray-600 dark:text-gray-300">{{ $assignment->grade->name }}</td>
                                        <td class="py-3 px-2 text-gray-600 dark:text-gray-300">{{ $assignment->sede->name }}</td>
                                        <td class="py-3 px-2 text-right">
                                            <a href="{{ \App\Filament\Teacher\Pages\GradingBoardPage::getUrl(['assignment' => $assignment->id]) }}" 
                                               class="inline-flex items-center gap-1 text-primary-600 dark:text-primary-400 hover:underline text-sm font-medium">
                                                Calificar
                                                <x-heroicon-m-arrow-right class="h-4 w-4" />
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-filament::section>
        </div>

        {{-- Panel de Información --}}
        <div class="space-y-6">
            {{-- Año y Periodo Activo --}}
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-calendar class="h-5 w-5 text-info-500" />
                        Información Actual
                    </div>
                </x-slot>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Año Escolar</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $activeYear?->name ?? 'Sin definir' }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Periodo</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $activePeriod?->name ?? 'Sin definir' }}</span>
                    </div>
                    @if($activePeriod && $activePeriod->is_finalized)
                        <div class="flex items-center gap-2 p-3 bg-danger-50 dark:bg-danger-900/20 rounded-lg text-danger-700 dark:text-danger-400">
                            <x-heroicon-o-lock-closed class="h-5 w-5" />
                            <span class="text-sm font-medium">Periodo cerrado</span>
                        </div>
                    @else
                        <div class="flex items-center gap-2 p-3 bg-success-50 dark:bg-success-900/20 rounded-lg text-success-700 dark:text-success-400">
                            <x-heroicon-o-lock-open class="h-5 w-5" />
                            <span class="text-sm font-medium">Periodo abierto</span>
                        </div>
                    @endif
                </div>
            </x-filament::section>

            {{-- Ponderación de Notas --}}
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-scale class="h-5 w-5 text-warning-500" />
                        Ponderación
                    </div>
                </x-slot>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Tareas</span>
                        </div>
                        <span class="font-bold text-gray-900 dark:text-white">40%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-purple-500"></span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Evaluaciones</span>
                        </div>
                        <span class="font-bold text-gray-900 dark:text-white">50%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-green-500"></span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">Autoevaluación</span>
                        </div>
                        <span class="font-bold text-gray-900 dark:text-white">10%</span>
                    </div>
                    <hr class="border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Nota mínima aprobatoria</span>
                        <x-filament::badge color="success">≥ 3.0</x-filament::badge>
                    </div>
                </div>
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>
