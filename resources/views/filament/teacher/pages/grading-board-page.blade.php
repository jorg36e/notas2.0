<x-filament-panels::page>
    <div class="space-y-6">
        
        {{-- Card principal con información del curso --}}
        <div style="background: linear-gradient(to right, #7c3aed, #6366f1, #8b5cf6) !important;" class="relative overflow-hidden rounded-2xl p-6 shadow-xl">
            {{-- Patrón decorativo de fondo --}}
            <div class="absolute inset-0 opacity-10">
                <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <defs>
                        <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                            <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                        </pattern>
                    </defs>
                    <rect width="100" height="100" fill="url(#grid)"/>
                </svg>
            </div>
            
            <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div style="background-color: rgba(255,255,255,0.2) !important;" class="flex h-16 w-16 items-center justify-center rounded-2xl backdrop-blur-sm">
                        <x-heroicon-o-academic-cap class="h-8 w-8" style="color: white !important;" />
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold" style="color: white !important;">
                            {{ $teachingAssignment->subject->name ?? 'Asignatura' }}
                        </h2>
                        <div class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-1" style="color: #e0e7ff !important;">
                            <span class="flex items-center gap-1">
                                <x-heroicon-m-user-group class="h-4 w-4" />
                                {{ $teachingAssignment->grade->name ?? 'Grado' }}
                                @if($isMultigrade)
                                    <span style="background-color: #fbbf24 !important; color: #78350f !important;" class="ml-1 rounded-full px-2 py-0.5 text-xs font-bold">
                                        MULTIGRADO
                                    </span>
                                @endif
                            </span>
                            <span class="flex items-center gap-1">
                                <x-heroicon-m-building-office class="h-4 w-4" />
                                {{ $teachingAssignment->sede->name ?? 'Sede' }}
                            </span>
                            <span class="flex items-center gap-1">
                                <x-heroicon-m-calendar class="h-4 w-4" />
                                {{ $teachingAssignment->schoolYear->name ?? 'Año' }}
                            </span>
                        </div>
                    </div>
                </div>
                
                {{-- Selector de periodo --}}
                <div class="flex items-center gap-3">
                    <div class="w-48">
                        {{ $this->form }}
                    </div>
                    <a href="{{ \App\Filament\Teacher\Pages\GradesPage::getUrl() }}" 
                       style="background-color: rgba(255,255,255,0.2) !important; color: white !important;"
                       class="flex h-10 w-10 items-center justify-center rounded-xl backdrop-blur-sm transition hover:bg-white/30"
                       title="Volver">
                        <x-heroicon-o-arrow-left class="h-5 w-5" />
                    </a>
                </div>
            </div>

        </div>

        {{-- Estadísticas rápidas - Barra horizontal --}}
        @php
            $graded = collect($grades)->filter(fn($g) => isset($g['final_score']) && $g['final_score'] !== null)->count();
            $passing = collect($grades)->filter(fn($g) => isset($g['final_score']) && $g['final_score'] >= 3.0)->count();
            $failing = $graded - $passing;
            $avgScore = collect($grades)->filter(fn($g) => isset($g['final_score']) && $g['final_score'] !== null)->avg('final_score');
        @endphp
        <div style="background-color: #ffffff !important; border: 1px solid #e5e7eb !important;" class="rounded-2xl p-4 shadow-lg">
            <div class="flex flex-wrap items-center justify-center gap-6 sm:justify-between">
                <div class="flex items-center gap-3">
                    <div style="background-color: #3b82f6 !important;" class="flex h-10 w-10 items-center justify-center rounded-lg">
                        <x-heroicon-s-users class="h-5 w-5" style="color: white !important;" />
                    </div>
                    <div>
                        <p class="text-xl font-bold" style="color: #111827 !important;">{{ $students->count() }}</p>
                        <p class="text-xs font-medium" style="color: #6b7280 !important;">Estudiantes</p>
                    </div>
                </div>
                <div class="hidden sm:block h-8 w-px" style="background-color: #e5e7eb !important;"></div>
                <div class="flex items-center gap-3">
                    <div style="background-color: #f59e0b !important;" class="flex h-10 w-10 items-center justify-center rounded-lg">
                        <x-heroicon-s-pencil-square class="h-5 w-5" style="color: white !important;" />
                    </div>
                    <div>
                        <p class="text-xl font-bold" style="color: #111827 !important;">{{ $graded }}<span class="text-sm font-normal" style="color: #9ca3af !important;">/{{ $students->count() }}</span></p>
                        <p class="text-xs font-medium" style="color: #6b7280 !important;">Calificados</p>
                    </div>
                </div>
                <div class="hidden sm:block h-8 w-px" style="background-color: #e5e7eb !important;"></div>
                <div class="flex items-center gap-3">
                    <div style="background-color: #22c55e !important;" class="flex h-10 w-10 items-center justify-center rounded-lg">
                        <x-heroicon-s-check-circle class="h-5 w-5" style="color: white !important;" />
                    </div>
                    <div>
                        <p class="text-xl font-bold" style="color: #16a34a !important;">{{ $passing }}</p>
                        <p class="text-xs font-medium" style="color: #6b7280 !important;">Aprobados</p>
                    </div>
                </div>
                <div class="hidden sm:block h-8 w-px" style="background-color: #e5e7eb !important;"></div>
                <div class="flex items-center gap-3">
                    <div style="background-color: #a855f7 !important;" class="flex h-10 w-10 items-center justify-center rounded-lg">
                        <x-heroicon-s-chart-bar class="h-5 w-5" style="color: white !important;" />
                    </div>
                    <div>
                        <p class="text-xl font-bold" style="color: #111827 !important;">{{ $avgScore ? number_format($avgScore, 1) : '-' }}</p>
                        <p class="text-xs font-medium" style="color: #6b7280 !important;">Promedio</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Barra de acciones --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            {{-- Leyenda de notas --}}
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-medium" style="color: #6b7280 !important;">Ponderación:</span>
                <span style="background-color: #fef3c7 !important; color: #92400e !important;" class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium">
                    <span style="background-color: #f59e0b !important;" class="h-1.5 w-1.5 rounded-full"></span>
                    Tareas 40%
                </span>
                <span style="background-color: #dbeafe !important; color: #1e40af !important;" class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium">
                    <span style="background-color: #3b82f6 !important;" class="h-1.5 w-1.5 rounded-full"></span>
                    Evaluaciones 50%
                </span>
                <span style="background-color: #f3e8ff !important; color: #6b21a8 !important;" class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium">
                    <span style="background-color: #a855f7 !important;" class="h-1.5 w-1.5 rounded-full"></span>
                    Autoeval. 10%
                </span>
                <span style="background-color: #dcfce7 !important; color: #166534 !important;" class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium">
                    <x-heroicon-m-check class="h-3 w-3" />
                    Aprueba ≥ 3.0
                </span>
            </div>

            {{-- Botones de acción --}}
            @if(!$isReadOnly)
                <div class="flex items-center gap-2">
                    <button wire:click="downloadExcel" 
                            wire:loading.attr="disabled"
                            style="background-color: #16a34a !important; color: white !important;"
                            class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold shadow-lg transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50">
                        <span wire:loading.remove wire:target="downloadExcel">
                            <x-heroicon-m-arrow-down-tray class="h-5 w-5" />
                        </span>
                        <span wire:loading wire:target="downloadExcel">
                            <x-heroicon-m-arrow-path class="h-4 w-4 animate-spin" />
                        </span>
                        Descargar Excel
                    </button>
                </div>
            @endif
        </div>

        {{-- Sección de importar Excel --}}
        @if(!$isReadOnly)
            <div style="background-color: #f9fafb !important; border: 2px dashed #d1d5db !important;" class="rounded-2xl p-4">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div style="background-color: #eef2ff !important;" class="flex h-10 w-10 items-center justify-center rounded-xl">
                            <x-heroicon-o-document-arrow-up class="h-5 w-5" style="color: #6366f1 !important;" />
                        </div>
                        <div>
                            <p class="text-sm font-medium" style="color: #374151 !important;">Importar calificaciones</p>
                            <p class="text-xs" style="color: #6b7280 !important;">Suba un archivo Excel con las notas</p>
                        </div>
                    </div>
                    <form wire:submit="uploadExcel" class="flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                        <label class="flex-1 cursor-pointer">
                            <input type="file" 
                                   wire:model="excelFile"
                                   accept=".xlsx,.xls"
                                   class="hidden"
                                   id="excelFileInput">
                            <div style="background-color: #ffffff !important; border: 2px solid #e5e7eb !important; color: #4b5563 !important;" class="flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm transition hover:border-indigo-300 hover:bg-indigo-50">
                                <x-heroicon-o-folder-open class="h-4 w-4" />
                                <span wire:loading.remove wire:target="excelFile">
                                    {{ $excelFile ? $excelFile->getClientOriginalName() : 'Seleccionar archivo...' }}
                                </span>
                                <span wire:loading wire:target="excelFile" class="flex items-center gap-2">
                                    <x-heroicon-m-arrow-path class="h-4 w-4 animate-spin" />
                                    Cargando...
                                </span>
                            </div>
                        </label>
                        <button type="submit"
                                wire:loading.attr="disabled"
                                wire:target="excelFile,uploadExcel"
                                style="background-color: #6366f1 !important; color: white !important;"
                                class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold shadow-sm transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                            <span wire:loading.remove wire:target="uploadExcel">
                                <x-heroicon-m-arrow-up-tray class="h-4 w-4" />
                            </span>
                            <span wire:loading wire:target="uploadExcel">
                                <x-heroicon-m-arrow-path class="h-4 w-4 animate-spin" />
                            </span>
                            Importar
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Banner de periodo finalizado --}}
        @if($isReadOnly && $currentPeriod)
            <div style="background: linear-gradient(to right, #ef4444, #f97316) !important;" class="relative overflow-hidden rounded-2xl p-4 shadow-lg">
                <div style="background-color: rgba(255,255,255,0.1) !important;" class="absolute -right-4 -top-4 h-24 w-24 rounded-full"></div>
                <div style="background-color: rgba(255,255,255,0.1) !important;" class="absolute -bottom-4 -left-4 h-16 w-16 rounded-full"></div>
                <div class="relative flex items-center gap-4">
                    <div style="background-color: rgba(255,255,255,0.2) !important;" class="flex h-12 w-12 items-center justify-center rounded-xl backdrop-blur-sm">
                        <x-heroicon-o-lock-closed class="h-6 w-6" style="color: white !important;" />
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold" style="color: white !important;">Periodo Histórico - Solo Lectura</h3>
                        <p class="text-sm" style="color: #fee2e2 !important;">
                            El {{ $currentPeriod->name }} está finalizado. No se permiten modificaciones.
                        </p>
                    </div>
                    <button wire:click="goToActivePeriod" 
                            style="background-color: white !important; color: #dc2626 !important;"
                            class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold shadow-sm transition hover:bg-red-50">
                        <x-heroicon-m-arrow-right class="h-4 w-4" />
                        Ir al periodo actual
                    </button>
                </div>
            </div>
        @endif

        {{-- Tabla de calificaciones --}}
        @if($students->count() > 0)
            <div style="background-color: #ffffff !important; border: 1px solid #e5e7eb !important;" class="overflow-hidden rounded-2xl shadow-xl">
                {{-- Header de la tabla --}}
                <div style="background-color: #f9fafb !important; border-bottom: 1px solid #e5e7eb !important;" class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="flex items-center gap-2 text-sm font-semibold" style="color: #111827 !important;">
                            <x-heroicon-m-table-cells class="h-5 w-5" style="color: #9ca3af !important;" />
                            Lista de Estudiantes
                            <span style="background-color: #eef2ff !important; color: #4338ca !important;" class="ml-2 rounded-full px-2 py-0.5 text-xs font-medium">
                                {{ $students->count() }} estudiantes
                            </span>
                        </h3>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr style="background-color: #f9fafb !important; border-bottom: 1px solid #e5e7eb !important;">
                                <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: #6b7280 !important;">
                                    #
                                </th>
                                <th class="whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider" style="color: #6b7280 !important;">
                                    Estudiante
                                </th>
                                @if($isMultigrade)
                                <th class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider" style="color: #6b7280 !important;">
                                    Grado
                                </th>
                                @endif
                                <th class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider" style="color: #d97706 !important; min-width: 90px;">
                                    <div class="flex flex-col items-center">
                                        <span>Tareas</span>
                                        <span class="text-[10px] font-normal" style="color: #9ca3af !important;">40%</span>
                                    </div>
                                </th>
                                <th class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider" style="color: #2563eb !important; min-width: 90px;">
                                    <div class="flex flex-col items-center">
                                        <span>Evaluac.</span>
                                        <span class="text-[10px] font-normal" style="color: #9ca3af !important;">50%</span>
                                    </div>
                                </th>
                                <th class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider" style="color: #9333ea !important; min-width: 90px;">
                                    <div class="flex flex-col items-center">
                                        <span>Autoeval.</span>
                                        <span class="text-[10px] font-normal" style="color: #9ca3af !important;">10%</span>
                                    </div>
                                </th>
                                <th class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider" style="color: #111827 !important; min-width: 80px;">
                                    Definitiva
                                </th>
                                <th class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider" style="color: #6b7280 !important; min-width: 120px;">
                                    Comportamiento
                                </th>
                                <th class="whitespace-nowrap px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider" style="color: #6b7280 !important; min-width: 80px;">
                                    Fallas
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="border-color: #e5e7eb !important;">
                            @foreach($students as $index => $enrollment)
                                @php
                                    $studentId = $enrollment->student_id;
                                    $studentGrades = $grades[$studentId] ?? [];
                                    $finalScore = $studentGrades['final_score'] ?? null;
                                    $isPassing = $finalScore !== null && $finalScore >= 3.0;
                                    $behavior = $studentGrades['behavior'] ?? null;
                                    $behaviorColors = [
                                        'BAJO' => 'background-color: #fee2e2 !important; color: #b91c1c !important;',
                                        'BASICO' => 'background-color: #fef3c7 !important; color: #a16207 !important;',
                                        'ALTO' => 'background-color: #dbeafe !important; color: #1e40af !important;',
                                        'SUPERIOR' => 'background-color: #dcfce7 !important; color: #166534 !important;',
                                    ];
                                @endphp
                                <tr class="group transition" style="{{ $loop->even ? 'background-color: #f9fafb !important;' : 'background-color: #ffffff !important;' }}">
                                    {{-- Número --}}
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <span style="background-color: #f3f4f6 !important; color: #4b5563 !important;" class="flex h-7 w-7 items-center justify-center rounded-lg text-xs font-semibold">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>
                                    
                                    {{-- Info estudiante --}}
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            @if($enrollment->student->profile_photo)
                                                <img src="{{ asset('storage/' . $enrollment->student->profile_photo) }}" 
                                                     alt="{{ $enrollment->student->name }}"
                                                     class="h-10 w-10 rounded-full object-cover shadow-sm ring-2 ring-indigo-200">
                                            @else
                                                <div style="background: linear-gradient(to bottom right, #818cf8, #6366f1) !important; color: white !important;" class="flex h-10 w-10 items-center justify-center rounded-full text-sm font-bold shadow-sm">
                                                    {{ strtoupper(substr($enrollment->student->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <p class="font-medium" style="color: #111827 !important;">
                                                    {{ $enrollment->student->name }}
                                                </p>
                                                <p class="text-xs" style="color: #6b7280 !important;">
                                                    ID: {{ $enrollment->student->identification }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    @if($isMultigrade)
                                    {{-- Grado del estudiante (solo en multigrado) --}}
                                    <td class="px-2 py-3 text-center">
                                        <span style="background-color: #eef2ff !important; color: #4338ca !important; border: 1px solid #c7d2fe !important;" class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-semibold">
                                            {{ $enrollment->grade->name ?? '-' }}
                                        </span>
                                    </td>
                                    @endif
                                    
                                    {{-- Tareas --}}
                                    <td class="px-2 py-3">
                                        <input type="number" 
                                               wire:model.lazy="grades.{{ $studentId }}.tasks_score"
                                               step="0.1" min="1.0" max="5.0"
                                               placeholder="-"
                                               @if($isReadOnly) disabled @endif
                                               style="background-color: #fffbeb !important; border: 2px solid #fbbf24 !important; color: #92400e !important;"
                                               class="w-full rounded-lg px-2 py-2 text-center text-sm font-bold transition focus:ring-2 focus:ring-amber-300 disabled:cursor-not-allowed disabled:border-gray-300 disabled:bg-gray-100 disabled:text-gray-500">
                                    </td>
                                    
                                    {{-- Evaluaciones --}}
                                    <td class="px-2 py-3">
                                        <input type="number" 
                                               wire:model.lazy="grades.{{ $studentId }}.evaluations_score"
                                               step="0.1" min="1.0" max="5.0"
                                               placeholder="-"
                                               @if($isReadOnly) disabled @endif
                                               style="background-color: #eff6ff !important; border: 2px solid #60a5fa !important; color: #1e40af !important;"
                                               class="w-full rounded-lg px-2 py-2 text-center text-sm font-bold transition focus:ring-2 focus:ring-blue-300 disabled:cursor-not-allowed disabled:border-gray-300 disabled:bg-gray-100 disabled:text-gray-500">
                                    </td>
                                    
                                    {{-- Autoevaluación --}}
                                    <td class="px-2 py-3">
                                        <input type="number" 
                                               wire:model.lazy="grades.{{ $studentId }}.self_score"
                                               step="0.1" min="1.0" max="5.0"
                                               placeholder="-"
                                               @if($isReadOnly) disabled @endif
                                               style="background-color: #faf5ff !important; border: 2px solid #c084fc !important; color: #6b21a8 !important;"
                                               class="w-full rounded-lg px-2 py-2 text-center text-sm font-bold transition focus:ring-2 focus:ring-purple-300 disabled:cursor-not-allowed disabled:border-gray-300 disabled:bg-gray-100 disabled:text-gray-500">
                                    </td>
                                    
                                    {{-- Definitiva --}}
                                    <td class="px-2 py-3">
                                        <div class="flex justify-center">
                                            @if($finalScore !== null)
                                                <div style="{{ $isPassing ? 'background: linear-gradient(to right, #22c55e, #10b981) !important;' : 'background: linear-gradient(to right, #ef4444, #f43f5e) !important;' }} color: white !important;" class="flex items-center gap-1.5 rounded-xl px-3 py-1.5 font-bold shadow-sm">
                                                    @if($isPassing)
                                                        <x-heroicon-m-check-circle class="h-4 w-4" />
                                                    @else
                                                        <x-heroicon-m-x-circle class="h-4 w-4" />
                                                    @endif
                                                    {{ number_format($finalScore, 1) }}
                                                </div>
                                            @else
                                                <span style="background-color: #f3f4f6 !important; color: #9ca3af !important;" class="flex items-center gap-1 rounded-xl px-3 py-1.5 text-sm">
                                                    <x-heroicon-m-minus class="h-4 w-4" />
                                                    -
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    
                                    {{-- Comportamiento --}}
                                    <td class="px-2 py-3">
                                        <select 
                                            wire:model.lazy="grades.{{ $studentId }}.behavior"
                                            @if($isReadOnly) disabled @endif
                                            style="{{ $behavior ? ($behaviorColors[$behavior] ?? '') : 'background-color: #ffffff !important; color: #6b7280 !important;' }} border: 2px solid #e5e7eb !important;"
                                            class="w-full appearance-none rounded-lg px-3 py-2 text-center text-xs font-semibold transition focus:ring-2 focus:ring-indigo-200 disabled:cursor-not-allowed disabled:bg-gray-100">
                                            <option value="" style="background-color: white !important; color: #6b7280 !important;">Seleccionar...</option>
                                            <option value="BAJO" style="background-color: white !important; color: #dc2626 !important;">🔴 BAJO</option>
                                            <option value="BASICO" style="background-color: white !important; color: #ca8a04 !important;">🟡 BÁSICO</option>
                                            <option value="ALTO" style="background-color: white !important; color: #2563eb !important;">🔵 ALTO</option>
                                            <option value="SUPERIOR" style="background-color: white !important; color: #16a34a !important;">🟢 SUPERIOR</option>
                                        </select>
                                    </td>
                                    
                                    {{-- Inasistencias --}}
                                    <td class="px-2 py-3">
                                        <div class="flex items-center justify-center">
                                            <input type="number" 
                                                   wire:model.lazy="grades.{{ $studentId }}.absences"
                                                   min="0" step="1"
                                                   placeholder="0"
                                                   @if($isReadOnly) disabled @endif
                                                   style="background-color: #ffffff !important; border: 2px solid #e5e7eb !important; color: #374151 !important;"
                                                   class="w-16 rounded-lg px-2 py-2 text-center text-sm font-medium transition focus:border-orange-400 focus:ring-2 focus:ring-orange-200 disabled:cursor-not-allowed disabled:bg-gray-100">
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Footer con botón guardar --}}
                @if(!$isReadOnly)
                    <div style="background-color: #f9fafb !important; border-top: 1px solid #e5e7eb !important;" class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <p class="text-sm" style="color: #6b7280 !important;">
                                <x-heroicon-m-information-circle class="mr-1 inline h-4 w-4" />
                                Los cambios se guardan al presionar el botón
                            </p>
                            <button wire:click="saveGrades" 
                                    wire:loading.attr="disabled"
                                    style="background-color: #2563eb !important; color: white !important;"
                                    class="inline-flex items-center gap-2 rounded-xl px-6 py-3 text-sm font-bold shadow-lg transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50">
                                <span wire:loading.remove wire:target="saveGrades">
                                    <x-heroicon-m-check class="h-5 w-5" />
                                </span>
                                <span wire:loading wire:target="saveGrades">
                                    <x-heroicon-m-arrow-path class="h-5 w-5 animate-spin" />
                                </span>
                                Guardar Calificaciones
                            </button>
                        </div>
                    </div>
                @endif
            </div>

        @else
            {{-- Estado vacío --}}
            <div style="background-color: #ffffff !important; border: 1px solid #e5e7eb !important;" class="rounded-2xl p-12 shadow-xl">
                <div class="text-center">
                    <div style="background-color: #f3f4f6 !important;" class="mx-auto flex h-20 w-20 items-center justify-center rounded-full">
                        <x-heroicon-o-users class="h-10 w-10" style="color: #9ca3af !important;" />
                    </div>
                    <h3 class="mt-4 text-lg font-semibold" style="color: #111827 !important;">Sin estudiantes matriculados</h3>
                    <p class="mt-2 text-sm" style="color: #6b7280 !important;">
                        No hay estudiantes matriculados en este grado y sede para el año escolar actual.
                    </p>
                    <a href="{{ \App\Filament\Teacher\Pages\GradesPage::getUrl() }}" 
                       style="background-color: #6366f1 !important; color: white !important;"
                       class="mt-6 inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold transition hover:opacity-90">
                        <x-heroicon-m-arrow-left class="h-4 w-4" />
                        Volver a mis asignaturas
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
