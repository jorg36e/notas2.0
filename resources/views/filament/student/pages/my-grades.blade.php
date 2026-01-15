<x-filament-panels::page>
    <div class="space-y-4">
        {{-- Header --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-xl" style="background: {{ $primaryColor }}">
                        📚
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Mis Calificaciones</h1>
                        @if($enrollment)
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $enrollment->grade->name }} · {{ $enrollment->sede->name }}</p>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <select wire:model.live="selectedSchoolYear" class="rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm font-medium px-4 py-2 shadow-sm focus:ring-2 focus:ring-blue-500">
                        @foreach($schoolYears as $id => $year)
                            <option value="{{ $id }}">{{ $year }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="selectedPeriod" class="rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm font-medium px-4 py-2 shadow-sm focus:ring-2 focus:ring-blue-500">
                        @foreach($periods as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            {{-- Stats bar --}}
            @php $stats = $this->getStats(); @endphp
            <div class="px-6 py-3 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <div class="flex items-center gap-8 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full" style="background: {{ $primaryColor }}"></span>
                        <span class="text-gray-600 dark:text-gray-400">Materias:</span>
                        <span class="font-bold" style="color: {{ $primaryColor }}">{{ $stats['total'] }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        <span class="text-gray-600 dark:text-gray-400">Aprobadas:</span>
                        <span class="font-bold text-emerald-600">{{ $stats['aprobadas'] }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                        <span class="text-gray-600 dark:text-gray-400">Perdidas:</span>
                        <span class="font-bold text-rose-600">{{ $stats['perdidas'] }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                        <span class="text-gray-600 dark:text-gray-400">Pendientes:</span>
                        <span class="font-bold text-amber-600">{{ $stats['sinCalificar'] ?? 0 }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <span class="text-xs text-gray-500 dark:text-gray-400 block">Promedio</span>
                        <span class="text-lg font-black" style="color: {{ $secondaryColor }}">{{ $stats['promedio'] ?? '—' }}</span>
                    </div>
                    <div class="px-4 py-2 rounded-lg {{ $stats['porcentajeAprobacion'] >= 60 ? 'bg-emerald-500' : 'bg-rose-500' }} text-white text-center">
                        <span class="text-xs block">Aprobación</span>
                        <span class="text-lg font-black">{{ $stats['porcentajeAprobacion'] }}%</span>
                    </div>
                </div>
            </div>
        </div>

        @if(!$enrollment)
            <div class="bg-white dark:bg-gray-800 rounded-xl p-12 text-center shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-5xl mb-4">🎒</div>
                <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300">Sin matrícula activa</h3>
                <p class="text-gray-500 mt-2">No tienes matrícula para este año escolar.</p>
            </div>
        @elseif($subjects->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl p-12 text-center shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-5xl mb-4">📋</div>
                <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300">Sin asignaturas</h3>
                <p class="text-gray-500 mt-2">Aún no hay asignaturas asignadas para este período.</p>
            </div>
        @else
            {{-- Lista de materias --}}
            <div class="space-y-3">
                @foreach($subjects as $item)
                    @php
                        $isPassing = $item->has_grades && $item->final_score >= 3.0;
                        $hasGrades = $item->has_grades;
                    @endphp
                    
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                        {{-- Fila principal con todo horizontal --}}
                        <div class="flex items-center gap-4">
                            {{-- Icono materia --}}
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center text-white font-bold text-sm flex-shrink-0 {{ $hasGrades ? ($isPassing ? 'bg-emerald-500' : 'bg-rose-500') : 'bg-gray-400' }}">
                                {{ strtoupper(substr($item->subject->name, 0, 2)) }}
                            </div>
                            
                            {{-- Info materia --}}
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-gray-900 dark:text-white truncate">{{ $item->subject->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $item->teacher->name }}</p>
                            </div>
                            
                            {{-- Notas en fila horizontal --}}
                            <div class="flex items-center gap-2">
                                {{-- Tareas --}}
                                <div class="w-16 h-14 rounded-lg flex flex-col items-center justify-center" style="background: {{ $primaryColor }}20">
                                    <span class="text-[10px] font-bold" style="color: {{ $primaryColor }}">TAREAS</span>
                                    <span class="text-base font-black" style="color: {{ $primaryColor }}">{{ $item->tasks_score ? number_format($item->tasks_score, 1) : '—' }}</span>
                                </div>
                                
                                {{-- Evaluaciones --}}
                                <div class="w-16 h-14 rounded-lg flex flex-col items-center justify-center" style="background: {{ $secondaryColor }}20">
                                    <span class="text-[10px] font-bold" style="color: {{ $secondaryColor }}">EVAL.</span>
                                    <span class="text-base font-black" style="color: {{ $secondaryColor }}">{{ $item->evaluations_score ? number_format($item->evaluations_score, 1) : '—' }}</span>
                                </div>
                                
                                {{-- Autoevaluación --}}
                                <div class="w-16 h-14 rounded-lg flex flex-col items-center justify-center" style="background: {{ $accentColor }}20">
                                    <span class="text-[10px] font-bold" style="color: {{ $accentColor }}">AUTO.</span>
                                    <span class="text-base font-black" style="color: {{ $accentColor }}">{{ $item->self_score ? number_format($item->self_score, 1) : '—' }}</span>
                                </div>
                                
                                {{-- Final --}}
                                <div class="w-20 h-14 rounded-lg {{ $hasGrades ? ($isPassing ? 'bg-emerald-100' : 'bg-rose-100') : 'bg-gray-100' }} flex flex-col items-center justify-center">
                                    <span class="text-[10px] font-bold {{ $hasGrades ? ($isPassing ? 'text-emerald-600' : 'text-rose-600') : 'text-gray-500' }}">FINAL</span>
                                    <span class="text-xl font-black {{ $hasGrades ? ($isPassing ? 'text-emerald-700' : 'text-rose-700') : 'text-gray-400' }}">{{ $hasGrades ? number_format($item->final_score, 1) : '—' }}</span>
                                </div>
                            </div>
                            
                            {{-- Estado --}}
                            <div class="flex-shrink-0">
                                @if($hasGrades)
                                    @if($isPassing)
                                        <span style="background-color: #16a34a; color: white; padding: 10px 20px; border-radius: 9999px; font-size: 14px; font-weight: bold; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                            APROBÓ
                                        </span>
                                    @else
                                        <span style="background-color: #dc2626; color: white; padding: 10px 20px; border-radius: 9999px; font-size: 14px; font-weight: bold; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                            PERDIÓ
                                        </span>
                                    @endif
                                @else
                                    <span style="background-color: #6b7280; color: white; padding: 10px 20px; border-radius: 9999px; font-size: 14px; font-weight: bold; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        PENDIENTE
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Info adicional: Comportamiento y Fallas --}}
                        @if($hasGrades)
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700 flex items-center gap-6">
                                <div class="flex items-center gap-2">
                                    <span style="font-size: 14px; font-weight: 600; color: #374151;">Comportamiento:</span>
                                    @if($item->behavior)
                                        @if($item->behavior === 'SUPERIOR')
                                            <span style="background-color: #16a34a; color: white; padding: 6px 16px; border-radius: 9999px; font-size: 12px; font-weight: bold;">SUPERIOR</span>
                                        @elseif($item->behavior === 'ALTO')
                                            <span style="background-color: #2563eb; color: white; padding: 6px 16px; border-radius: 9999px; font-size: 12px; font-weight: bold;">ALTO</span>
                                        @elseif($item->behavior === 'BASICO')
                                            <span style="background-color: #eab308; color: white; padding: 6px 16px; border-radius: 9999px; font-size: 12px; font-weight: bold;">BÁSICO</span>
                                        @elseif($item->behavior === 'BAJO')
                                            <span style="background-color: #dc2626; color: white; padding: 6px 16px; border-radius: 9999px; font-size: 12px; font-weight: bold;">BAJO</span>
                                        @else
                                            <span style="background-color: #6b7280; color: white; padding: 6px 16px; border-radius: 9999px; font-size: 12px; font-weight: bold;">{{ $item->behavior }}</span>
                                        @endif
                                    @else
                                        <span style="background-color: #9ca3af; color: white; padding: 6px 16px; border-radius: 9999px; font-size: 12px; font-weight: bold;">Sin registrar</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <span style="font-size: 14px; font-weight: 600; color: #374151;">Inasistencias:</span>
                                    <span style="background-color: {{ ($item->absences ?? 0) > 5 ? '#dc2626' : '#6b7280' }}; color: white; padding: 6px 16px; border-radius: 9999px; font-size: 12px; font-weight: bold;">{{ $item->absences ?? 0 }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
        
        {{-- Footer con ponderación --}}
        <div class="flex items-center justify-center gap-6 text-sm text-gray-600 dark:text-gray-400 py-2">
            <span class="flex items-center gap-2">
                <span class="w-4 h-4 rounded" style="background: {{ $primaryColor }}"></span>
                Tareas <strong style="color: {{ $primaryColor }}">40%</strong>
            </span>
            <span class="flex items-center gap-2">
                <span class="w-4 h-4 rounded" style="background: {{ $secondaryColor }}"></span>
                Evaluaciones <strong style="color: {{ $secondaryColor }}">50%</strong>
            </span>
            <span class="flex items-center gap-2">
                <span class="w-4 h-4 rounded" style="background: {{ $accentColor }}"></span>
                Autoevaluación <strong style="color: {{ $accentColor }}">10%</strong>
            </span>
            <span class="px-3 py-1 bg-emerald-500 text-white rounded-full text-xs font-bold">
                Aprueba ≥ 3.0
            </span>
        </div>
    </div>
</x-filament-panels::page>
