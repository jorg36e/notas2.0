<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Header --}}
        <div style="background: linear-gradient(to right, #7c3aed, #6366f1, #8b5cf6) !important;" class="rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div style="background-color: rgba(255,255,255,0.2) !important;" class="flex h-14 w-14 items-center justify-center rounded-2xl backdrop-blur-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" class="h-7 w-7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold" style="color: white !important;">
                            Mis Asignaturas
                        </h2>
                        <p class="mt-1" style="color: #e0e7ff !important;">
                            Selecciona una asignatura para gestionar las calificaciones
                        </p>
                    </div>
                </div>
                <div style="background-color: rgba(255,255,255,0.2) !important;" class="rounded-xl px-5 py-3 text-center backdrop-blur-sm">
                    <div class="text-3xl font-bold" style="color: white !important;">
                        {{ $this->getAssignmentsCount() }}
                    </div>
                    <div class="text-sm" style="color: #e0e7ff !important;">Asignaturas</div>
                </div>
            </div>
        </div>

        {{-- Panel de Carga Masiva de Notas --}}
        <div style="background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;" class="rounded-2xl p-6 shadow-xl">
            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div style="background-color: rgba(255,255,255,0.2) !important;" class="flex h-12 w-12 items-center justify-center rounded-xl backdrop-blur-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold" style="color: white !important;">
                            📊 Carga Masiva de Notas con Excel
                        </h3>
                        <p class="text-sm" style="color: #d1fae5 !important;">
                            Descarga una plantilla con TODAS tus asignaturas, llénala offline y súbela para actualizar todas las notas de una vez
                        </p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full lg:w-auto">
                    {{-- Selector de Periodo --}}
                    <div class="flex items-center gap-2">
                        <label class="text-sm font-medium" style="color: white !important;">Periodo:</label>
                        <select 
                            wire:model.live="selectedPeriod" 
                            class="rounded-lg border-0 px-4 py-2 text-sm font-medium shadow-sm"
                            style="background-color: rgba(255,255,255,0.95) !important; color: #047857 !important;"
                        >
                            @foreach($this->getPeriods() as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Botón Descargar --}}
                    <button 
                        wire:click="downloadBulkExcel"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-wait"
                        @if(!$this->isPeriodEditable()) disabled @endif
                        class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold shadow-lg transition-all hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                        style="background-color: white !important; color: #047857 !important;"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        <span wire:loading.remove wire:target="downloadBulkExcel">Descargar Plantilla</span>
                        <span wire:loading wire:target="downloadBulkExcel">Generando...</span>
                    </button>

                    {{-- Botón Subir --}}
                    <button 
                        wire:click="openImportModal"
                        @if(!$this->isPeriodEditable()) disabled @endif
                        class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold shadow-lg transition-all hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                        style="background-color: #fbbf24 !important; color: #78350f !important;"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                        </svg>
                        Subir Excel
                    </button>
                </div>
            </div>

            {{-- Nota informativa --}}
            @if(!$this->isPeriodEditable())
                <div class="mt-4 rounded-lg p-3" style="background-color: rgba(255,255,255,0.2) !important;">
                    <p class="text-sm" style="color: #fef3c7 !important;">
                        ⚠️ El periodo seleccionado está finalizado. No se pueden modificar las calificaciones.
                    </p>
                </div>
            @else
                <div class="mt-4 rounded-lg p-3" style="background-color: rgba(255,255,255,0.15) !important;">
                    <p class="text-sm" style="color: #d1fae5 !important;">
                        💡 <strong>Tip:</strong> El archivo Excel tendrá una hoja por cada asignatura. Cada hoja incluye la lista de estudiantes 
                        (tanto para grados únicos como multigrados). Puedes editar todas las notas offline y subirlas de una sola vez.
                    </p>
                </div>
            @endif
        </div>

        {{-- Instrucciones rápidas --}}
        <div style="background-color: #eff6ff !important; border: 1px solid #bfdbfe !important;" class="rounded-xl p-4">
            <div class="flex items-center gap-3">
                <div style="background-color: #3b82f6 !important;" class="flex h-8 w-8 items-center justify-center rounded-lg flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="white" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                </div>
                <p class="text-sm" style="color: #1e40af !important;">
                    <strong>Tip:</strong> Haz clic en el botón <strong>"Calificar"</strong> de cada asignatura para acceder al tablero de calificaciones individual, 
                    o usa la <strong>carga masiva</strong> de arriba para calificar todas las asignaturas desde un solo archivo Excel.
                </p>
            </div>
        </div>

        {{-- Tabla de asignaturas --}}
        <div style="background-color: #ffffff !important; border: 1px solid #e5e7eb !important;" class="rounded-2xl shadow-lg overflow-hidden">
            {{ $this->table }}
        </div>

        {{-- Leyenda de estados --}}
        <div style="background-color: #f9fafb !important; border: 1px solid #e5e7eb !important;" class="rounded-xl p-4">
            <div class="flex flex-wrap items-center gap-4">
                <span class="text-xs font-medium" style="color: #6b7280 !important;">Leyenda:</span>
                <span style="background-color: #dcfce7 !important; color: #166534 !important;" class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium">
                    <span style="background-color: #22c55e !important;" class="h-2 w-2 rounded-full"></span>
                    Periodo Activo
                </span>
                <span style="background-color: #fee2e2 !important; color: #991b1b !important;" class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium">
                    <span style="background-color: #ef4444 !important;" class="h-2 w-2 rounded-full"></span>
                    Periodo Cerrado
                </span>
                <span style="background-color: #fef3c7 !important; color: #92400e !important;" class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium">
                    <span style="background-color: #f59e0b !important;" class="h-2 w-2 rounded-full"></span>
                    Multigrado
                </span>
                <span style="background-color: #dbeafe !important; color: #1e40af !important;" class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-3 w-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                    Calificar
                </span>
            </div>
        </div>
    </div>

    {{-- Modal de Importación --}}
    @if($showImportModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(0,0,0,0.5) !important;">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    📤 Subir Archivo Excel con Notas
                </h3>
                <button 
                    wire:click="closeImportModal"
                    class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                {{-- Instrucciones --}}
                <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                    <p class="text-sm text-blue-700 dark:text-blue-300">
                        <strong>Instrucciones:</strong><br>
                        1. Seleccione el archivo Excel que descargó previamente.<br>
                        2. Asegúrese de haber llenado las notas en las columnas amarillas.<br>
                        3. NO modifique las columnas de identificación ni el nombre del estudiante.<br>
                        4. El sistema procesará todas las hojas (asignaturas) automáticamente.
                    </p>
                </div>

                {{-- Input de archivo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Archivo Excel (.xlsx)
                    </label>
                    <input 
                        type="file" 
                        wire:model="bulkExcelFile"
                        accept=".xlsx,.xls"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-900/20 dark:file:text-emerald-400"
                    />
                    @error('bulkExcelFile') 
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Estado de carga --}}
                <div wire:loading wire:target="bulkExcelFile" class="text-sm text-gray-500">
                    Cargando archivo...
                </div>

                {{-- Botones --}}
                <div class="flex justify-end gap-3 pt-4">
                    <button 
                        wire:click="closeImportModal"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
                    >
                        Cancelar
                    </button>
                    <button 
                        wire:click="uploadBulkExcel"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50 cursor-wait"
                        @if(!$bulkExcelFile) disabled @endif
                        class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                        style="background-color: #059669 !important;"
                    >
                        <span wire:loading.remove wire:target="uploadBulkExcel">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                            </svg>
                        </span>
                        <span wire:loading wire:target="uploadBulkExcel">
                            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                        <span wire:loading.remove wire:target="uploadBulkExcel">Procesar Archivo</span>
                        <span wire:loading wire:target="uploadBulkExcel">Procesando...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</x-filament-panels::page>
