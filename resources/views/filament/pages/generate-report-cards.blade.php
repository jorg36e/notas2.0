<x-filament-panels::page>
    <div class="space-y-6">
        
        {{-- ==================== HEADER CARD ==================== --}}
        <div style="background: linear-gradient(135deg, #1e40af 0%, #7c3aed 100%) !important;" class="rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold flex items-center gap-3" style="color: white !important;">
                        <x-heroicon-o-document-text class="w-8 h-8" />
                        Generación de Boletines V2
                    </h2>
                    <p class="mt-2 text-base" style="color: rgba(255,255,255,0.9) !important;">
                        Sistema optimizado de generación masiva de informes académicos.
                        <br>
                        <span class="text-sm opacity-80">
                            Incluye soporte para multigrados, escala institucional y diseño optimizado para impresión.
                        </span>
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-5xl font-bold" style="color: white !important;">{{ $totalReports }}</div>
                    <div class="text-sm" style="color: rgba(255,255,255,0.8) !important;">Boletines disponibles</div>
                    @if($school_year_id && $period_id)
                        <div class="mt-2 inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs" 
                             style="background: rgba(255,255,255,0.2) !important;">
                            <x-heroicon-o-check-circle class="w-4 h-4" />
                            Filtros activos
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ==================== FORM SECTION ==================== --}}
        <div style="background-color: #ffffff !important; border: 1px solid #e5e7eb !important;" class="rounded-xl shadow-sm">
            {{ $this->form }}
        </div>

        {{-- ==================== STATISTICS CARD (after generation) ==================== --}}
        @if(!empty($generationStats))
            <div style="background-color: #ecfdf5 !important; border: 2px solid #10b981 !important;" class="rounded-xl p-6">
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2" style="color: #065f46 !important;">
                    <x-heroicon-o-chart-bar class="w-6 h-6" />
                    Resultado de la Generación
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div style="background-color: white !important;" class="rounded-lg p-4 text-center shadow-sm">
                        <div class="text-3xl font-bold" style="color: #059669 !important;">{{ $generationStats['success'] ?? 0 }}</div>
                        <div class="text-sm" style="color: #6b7280 !important;">Generados</div>
                    </div>
                    <div style="background-color: white !important;" class="rounded-lg p-4 text-center shadow-sm">
                        <div class="text-3xl font-bold" style="color: #dc2626 !important;">{{ $generationStats['errors'] ?? 0 }}</div>
                        <div class="text-sm" style="color: #6b7280 !important;">Errores</div>
                    </div>
                    <div style="background-color: white !important;" class="rounded-lg p-4 text-center shadow-sm">
                        <div class="text-3xl font-bold" style="color: #7c3aed !important;">{{ $generationStats['total'] ?? 0 }}</div>
                        <div class="text-sm" style="color: #6b7280 !important;">Total</div>
                    </div>
                    <div style="background-color: white !important;" class="rounded-lg p-4 text-center shadow-sm">
                        <div class="text-3xl font-bold" style="color: #2563eb !important;">{{ number_format($generationStats['time'] ?? 0, 1) }}s</div>
                        <div class="text-sm" style="color: #6b7280 !important;">Tiempo</div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ==================== PREVIEW SECTION ==================== --}}
        @if(count($previewData) > 0)
            <div style="background-color: #ffffff !important; border: 1px solid #e5e7eb !important;" class="rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold flex items-center gap-2" style="color: #111827 !important;">
                        <x-heroicon-o-eye class="w-5 h-5" style="color: #6366f1 !important;" />
                        Distribución por Sede y Grado
                    </h3>
                    <span class="text-sm px-3 py-1 rounded-full" style="background-color: #f3f4f6 !important; color: #374151 !important;">
                        {{ count($previewData) }} {{ count($previewData) === 1 ? 'sede' : 'sedes' }}
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($previewData as $sede)
                        <div style="background-color: #f9fafb !important; border: 1px solid #e5e7eb !important; border-left: 4px solid #6366f1 !important;" class="rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold flex items-center gap-2" style="color: #111827 !important;">
                                    <x-heroicon-o-building-office-2 class="w-4 h-4" style="color: #6366f1 !important;" />
                                    {{ $sede['name'] }}
                                </h4>
                                <span style="background-color: #4f46e5 !important; color: white !important;" class="text-sm font-bold px-3 py-1 rounded-full">
                                    {{ $sede['total'] }}
                                </span>
                            </div>
                            
                            <div class="space-y-2">
                                @foreach($sede['grades'] as $grade)
                                    <div class="flex items-center justify-between text-sm py-1 border-b border-gray-100 last:border-0">
                                        <span style="color: #4b5563 !important;">
                                            <x-heroicon-o-academic-cap class="w-3 h-3 inline mr-1" style="color: #9ca3af !important;" />
                                            {{ $grade['name'] }}
                                        </span>
                                        <span style="background-color: #e0e7ff !important; color: #4338ca !important;" class="font-semibold px-2 py-0.5 rounded text-xs">
                                            {{ $grade['count'] }} est.
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @elseif($school_year_id && $period_id)
            <div style="background-color: #fefce8 !important; border: 1px solid #fde047 !important;" class="rounded-xl p-6">
                <div class="flex items-center gap-3">
                    <x-heroicon-o-exclamation-triangle class="w-8 h-8" style="color: #eab308 !important;" />
                    <div>
                        <h4 class="font-semibold text-lg" style="color: #854d0e !important;">Sin estudiantes encontrados</h4>
                        <p class="text-sm mt-1" style="color: #a16207 !important;">
                            No se encontraron estudiantes matriculados con los filtros seleccionados.
                            Verifica que existan matrículas activas para el año escolar y período elegidos.
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div style="background-color: #f3f4f6 !important; border: 1px dashed #9ca3af !important;" class="rounded-xl p-8 text-center">
                <x-heroicon-o-document-magnifying-glass class="w-16 h-16 mx-auto mb-4" style="color: #9ca3af !important;" />
                <h4 class="font-semibold text-lg" style="color: #6b7280 !important;">Selecciona los filtros</h4>
                <p class="text-sm mt-2" style="color: #9ca3af !important;">
                    Elige al menos el año escolar y período para ver la distribución de estudiantes.
                </p>
            </div>
        @endif

        {{-- ==================== ACTION BUTTONS ==================== --}}
        <div style="background-color: #ffffff !important; border: 1px solid #e5e7eb !important;" class="rounded-xl shadow-sm p-6">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-3">
                    {{-- Preview Button --}}
                    <x-filament::button
                        wire:click="generatePreview"
                        color="gray"
                        icon="heroicon-o-eye"
                        :disabled="$totalReports === 0"
                        size="lg"
                    >
                        Vista Previa
                    </x-filament::button>
                    
                    {{-- Generate All Button --}}
                    <x-filament::button
                        wire:click="generateAll"
                        icon="heroicon-o-document-arrow-down"
                        size="lg"
                        :disabled="$totalReports === 0 || $isGenerating"
                        style="background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;"
                    >
                        @if($isGenerating)
                            <x-filament::loading-indicator class="h-5 w-5 mr-2" />
                            Generando {{ $totalReports }} boletines...
                        @else
                            <x-heroicon-o-arrow-down-tray class="w-5 h-5 mr-2" />
                            Generar {{ $totalReports }} Boletines (ZIP)
                        @endif
                    </x-filament::button>
                    
                    @if($downloadUrl)
                        {{-- Clear Button --}}
                        <x-filament::button
                            wire:click="clearDownload"
                            color="gray"
                            icon="heroicon-o-x-mark"
                            size="sm"
                        >
                            Limpiar
                        </x-filament::button>
                    @endif
                </div>
                
                {{-- Download Link --}}
                @if($downloadUrl)
                    <div class="flex items-center gap-3">
                        <span class="text-sm" style="color: #6b7280 !important;">Archivo listo:</span>
                        <a 
                            href="{{ $downloadUrl }}" 
                            target="_blank"
                            style="background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%) !important; color: white !important;"
                            class="inline-flex items-center gap-2 px-5 py-3 font-bold rounded-xl transition-all hover:scale-105 hover:shadow-lg"
                        >
                            <x-heroicon-o-arrow-down-tray class="w-6 h-6" />
                            <span>Descargar Boletines</span>
                        </a>
                    </div>
                @endif
            </div>
            
            {{-- Progress Indicator --}}
            @if($isGenerating)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-indigo-600 h-2.5 rounded-full animate-pulse" style="width: 100%"></div>
                        </div>
                        <span class="text-sm whitespace-nowrap" style="color: #6b7280 !important;">
                            Procesando...
                        </span>
                    </div>
                    <p class="text-xs mt-2" style="color: #9ca3af !important;">
                        Generando PDFs y comprimiendo en archivo ZIP. Este proceso puede tardar algunos minutos.
                    </p>
                </div>
            @endif
        </div>

        {{-- ==================== SCALE INFO ==================== --}}
        <div style="background-color: #f8fafc !important; border: 1px solid #e2e8f0 !important;" class="rounded-xl p-6">
            <h4 class="font-semibold mb-4 flex items-center gap-2" style="color: #334155 !important;">
                <x-heroicon-o-scale class="w-5 h-5" style="color: #6366f1 !important;" />
                Escala de Valoración en los Boletines
            </h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="flex items-center gap-2 p-2 rounded" style="background-color: #ede9fe !important;">
                    <span class="w-3 h-3 rounded-full" style="background-color: #7c3aed !important;"></span>
                    <span class="text-sm font-medium" style="color: #6d28d9 !important;">SUPERIOR (4.6-5.0)</span>
                </div>
                <div class="flex items-center gap-2 p-2 rounded" style="background-color: #dbeafe !important;">
                    <span class="w-3 h-3 rounded-full" style="background-color: #2563eb !important;"></span>
                    <span class="text-sm font-medium" style="color: #1d4ed8 !important;">ALTO (4.0-4.5)</span>
                </div>
                <div class="flex items-center gap-2 p-2 rounded" style="background-color: #fef3c7 !important;">
                    <span class="w-3 h-3 rounded-full" style="background-color: #d97706 !important;"></span>
                    <span class="text-sm font-medium" style="color: #b45309 !important;">BÁSICO (3.0-3.9)</span>
                </div>
                <div class="flex items-center gap-2 p-2 rounded" style="background-color: #fee2e2 !important;">
                    <span class="w-3 h-3 rounded-full" style="background-color: #dc2626 !important;"></span>
                    <span class="text-sm font-medium" style="color: #dc2626 !important;">BAJO (1.0-2.9)</span>
                </div>
            </div>
        </div>

        {{-- ==================== INSTRUCTIONS ==================== --}}
        <div style="background-color: #eff6ff !important; border: 1px solid #bfdbfe !important;" class="rounded-xl p-6">
            <h4 class="font-semibold mb-4 flex items-center gap-2" style="color: #1e40af !important;">
                <x-heroicon-o-information-circle class="w-5 h-5" />
                Instrucciones de Uso
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h5 class="font-medium mb-2" style="color: #1e3a8a !important;">Generación Básica</h5>
                    <ul class="space-y-2 text-sm" style="color: #1d4ed8 !important;">
                        <li class="flex items-start gap-2">
                            <span style="background-color: #3b82f6 !important; color: white !important;" class="w-5 h-5 rounded-full flex items-center justify-center text-xs flex-shrink-0">1</span>
                            Selecciona el <strong>Año Escolar</strong> y el <strong>Período</strong>
                        </li>
                        <li class="flex items-start gap-2">
                            <span style="background-color: #3b82f6 !important; color: white !important;" class="w-5 h-5 rounded-full flex items-center justify-center text-xs flex-shrink-0">2</span>
                            Filtra por <strong>Sede</strong> y/o <strong>Grado</strong> (opcional)
                        </li>
                        <li class="flex items-start gap-2">
                            <span style="background-color: #3b82f6 !important; color: white !important;" class="w-5 h-5 rounded-full flex items-center justify-center text-xs flex-shrink-0">3</span>
                            Haz clic en <strong>Generar Boletines</strong>
                        </li>
                        <li class="flex items-start gap-2">
                            <span style="background-color: #3b82f6 !important; color: white !important;" class="w-5 h-5 rounded-full flex items-center justify-center text-xs flex-shrink-0">4</span>
                            Descarga el archivo <strong>ZIP</strong> generado
                        </li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-medium mb-2" style="color: #1e3a8a !important;">Características V2</h5>
                    <ul class="space-y-1 text-sm" style="color: #1d4ed8 !important;">
                        <li class="flex items-center gap-2">
                            <x-heroicon-o-check class="w-4 h-4" style="color: #10b981 !important;" />
                            Soporte completo para <strong>multigrados</strong>
                        </li>
                        <li class="flex items-center gap-2">
                            <x-heroicon-o-check class="w-4 h-4" style="color: #10b981 !important;" />
                            Escala de valoración institucional incluida
                        </li>
                        <li class="flex items-center gap-2">
                            <x-heroicon-o-check class="w-4 h-4" style="color: #10b981 !important;" />
                            Ranking de estudiantes por grupo
                        </li>
                        <li class="flex items-center gap-2">
                            <x-heroicon-o-check class="w-4 h-4" style="color: #10b981 !important;" />
                            Diseño optimizado para impresión
                        </li>
                        <li class="flex items-center gap-2">
                            <x-heroicon-o-check class="w-4 h-4" style="color: #10b981 !important;" />
                            Estado de aprobación automático
                        </li>
                        <li class="flex items-center gap-2">
                            <x-heroicon-o-check class="w-4 h-4" style="color: #10b981 !important;" />
                            Firma digital y director de grupo
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
    </div>
</x-filament-panels::page>
